<?php
declare(strict_types=1);
$pageTitle  = 'Ingresar';
require __DIR__ . '/../layouts/header.php';
$error      = Session::getFlash('error');
$success    = Session::getFlash('success');
$oldId      = htmlspecialchars((string) Session::getFlash('old_identifier', ''));
?>

<div class="row justify-content-center">
  <div class="col-sm-8 col-md-5 col-lg-4">
    <div class="card mt-2">
      <div class="card-body p-4">
        <h2 class="card-title mb-4 text-center">Ingresar</h2>

        <?php if ($error): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
          <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="/login" novalidate>
          <?= Csrf::field() ?>

          <div class="mb-3">
            <label for="identifier" class="form-label">Usuario o email</label>
            <input type="text" id="identifier" name="identifier"
                   class="form-control" value="<?= $oldId ?>"
                   required autofocus autocomplete="username">
          </div>

          <div class="mb-4">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" id="password" name="password"
                   class="form-control" required autocomplete="current-password">
          </div>

          <button type="submit" class="btn btn-dark w-100">
            <i class="fa-solid fa-right-to-bracket me-1"></i>Ingresar
          </button>
        </form>

        <hr>
        <p class="text-center mb-0 small">
          ¿No tenés cuenta? <a href="/register">Registrate</a>
        </p>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
