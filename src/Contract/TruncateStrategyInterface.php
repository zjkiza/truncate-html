<?php

declare(strict_types=1);

namespace ZJKiza\TruncateHtml\Contract;

interface TruncateStrategyInterface
{
    public function length(string $str, string $enc): int;

    public function cut(string $str, int $limit, string $enc): string;
}
