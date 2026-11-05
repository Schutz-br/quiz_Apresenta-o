
<?php

$host = "localhost";
$banco = "quiz_db";
$usuario = "root";
$senha = "7433";

try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$banco;charset=utf8mb4",
        $usuario,
        $senha,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

} catch (PDOException $e) {

    error_log("Erro de conexão: " . $e->getMessage());

    http_response_code(500);

    header("Content-Type: application/json; charset=utf-8");

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao conectar com o banco de dados."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}
