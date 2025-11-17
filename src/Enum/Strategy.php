<?php

declare(strict_types=1);

namespace ZJKiza\TruncateHtml\Enum;

enum Strategy: string
{
    case Byte = 'byte';
    case Character = 'character';
}
