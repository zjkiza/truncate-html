<?php

declare(strict_types=1);

namespace ZJKiza\TruncateHtml;

use ZJKiza\TruncateHtml\Contract\TruncateHtmlInterface;
use ZJKiza\TruncateHtml\Contract\TruncateStrategyInterface;
use ZJKiza\TruncateHtml\Enum\Strategy;
use ZJKiza\TruncateHtml\Enum\TypeTag;
use ZJKiza\TruncateHtml\Strategy\BytesStrategy;
use ZJKiza\TruncateHtml\Strategy\CharactersStrategy;

final class TruncateHtml implements TruncateHtmlInterface
{
    /**
     * @var string[]
     */
    private array $defaultVoidTag = [
        'area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source', 'track', 'wbr'
    ];

    /**
     * @var array<string, TruncateStrategyInterface>
     */
    private array $strategy = [];

    /**
     * @param string[] $voidTag
     */
    public function __construct(
        array $voidTag = []
    ) {

        $this
            ->addStrategy(new BytesStrategy())
            ->addStrategy(new CharactersStrategy());

        if (false === (bool)$voidTag) {
            return;
        }

        $this->defaultVoidTag = \array_merge(
            $voidTag,
            \array_diff($voidTag, $this->defaultVoidTag)
        );
    }

    public function execute(string $html, Strategy $strategy, int $limit = 950, string $enc = 'UTF-8'): string
    {
        $strategyAction = $this->strategy[$strategy->value];

        $len = $strategyAction->length($html, $enc);
        if ($limit <= 0 || $html === '' || $len <= $limit) {
            return $len <= $limit ? $html : '';
        }

        $openTags = [];
        $out = '';
        $used = 0;
        $tokens = $this->tokenize($html);

        if ($tokens === []) {
            return $strategyAction->cut($html, $limit, $enc);
        }

        $closingCost = 0;

        foreach ($tokens as $token) {
            $isTag = $token[0] === '<';
            $tokBytes = $strategyAction->length($token, $enc);

            if ($used >= $limit) {
                break;
            }

            if ($isTag) {
                /**
                 * @var TypeTag $type
                 * @var string $name
                 * @var bool $self
                 */
                [$type, $name, $self] = $this->parseTag($token, $this->defaultVoidTag);


                if ($type === TypeTag::Open && !$self) {
                    $tagCloseLen = $strategyAction->length(\sprintf("</%s>", $name), $enc);

                    if ($used + $tokBytes + $closingCost + $tagCloseLen > $limit) {
                        break;
                    }

                    $out .= $token;
                    $used += $tokBytes;

                    $openTags[] = $name;
                    $closingCost += $tagCloseLen;
                    continue;
                }

                if ($type === TypeTag::Close) {
                    $tagCloseLen = $strategyAction->length(\sprintf("</%s>", $name), $enc);

                    if ($used + $tokBytes + ($closingCost - $tagCloseLen) > $limit) {
                        break;
                    }

                    for ($i = \count($openTags) - 1; $i >= 0; $i--) {
                        if ($openTags[$i] === $name) {
                            unset($openTags[$i]);
                            $openTags = \array_values($openTags);
                            $closingCost -= $tagCloseLen;
                            break;
                        }
                    }

                    $out .= $token;
                    $used += $tokBytes;
                    continue;
                }

                if ($used + $tokBytes + $closingCost > $limit) {
                    break;
                }

                $out .= $token;
                $used += $tokBytes;
                continue;
            }

            $avail = $limit - $used - $closingCost;
            if ($avail <= 0) {
                break;
            }

            if ($tokBytes <= $avail) {
                $out .= $token;
                $used += $tokBytes;
                continue;
            }

            $cut = $strategyAction->cut($token, $avail, $enc);
            if ($cut !== '') {
                $out .= $cut;
                $used += $strategyAction->length($cut, $enc);
            }
            break;
        }

        foreach (\array_reverse($openTags) as $name) {
            $closer = \sprintf("</%s>", $name);
            $lenClose = $strategyAction->length($closer, $enc);

            if ($used + $lenClose > $limit) {
                break;
            }

            $out .= $closer;
            $used += $lenClose;
        }

        return $out;
    }

    /**
     * @return string[]
     */
    private function tokenize(string $html): array
    {
        return false !== \preg_match_all('/(<[^>]+>|[^<]+)/u', $html, $m) ? $m[0] : [];
    }

    /**
     * @param string[] $voidTags
     * @return array{TypeTag, string, bool}
     */
    private function parseTag(string $raw, array $voidTags): array
    {
        $trimmed = \trim($raw);

        if (\str_starts_with($trimmed, '<!--') && \str_ends_with($trimmed, '-->')) {
            return [TypeTag::Comment, '', false];
        }

        if (\preg_match('/^<![^>]*>$/s', $trimmed)) {
            return [TypeTag::Decl, '', false];
        }

        if (\preg_match('/^<\/\s*([a-zA-Z0-9:-]+)\s*>$/', $trimmed, $m)) {
            return [ TypeTag::Close, \strtolower($m[1]), false];
        }

        if (\preg_match('/^<\s*([a-zA-Z0-9:-]+)([^>]*)>$/s', $trimmed, $m)) {
            $name = \strtolower($m[1]);
            $attrs = $m[2] ?? ''; // @phpstan-ignore-line
            $self = \str_contains($attrs, '/>') || \in_array($name, $voidTags, true);
            return [TypeTag::Open, $name, $self];
        }

        return [TypeTag::Other, '', false];
    }

    private function addStrategy(TruncateStrategyInterface $strategy): self
    {
        $this->strategy[$strategy->key()->value] = $strategy;

        return $this;
    }
}
