<?php
declare(strict_types=1);
$pageTitle    = 'Simulador';
$totalQ       = count($questionsData);
$questionsJson = json_encode($questionsData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
require __DIR__ . '/layouts/header.php';
?>

<style>
/* ── Simulador pantallas ─────────────────────────────────────── */
.screen { animation: fadeIn .25s ease both; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }

/* ── Timer bar ───────────────────────────────────────────────── */
#quiz-timer-bar { transition: background-color .4s, width .1s linear; }

/* ── Pregunta card ───────────────────────────────────────────── */
#quiz-question { color: var(--tech-text); font-size: 1.05rem; }

/* ── SVG ring ────────────────────────────────────────────────── */
.score-ring { transform: rotate(-90deg); }
.score-ring-bg  { fill: none; stroke: rgba(255,255,255,.1); stroke-width: 9; }
.score-ring-val {
    fill: none; stroke-width: 9; stroke-linecap: round;
    transition: stroke-dashoffset .85s cubic-bezier(.4,0,.2,1), stroke .4s;
}
#score-text {
    font-family: inherit; font-weight: 700; font-size: 1.35rem;
    fill: var(--tech-text);
}

/* ── Results table ───────────────────────────────────────────── */
#res-table td { vertical-align: middle; font-size: .86rem; }
</style>

<!-- ═══════════════════════════════════════════════════════════
     PANTALLA 1 — CONFIGURACIÓN
     ═══════════════════════════════════════════════════════════ -->
<div id="screen-setup" class="screen">
  <div class="row justify-content-center">
    <div class="col-sm-10 col-md-7 col-lg-5">
      <div class="card">
        <div class="card-body p-4 p-md-5 text-center">

          <div class="mb-2">
            <i class="fa-solid fa-bolt fa-2x text-warning"></i>
          </div>
          <h2 class="mb-1">Simulador</h2>
          <p class="text-muted mb-4">
            <?php if ($totalQ > 0): ?>
              <span class="badge bg-secondary"><?= $totalQ ?></span> pregunta<?= $totalQ !== 1 ? 's' : '' ?> disponible<?= $totalQ !== 1 ? 's' : '' ?>
            <?php else: ?>
              <span class="badge bg-warning text-dark">Sin preguntas</span>
            <?php endif; ?>
          </p>

          <!-- Selector de tiempo -->
          <div class="mb-4 text-start">
            <label class="form-label fw-bold d-block text-center mb-2">
              Tiempo máximo por pregunta
            </label>
            <div class="d-flex justify-content-center flex-wrap gap-2 mb-3">
              <button class="btn btn-outline-dark time-pill" data-secs="10">10 s</button>
              <button class="btn btn-dark        time-pill active" data-secs="30">30 s</button>
              <button class="btn btn-outline-dark time-pill" data-secs="60">60 s</button>
              <button class="btn btn-outline-dark time-pill" data-secs="120">2 min</button>
            </div>
            <div class="d-flex align-items-center justify-content-center gap-2">
              <label for="custom-time" class="form-label mb-0 text-muted small">Personalizado:</label>
              <input type="number" id="custom-time" class="form-control form-control-sm text-center"
                     min="5" max="300" placeholder="seg" style="width:80px">
            </div>
          </div>

          <button id="btn-start" class="btn btn-dark btn-lg w-100"
                  <?= $totalQ === 0 ? 'disabled' : '' ?>>
            <i class="fa-solid fa-play me-2"></i>Comenzar
          </button>

          <?php if ($totalQ === 0): ?>
            <p class="text-muted small mt-3">
              <a href="/preguntas/create">Creá preguntas</a> para poder usar el simulador.
            </p>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════════
     PANTALLA 2 — CUESTIONARIO
     ═══════════════════════════════════════════════════════════ -->
<div id="screen-quiz" class="screen d-none">

  <!-- Progreso + temporizador -->
  <div class="d-flex justify-content-between align-items-baseline mb-1">
    <span id="quiz-progress" class="text-muted small fw-bold"></span>
    <span id="quiz-timer-secs" class="fw-bold fs-4 font-monospace">30</span>
  </div>
  <div class="progress mb-4" style="height:7px;border-radius:4px">
    <div id="quiz-timer-bar" class="progress-bar bg-success" style="width:100%"></div>
  </div>

  <!-- Enunciado -->
  <div class="card mb-4">
    <div class="card-body py-4">
      <p id="quiz-question" class="mb-0 fs-5 lh-base"></p>
    </div>
  </div>

  <!-- Respuestas -->
  <div id="quiz-answers" class="row g-3"></div>

  <!-- Feedback de timeout -->
  <div id="quiz-timeout-msg" class="text-center text-danger fw-bold mt-3 d-none">
    <i class="fa-solid fa-hourglass-end me-1"></i>Tiempo agotado
  </div>

</div>

<!-- ═══════════════════════════════════════════════════════════
     PANTALLA 3 — RESULTADOS
     ═══════════════════════════════════════════════════════════ -->
<div id="screen-results" class="screen d-none">

  <h2 class="mb-4 text-center">Resultados</h2>

  <!-- Círculo SVG + tarjetas de stats -->
  <div class="row justify-content-center g-3 mb-4 align-items-center">
    <div class="col-auto text-center">
      <svg width="140" height="140" viewBox="0 0 100 100">
        <circle class="score-ring-bg" cx="50" cy="50" r="42"/>
        <circle id="score-ring-val" class="score-ring-val score-ring" cx="50" cy="50" r="42"
                stroke="#198754"
                stroke-dasharray="263.9"
                stroke-dashoffset="263.9"/>
        <text id="score-text" x="50" y="56" text-anchor="middle">0%</text>
      </svg>
      <div class="text-muted small mt-1">Aciertos</div>
    </div>

    <div class="col-sm">
      <div class="row g-2">
        <div class="col-6 col-sm-12 col-md-6">
          <div class="card text-center py-3">
            <div id="res-correct" class="display-6 fw-bold text-success">0</div>
            <div class="small text-muted">Correctas</div>
          </div>
        </div>
        <div class="col-6 col-sm-12 col-md-6">
          <div class="card text-center py-3">
            <div id="res-wrong" class="display-6 fw-bold text-danger">0</div>
            <div class="small text-muted">Incorrectas</div>
          </div>
        </div>
        <div class="col-6 col-sm-12 col-md-6">
          <div class="card text-center py-3">
            <div id="res-avg" class="display-6 fw-bold text-primary">—</div>
            <div class="small text-muted">Tiempo promedio</div>
          </div>
        </div>
        <div class="col-6 col-sm-12 col-md-6">
          <div class="card text-center py-3">
            <div id="res-total" class="display-6 fw-bold">0</div>
            <div class="small text-muted">Total</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Detalle por pregunta -->
  <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span class="fw-bold">Detalle</span>
      <button class="btn btn-sm btn-outline-secondary" id="btn-toggle-table">
        <i class="fa-solid fa-chevron-down"></i>
      </button>
    </div>
    <div id="res-table-wrap">
      <div class="table-responsive">
        <table id="res-table" class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:42px" class="text-center">#</th>
              <th>Enunciado</th>
              <th>Tu respuesta</th>
              <th>Resp. correcta</th>
              <th style="width:75px">Tiempo</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="d-flex gap-2 flex-wrap justify-content-center">
    <button id="btn-retry" class="btn btn-dark">
      <i class="fa-solid fa-rotate-right me-1"></i>Volver a intentar
    </button>
    <a href="/preguntas" class="btn btn-outline-secondary">
      <i class="fa-solid fa-list-ul me-1"></i>Ver preguntas
    </a>
  </div>

</div>

<script>
(function () {
    'use strict';

    /* ── Datos inyectados por PHP ──────────────────────────────────────────── */
    var QUESTIONS = <?= $questionsJson ?>;

    /* ── Estado ────────────────────────────────────────────────────────────── */
    var state = {
        questions:    [],   // QUESTIONS barajadas para la sesión actual
        current:      0,    // índice de la pregunta actual
        timeLimit:    30,   // segundos por pregunta
        timerInt:     null, // ID del setInterval del temporizador
        timerStartMs: 0,    // performance.now() al mostrar la pregunta
        results:      [],   // resultados acumulados
        curAnswers:   [],   // respuestas barajadas de la pregunta actual
        answered:     false // bloquea doble-click
    };

    /* ── Helpers ───────────────────────────────────────────────────────────── */
    function el(id)  { return document.getElementById(id); }
    function qs(sel) { return document.querySelector(sel); }

    function shuffle(arr) {
        var a = arr.slice(), i, j, tmp;
        for (i = a.length - 1; i > 0; i--) {
            j = Math.floor(Math.random() * (i + 1));
            tmp = a[i]; a[i] = a[j]; a[j] = tmp;
        }
        return a;
    }

    function escHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function showScreen(id) {
        ['screen-setup', 'screen-quiz', 'screen-results'].forEach(function (s) {
            el(s).classList.toggle('d-none', s !== id);
        });
    }

    /* ── Pantalla de configuración ─────────────────────────────────────────── */
    var selectedSecs = 30;

    document.querySelectorAll('.time-pill').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.time-pill').forEach(function (b) {
                b.classList.remove('btn-dark', 'active');
                b.classList.add('btn-outline-dark');
            });
            btn.classList.add('btn-dark', 'active');
            btn.classList.remove('btn-outline-dark');
            selectedSecs = parseInt(btn.dataset.secs, 10);
            el('custom-time').value = '';
        });
    });

    el('custom-time').addEventListener('input', function () {
        document.querySelectorAll('.time-pill').forEach(function (b) {
            b.classList.remove('btn-dark', 'active');
            b.classList.add('btn-outline-dark');
        });
    });

    el('btn-start').addEventListener('click', function () {
        var custom = parseInt(el('custom-time').value, 10);
        state.timeLimit = (!isNaN(custom) && custom >= 5 && custom <= 300) ? custom : selectedSecs;
        startQuiz();
    });

    /* ── Lógica del cuestionario ───────────────────────────────────────────── */
    function startQuiz() {
        state.questions = shuffle(QUESTIONS);
        state.current   = 0;
        state.results   = [];
        showScreen('screen-quiz');
        showQuestion();
    }

    function showQuestion() {
        state.answered   = false;
        var q            = state.questions[state.current];
        state.curAnswers = shuffle(q.respuestas);

        /* Progreso */
        el('quiz-progress').textContent =
            'Pregunta ' + (state.current + 1) + ' de ' + state.questions.length;

        /* Enunciado */
        el('quiz-question').textContent = q.enunciado;

        /* Timeout badge oculto */
        el('quiz-timeout-msg').classList.add('d-none');

        /* Respuestas */
        var container = el('quiz-answers');
        container.innerHTML = '';
        state.curAnswers.forEach(function (r, i) {
            var col = document.createElement('div');
            col.className = 'col-12 col-md-6';

            var btn = document.createElement('button');
            btn.className    = 'btn btn-outline-dark w-100 h-100 text-start answer-btn';
            btn.dataset.idx  = i;
            btn.textContent  = r.texto;
            btn.addEventListener('click', function () { handleAnswer(i); });

            col.appendChild(btn);
            container.appendChild(col);
        });

        /* Temporizador */
        startTimer();
        state.timerStartMs = performance.now();
    }

    function startTimer() {
        clearInterval(state.timerInt);

        var bar    = el('quiz-timer-bar');
        var secsEl = el('quiz-timer-secs');
        var total  = state.timeLimit;
        var elapsed = 0;
        var TICK   = 100; // ms

        bar.style.backgroundColor = '#198754';
        bar.style.width = '100%';
        secsEl.textContent = total;

        state.timerInt = setInterval(function () {
            elapsed += TICK;
            var remaining = total - elapsed / 1000;

            if (remaining <= 0) {
                clearInterval(state.timerInt);
                bar.style.width = '0%';
                secsEl.textContent = '0';
                handleAnswer(null);
                return;
            }

            var pct = (remaining / total) * 100;
            bar.style.width = pct + '%';
            secsEl.textContent = Math.ceil(remaining);

            /* Color: verde → naranja → rojo */
            if (pct <= 25)      bar.style.backgroundColor = '#dc3545';
            else if (pct <= 50) bar.style.backgroundColor = '#fd7e14';
            else                bar.style.backgroundColor = '#198754';
        }, TICK);
    }

    function handleAnswer(idx) {
        if (state.answered) return;
        state.answered = true;
        clearInterval(state.timerInt);

        var timeSpent    = Math.min((performance.now() - state.timerStartMs) / 1000, state.timeLimit);
        var answers      = state.curAnswers;
        var isTimeout    = (idx === null);
        var isCorrect    = !isTimeout && answers[idx].es_correcta;
        var correctAnswer = answers.find(function (a) { return a.es_correcta; }) || answers[0];

        state.results.push({
            enunciado:     state.questions[state.current].enunciado,
            timeSpent:     timeSpent,
            correct:       isCorrect,
            selected:      isTimeout ? null : answers[idx].texto,
            correctAnswer: correctAnswer.texto,
            timeout:       isTimeout
        });

        showFeedback(idx, answers, isTimeout);

        setTimeout(function () {
            state.current++;
            if (state.current >= state.questions.length) {
                finishQuiz();
            } else {
                showQuestion();
            }
        }, 1800);
    }

    function showFeedback(selectedIdx, answers, isTimeout) {
        document.querySelectorAll('.answer-btn').forEach(function (btn) {
            btn.disabled = true;
            var i = parseInt(btn.dataset.idx, 10);

            if (answers[i].es_correcta) {
                /* Respuesta/s correcta/s → verde */
                btn.classList.remove('btn-outline-dark');
                btn.classList.add('btn-success');
                btn.style.color = '#fff';
                btn.style.borderColor = '#198754';
            } else if (!isTimeout && i === selectedIdx) {
                /* Selección incorrecta → roja */
                btn.classList.remove('btn-outline-dark');
                btn.classList.add('btn-danger');
                btn.style.color = '#fff';
                btn.style.borderColor = '#dc3545';
            } else {
                btn.style.opacity = '0.45';
            }
        });

        if (isTimeout) {
            el('quiz-timeout-msg').classList.remove('d-none');
        }
    }

    /* ── Resultados ────────────────────────────────────────────────────────── */
    function finishQuiz() {
        var total   = state.results.length;
        var correct = state.results.filter(function (r) { return r.correct; }).length;
        var wrong   = total - correct;
        var pct     = total > 0 ? Math.round((correct / total) * 100) : 0;
        var avgTime = total > 0
            ? state.results.reduce(function (s, r) { return s + r.timeSpent; }, 0) / total
            : 0;

        /* Guardar en sessionStorage */
        try {
            sessionStorage.setItem('simulador_results', JSON.stringify({
                timestamp: new Date().toISOString(),
                timeLimit: state.timeLimit,
                total:     total,
                correct:   correct,
                wrong:     wrong,
                pct:       pct,
                avgTime:   avgTime,
                results:   state.results
            }));
        } catch (e) { /* sessionStorage no disponible */ }

        renderResults(total, correct, wrong, pct, avgTime);
        showScreen('screen-results');
    }

    function renderResults(total, correct, wrong, pct, avgTime) {
        /* Círculo SVG */
        var ring         = el('score-ring-val');
        var circumference = 263.9; /* 2π × 42 */
        var offset       = circumference * (1 - pct / 100);
        var color        = pct >= 70 ? '#198754' : pct >= 50 ? '#fd7e14' : '#dc3545';

        ring.style.stroke = color;
        /* Animar con requestAnimationFrame para que la transición CSS funcione */
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                ring.style.strokeDashoffset = offset;
            });
        });

        el('score-text').textContent = pct + '%';
        el('score-text').style.fill = color;

        /* Tarjetas */
        el('res-correct').textContent = correct;
        el('res-wrong').textContent   = wrong;
        el('res-total').textContent   = total;
        el('res-avg').textContent     = avgTime.toFixed(1) + ' s';

        /* Tabla de detalle */
        var tbody = qs('#res-table tbody');
        tbody.innerHTML = '';

        state.results.forEach(function (r, i) {
            var tr = document.createElement('tr');

            var iconHtml = r.correct
                ? '<i class="fa-solid fa-check text-success"></i>'
                : '<i class="fa-solid fa-xmark text-danger"></i>';

            var selectedHtml;
            if (r.timeout) {
                selectedHtml = '<em class="text-muted">Sin respuesta</em>';
            } else if (r.correct) {
                selectedHtml = '<span class="text-success">' + escHtml(r.selected) + '</span>';
            } else {
                selectedHtml = '<span class="text-danger">' + escHtml(r.selected) + '</span>';
            }

            var correctHtml = r.correct
                ? '<span class="text-muted">—</span>'
                : '<span class="text-success fw-bold">' + escHtml(r.correctAnswer) + '</span>';

            tr.innerHTML =
                '<td class="text-center">' + iconHtml + '</td>' +
                '<td>' + escHtml(r.enunciado) + '</td>' +
                '<td>' + selectedHtml + '</td>' +
                '<td>' + correctHtml + '</td>' +
                '<td class="text-nowrap font-monospace">' + r.timeSpent.toFixed(1) + ' s</td>';

            tbody.appendChild(tr);
        });
    }

    /* ── Toggle tabla de detalle ───────────────────────────────────────────── */
    el('btn-toggle-table').addEventListener('click', function () {
        var wrap = el('res-table-wrap');
        var icon = this.querySelector('i');
        var hidden = wrap.style.display === 'none';
        wrap.style.display = hidden ? '' : 'none';
        icon.className = hidden ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down';
    });

    /* ── Repetir ───────────────────────────────────────────────────────────── */
    el('btn-retry').addEventListener('click', function () {
        showScreen('screen-setup');
    });

}());
</script>

<?php require __DIR__ . '/layouts/footer.php'; ?>
