<?php

declare(strict_types=1);

namespace ZJKiza\TruncateHtml\Contract;

use ZJKiza\TruncateHtml\Enum\Strategy;

interface TruncateHtmlInterface
{
    public function execute(string $html, Strategy $strategy, int $limit = 950, string $enc = 'UTF-8'): string;
}
