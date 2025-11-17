<?php

declare(strict_types=1);

namespace ZJKiza\TruncateHtml\Tests\Functionality;

use PHPUnit\Framework\TestCase;
use ZJKiza\TruncateHtml\Enum\Strategy;
use ZJKiza\TruncateHtml\TruncateHtml;
use PHPUnit\Framework\Attributes\DataProvider;

final class TruncateHtmlTest extends TestCase
{
    #[DataProvider('getDataByteTruncate')]
    public function testByteTruncate(int $limit, string $expected): void
    {
        $truncate = new TruncateHtml();
        $result = $truncate->execute($this->getHtml(), Strategy::Bytes, $limit);

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
        $result = $truncate->execute($this->getHtml(), Strategy::Characters, $limit);

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

    #[DataProvider('getDataByteMultibyteTruncate')]
    public function testByteTruncateMultibyte(int $limit, string $expected): void
    {
        $truncate = new TruncateHtml();
        $result = $truncate->execute($this->getMultibyteHtml(), Strategy::Bytes, $limit);
        $this->assertLessThanOrEqual($limit, \strlen($result));
        $this->assertSame($expected, $result);
    }
    /**
     * @return iterable<string, array{int, string}>
     */
    public static function getDataByteMultibyteTruncate(): iterable
    {
        // 😊 = 4 bytes, ć = 2, 汉 = 3, аз = 4
        yield 'Byte limit cuts multi-byte emoji in middle (should not break)' => [
            30,
            '<div><p>Ćao 😊汉</p></div>'
        ];
        yield 'Byte limit exactly emoji + char (clip)' => [
            27,
            '<div><p>Ćao 😊</p></div>'
        ];
        yield 'Byte limit forces tag cut/closing' => [
            20,
            '<div><p>Ć</p></div>'
        ];
    }

    #[DataProvider('getDataStringMultibyteTruncate')]
    public function testStringTruncateMultibyte(int $limit, string $expected): void
    {
        $truncate = new TruncateHtml();
        $result = $truncate->execute($this->getMultibyteHtml(), Strategy::Characters, $limit);
        $this->assertLessThanOrEqual($limit, \mb_strlen($result, 'UTF-8'));
        $this->assertSame($expected, $result);
    }
    /**
     * @return iterable<string, array{int, string}>
     */
    public static function getDataStringMultibyteTruncate(): iterable
    {
        yield 'Chars limit: ć, emoji, 汉 full' => [
            24,
            '<div><p>Ćao 😊汉</p></div>'
        ];
        yield 'Chars limit: ć, emoji only' => [
            23,
            '<div><p>Ćao 😊</p></div>'
        ];
        yield 'Chars limit: ć only' => [
            19,
            '<div><p>Ć</p></div>'
        ];
    }

    private function getHtml(): string
    {
        return '<div><p>Lorem ipsum <b>dolor sit amet</b>, consectetur <i>adipiscing elit</i>. Sed do eiusmod tempor <span style="color:blue;">incididunt</span> ut labore et dolore magna aliqua. <img alt="Etiam tempor" /> Ut enim ad minim veniam, quis nostrud <u>exercitation ullamco laboris</u> nisi ut aliquip ex ea commodo consequat.</p></div>';
    }

    private function getMultibyteHtml(): string
    {
        // ć = 2, 😊 = 4 bytes, 汉 = 3,  аз = 4
        return '<div><p>Ćao 😊汉</p></div>';
    }
}
