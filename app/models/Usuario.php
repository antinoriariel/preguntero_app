<?php
declare(strict_types=1);

final class Usuario
{
    /**
     * Busca por username O email (para el login con un solo campo).
     */
    public static function findByIdentifier(string $identifier): array|false
    {
        return Database::run(
            'SELECT * FROM usuarios WHERE username = ? OR email = ? LIMIT 1',
            [$identifier, $identifier]
        )->fetch();
    }

    public static function findByDni(string $dni): array|false
    {
        return Database::run(
            'SELECT * FROM usuarios WHERE dni_usuario = ? LIMIT 1',
            [$dni]
        )->fetch();
    }

    public static function findByUsername(string $username): array|false
    {
        return Database::run(
            'SELECT * FROM usuarios WHERE username = ? LIMIT 1',
            [$username]
        )->fetch();
    }

    public static function findByEmail(string $email): array|false
    {
        return Database::run(
            'SELECT * FROM usuarios WHERE email = ? LIMIT 1',
            [$email]
        )->fetch();
    }

    public static function create(array $data): void
    {
        $algo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_BCRYPT;
        $hash = password_hash($data['password'], $algo);

        Database::run(
            'INSERT INTO usuarios (dni_usuario, username, nombre, apellido, email, password_hash)
             VALUES (?, ?, ?, ?, ?, ?)',
            [
                $data['dni_usuario'],
                $data['username'],
                $data['nombre'],
                $data['apellido'],
                $data['email'],
                $hash,
            ]
        );
    }
}
