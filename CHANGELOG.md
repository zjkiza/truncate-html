# Changelog

All notable changes to this project will be documented in this file.

## [0.7.0]
Added:
- Support for PHP 8.1 - 8.5 (`"php": ">=8.1 <8.6"` in composer)
- GitHub Actions workflow (`.github/workflows/ci.yml`) running the CI checks on PHP 8.1, 8.2, 8.3, 8.4 and 8.5
- `PHP_VERSION` variable for `bin/ci.py` and docker (e.g. `PHP_VERSION=8.3 bin/ci.py`), default is 8.4
- CI checks: `composer validate`, `composer-unused`, Rector and PHP-CS-Fixer in dry-run mode (`rector-check`, `php-cs-fixer-check`, `phpunit-ci` composer scripts)
- Badges in README.md, `Makefile` and `AGENTS.md`
- `.env.dist` used by CI to create `.env`

Fixed:
- PHPUnit warnings (`Failed to set "intl.default_locale"` and `"intl.error_level"`) - removed unused `intl` ini settings from `phpunit.xml.dist`
- Rector failing with `Undefined constant SetList::STRICT_BOOLEANS` - `rector.php` migrated to the Rector 2 fluent configuration (PHP 8.1 set, dead code, code quality, early return, type declarations)
- PHPUnit constraint widened to `^10.5 || ^11.5 || ^12.4` (PHPUnit 12 requires PHP 8.3+)
- Psalm no longer pinned to PHP 8.4 (`phpVersion` removed from `psalm.xml`)
- `bin/ci.py` works without a TTY (removed `docker exec -it`), supports both `docker-compose` and `docker compose`, container user gets the host UID/GID
- Docker image runs as an unprivileged user and installs composer dependencies in a cached layer

Changed:
- `composer.lock` is no longer versioned (the library is tested against a matrix of PHP versions, so dependencies are resolved per version)

## [0.6.2]
Optimization
- Docker optimization for development

## [0.6.1]
Fixed:
- Readme.md

## [0.6.0]
Fixed:
- PHP version in composer

Added
- Unit tests

## [0.5.0]
Initial version