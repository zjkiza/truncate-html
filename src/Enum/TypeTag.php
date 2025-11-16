<?php

declare(strict_types=1);

namespace ZJKiza\TruncateHtml\Enum;

enum TypeTag: string
{
    case Open = 'open';
    case Close = 'close';
    case Comment = 'comment';
    case Decl = 'decl';
    case Other = 'other';
}
