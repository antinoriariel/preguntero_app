<?php
declare(strict_types=1);
$pageTitle = 'Preguntas';
require __DIR__ . '/../layouts/header.php';

$success = Session::getFlash('success');
?>

<?php if ($success): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($success) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="mb-0">Preguntas</h2>
  <a href="/preguntas/create" class="btn btn-dark btn-sm">
    <i class="fa-solid fa-plus me-1"></i>Nueva pregunta
  </a>
</div>

<form class="mb-4" method="GET" action="/preguntas">
  <div class="input-group">
    <input type="text" name="q" class="form-control"
           placeholder="Buscar por enunciado…"
           value="<?= htmlspecialchars($search) ?>">
    <button type="submit" class="btn btn-outline-secondary">
      <i class="fa-solid fa-magnifying-glass"></i>
    </button>
    <?php if ($search !== ''): ?>
      <a href="/preguntas" class="btn btn-outline-danger" title="Limpiar búsqueda">
        <i class="fa-solid fa-xmark"></i>
      </a>
    <?php endif; ?>
  </div>
</form>

<?php if (empty($preguntas)): ?>
  <p class="text-muted">
    <?= $search !== '' ? 'No hay resultados para "' . htmlspecialchars($search) . '".' : 'Todavía no hay preguntas. ¡Creá la primera!' ?>
  </p>
<?php else: ?>
  <div class="list-group mb-4">
    <?php foreach ($preguntas as $p): ?>
      <a href="/preguntas/<?= $p['id_pregunta'] ?>"
         class="list-group-item list-group-item-action d-flex justify-content-between align-items-start py-3">
        <span class="me-3"><?= htmlspecialchars($p['enunciado']) ?></span>
        <small class="text-muted text-nowrap mt-1">
          <?= date('d/m/Y', strtotime($p['created_at'])) ?>
        </small>
      </a>
    <?php endforeach; ?>
  </div>

  <?php if ($pages > 1): ?>
  <nav aria-label="Paginación">
    <ul class="pagination">
      <?php if ($page > 1): ?>
        <li class="page-item">
          <a class="page-link" href="/preguntas?page=<?= $page - 1 ?><?= $search !== '' ? '&q=' . urlencode($search) : '' ?>">
            &laquo;
          </a>
        </li>
      <?php endif; ?>
      <?php for ($i = 1; $i <= $pages; $i++): ?>
        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
          <a class="page-link" href="/preguntas?page=<?= $i ?><?= $search !== '' ? '&q=' . urlencode($search) : '' ?>">
            <?= $i ?>
          </a>
        </li>
      <?php endfor; ?>
      <?php if ($page < $pages): ?>
        <li class="page-item">
          <a class="page-link" href="/preguntas?page=<?= $page + 1 ?><?= $search !== '' ? '&q=' . urlencode($search) : '' ?>">
            &raquo;
          </a>
        </li>
      <?php endif; ?>
    </ul>
  </nav>
  <?php endif; ?>

  <p class="text-muted small">
    <?= $total ?> pregunta<?= $total !== 1 ? 's' : '' ?><?= $search !== '' ? ' encontrada' . ($total !== 1 ? 's' : '') : '' ?>.
  </p>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
