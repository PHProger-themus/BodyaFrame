<?php

namespace system\classes;

class SafetyManager
{

    public static function generateRandomString(int $size = 10, bool $with_characters = false)
    {
        $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        if ($with_characters) $characters .= "-_+=?|@#$%^&*()[]{}:";
        $charactersLength = strlen($characters);
        $randomString = null;
        for ($i = 0; $i < $size; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function generateByteString(int $size = 5)
    {
        $bytes = random_bytes(5);
        return bin2hex($bytes);
    }

    public static function encryptPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public static function checkPassword(string $user_password, string $db_password): bool
    {
        if (hash_equals($db_password, crypt($user_password, $db_password))) {
            return true;
        }
        return false;
    }

    public static function sendCSPReport(string $format = "@key : @value\n", string $path = HOME_DIR . '/logs/csp-report.txt')
    {
        $csp_report = json_decode(file_get_contents('php://input'), true);
        $report = "CSP Violation Report. Date: " . date('d.m.Y H:i:s') . "\n";
        foreach ($csp_report['csp-report'] as $report_key => $report_string) {
            $replace = ['@key' => $report_key, '@value' => $report_string];
            $report .= strtr($format, $replace);
        }
        $report .= "\n";
        file_put_contents($path, $report, FILE_APPEND);
    }

    public static function filterString(string $str)
    {
        return htmlspecialchars($str, ENT_QUOTES);
    }

}