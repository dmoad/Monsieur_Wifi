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
    // Excluded from the special-char set:
    //   $  — expands inside the AP's double-quoted shell strings (uci set "wireless.x.key=$wifi_key")
    //   &  — gets HTML-encoded as &amp; in templates that render the password
    //   ^  — non-trivial to type on FR/DE keyboard layouts (deadkey)
    //   ~  — same keyboard-layout problem
    private const SPECIAL_CHARS = ['!', '@', '#', '%', '*', '-', '+', '?', '='];

    public static function generate(int $numWords = 3, int $minLength = 20, bool $ucfirst = true, bool $spchar = true): string
    {
        $numWords  = max(3, min(6, $numWords));
        $minLength = max($numWords * 5, min($numWords * 8, $minLength));

        // Filter out words containing letters that look like digits or other letters in
        // common fonts (i/I/l/L/o/O all confuse with 1/0). Leaves ~27% of the wordlist
        // (~520 words), still plenty for 3-word combinations.
        $words = array_values(array_filter(
            require database_path('data/passphrase_words.php'),
            fn ($w) => ! preg_match('/[ilo]/i', $w)
        ));
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

        // Append 3 digits drawn from 2-9 (skip 0 and 1 to avoid 0/O and 1/l/I lookalike confusion)
        $out = $block;
        for ($i = 0; $i < 3; $i++) {
            $out .= random_int(2, 9);
        }
        if ($spchar) {
            $out .= self::SPECIAL_CHARS[random_int(0, count(self::SPECIAL_CHARS) - 1)];
        }

        return $out;
    }
}
