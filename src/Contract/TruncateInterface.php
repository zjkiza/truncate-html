<?php

declare(strict_types=1);

namespace ZJKiza\TruncateHtml\Contract;

interface TruncateInterface
{
    public function execute(string $html, int $limit = 950, string $enc = 'UTF-8'): string;
}
