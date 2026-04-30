<?php
declare(strict_types=1);

final class AuthController
{
    public function showLogin(): void
    {
        Session::requireGuest();
        require __DIR__ . '/../views/auth/login.php';
    }

    public function login(): void
    {
        Session::requireGuest();
        Csrf::verify();

        $identifier = trim($_POST['identifier'] ?? '');
        $password   = $_POST['password'] ?? '';

        // Mensaje genérico: no revela si existe o no el usuario
        $genericError = 'Credenciales incorrectas.';

        if ($identifier !== '' && $password !== '') {
            $user = Usuario::findByIdentifier($identifier);
            if ($user && password_verify($password, $user['password_hash'])) {
                Session::login([
                    'dni'      => $user['dni_usuario'],
                    'username' => $user['username'],
                    'nombre'   => $user['nombre'],
                    'apellido' => $user['apellido'],
                    'email'    => $user['email'],
                ]);
                header('Location: /preguntas');
                exit;
            }
        }

        Session::flash('error', $genericError);
        Session::flash('old_identifier', $identifier);
        header('Location: /login');
        exit;
    }

    public function showRegister(): void
    {
        Session::requireGuest();
        require __DIR__ . '/../views/auth/register.php';
    }

    public function register(): void
    {
        Session::requireGuest();
        Csrf::verify();

        $data = [
            'dni_usuario'      => trim($_POST['dni_usuario'] ?? ''),
            'username'         => trim($_POST['username'] ?? ''),
            'nombre'           => trim($_POST['nombre'] ?? ''),
            'apellido'         => trim($_POST['apellido'] ?? ''),
            'email'            => trim($_POST['email'] ?? ''),
            'password'         => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
        ];

        $v = Validator::make($data, [
            'dni_usuario' => 'required|digits|exact:8',
            'username'    => 'required|alpha_num|min:3|max:50',
            'nombre'      => 'required|max:100',
            'apellido'    => 'required|max:100',
            'email'       => 'required|email|max:255',
            'password'    => 'required|min:8|max:255',
        ]);

        $errors = $v->errors();

        if ($data['password'] !== $data['password_confirm']) {
            $errors['password_confirm'] = 'Las contraseñas no coinciden.';
        }

        // Verificar unicidad solo si no hay errores de formato
        if (empty($errors)) {
            if (Usuario::findByDni($data['dni_usuario'])) {
                $errors['dni_usuario'] = 'El DNI ya está registrado.';
            }
            if (Usuario::findByUsername($data['username'])) {
                $errors['username'] = 'El nombre de usuario ya existe.';
            }
            if (Usuario::findByEmail($data['email'])) {
                $errors['email'] = 'El email ya está registrado.';
            }
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            // Guardamos todo menos las contraseñas
            unset($data['password'], $data['password_confirm']);
            Session::flash('old', $data);
            header('Location: /register');
            exit;
        }

        Usuario::create($data);
        Session::flash('success', '¡Cuenta creada! Ya podés iniciar sesión.');
        header('Location: /login');
        exit;
    }

    public function logout(): void
    {
        Session::logout();
        header('Location: /login');
        exit;
    }
}
