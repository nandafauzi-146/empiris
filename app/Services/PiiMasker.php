<?php

declare(strict_types=1);

namespace App\Services;

class PiiMasker
{
    /**
     * Samarkan PII (email, telepon, identitas kontak) dari teks sebelum dikirim ke AI.
     * Sesuai aturan keras R3 di AGENTS.md.
     */
    public static function mask(string $text): string
    {
        // 1. Samarkan Email
        $text = (string) preg_replace(
            '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/',
            '[EMAIL_DISAMARKAN]',
            $text
        );

        // 2. Samarkan Nomor Telepon Indonesia (+62 / 08 / 02x / dsb)
        $text = (string) preg_replace(
            '/(?:\+?62|08|02\d)[\s\-\.]?(?:\d[\s\-\.]?){7,12}\d/',
            '[TELEPON_DISAMARKAN]',
            $text
        );

        return $text;
    }
}
