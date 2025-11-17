<?php

declare(strict_types=1);

namespace ZJKiza\TruncateHtml\Tests\Functionality;

use PHPUnit\Framework\TestCase;
use ZJKiza\TruncateHtml\Enum\Strategy;
use ZJKiza\TruncateHtml\Strategy\ByteStrategy;
use ZJKiza\TruncateHtml\Strategy\StringStrategy;
use ZJKiza\TruncateHtml\TruncateHtml;
use PHPUnit\Framework\Attributes\DataProvider;

use function strlen;

final class TruncateHtmlTest extends TestCase
{
    #[DataProvider('getDataByteTruncate')]
    public function testByteTruncate(int $limit, string $expected): void
    {
        $truncate = new TruncateHtml();
        $result = $truncate->execute($this->getHtml(), Strategy::Byte, $limit);

        $this->assertLessThanOrEqual($limit, \strlen($result));
        $this->assertSame($expected, $result);
    }

    /**
     * @return iterable<string, array{int, string}>
     */
    public static function getDataByteTruncate(): iterable
    {
        yield 'Byte limit 100' => [
            100,
            '<div><p>Lorem ipsum <b>dolor sit amet</b>, consectetur <i>adipiscing elit</i>. Sed do eius</p></div>'
        ];

        yield 'Byte limit 200' => [
            200,
            '<div><p>Lorem ipsum <b>dolor sit amet</b>, consectetur <i>adipiscing elit</i>. Sed do eiusmod tempor <span style="color:blue;">incididunt</span> ut labore et dolore magna aliqua. </p></div>'
        ];

        yield 'Byte limit 300' => [
            300,
            '<div><p>Lorem ipsum <b>dolor sit amet</b>, consectetur <i>adipiscing elit</i>. Sed do eiusmod tempor <span style="color:blue;">incididunt</span> ut labore et dolore magna aliqua. <img alt="Etiam tempor" /> Ut enim ad minim veniam, quis nostrud <u>exercitation ullamco laboris</u> nisi ut al</p></div>'
        ];
    }

    #[DataProvider('getDataStringTruncate')]
    public function testStringTruncate(int $limit, string $expected): void
    {
        $truncate = new TruncateHtml();
        $result = $truncate->execute($this->getHtml(), Strategy::String, $limit);

        $this->assertLessThanOrEqual($limit, \strlen($result));
        $this->assertSame($expected, $result);
    }

    /**
     * @return iterable<string, array{int, string}>
     */
    public static function getDataStringTruncate(): iterable
    {
        yield 'Char limit 100' => [
            100,
            '<div><p>Lorem ipsum <b>dolor sit amet</b>, consectetur <i>adipiscing elit</i>. Sed do eius</p></div>'
        ];

        yield 'Char limit 200' => [
            200,
            '<div><p>Lorem ipsum <b>dolor sit amet</b>, consectetur <i>adipiscing elit</i>. Sed do eiusmod tempor <span style="color:blue;">incididunt</span> ut labore et dolore magna aliqua. </p></div>'
        ];

        yield 'Char limit 300' => [
            300,
            '<div><p>Lorem ipsum <b>dolor sit amet</b>, consectetur <i>adipiscing elit</i>. Sed do eiusmod tempor <span style="color:blue;">incididunt</span> ut labore et dolore magna aliqua. <img alt="Etiam tempor" /> Ut enim ad minim veniam, quis nostrud <u>exercitation ullamco laboris</u> nisi ut al</p></div>'
        ];
    }

    private function getHtml(): string
    {
        return '<div><p>Lorem ipsum <b>dolor sit amet</b>, consectetur <i>adipiscing elit</i>. Sed do eiusmod tempor <span style="color:blue;">incididunt</span> ut labore et dolore magna aliqua. <img alt="Etiam tempor" /> Ut enim ad minim veniam, quis nostrud <u>exercitation ullamco laboris</u> nisi ut aliquip ex ea commodo consequat.</p></div>';
    }
}
