<?php
declare(strict_types=1);

final class PreguntaController
{
    private const PER_PAGE           = 10;
    private const MIN_RESPUESTAS      = 2;
    private const MAX_QUESTIONS       = 50;
    private const MAX_TEXTAREA_BYTES  = 50000;

    public function index(): void
    {
        Session::requireLogin();

        $page   = max(1, (int)($_GET['page'] ?? 1));
        $search = trim($_GET['q'] ?? '');
        $total  = Pregunta::count($search);
        $pages  = max(1, (int)ceil($total / self::PER_PAGE));
        $page   = min($page, $pages);

        $preguntas = Pregunta::paginate(self::PER_PAGE, ($page - 1) * self::PER_PAGE, $search);

        require __DIR__ . '/../views/preguntas/index.php';
    }

    public function show(int $id): void
    {
        Session::requireLogin();

        $pregunta = Pregunta::findById($id);
        if (!$pregunta) {
            $this->notFound();
            return;
        }

        $respuestas = Respuesta::findByPregunta($id);
        require __DIR__ . '/../views/preguntas/show.php';
    }

    public function create(): void
    {
        Session::requireLogin();
        require __DIR__ . '/../views/preguntas/form.php';
    }

    public function store(): void
    {
        Session::requireLogin();
        Csrf::verify();

        [$errors, $enunciado, $respuestas] = $this->validateForm($_POST);

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('old', $_POST);
            header('Location: /preguntas/create');
            exit;
        }

        $id = Pregunta::create($enunciado);
        Respuesta::replaceForPregunta($id, $respuestas);

        Session::flash('success', 'Pregunta creada correctamente.');
        header("Location: /preguntas/$id");
        exit;
    }

    public function edit(int $id): void
    {
        Session::requireLogin();

        $pregunta = Pregunta::findById($id);
        if (!$pregunta) {
            $this->notFound();
            return;
        }

        $respuestas = Respuesta::findByPregunta($id);
        require __DIR__ . '/../views/preguntas/form.php';
    }

    public function update(int $id): void
    {
        Session::requireLogin();
        Csrf::verify();

        $pregunta = Pregunta::findById($id);
        if (!$pregunta) {
            $this->notFound();
            return;
        }

        [$errors, $enunciado, $respuestas] = $this->validateForm($_POST);

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('old', $_POST);
            header("Location: /preguntas/$id/edit");
            exit;
        }

        Pregunta::update($id, $enunciado);
        Respuesta::replaceForPregunta($id, $respuestas);

        Session::flash('success', 'Pregunta actualizada correctamente.');
        header("Location: /preguntas/$id");
        exit;
    }

    public function destroy(int $id): void
    {
        Session::requireLogin();
        Csrf::verify();

        $pregunta = Pregunta::findById($id);
        if (!$pregunta) {
            $this->notFound();
            return;
        }

        Pregunta::delete($id);

        Session::flash('success', 'Pregunta eliminada.');
        header('Location: /preguntas');
        exit;
    }

    /**
     * Valida el formulario de pregunta+respuestas.
     * Devuelve [errors, enunciado, respuestas].
     */
    public function simulador(): void
    {
        Session::requireLogin();

        $questionsData = [];
        foreach (Pregunta::all() as $p) {
            $rs = Respuesta::findByPregunta((int)$p['id_pregunta']);
            if (empty($rs)) continue;
            if (empty(array_filter($rs, fn($r) => (bool)$r['es_correcta']))) continue;

            $questionsData[] = [
                'id'        => $p['id_pregunta'],
                'enunciado' => $p['enunciado'],
                'respuestas' => array_values(array_map(fn($r) => [
                    'texto'       => $r['respuesta'],
                    'es_correcta' => (bool)$r['es_correcta'],
                ], $rs)),
            ];
        }

        require __DIR__ . '/../views/simulador.php';
    }

    private function validateForm(array $post): array
    {
        $errors    = [];
        $enunciado = trim($post['enunciado'] ?? '');

        if ($enunciado === '') {
            $errors['enunciado'] = 'El enunciado es obligatorio.';
        } elseif (mb_strlen($enunciado) > 500) {
            $errors['enunciado'] = 'El enunciado no puede superar 500 caracteres.';
        }

        // Los índices de los checkboxes son los valores enviados en es_correcta[]
        $correctaSet = [];
        foreach ((array)($post['es_correcta'] ?? []) as $idx) {
            $correctaSet[(int)$idx] = true;
        }

        $respuestas = [];
        foreach ((array)($post['respuesta'] ?? []) as $i => $texto) {
            $texto = trim((string)$texto);
            if ($texto !== '') {
                $respuestas[] = [
                    'respuesta'   => $texto,
                    'es_correcta' => isset($correctaSet[$i]),
                ];
            }
        }

        if (count($respuestas) < self::MIN_RESPUESTAS) {
            $errors['respuestas'] = 'Debe ingresar al menos ' . self::MIN_RESPUESTAS . ' respuestas.';
        } elseif (empty(array_filter(array_column($respuestas, 'es_correcta')))) {
            $errors['respuestas'] = 'Debe marcar al menos una respuesta como correcta.';
        }

        return [$errors, $enunciado, $respuestas];
    }

    public function importarForm(): void
    {
        Session::requireLogin();
        $errors  = Session::getFlash('import_errors', []);
        $old_raw = Session::getFlash('import_raw', '');
        require __DIR__ . '/../views/preguntas/import.php';
    }

    public function importar(): void
    {
        Session::requireLogin();
        Csrf::verify();

        $raw = $_POST['lote'] ?? '';

        if (mb_strlen($raw) > self::MAX_TEXTAREA_BYTES) {
            Session::flash('import_errors', ['El texto supera el límite de ' . self::MAX_TEXTAREA_BYTES . ' caracteres.']);
            Session::flash('import_raw', mb_substr($raw, 0, self::MAX_TEXTAREA_BYTES));
            header('Location: /preguntas/importar');
            exit;
        }

        $result = $this->parseImportBatch($raw);

        if (!$result['ok']) {
            Session::flash('import_errors', $result['errors']);
            Session::flash('import_raw', $raw);
            header('Location: /preguntas/importar');
            exit;
        }

        $inserted = Pregunta::insertBatch($result['blocks']);

        $q = $inserted['count'];
        $r = $inserted['total_respuestas'];
        $msg = 'Se importaron ' . $q . ' pregunta' . ($q !== 1 ? 's' : '') .
               ' con ' . $r . ' respuesta' . ($r !== 1 ? 's' : '') . ' en total.';
        Session::flash('success', $msg);
        header('Location: /preguntas');
        exit;
    }

    private function parseImportBatch(string $raw): array
    {
        $raw = str_replace(["\r\n", "\r"], "\n", $raw);

        if (trim($raw) === '') {
            return ['ok' => false, 'errors' => ['El campo de texto está vacío.']];
        }

        $lines = explode("\n", $raw);

        // Split into blocks delimited by lines that are exactly '---'
        $blocks  = [];
        $current = [];
        foreach ($lines as $line) {
            if (trim($line) === '---') {
                $blocks[] = $current;
                $current  = [];
            } else {
                $current[] = $line;
            }
        }
        $blocks[] = $current;

        // Discard blocks that are entirely whitespace
        $blocks = array_values(array_filter(
            $blocks,
            fn($b) => !empty(array_filter($b, fn($l) => trim($l) !== ''))
        ));

        if (empty($blocks)) {
            return ['ok' => false, 'errors' => ['No se encontró ningún bloque de preguntas.']];
        }

        if (count($blocks) > self::MAX_QUESTIONS) {
            return ['ok' => false, 'errors' => [
                'El lote supera el máximo de ' . self::MAX_QUESTIONS . ' preguntas por importación.',
            ]];
        }

        $parsed = [];
        $errors = [];

        foreach ($blocks as $blockIdx => $blockLines) {
            $blockNum   = $blockIdx + 1;
            $blockErrors = [];

            // Strip leading/trailing blank lines within the block
            while (!empty($blockLines) && trim($blockLines[0]) === '') {
                array_shift($blockLines);
            }
            while (!empty($blockLines) && trim(end($blockLines)) === '') {
                array_pop($blockLines);
            }

            if (empty($blockLines)) {
                $errors[] = "Bloque $blockNum: el bloque está vacío.";
                continue;
            }

            $enunciado = trim(array_shift($blockLines));

            if ($enunciado === '') {
                $blockErrors[] = "Bloque $blockNum: el enunciado no puede estar vacío.";
            } elseif (mb_strlen($enunciado) > 500) {
                $blockErrors[] = "Bloque $blockNum: el enunciado supera los 500 caracteres.";
            }

            if (empty($blockLines)) {
                $blockErrors[] = "Bloque $blockNum: no se encontraron respuestas.";
                $errors = array_merge($errors, $blockErrors);
                continue;
            }

            $respuestas = [];
            $answerNum  = 1;

            foreach ($blockLines as $lineIdx => $line) {
                $lineNum = $lineIdx + 2; // +1 one-based, +1 because enunciado was removed

                if (trim($line) === '') {
                    $blockErrors[] = "Bloque $blockNum, línea $lineNum: línea vacía dentro del bloque.";
                    continue;
                }

                if (!preg_match('/^(\d+)\.\s(.+)$/', $line, $m)) {
                    $blockErrors[] = "Bloque $blockNum, línea $lineNum: formato incorrecto. "
                        . "Esperado: \"$answerNum. texto de la respuesta\".";
                    $answerNum++;
                    continue;
                }

                $num  = (int)$m[1];
                $text = $m[2];

                if ($num !== $answerNum) {
                    $blockErrors[] = "Bloque $blockNum, línea $lineNum: "
                        . "se esperaba la respuesta $answerNum pero se encontró $num.";
                }
                $answerNum++;

                $esCorrecta = false;
                if (str_ends_with($text, ' *')) {
                    $esCorrecta = true;
                    $text       = substr($text, 0, -2);
                }

                $text = trim($text);
                if ($text === '') {
                    $blockErrors[] = "Bloque $blockNum, línea $lineNum: el texto de la respuesta no puede estar vacío.";
                    continue;
                }

                $respuestas[] = [
                    'respuesta'   => $text,
                    'es_correcta' => $esCorrecta,
                ];
            }

            $validCount = count($respuestas);

            if (empty($blockErrors)) {
                if ($validCount < self::MIN_RESPUESTAS) {
                    $blockErrors[] = "Bloque $blockNum: se requieren al menos "
                        . self::MIN_RESPUESTAS . " respuestas (se encontraron $validCount).";
                } elseif (empty(array_filter($respuestas, fn($r) => $r['es_correcta']))) {
                    $blockErrors[] = "Bloque $blockNum: debe haber al menos una respuesta marcada como correcta (con \" *\" al final).";
                }
            }

            if (!empty($blockErrors)) {
                $errors = array_merge($errors, $blockErrors);
                continue;
            }

            $parsed[] = ['enunciado' => $enunciado, 'respuestas' => $respuestas];
        }

        if (!empty($errors)) {
            return ['ok' => false, 'errors' => $errors];
        }

        return ['ok' => true, 'blocks' => $parsed];
    }

    private function notFound(): void
    {
        http_response_code(404);
        $pageTitle = 'No encontrado';
        require __DIR__ . '/../views/layouts/header.php';
        echo '<div class="alert alert-warning">La pregunta no existe o fue eliminada.</div>';
        echo '<a href="/preguntas" class="btn btn-outline-secondary">Volver al listado</a>';
        require __DIR__ . '/../views/layouts/footer.php';
    }
}
