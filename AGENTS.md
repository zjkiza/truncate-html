# AGENTS.md

Guidance for AI agents (and humans) working on `zjkiza/truncate-html`. Usage instructions for the package itself are in [README.md](./README.md).

## What this project is

A small PHP library that truncates HTML by **bytes** or **characters** without leaving unclosed tags.
Supported PHP: **8.1 – 8.5** (`"php": ">=8.1 <8.6"`), requires `ext-mbstring`. Namespace `ZJKiza\TruncateHtml\` → `src/`.

```
src/TruncateHtml.php               final class, entry point: execute(string $html, Strategy $strategy, int $limit = 950, string $enc = 'UTF-8')
src/Contract/TruncateHtmlInterface public API contract (what consumers type-hint)
src/Contract/TruncateStrategyInterface  length() / cut() / key() — one implementation per strategy
src/Strategy/BytesStrategy         strlen + mb_strcut
src/Strategy/CharactersStrategy    mb_strlen + mb_substr
src/Enum/Strategy                  Bytes | Characters (the key used to pick a strategy)
src/Enum/TypeTag                   Open | Close | Comment | Decl | Other (result of tag parsing)
tests/Functionality/TruncateHtmlTest.php   PHPUnit tests (data providers)
```

How `execute()` works: tokenize into tags and text → walk tokens while tracking the cost of closing tags that will be
needed (`$closingCost`) → cut the last text token with the strategy → append closing tags for still-open elements.
The result must never exceed `$limit` (bytes or characters, depending on the strategy).

## Hard constraints

- **Code must run on PHP 8.1.** Do not use syntax/features from 8.2+ (readonly classes, `true`/`null`/`false` standalone
  types, DNF types, `#[\Override]`, typed class constants, `json_validate`, …). Rector is configured with `php81: true` on purpose.
- Every file has `declare(strict_types=1);`. Native functions are called with a leading backslash (`\strlen`) — enforced by
  PHP-CS-Fixer (`native_function_invocation`).
- Static analysis must stay clean: **PHPStan level `max`** and **Psalm level 2**. Do not lower levels or add baselines;
  fix the code (a narrowly scoped `@phpstan-ignore-line` already exists in `TruncateHtml::parseTag`).
- Public API (`TruncateHtmlInterface`, `Strategy` enum, constructor `array $voidTag`) is semver-relevant. Note it in `CHANGELOG.md`.
- `composer.lock` is intentionally **not versioned** (git-ignored): dev dependencies are resolved per PHP version
  (PHPUnit `^10.5 || ^11.5 || ^12.4`, since PHPUnit 12 needs PHP 8.3+). Never commit a lock file; the Docker build uses `composer.lock*` glob.

## Commands

Everything runs in the Docker container `php_truncate_html` (see `docker-compose.yaml`, `docker/php/Dockerfile`).

| Goal | Command |
| --- | --- |
| Full CI (all checks) on default PHP 8.4 | `bin/ci.py` (add `--verbose` for each check's output) |
| Full CI on a specific PHP version | `PHP_VERSION=8.3 bin/ci.py` (8.1, 8.2, 8.3, 8.4, 8.5) |
| Start / stop the container | `make up` / `make down` (or `bin/run.py`, `bin/shutdown.py`) |
| Shell in the container | `make attach` (or `bin/attach.py`) |
| Tests with coverage / without | `make test` / `make test-without-coverage` |

`bin/ci.py` needs Python 3 with `click`, `rich`, `python-dotenv` and Docker; it builds the image for `PHP_VERSION`,
runs the checks listed in `bin/config.py` (`commands`) and stops the container on success.

Individual checks (inside the container, `/www`):

```bash
composer validate
composer composer-unused
composer run phpunit-ci        # vendor/bin/phpunit --testdox   (use `composer run phpunit` for HTML coverage → build/)
composer run phpstan
composer run psalm
composer run rector-check      # dry-run; `composer run rector` applies changes
composer run php-cs-fixer-check  # dry-run; `composer run php-cs-fixer` applies fixes
```

`vendor/` is bind-mounted from the host and is shared between PHP versions, so after switching `PHP_VERSION` the next run
reinstalls dependencies for that version. Without Docker, `composer install && vendor/bin/phpunit` works if PHP ≥ 8.1 with mbstring is installed locally.

## Definition of done

1. `PHP_VERSION=<min and max you can> bin/ci.py` passes — at minimum on 8.1 and 8.5 for anything touching syntax/types.
2. PHPUnit reports no warnings/deprecations (`OK (N tests, M assertions)`); add or update tests in
   `tests/Functionality/TruncateHtmlTest.php` for every behaviour change (cover both strategies and multibyte input).
3. `CHANGELOG.md` updated (new version block at the top: Added / Fixed / Changed); `README.md` updated if usage changes.
4. Don't commit unless asked.

## CI / GitHub

`.github/workflows/ci.yml` runs on push to `main` and on pull requests: a matrix over PHP 8.1–8.5, each job copies
`.env.dist` → `.env` (`.env` is git-ignored) and runs `PHP_VERSION=<v> python3 bin/ci.py --verbose`.
README badges (CI, Packagist version / PHP / downloads / license, PHPStan max, Psalm 2) point to `zjkiza/truncate-html`.

## Gotchas

- `phpunit.xml.dist` must not set `intl.*` ini values — the image has no `intl` extension and PHPUnit prints warnings.
- Rector 2 has no `LevelSetList`/`SetList::STRICT_BOOLEANS`; use the fluent `RectorConfig::configure()` API (see `rector.php`).
- Don't set `phpVersion` in `psalm.xml`; Psalm must follow the runtime PHP version.
- `bin/ci.py` must not use `docker exec -it` (fails without a TTY, e.g. on GitHub Actions).
- The container user is created with the host's UID/GID (`HOST_UID` / `HOST_GID`, set automatically by `bin/utility/dockerManager.py`)
  so the bind mount stays writable; the Dockerfile uses `groupadd -o`/`useradd -o` because the IDs may already exist in the image.
- `var/`, `build/`, `vendor/`, `.env` and `composer.lock` are git-ignored — don't put documentation there.
