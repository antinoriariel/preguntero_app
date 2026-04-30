<?php
declare(strict_types=1);

final class Csrf
{
    private const KEY = '_csrf_token';

    private function __construct() {}

    public static function token(): string
    {
        if (empty($_SESSION[self::KEY])) {
            $_SESSION[self::KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::KEY];
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . self::token() . '">';
    }

    public static function verify(): void
    {
        $submitted = $_POST['_csrf'] ?? '';
        $expected  = $_SESSION[self::KEY] ?? '';

        if ($expected === '' || !hash_equals($expected, $submitted)) {
            http_response_code(403);
            exit('Solicitud inválida (CSRF).');
        }
    }
}
