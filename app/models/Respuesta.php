<?php
declare(strict_types=1);

final class Respuesta
{
    public static function findByPregunta(int $idPregunta): array
    {
        return Database::run(
            'SELECT * FROM respuestas WHERE id_pregunta = ? ORDER BY id_respuesta',
            [$idPregunta]
        )->fetchAll();
    }

    /**
     * Reemplaza todas las respuestas de una pregunta dentro de una transacción.
     * Garantiza que nunca quede la pregunta en estado parcial.
     */
    public static function replaceForPregunta(int $idPregunta, array $respuestas): void
    {
        $pdo = Database::get();
        $pdo->beginTransaction();

        try {
            $pdo->prepare('DELETE FROM respuestas WHERE id_pregunta = ?')->execute([$idPregunta]);

            $stmt = $pdo->prepare(
                'INSERT INTO respuestas (id_pregunta, respuesta, es_correcta) VALUES (?, ?, ?)'
            );
            foreach ($respuestas as $r) {
                $stmt->execute([
                    $idPregunta,
                    $r['respuesta'],
                    $r['es_correcta'] ? 1 : 0,
                ]);
            }

            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
