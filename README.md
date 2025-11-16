# PHP Package: Truncate Html

A flexible HTML truncation service that safely truncates HTML content without breaking the tag structure. Enable different ways of measuring length by bytes or by characters.

# About the Package


# Installation

Add the package to your composer.json file:
```
composer require zjkiza/truncate-html
```
If you're using the Composer autoloader, all necessary files will be automatically included.

# Working with the Package

## Usage Without a Framework 

## Usage with a Framework

If you're using a PHP framework, you can integrate this package through dependency injection. 

1. Register the Interface and Implementation:
   -
   -

### Example in Symfony

- Service registration in services.yaml:
  ```yaml
    services:

    ```
- Usage in a code:
    ```php

   ```
### Example in Laravel

- Add the binding in a service provider, such as `AppServiceProvider`

   ```php

   ```

- Usage in a code:

   ```php

   ```

# Package Benefits


# Example :

Truncate html by length byte:

```php

    use ZJKiza\TruncateHtml\TruncateHtml;
    use ZJKiza\TruncateHtml\Strategy\ByteStrategy;

    $html= '<div>...</div>'    
    $limit = 400; 
    
    $truncateService = new TruncateHtml(new ByteStrategy());
    $truncatedHtml = $truncate->execute($html, $limit);

```

Truncate html by length chart:

```php

    use ZJKiza\TruncateHtml\TruncateHtml;
    use ZJKiza\TruncateHtml\Strategy\ChartStrategy;
    
    $html= '<div>...</div>'    
    $limit = 400; 

    $truncateService = new TruncateHtml(new CharStrategy());
    $truncatedHtml = $truncate->execute($html, $limit);

```