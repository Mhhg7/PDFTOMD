<?php

namespace App\Support;

/**
 * Cleans text typed into the dashboard before it is stored. The site inserts
 * content strings as HTML, so only a few inline tags survive: <bdi dir>, <b>,
 * <strong>, <em> and <br>. Everything else is escaped.
 */
class Html
{
    private const TAGS = ['bdi', 'b', 'strong', 'em', 'br'];

    public static function clean(?string $s): string
    {
        if ($s === null || $s === '') {
            return '';
        }
        $s = str_replace("\r\n", "\n", $s);
        $out = '';
        foreach (preg_split('/(<[^<>]*>)/', $s, -1, PREG_SPLIT_DELIM_CAPTURE) as $part) {
            if ($part === '') {
                continue;
            }
            if ($part[0] === '<' && preg_match('/^<(\/?)([a-z]+)\b([^>]*)>$/i', $part, $m)) {
                $tag = strtolower($m[2]);
                if (! in_array($tag, self::TAGS, true)) {
                    continue;
                }
                if ($m[1] === '/') {
                    $out .= $tag === 'br' ? '' : "</{$tag}>";
                } elseif ($tag === 'bdi' && preg_match('/\bdir\s*=\s*["\']?(ltr|rtl)\b/i', $m[3], $d)) {
                    $out .= '<bdi dir="'.strtolower($d[1]).'">';
                } else {
                    $out .= "<{$tag}>";
                }

                continue;
            }
            $out .= strtr($part, ['<' => '&lt;', '>' => '&gt;', '"' => '&quot;', "'" => '&#39;']);
        }

        return trim($out);
    }

    /** Cleans a bilingual {en, ar} value; returns null when both sides are empty. */
    public static function l10n($v): ?array
    {
        if (! is_array($v)) {
            return null;
        }
        $en = self::clean(is_string($v['en'] ?? null) ? $v['en'] : '');
        $ar = self::clean(is_string($v['ar'] ?? null) ? $v['ar'] : '');

        return ($en === '' && $ar === '') ? null : ['en' => $en, 'ar' => $ar];
    }

    /** Accepts http(s) links, in-site hash links and root-relative paths. */
    public static function url(?string $s): ?string
    {
        $s = trim((string) $s);
        if ($s === '') {
            return null;
        }
        if (preg_match('~^(https?://|mailto:|tel:|#|/(?!/))~i', $s)) {
            return str_replace(['"', "'", '<', '>', ' '], ['%22', '%27', '%3C', '%3E', '%20'], $s);
        }

        return null;
    }
}
