<?php

declare(strict_types=1);

namespace ZJKiza\TruncateHtml\Contract;

use ZJKiza\TruncateHtml\Enum\Strategy;

interface TruncateStrategyInterface
{
    public function length(string $str, string $enc): int;

    public function cut(string $str, int $limit, string $enc): string;

    public function key(): Strategy;
}
