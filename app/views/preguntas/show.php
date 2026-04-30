<?php
declare(strict_types=1);
$pageTitle = 'Pregunta #' . $pregunta['id_pregunta'];
require __DIR__ . '/../layouts/header.php';

$success = Session::getFlash('success');
?>

<?php if ($success): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($success) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<nav aria-label="Ruta" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/preguntas">Preguntas</a></li>
    <li class="breadcrumb-item active">#<?= $pregunta['id_pregunta'] ?></li>
  </ol>
</nav>

<div class="card mb-4">
  <div class="card-body">
    <h4 class="card-title"><?= htmlspecialchars($pregunta['enunciado']) ?></h4>
    <small class="text-muted">
      Creada el <?= date('d/m/Y \a \l\a\s H:i', strtotime($pregunta['created_at'])) ?>
      <?php if ($pregunta['updated_at'] !== $pregunta['created_at']): ?>
        · Editada el <?= date('d/m/Y', strtotime($pregunta['updated_at'])) ?>
      <?php endif; ?>
    </small>
  </div>
</div>

<h5 class="mb-3">
  Respuestas
  <span class="badge bg-secondary ms-1"><?= count($respuestas) ?></span>
</h5>

<?php if (empty($respuestas)): ?>
  <p class="text-muted">Sin respuestas cargadas.</p>
<?php else: ?>
  <ul class="list-group mb-4">
    <?php foreach ($respuestas as $r): ?>
      <li class="list-group-item d-flex justify-content-between align-items-start gap-2 py-3">
        <span><?= htmlspecialchars($r['respuesta']) ?></span>
        <?php if ($r['es_correcta']): ?>
          <span class="badge bg-success flex-shrink-0 align-self-center">
            <i class="fa-solid fa-check me-1"></i>Correcta
          </span>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

<div class="d-flex gap-2 flex-wrap">
  <a href="/preguntas/<?= $pregunta['id_pregunta'] ?>/edit" class="btn btn-outline-secondary btn-sm">
    <i class="fa-solid fa-pen me-1"></i>Editar
  </a>

  <form method="POST"
        action="/preguntas/<?= $pregunta['id_pregunta'] ?>/delete"
        onsubmit="return confirm('¿Eliminar esta pregunta y todas sus respuestas?')">
    <?= Csrf::field() ?>
    <button type="submit" class="btn btn-outline-danger btn-sm">
      <i class="fa-solid fa-trash me-1"></i>Eliminar
    </button>
  </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
