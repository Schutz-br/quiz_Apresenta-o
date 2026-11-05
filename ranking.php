<?php

header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/conexao.php";

try {

    $sql = "
        SELECT
            j.nick,

            COALESCE(
                SUM(CASE WHEN p.quiz = 'basico'
                    THEN p.pontos ELSE 0 END), 0
            ) AS basico,

            COALESCE(
                SUM(CASE WHEN p.quiz = 'intermediario'
                    THEN p.pontos ELSE 0 END), 0
            ) AS intermediario,

            COALESCE(
                SUM(CASE WHEN p.quiz = 'avancado'
                    THEN p.pontos ELSE 0 END), 0
            ) AS avancado,

            COALESCE(SUM(p.pontos), 0) AS total

        FROM jogadores j

        LEFT JOIN pontuacoes p
            ON p.jogador_id = j.id

        GROUP BY j.id, j.nick

        ORDER BY total DESC, j.nick ASC
    ";

    $stmt = $pdo->query($sql);

    echo json_encode([
        "sucesso" => true,
        "ranking" => $stmt->fetchAll()
    ]);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao consultar o ranking."
    ]);
}