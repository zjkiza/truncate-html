<?php

declare(strict_types=1);

namespace ZJKiza\TruncateHtml\Strategy;

use ZJKiza\TruncateHtml\Contract\TruncateStrategyInterface;

final class ByteStrategy implements TruncateStrategyInterface
{
    #[\Override]
    public function length(string $str, string $enc): int
    {
        return \strlen($str);
    }

    #[\Override]
    public function cut(string $str, int $limit, string $enc): string
    {
        return \mb_strcut($str, 0, $limit, $enc);
    }
}
