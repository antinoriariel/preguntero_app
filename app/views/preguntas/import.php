<?php
declare(strict_types=1);
$pageTitle = 'Importar lote';
require __DIR__ . '/../layouts/header.php';

$errors  = isset($errors)  && is_array($errors)  ? $errors  : [];
$old_raw = isset($old_raw) && is_string($old_raw) ? $old_raw : '';
?>

<nav aria-label="Ruta" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/preguntas">Preguntas</a></li>
    <li class="breadcrumb-item active">Importar lote</li>
  </ol>
</nav>

<h2 class="mb-4">Importar lote de preguntas</h2>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <strong><i class="fa-solid fa-circle-exclamation me-1"></i>Se encontraron errores en el lote:</strong>
    <ul class="mb-0 mt-2">
      <?php foreach ($errors as $e): ?>
        <li><?= htmlspecialchars($e) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="POST" action="/preguntas/importar" novalidate>
  <?= Csrf::field() ?>

  <div class="mb-3">
    <label for="lote" class="form-label fw-bold">Texto del lote</label>
    <textarea id="lote" name="lote" class="form-control font-monospace <?= !empty($errors) ? 'is-invalid' : '' ?>"
              rows="18" spellcheck="false" required
              placeholder="Pegá el lote aquí…"><?= htmlspecialchars($old_raw) ?></textarea>
    <div class="form-text">Máximo 50 preguntas y 50 000 caracteres por importación.</div>
  </div>

  <div class="d-flex gap-2 flex-wrap mb-4">
    <button type="submit" class="btn btn-dark">
      <i class="fa-solid fa-file-import me-1"></i>Importar
    </button>
    <a href="/preguntas" class="btn btn-outline-secondary">Cancelar</a>
  </div>
</form>

<div class="card border-secondary-subtle">
  <div class="card-header bg-body-secondary fw-semibold">
    <i class="fa-solid fa-circle-info me-1"></i>Formato del lote
  </div>
  <div class="card-body">
    <p class="mb-2">Cada bloque representa una pregunta. Separalos con una línea que contenga únicamente <code>---</code>.</p>
    <pre class="bg-body-tertiary rounded p-3 mb-3"><code>Enunciado de la pregunta uno
1. Primera respuesta
2. Segunda respuesta *
3. Tercera respuesta
---
Enunciado de la pregunta dos
1. Respuesta correcta *
2. Otra respuesta</code></pre>
    <ul class="mb-0 small">
      <li>La <strong>primera línea</strong> del bloque es el enunciado (máx. 500 caracteres).</li>
      <li>Cada respuesta comienza con su número, un punto y un espacio: <code>1. texto</code>.</li>
      <li>El número debe coincidir con la posición de la respuesta dentro del bloque.</li>
      <li>Marcá una respuesta como correcta agregando <code> *</code> al final del texto.</li>
      <li>Se permiten <strong>múltiples respuestas correctas</strong> por pregunta.</li>
      <li>Cada pregunta debe tener al menos <strong>2 respuestas</strong> y <strong>1 correcta</strong>.</li>
      <li>Las líneas vacías dentro de un bloque son un error de formato.</li>
    </ul>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
