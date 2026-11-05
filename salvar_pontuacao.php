
<?php

header("Content-Type: application/json; charset=utf-8");

ini_set("display_errors", "0");
error_reporting(E_ALL);

function responder($sucesso, $mensagem, $codigo = 200) {
    http_response_code($codigo);

    echo json_encode([
        "sucesso" => $sucesso,
        "mensagem" => $mensagem
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

try {

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        responder(false, "Método não permitido.", 405);
    }

    require_once __DIR__ . "/conexao.php";

    if (!isset($pdo) || !($pdo instanceof PDO)) {
        throw new Exception("Variável PDO não encontrada no conexao.php.");
    }

    $dados = json_decode(file_get_contents("php://input"), true);

    if (!is_array($dados)) {
        responder(false, "Dados JSON inválidos.", 400);
    }

    $nick = trim($dados["nick"] ?? "");
    $quiz = $dados["quiz"] ?? "";
    $pontos = $dados["pontos"] ?? null;

    $quizzesPermitidos = [
        "basico",
        "intermediario",
        "avancado"
    ];

    if (mb_strlen($nick) < 3 || mb_strlen($nick) > 20) {
        responder(false, "O nick deve ter entre 3 e 20 caracteres.", 400);
    }

    if (!preg_match('/^[\p{L}\p{N}_ -]+$/u', $nick)) {
        responder(false, "Nick contém caracteres inválidos.", 400);
    }

    if (!in_array($quiz, $quizzesPermitidos, true)) {
        responder(false, "Quiz inválido.", 400);
    }

    if (!is_int($pontos) || $pontos < 0 || $pontos > 5) {
        responder(false, "Pontuação inválida.", 400);
    }

    $pdo->beginTransaction();

    $sqlJogador = "
        INSERT INTO jogadores (nick)
        VALUES (:nick)
        ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id)
    ";

    $stmtJogador = $pdo->prepare($sqlJogador);

    $stmtJogador->execute([
        ":nick" => $nick
    ]);

    $jogadorId = (int) $pdo->lastInsertId();

    $sqlPontuacao = "
        INSERT INTO pontuacoes (jogador_id, quiz, pontos)
        VALUES (:jogador_id, :quiz, :pontos)
        ON DUPLICATE KEY UPDATE
        pontos = GREATEST(pontos, VALUES(pontos))
    ";

    $stmtPontuacao = $pdo->prepare($sqlPontuacao);

    $stmtPontuacao->execute([
        ":jogador_id" => $jogadorId,
        ":quiz" => $quiz,
        ":pontos" => $pontos
    ]);

    $pdo->commit();

    responder(true, "Pontuação salva com sucesso!");

} catch (Throwable $e) {

    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log("Erro ao salvar pontuação: " . $e->getMessage());

    responder(
        false,
        "Erro no PHP: " . $e->getMessage(),
        500
    );
}
