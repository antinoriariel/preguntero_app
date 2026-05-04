<?php
declare(strict_types=1);
/** @var array $questionsData Inyectado por PreguntaController::simulador() */
$pageTitle    = 'Simulador';
$totalQ       = count($questionsData);
$questionsJson = json_encode($questionsData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
require __DIR__ . '/layouts/header.php';
?>

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

          <!-- Progreso adaptativo guardado (oculto hasta que haya estado) -->
          <div id="progress-info" class="d-none mb-3">
            <div id="progress-alert" class="alert alert-info py-2 px-3 mb-0 text-start small">
              <i id="progress-icon" class="fa-solid fa-brain me-1"></i>
              <span id="progress-label"></span>
            </div>
            <button id="btn-reset" class="btn btn-outline-danger btn-sm w-100 mt-2">
              <i class="fa-solid fa-arrow-rotate-left me-1"></i>Reiniciar progreso
            </button>
          </div>

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
              <input type="number" id="custom-time" class="form-control form-control-sm text-center w-auto"
                     min="5" max="300" placeholder="seg" size="4">
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

          <!-- Documentación del algoritmo adaptativo -->
          <div class="text-start mt-4 pt-3 border-top">
            <p class="small fw-semibold mb-1 text-muted">Repaso adaptativo</p>
            <ul class="small text-muted ps-3 mb-0">
              <li>Las preguntas acertadas <strong>no aparecen en la siguiente pasada</strong>.</li>
              <li>Las que fallás vuelven en la próxima pasada con mayor prioridad.</li>
              <li>Tras <strong>3 aciertos consecutivos</strong> una pregunta queda dominada y deja de aparecer.</li>
              <li>El progreso se guarda en el navegador y sobrevive a recargas de página.</li>
            </ul>
          </div>

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
  <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-baseline gap-1 mb-1">
    <span id="quiz-progress" class="text-muted small fw-bold"></span>
    <span id="quiz-timer-secs" class="fw-bold fs-4 font-monospace">30</span>
  </div>
  <div class="progress mb-4 rounded-pill">
    <div id="quiz-timer-bar" class="progress-bar bg-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
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
    <div class="col-12 col-sm-auto text-center">
      <svg width="130" height="130" viewBox="0 0 100 100">
        <circle cx="50" cy="50" r="42" fill="none" stroke="#dee2e6" stroke-opacity=".25" stroke-width="9"/>
        <circle id="score-ring-val" cx="50" cy="50" r="42" fill="none"
          stroke="#198754" stroke-width="9" stroke-linecap="round"
          stroke-dasharray="263.9" stroke-dashoffset="263.9"
          transform="rotate(-90 50 50)"/>
        <text id="score-text" x="50" y="56" text-anchor="middle" fill="#212529">0%</text>
      </svg>
      <div class="text-muted small mt-1">Aciertos</div>
    </div>

    <div class="col-sm">
      <div class="row g-2">
        <div class="col-6">
          <div class="card text-center py-3">
            <div id="res-correct" class="display-6 fw-bold text-success">0</div>
            <div class="small text-muted">Correctas</div>
          </div>
        </div>
        <div class="col-6">
          <div class="card text-center py-3">
            <div id="res-wrong" class="display-6 fw-bold text-danger">0</div>
            <div class="small text-muted">Incorrectas</div>
          </div>
        </div>
        <div class="col-6">
          <div class="card text-center py-3">
            <div id="res-avg" class="display-6 fw-bold text-primary">—</div>
            <div class="small text-muted">Tiempo promedio</div>
          </div>
        </div>
        <div class="col-6">
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
    <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
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
              <th class="text-center">#</th>
              <th>Enunciado</th>
              <th>Tu respuesta</th>
              <th>Resp. correcta</th>
              <th>Tiempo</th>
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

    /* ── Constantes del algoritmo adaptativo ──────────────────────────────── */
    var STORAGE_KEY   = 'simulador_adaptive_v1';
    var RETIRE_STREAK = 3;  // aciertos consecutivos para retirar la pregunta definitivamente

    /* ── Estado de sesión (no persistido) ─────────────────────────────────── */
    var state = {
        timeLimit:    30,
        timerInt:     null,
        timerStartMs: 0,
        results:      [],
        curAnswers:   [],
        answered:     false
    };

    /* ── Estado adaptativo (persistido en sessionStorage) ─────────────────── */
    /*
     * Estructura de adaptiveState:
     *   version         : 1
     *   questionSetHash : string — ids concatenados; si cambia, se descarta el estado guardado
     *   stats           : { [id]: { id, correctStreak, wrongCount, attempts,
     *                               lastSeenAt, cooldownRemaining, retired } }
     *   activeQueue     : number[] — ids ordenados para la pasada actual
     *   passNumber      : number   — contador de pasadas (empieza en 1)
     *   currentPassIdx  : number   — posición dentro de activeQueue
     *   timeLimit       : number   — segundos por pregunta, para restaurar la selección
     *
     * Reglas por respuesta:
     *   Acierto      → cooldownRemaining = 1 (salta la próxima pasada), correctStreak++
     *   3 aciertos seguidos → retired = true (se retira definitivamente)
     *   Fallo/timeout → wrongCount++, correctStreak = 0, cooldownRemaining = 0 (vuelve enseguida)
     *
     * buildQueue: excluye retired y cooldownRemaining > 0; ordena por wrongCount ↓,
     *             correctStreak ↑, lastSeenAt ↑, id ↑. Si todas en pausa, libera
     *             las no-retiradas. Si todas retiradas, devuelve array vacío.
     */
    var adaptiveState = null;

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

    /* ── Cola adaptativa ───────────────────────────────────────────────────── */

    function makeQSetHash() {
        return QUESTIONS.map(function (q) { return q.id; })
            .sort(function (a, b) { return a - b; })
            .join(',');
    }

    /*
     * Construye el orden de preguntas para la próxima pasada.
     * Excluye: retired === true  y  cooldownRemaining > 0.
     * Si todas las no-retiradas están en pausa, las libera.
     * Si todas están retiradas, devuelve [] (startQuiz lo maneja).
     */
    function buildQueue(stats) {
        var activeIds = QUESTIONS
            .map(function (q) { return q.id; })
            .filter(function (id) {
                var s = stats[id];
                if (!s || s.retired) return false;
                return !(s.cooldownRemaining > 0);
            });

        if (activeIds.length === 0) {
            // Liberar pausa de las no-retiradas (si todas están retiradas queda vacío)
            var nonRetired = QUESTIONS.filter(function (q) {
                return stats[q.id] && !stats[q.id].retired;
            });
            nonRetired.forEach(function (q) { stats[q.id].cooldownRemaining = 0; });
            activeIds = nonRetired.map(function (q) { return q.id; });
        }

        activeIds.sort(function (a, b) {
            var sa = stats[a], sb = stats[b];
            if (sb.wrongCount    !== sa.wrongCount)    return sb.wrongCount    - sa.wrongCount;
            if (sa.correctStreak !== sb.correctStreak) return sa.correctStreak - sb.correctStreak;
            if (sa.lastSeenAt    !== sb.lastSeenAt)    return sa.lastSeenAt    - sb.lastSeenAt;
            return a - b;
        });

        return activeIds;
    }

    function buildFreshAdaptiveState() {
        var stats = {};
        QUESTIONS.forEach(function (q) {
            stats[q.id] = {
                id: q.id, correctStreak: 0, wrongCount: 0,
                attempts: 0, lastSeenAt: 0, cooldownRemaining: 0, retired: false
            };
        });
        return {
            version:         1,
            questionSetHash: makeQSetHash(),
            stats:           stats,
            activeQueue:     shuffle(QUESTIONS.map(function (q) { return q.id; })),
            passNumber:      1,
            currentPassIdx:  0,
            timeLimit:       state.timeLimit
        };
    }

    function loadAdaptiveState() {
        try {
            var raw = sessionStorage.getItem(STORAGE_KEY);
            if (!raw) return null;
            var s = JSON.parse(raw);
            if (!s || s.version !== 1) return null;
            if (s.questionSetHash !== makeQSetHash()) return null;
            var valid = QUESTIONS.every(function (q) { return q.id in s.stats; });
            if (!valid) return null;
            return s;
        } catch (e) { return null; }
    }

    function saveAdaptiveState() {
        if (!adaptiveState) return;
        adaptiveState.timeLimit = state.timeLimit;
        try {
            sessionStorage.setItem(STORAGE_KEY, JSON.stringify(adaptiveState));
        } catch (e) { /* sessionStorage no disponible */ }
    }

    function clearAdaptiveState() {
        try { sessionStorage.removeItem(STORAGE_KEY); } catch (e) {}
        adaptiveState = null;
    }

    function getCurrentQuestion() {
        if (!adaptiveState) return null;
        var id = adaptiveState.activeQueue[adaptiveState.currentPassIdx];
        if (id === undefined) return null;
        return QUESTIONS.find(function (q) { return q.id === id; }) || null;
    }

    /* ── Pantalla de configuración ─────────────────────────────────────────── */
    var selectedSecs = 30;

    function updateProgressInfo() {
        var saved   = loadAdaptiveState();
        var infoDiv = el('progress-info');

        var isVirginState = !saved
            || (saved.passNumber === 1 && saved.currentPassIdx === 0);
        if (isVirginState) {
            infoDiv.classList.add('d-none');
            el('btn-start').disabled = (QUESTIONS.length === 0);
            return;
        }
        infoDiv.classList.remove('d-none');

        // Restaurar selección de tiempo del estado guardado
        if (saved.timeLimit) {
            var pill = document.querySelector('.time-pill[data-secs="' + saved.timeLimit + '"]');
            if (pill) {
                document.querySelectorAll('.time-pill').forEach(function (b) {
                    b.classList.remove('btn-dark', 'active');
                    b.classList.add('btn-outline-dark');
                });
                pill.classList.add('btn-dark', 'active');
                pill.classList.remove('btn-outline-dark');
                selectedSecs = saved.timeLimit;
            }
        }

        var totalRetired = Object.keys(saved.stats).filter(function (id) {
            return saved.stats[id] && saved.stats[id].retired;
        }).length;
        var allMastered  = QUESTIONS.length > 0 && totalRetired === QUESTIONS.length;
        var alertEl      = el('progress-alert');
        var iconEl       = el('progress-icon');

        if (allMastered) {
            alertEl.className  = 'alert alert-success py-2 px-3 mb-0 text-start small';
            iconEl.className   = 'fa-solid fa-trophy me-1';
            el('btn-start').disabled = true;
            el('progress-label').textContent = '¡Dominaste todas las preguntas! Reiniciá el progreso para volver a practicar.';
            return;
        }

        alertEl.className = 'alert alert-info py-2 px-3 mb-0 text-start small';
        iconEl.className  = 'fa-solid fa-brain me-1';
        el('btn-start').disabled = false;

        var active     = saved.activeQueue.length;
        var inCooldown = Object.keys(saved.stats).filter(function (id) {
            return saved.stats[id].cooldownRemaining > 0 && !saved.stats[id].retired;
        }).length;
        var remaining  = Math.max(0, active - saved.currentPassIdx);

        var label = 'Pasada ' + saved.passNumber;
        if (saved.currentPassIdx > 0) {
            label += ' · ' + remaining + ' restante' + (remaining !== 1 ? 's' : '');
        } else {
            label += ' · ' + active + ' pregunta' + (active !== 1 ? 's' : '');
        }
        if (inCooldown   > 0) label += ' · ' + inCooldown   + ' en pausa';
        if (totalRetired > 0) label += ' · ' + totalRetired + ' dominada' + (totalRetired !== 1 ? 's' : '');
        el('progress-label').textContent = label;
    }

    function showSetup() {
        updateProgressInfo();
        showScreen('screen-setup');
    }

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

    el('btn-reset').addEventListener('click', function () {
        clearAdaptiveState();
        updateProgressInfo();
    });

    /* ── Lógica del cuestionario ───────────────────────────────────────────── */
    function startQuiz() {
        var saved = loadAdaptiveState();
        if (saved) {
            adaptiveState = saved;
            adaptiveState.timeLimit = state.timeLimit;
            // Guardia: si la cola quedó vacía (no debería), reconstruir
            if (!adaptiveState.activeQueue || adaptiveState.activeQueue.length === 0) {
                adaptiveState.activeQueue    = buildQueue(adaptiveState.stats);
                adaptiveState.currentPassIdx = 0;
            }
        } else {
            adaptiveState = buildFreshAdaptiveState();
        }

        // Cola vacía significa que todo está dominado; volver a setup con el banner de trofeo
        if (!adaptiveState.activeQueue || adaptiveState.activeQueue.length === 0) {
            showSetup();
            return;
        }

        state.results = [];
        showScreen('screen-quiz');
        showQuestion();
    }

    function showQuestion() {
        state.answered = false;

        var q = getCurrentQuestion();
        if (!q) {
            // Pregunta no encontrada en el conjunto actual: saltar
            adaptiveState.currentPassIdx++;
            saveAdaptiveState();
            if (adaptiveState.currentPassIdx >= adaptiveState.activeQueue.length) {
                finishQuiz();
            } else {
                showQuestion();
            }
            return;
        }

        state.curAnswers = shuffle(q.respuestas);

        el('quiz-progress').textContent =
            'Pregunta ' + (adaptiveState.currentPassIdx + 1) + ' de ' + adaptiveState.activeQueue.length;
        el('quiz-question').textContent = q.enunciado;
        el('quiz-timeout-msg').classList.add('d-none');

        var container = el('quiz-answers');
        container.innerHTML = '';
        state.curAnswers.forEach(function (r, i) {
            var col = document.createElement('div');
            col.className = 'col-12 col-md-6';
            var btn = document.createElement('button');
            btn.className   = 'btn btn-outline-dark w-100 h-100 text-start answer-btn';
            btn.dataset.idx = i;
            btn.textContent = r.texto;
            btn.addEventListener('click', function () { handleAnswer(i); });
            col.appendChild(btn);
            container.appendChild(col);
        });

        startTimer();
        state.timerStartMs = performance.now();
    }

    function startTimer() {
        clearInterval(state.timerInt);
        var bar     = el('quiz-timer-bar');
        var secsEl  = el('quiz-timer-secs');
        var total   = state.timeLimit;
        var elapsed = 0;
        var TICK    = 100;

        bar.classList.remove('bg-warning', 'bg-danger');
        bar.classList.add('bg-success');
        bar.style.width        = '100%';
        secsEl.textContent     = total;

        state.timerInt = setInterval(function () {
            elapsed += TICK;
            var remaining = total - elapsed / 1000;

            if (remaining <= 0) {
                clearInterval(state.timerInt);
                bar.style.width    = '0%';
                secsEl.textContent = '0';
                handleAnswer(null);
                return;
            }

            var pct = (remaining / total) * 100;
            bar.style.width    = pct + '%';
            secsEl.textContent = Math.ceil(remaining);

            bar.classList.remove('bg-success', 'bg-warning', 'bg-danger');
            if (pct <= 25)      bar.classList.add('bg-danger');
            else if (pct <= 50) bar.classList.add('bg-warning');
            else                bar.classList.add('bg-success');
        }, TICK);
    }

    function handleAnswer(idx) {
        if (state.answered) return;
        state.answered = true;
        clearInterval(state.timerInt);

        var timeSpent     = Math.min((performance.now() - state.timerStartMs) / 1000, state.timeLimit);
        var answers       = state.curAnswers;
        var isTimeout     = (idx === null);
        var isCorrect     = !isTimeout && answers[idx].es_correcta;
        var correctAnswer = answers.find(function (a) { return a.es_correcta; }) || answers[0];
        var q             = getCurrentQuestion();

        /* Actualizar estadísticas adaptativas de la pregunta */
        var st = adaptiveState.stats[q.id];
        st.attempts++;
        st.lastSeenAt = adaptiveState.passNumber;

        if (isCorrect) {
            st.correctStreak++;
            if (st.correctStreak >= RETIRE_STREAK) {
                st.retired = true;          // dominada: sale del circuito definitivamente
            } else {
                st.cooldownRemaining = 1;   // acertada: salta la próxima pasada
            }
        } else {
            // Fallo o timeout: reinicia racha, vuelve con prioridad alta
            st.wrongCount++;
            st.correctStreak = 0;
        }

        adaptiveState.currentPassIdx++;
        saveAdaptiveState();

        state.results.push({
            enunciado:     q.enunciado,
            timeSpent:     timeSpent,
            correct:       isCorrect,
            selected:      isTimeout ? null : answers[idx].texto,
            correctAnswer: correctAnswer.texto,
            timeout:       isTimeout
        });

        showFeedback(idx, answers, isTimeout);

        setTimeout(function () {
            if (adaptiveState.currentPassIdx >= adaptiveState.activeQueue.length) {
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
            btn.classList.remove('btn-outline-dark', 'btn-success', 'btn-danger', 'text-white', 'opacity-50');
            if (answers[i].es_correcta) {
                btn.classList.add('btn-success', 'text-white');
            } else if (!isTimeout && i === selectedIdx) {
                btn.classList.add('btn-danger', 'text-white');
            } else {
                btn.classList.add('opacity-50');
            }
        });
        if (isTimeout) {
            el('quiz-timeout-msg').classList.remove('d-none');
        }
    }

    /* ── Resultados ────────────────────────────────────────────────────────── */
    function finishQuiz() {
        /*
         * Preparar la cola adaptativa para la próxima pasada:
         * 1. Decrementar cooldown de todas las preguntas en pausa.
         * 2. Reconstruir activeQueue con el nuevo orden de prioridades.
         */
        adaptiveState.passNumber++;
        Object.keys(adaptiveState.stats).forEach(function (id) {
            var st = adaptiveState.stats[id];
            if (st.cooldownRemaining > 0) st.cooldownRemaining--;
        });
        adaptiveState.activeQueue    = buildQueue(adaptiveState.stats);
        adaptiveState.currentPassIdx = 0;
        saveAdaptiveState();

        var total   = state.results.length;
        var correct = state.results.filter(function (r) { return r.correct; }).length;
        var wrong   = total - correct;
        var pct     = total > 0 ? Math.round((correct / total) * 100) : 0;
        var avgTime = total > 0
            ? state.results.reduce(function (s, r) { return s + r.timeSpent; }, 0) / total
            : 0;

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
        var ring          = el('score-ring-val');
        var circumference = 263.9;
        var offset        = circumference * (1 - pct / 100);
        var color         = pct >= 70 ? '#198754' : pct >= 50 ? '#fd7e14' : '#dc3545';

        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                ring.setAttribute('stroke', color);
                ring.setAttribute('stroke-dashoffset', offset);
            });
        });

        el('score-text').textContent = pct + '%';
        el('score-text').setAttribute('fill', color);
        el('res-correct').textContent = correct;
        el('res-wrong').textContent   = wrong;
        el('res-total').textContent   = total;
        el('res-avg').textContent     = avgTime.toFixed(1) + ' s';

        var tbody = qs('#res-table tbody');
        tbody.innerHTML = '';
        state.results.forEach(function (r) {
            var tr = document.createElement('tr');

            var iconHtml = r.correct
                ? '<span class="badge bg-success">OK</span>'
                : '<span class="badge bg-danger">X</span>';

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
        var wrap   = el('res-table-wrap');
        var icon   = this.querySelector('i');
        var hidden = wrap.style.display === 'none';
        wrap.style.display = hidden ? '' : 'none';
        icon.className = hidden ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down';
    });

    /* ── Repetir ───────────────────────────────────────────────────────────── */
    el('btn-retry').addEventListener('click', function () {
        showSetup();
    });

    /* ── Inicialización ────────────────────────────────────────────────────── */
    updateProgressInfo();

}());
</script>

<?php require __DIR__ . '/layouts/footer.php'; ?>
