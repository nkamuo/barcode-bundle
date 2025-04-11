<?php

declare(strict_types=1);

namespace Nkamuo\Barcode\Decoder\GS1;

final class GS1CodeValidator
{
    public static function detectAndValidate(string $elementString): ?string
    {
        $value = trim($elementString);

        if (self::validateSSCC($value)) {
            return 'SSCC';
        }
        if (self::validateGTIN($value)) {
            return 'GTIN';
        }
        if (self::validateGLN($value)) {
            return 'GLN';
        }
        if (self::validateGRAI($value)) {
            return 'GRAI';
        }
        if (self::validateGIAI($value)) {
            return 'GIAI';
        }
        if (self::validateGSRN($value)) {
            return 'GSRN';
        }
        if (self::validateGDTI($value)) {
            return 'GDTI';
        }
        if (self::validateGINC($value)) {
            return 'GINC';
        }
        if (self::validateGSIN($value)) {
            return 'GSIN';
        }

        return null;

        // // Attempt to extract the AI prefix (2 to 4 digits)
        // $ai = self::extractAI($elementString);
        // if ($ai === null) {
        //     return null;
        // }

        // $value = substr($elementString, strlen($ai));
        // return match ($ai) {
        //     '00'    => self::validateSSCC($value) ? 'SSCC' : null,
        //     '01'    => self::validateGTIN($value) ? 'GTIN' : null,
        //     '410', '411', '412', '413', '414', '415', '416', '417' => self::validateGLN($value) ? 'GLN' : null,
        //     '8003'  => self::validateGRAI($value) ? 'GRAI' : null,
        //     '8004'  => self::validateGIAI($value) ? 'GIAI' : null,
        //     '8018'  => self::validateGSRN($value) ? 'GSRN' : null,
        //     '253'   => self::validateGDTI($value) ? 'GDTI' : null,
        //     '401'   => self::validateGINC($value) ? 'GINC' : null,
        //     '402'   => self::validateGSIN($value) ? 'GSIN' : null,
        //     default => null,
        // };
    }

    private static function extractAI(string $data): ?string
    {
        foreach ([4, 3, 2] as $len) {
            $candidate = substr($data, 0, $len);
            if (in_array($candidate, ['00', '01', '410', '414', '8003', '8004', '8018', '253', '401', '402'])) {
                return $candidate;
            }
        }
        return null;
    }

    public static function isValidCheckDigit(string $code): bool
    {
        $checkDigit = (int) substr($code, -1);
        $calculated = self::calculateMod10CheckDigit(substr($code, 0, -1));
        return $checkDigit === $calculated;
    }

    public static function calculateMod10CheckDigit(string $data): int
    {
        $sum = 0;
        $reverse = strrev($data);
        for ($i = 0; $i < strlen($reverse); $i++) {
            $digit = (int) $reverse[$i];
            $sum += ($i % 2 === 0) ? $digit * 3 : $digit;
        }
        return (10 - ($sum % 10)) % 10;
    }

    private static function validateGTIN(string $value): bool
    {
        return in_array(strlen($value), [8, 12, 13, 14]) && self::isValidCheckDigit($value);
    }

    private static function validateSSCC(string $value): bool
    {
        return strlen($value) === 18 && self::isValidCheckDigit($value);
    }

    private static function validateGLN(string $value): bool
    {
        return strlen($value) === 13 && self::isValidCheckDigit($value);
    }

    private static function validateGRAI(string $value): bool
    {
        // Format: 4+13 + optional serial
        if (strlen($value) < 14) return false;
        return self::isValidCheckDigit(substr($value, 0, 14));
    }

    private static function validateGIAI(string $value): bool
    {
        // No check digit, alphanumeric allowed (but assuming numeric-only for now)
        return strlen($value) >= 1 && strlen($value) <= 30;
    }

    private static function validateGSRN(string $value): bool
    {
        return strlen($value) === 18 && self::isValidCheckDigit($value);
    }

    private static function validateGDTI(string $value): bool
    {
        // GDTI has 13 digits (with check digit) + optional serial
        if (strlen($value) < 14) return false;
        return self::isValidCheckDigit(substr($value, 0, 13));
    }

    private static function validateGINC(string $value): bool
    {
        // Variable-length up to 30 characters, no check digit
        return strlen($value) >= 1 && strlen($value) <= 30;
    }

    private static function validateGSIN(string $value): bool
    {
        return strlen($value) === 17 && self::isValidCheckDigit($value);
    }
}
