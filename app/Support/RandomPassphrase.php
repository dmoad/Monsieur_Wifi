<?php

namespace App\Support;

/**
 * Generates readable random passphrases (TitleCased word chain + 3 digits + 1 special char).
 * Algorithm based on warpconduit.net's word generator.
 *
 * Example output (3 words, target word-block length 20): "QuickRiverHorizon742!"
 */
class RandomPassphrase
{
    private const SPECIAL_CHARS = ['!', '@', '#', '$', '%', '^', '&', '*', '-', '+', '?', '=', '~'];

    public static function generate(int $numWords = 3, int $minLength = 20, bool $ucfirst = true, bool $spchar = true): string
    {
        $numWords  = max(3, min(6, $numWords));
        $minLength = max($numWords * 5, min($numWords * 8, $minLength));

        $words = require database_path('data/passphrase_words.php');
        $count = count($words);

        do {
            $picked = [];
            for ($i = 0; $i < $numWords; $i++) {
                do {
                    $w = $words[random_int(0, $count - 1)];
                } while (in_array($w, $picked, true));
                $picked[] = $ucfirst ? ucfirst($w) : $w;
            }
            $block = implode('', $picked);
            $len   = strlen($block);
        } while ($len > $minLength || $len < $minLength - 3);

        $out = $block . random_int(100, 999);
        if ($spchar) {
            $out .= self::SPECIAL_CHARS[random_int(0, count(self::SPECIAL_CHARS) - 1)];
        }

        return $out;
    }
}
