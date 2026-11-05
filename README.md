# PHP package: Truncate Html

[![CI](https://github.com/zjkiza/truncate-html/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/zjkiza/truncate-html/actions/workflows/ci.yml)
[![Latest version](https://img.shields.io/packagist/v/zjkiza/truncate-html)](https://packagist.org/packages/zjkiza/truncate-html)
[![PHP version](https://img.shields.io/packagist/dependency-v/zjkiza/truncate-html/php)](https://packagist.org/packages/zjkiza/truncate-html)
[![Downloads](https://img.shields.io/packagist/dt/zjkiza/truncate-html)](https://packagist.org/packages/zjkiza/truncate-html/stats)
[![License](https://img.shields.io/packagist/l/zjkiza/truncate-html)](LICENSE)
![PHPStan level max](https://img.shields.io/badge/PHPStan-level%20max-brightgreen)
![Psalm level 2](https://img.shields.io/badge/Psalm-level%202-brightgreen)

A flexible PHP service to safely truncate HTML content **without breaking tag structure**. Provides multiple truncation strategies—by bytes or by characters—making it ideal for displaying safe previews, snippets or social meta content.

---

**Flexible PHP service for safely shortening HTML content without breaking tag structure. It supports different truncation strategies: byte-wise or character-wise.**
---

## Features
- ✔️ Preserves valid HTML – no broken or unclosed tags
- ✔️ Supports truncating by bytes (for precise control, ex. for database or API)
- ✔️ Supports truncating by characters (multibyte safe)
- ✔️ Custom void tags
- ✔️ Simple API, PSR-4 autoload compatible
- ✔️ PHP 8.1 – 8.5, mbstring required

## Installation

Install via [Composer](https://getcomposer.org/):

```bash
composer require zjkiza/truncate-html
```

> You need PHP 8.1 – 8.5 and ext-mbstring enabled.

---

## Basic Usage

Truncate HTML to a byte or character limit, safely:

```php
use ZJKiza\TruncateHtml\TruncateHtml;
use ZJKiza\TruncateHtml\Enum\Strategy;

$html = '<div><p>Some rich <b>HTML</b> ...</p></div>';

$truncate = new TruncateHtml();

// --- Byte limit (precise control, for APIs/db):
$resultByte = $truncate->execute($html, Strategy::Bytes, 100);

// --- Character limit (supports multibyte chars!):
$resultChar = $truncate->execute($html, Strategy::Characters, 80);
```

After truncation, the string remains a valid HTML fragment.

---

### Output Examples

For byte/char limits, HTML tags remain valid:

- Input:
  ```html
  <div><p>Lorem <b>ipsum</b> ...</p></div>
  ```
- Truncated output (limit adjusted):
  ```html
  <div><p>Lorem <b>ipsum</b> ...</p></div>
  ```
- Even if the cut is inside a tag, all tags are properly closed!

---

## Truncation Strategies
- **Strategy::Bytes**: Cuts by bytes. Use when you need strict byte limits (DB, APIs, etc).
- **Strategy::Characters**: Cuts by characters using mbstring (multi-byte safe, e.g., for Unicode strings).

**Default encoding is UTF-8**, but can be overridden.

## Usage in Frameworks / (example: Laravel, Symfony)

> Register TruncateHtml as a service for injection.

### Symfony (services.yaml)
```yaml
services:
  ZJKiza\TruncateHtml\Contract\TruncateHtmlInterface:
      class: ZJKiza\TruncateHtml\TruncateHtml
```

Then simply inject as needed in your services/controllers.

### Laravel (in AppServiceProvider)

```php

use ZJKiza\TruncateHtml\Contract\TruncateHtmlInterface;
use ZJKiza\TruncateHtml\TruncateHtml;

public function register()
{
        $this->app->bind(TruncateHtmlInterface::class, function () {
            return TruncateHtml::create();
}
```
And type-hint `TruncateHtmlInterface` in your controllers/services.

---

## Advanced: Custom void/self-closing tags
If you need to support additional self-closing/void tags, pass them as an array when constructing:
```php
$truncate = new TruncateHtml(['customtag', ... ]);
```

## Testing

Run the unit tests locally:
```bash
composer install
vendor/bin/phpunit
```

The package is tested on PHP 8.1 – 8.5. The full CI pipeline (composer validate, composer-unused, PHPUnit, PHPStan,
Psalm, Rector and PHP-CS-Fixer) runs inside Docker; pass the PHP version through the `PHP_VERSION` variable
(requires Docker and Python 3 with `click`, `rich` and `python-dotenv`):
```bash
PHP_VERSION=8.3 bin/ci.py
```
When `PHP_VERSION` is omitted, PHP 8.4 is used. The same checks run on GitHub Actions for every supported PHP version.

---

## License
MIT License (c) 2025 Zoran Jankovic

---
## Author & Contact
- Zoran Jankovic

## Contributions
Pull requests and suggestions are welcome! See `tests/Functionality/TruncateHtmlTest.php` for examples.

---

## Changelog
See [CHANGELOG.md](./CHANGELOG.md)