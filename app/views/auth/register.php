<?php
declare(strict_types=1);
$pageTitle = 'Crear cuenta';
require __DIR__ . '/../layouts/header.php';

$errors = Session::getFlash('errors', []);
$old    = Session::getFlash('old', []);

function _old(string $key, array $old): string
{
    return htmlspecialchars((string)($old[$key] ?? ''));
}
function _err(string $key, array $errors): string
{
    return isset($errors[$key])
        ? '<div class="invalid-feedback">' . htmlspecialchars($errors[$key]) . '</div>'
        : '';
}
function _cls(string $key, array $errors): string
{
    return 'form-control' . (isset($errors[$key]) ? ' is-invalid' : '');
}
?>

<div class="row justify-content-center">
  <div class="col-md-8 col-lg-7">
    <div class="card mt-2">
      <div class="card-body p-4">
        <h2 class="card-title mb-4">Crear cuenta</h2>

        <?php if (!empty($errors)): ?>
          <div class="alert alert-danger">
            Revisá los errores marcados en el formulario.
          </div>
        <?php endif; ?>

        <form method="POST" action="/register" novalidate>
          <?= Csrf::field() ?>

          <div class="row g-3">

            <div class="col-sm-4">
              <label for="dni_usuario" class="form-label">DNI <small class="text-muted">(8 dígitos)</small></label>
              <input type="text" id="dni_usuario" name="dni_usuario"
                     class="<?= _cls('dni_usuario', $errors) ?>"
                     value="<?= _old('dni_usuario', $old) ?>"
                     maxlength="8" inputmode="numeric" required>
              <?= _err('dni_usuario', $errors) ?>
            </div>

            <div class="col-sm-8">
              <label for="username" class="form-label">Nombre de usuario</label>
              <input type="text" id="username" name="username"
                     class="<?= _cls('username', $errors) ?>"
                     value="<?= _old('username', $old) ?>"
                     maxlength="50" autocomplete="username" required>
              <?= _err('username', $errors) ?>
            </div>

            <div class="col-sm-6">
              <label for="nombre" class="form-label">Nombre</label>
              <input type="text" id="nombre" name="nombre"
                     class="<?= _cls('nombre', $errors) ?>"
                     value="<?= _old('nombre', $old) ?>"
                     maxlength="100" autocomplete="given-name" required>
              <?= _err('nombre', $errors) ?>
            </div>

            <div class="col-sm-6">
              <label for="apellido" class="form-label">Apellido</label>
              <input type="text" id="apellido" name="apellido"
                     class="<?= _cls('apellido', $errors) ?>"
                     value="<?= _old('apellido', $old) ?>"
                     maxlength="100" autocomplete="family-name" required>
              <?= _err('apellido', $errors) ?>
            </div>

            <div class="col-12">
              <label for="email" class="form-label">Email</label>
              <input type="email" id="email" name="email"
                     class="<?= _cls('email', $errors) ?>"
                     value="<?= _old('email', $old) ?>"
                     maxlength="255" autocomplete="email" required>
              <?= _err('email', $errors) ?>
            </div>

            <div class="col-sm-6">
              <label for="password" class="form-label">Contraseña</label>
              <input type="password" id="password" name="password"
                     class="<?= _cls('password', $errors) ?>"
                     autocomplete="new-password" required>
              <?= _err('password', $errors) ?>
              <div class="form-text">Mínimo 8 caracteres.</div>
            </div>

            <div class="col-sm-6">
              <label for="password_confirm" class="form-label">Confirmar contraseña</label>
              <input type="password" id="password_confirm" name="password_confirm"
                     class="<?= _cls('password_confirm', $errors) ?>"
                     autocomplete="new-password" required>
              <?= _err('password_confirm', $errors) ?>
            </div>

          </div><!-- /.row -->

          <div class="mt-4">
            <button type="submit" class="btn btn-dark w-100">
              <i class="fa-solid fa-user-plus me-1"></i>Registrarse
            </button>
          </div>
        </form>

        <hr>
        <p class="text-center mb-0 small">
          ¿Ya tenés cuenta? <a href="/login">Ingresar</a>
        </p>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
