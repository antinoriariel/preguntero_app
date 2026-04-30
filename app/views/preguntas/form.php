<?php
declare(strict_types=1);

$isEdit    = isset($pregunta);
$pageTitle = $isEdit ? 'Editar pregunta' : 'Nueva pregunta';
$action    = $isEdit
    ? '/preguntas/' . $pregunta['id_pregunta'] . '/update'
    : '/preguntas/store';

require __DIR__ . '/../layouts/header.php';

$errors = Session::getFlash('errors', []);
$old    = Session::getFlash('old', []);

// Enunciado: prioridad → flash POST → DB → vacío
$enunciado = htmlspecialchars(
    $old['enunciado'] ?? ($isEdit ? $pregunta['enunciado'] : '')
);

/*
 * Filas de respuestas iniciales.
 * Prioridad: flash POST (re-render tras error) → BD (edición) → 2 vacías (creación).
 *
 * En el formulario, cada fila tiene:
 *   <input name="respuesta[]">
 *   <input type="checkbox" name="es_correcta[]" value="<índice_fila>">
 *
 * Los checkboxes envían solo los índices de las filas marcadas.
 */
if (!empty($old['respuesta'])) {
    // Re-render tras error: reconstruir desde el POST guardado en flash
    $correctaSet = [];
    foreach ((array)($old['es_correcta'] ?? []) as $idx) {
        $correctaSet[(int)$idx] = true;
    }
    $initRows = [];
    foreach ((array)$old['respuesta'] as $i => $texto) {
        $initRows[] = [
            'respuesta'   => $texto,
            'es_correcta' => isset($correctaSet[$i]),
        ];
    }
} elseif ($isEdit) {
    $initRows = $respuestas; // viene del controller (Respuesta::findByPregunta)
} else {
    // Creación: arrancar con 2 filas vacías
    $initRows = [
        ['respuesta' => '', 'es_correcta' => false],
        ['respuesta' => '', 'es_correcta' => false],
    ];
}
?>

<nav aria-label="Ruta" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/preguntas">Preguntas</a></li>
    <?php if ($isEdit): ?>
      <li class="breadcrumb-item">
        <a href="/preguntas/<?= $pregunta['id_pregunta'] ?>">#<?= $pregunta['id_pregunta'] ?></a>
      </li>
    <?php endif; ?>
    <li class="breadcrumb-item active"><?= $isEdit ? 'Editar' : 'Nueva' ?></li>
  </ol>
</nav>

<h2 class="mb-4"><?= $pageTitle ?></h2>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $e): ?>
        <li><?= htmlspecialchars($e) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="POST" action="<?= $action ?>" novalidate>
  <?= Csrf::field() ?>

  <!-- Enunciado -->
  <div class="mb-4">
    <label for="enunciado" class="form-label fw-bold">Enunciado</label>
    <textarea id="enunciado" name="enunciado"
              class="form-control <?= isset($errors['enunciado']) ? 'is-invalid' : '' ?>"
              rows="3" maxlength="500" required><?= $enunciado ?></textarea>
    <?php if (isset($errors['enunciado'])): ?>
      <div class="invalid-feedback"><?= htmlspecialchars($errors['enunciado']) ?></div>
    <?php endif; ?>
    <div class="form-text">Máximo 500 caracteres.</div>
  </div>

  <!-- Respuestas -->
  <div class="mb-4">
    <label class="form-label fw-bold">Respuestas</label>
    <?php if (isset($errors['respuestas'])): ?>
      <div class="text-danger small mb-2">
        <i class="fa-solid fa-circle-exclamation me-1"></i><?= htmlspecialchars($errors['respuestas']) ?>
      </div>
    <?php endif; ?>

    <div id="respuestas-container">
      <?php foreach ($initRows as $i => $r): ?>
      <div class="input-group mb-2 respuesta-row">
        <div class="input-group-text" title="Marcar como correcta">
          <input type="checkbox"
                 class="form-check-input mt-0"
                 name="es_correcta[]"
                 value="<?= $i ?>"
                 <?= $r['es_correcta'] ? 'checked' : '' ?>>
        </div>
        <input type="text"
               class="form-control"
               name="respuesta[]"
               placeholder="Texto de la respuesta"
               maxlength="255"
               value="<?= htmlspecialchars((string)$r['respuesta']) ?>"
               required>
        <button type="button" class="btn btn-outline-danger btn-remove" title="Eliminar fila">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
      <?php endforeach; ?>
    </div>

    <button type="button" id="btn-add" class="btn btn-outline-secondary btn-sm">
      <i class="fa-solid fa-plus me-1"></i>Agregar respuesta
    </button>
    <p class="form-text mt-2">
      <i class="fa-solid fa-circle-info me-1"></i>
      Tildá el casillero izquierdo de la/s respuesta/s correcta/s. Mínimo 2 respuestas.
    </p>
  </div>

  <div class="d-flex gap-2">
    <button type="submit" class="btn btn-dark">
      <i class="fa-solid fa-floppy-disk me-1"></i>Guardar
    </button>
    <a href="<?= $isEdit ? '/preguntas/' . $pregunta['id_pregunta'] : '/preguntas' ?>"
       class="btn btn-outline-secondary">
      Cancelar
    </a>
  </div>
</form>

<script>
(function () {
    'use strict';

    var container = document.getElementById('respuestas-container');
    var btnAdd    = document.getElementById('btn-add');

    function reindex() {
        container.querySelectorAll('.respuesta-row').forEach(function (row, i) {
            row.querySelector('input[type="checkbox"]').value = i;
        });
    }

    function attachRemove(row) {
        row.querySelector('.btn-remove').addEventListener('click', function () {
            if (container.querySelectorAll('.respuesta-row').length > 1) {
                row.remove();
                reindex();
            }
        });
    }

    container.querySelectorAll('.respuesta-row').forEach(attachRemove);

    btnAdd.addEventListener('click', function () {
        var idx = container.querySelectorAll('.respuesta-row').length;
        var row = document.createElement('div');
        row.className = 'input-group mb-2 respuesta-row';
        row.innerHTML =
            '<div class="input-group-text" title="Marcar como correcta">' +
                '<input type="checkbox" class="form-check-input mt-0" name="es_correcta[]" value="' + idx + '">' +
            '</div>' +
            '<input type="text" class="form-control" name="respuesta[]" ' +
                   'placeholder="Texto de la respuesta" maxlength="255" required>' +
            '<button type="button" class="btn btn-outline-danger btn-remove" title="Eliminar fila">' +
                '<i class="fa-solid fa-xmark"></i>' +
            '</button>';
        container.appendChild(row);
        attachRemove(row);
        row.querySelector('input[type="text"]').focus();
    });
}());
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
