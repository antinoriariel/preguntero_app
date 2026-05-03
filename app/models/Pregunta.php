<?php
declare(strict_types=1);

final class Pregunta
{
    public static function count(string $search = ''): int
    {
        if ($search !== '') {
            return (int) Database::run(
                'SELECT COUNT(*) FROM preguntas WHERE enunciado LIKE ?',
                ['%' . $search . '%']
            )->fetchColumn();
        }
        return (int) Database::run('SELECT COUNT(*) FROM preguntas')->fetchColumn();
    }

    public static function paginate(int $limit, int $offset, string $search = ''): array
    {
        if ($search !== '') {
            return Database::run(
                'SELECT * FROM preguntas WHERE enunciado LIKE ? ORDER BY created_at DESC LIMIT ? OFFSET ?',
                ['%' . $search . '%', $limit, $offset]
            )->fetchAll();
        }
        return Database::run(
            'SELECT * FROM preguntas ORDER BY created_at DESC LIMIT ? OFFSET ?',
            [$limit, $offset]
        )->fetchAll();
    }

    public static function findById(int $id): array|false
    {
        return Database::run(
            'SELECT * FROM preguntas WHERE id_pregunta = ? LIMIT 1',
            [$id]
        )->fetch();
    }

    public static function create(string $enunciado): int
    {
        Database::run('INSERT INTO preguntas (enunciado) VALUES (?)', [$enunciado]);
        return (int) Database::get()->lastInsertId();
    }

    public static function update(int $id, string $enunciado): void
    {
        Database::run(
            'UPDATE preguntas SET enunciado = ? WHERE id_pregunta = ?',
            [$enunciado, $id]
        );
    }

    public static function delete(int $id): void
    {
        Database::run('DELETE FROM preguntas WHERE id_pregunta = ?', [$id]);
    }

    public static function all(): array
    {
        return Database::run('SELECT * FROM preguntas ORDER BY id_pregunta')->fetchAll();
    }

    /**
     * Inserts multiple questions with their answers atomically.
     * Rolls back the entire batch on any failure.
     * Returns ['count' => N, 'total_respuestas' => M].
     */
    public static function insertBatch(array $blocks): array
    {
        $pdo = Database::get();
        $pdo->beginTransaction();

        try {
            $stmtP = $pdo->prepare('INSERT INTO preguntas (enunciado) VALUES (?)');
            $stmtR = $pdo->prepare(
                'INSERT INTO respuestas (id_pregunta, respuesta, es_correcta) VALUES (?, ?, ?)'
            );

            $count           = 0;
            $totalRespuestas = 0;

            foreach ($blocks as $block) {
                $stmtP->execute([$block['enunciado']]);
                $idPregunta = (int)$pdo->lastInsertId();
                $count++;

                foreach ($block['respuestas'] as $r) {
                    $stmtR->execute([$idPregunta, $r['respuesta'], $r['es_correcta'] ? 1 : 0]);
                    $totalRespuestas++;
                }
            }

            $pdo->commit();
            return ['count' => $count, 'total_respuestas' => $totalRespuestas];
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
