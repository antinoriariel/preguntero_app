<?php
declare(strict_types=1);

final class PreguntaController
{
    private const PER_PAGE      = 10;
    private const MIN_RESPUESTAS = 2;

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
