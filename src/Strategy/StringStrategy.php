<?php

declare(strict_types=1);

namespace ZJKiza\TruncateHtml\Strategy;

use ZJKiza\TruncateHtml\Contract\TruncateStrategyInterface;
use ZJKiza\TruncateHtml\Enum\Strategy;

final class StringStrategy implements TruncateStrategyInterface
{
    public function length(string $str, string $enc): int
    {
        return \mb_strlen($str, $enc);
    }

    public function cut(string $str, int $limit, string $enc): string
    {
        return \mb_substr($str, 0, $limit, $enc);
    }

    public function key(): Strategy
    {
        return Strategy::String;
    }
}
