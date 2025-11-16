<?php

declare(strict_types=1);

namespace ZJKiza\TruncateHtml\Strategy;

use ZJKiza\TruncateHtml\Contract\TruncateStrategyInterface;

final class ChartStrategy implements TruncateStrategyInterface
{
    #[\Override]
    public function length(string $str, string $enc): int
    {
        return \mb_strlen($str, $enc);
    }

    #[\Override]
    public function cut(string $str, int $limit, string $enc): string
    {
        return \mb_substr($str, 0, $limit, $enc);
    }
}
