<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    // The package supports PHP 8.1+, so the code must not be upgraded to a newer syntax level.
    ->withPhpSets(php81: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        earlyReturn: true,
        typeDeclarations: true,
    );
