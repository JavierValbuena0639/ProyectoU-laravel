<?php

namespace App\Support;

class Totp
{
    public static function generateSecret(int $length = 16): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'; // Base32
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }
        return $secret;
    }

    public static function verify(string $base32Secret, string $code, int $window = 1, int $digits = 6, int $period = 30): bool
    {
        $time = time();
        for ($i = -$window; $i <= $window; $i++) {
            $calc = self::totp($base32Secret, $time + ($i * $period), $digits, $period);
            if (hash_equals($calc, $code)) {
                return true;
            }
        }
        return false;
    }

    public static function otpauthUri(string $issuer, string $account, string $base32Secret, int $digits = 6, int $period = 30): string
    {
        $label = rawurlencode($issuer . ':' . $account);
        $params = http_build_query([
            'secret' => $base32Secret,
            'issuer' => $issuer,
            'digits' => $digits,
            'period' => $period,
        ]);
        return "otpauth://totp/{$label}?{$params}";
    }

    private static function totp(string $base32Secret, int $timestamp, int $digits = 6, int $period = 30): string
    {
        $counter = intdiv($timestamp, $period);
        $secret = self::base32Decode($base32Secret);
        $binCounter = pack('N*', 0) . pack('N*', $counter);
        $hash = hash_hmac('sha1', $binCounter, $secret, true);
        $offset = ord(substr($hash, -1)) & 0x0F;
        $truncatedHash = unpack('N', substr($hash, $offset, 4))[1] & 0x7FFFFFFF;
        $code = $truncatedHash % (10 ** $digits);
        return str_pad((string)$code, $digits, '0', STR_PAD_LEFT);
    }

    private static function base32Decode(string $b32): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $b32 = strtoupper($b32);
        $buffer = 0;
        $bitsLeft = 0;
        $result = '';
        for ($i = 0; $i < strlen($b32); $i++) {
            $val = strpos($alphabet, $b32[$i]);
            if ($val === false) {
                continue;
            }
            $buffer = ($buffer << 5) | $val;
            $bitsLeft += 5;
            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $result .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }
        return $result;
    }
}