<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require __DIR__ . '/conexao.php';

$rawBody = file_get_contents('php://input');
if (!empty($rawBody)) {
    $decoded = json_decode($rawBody, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $_POST = array_merge($_POST, $decoded);
    }
}

$action = $_POST['action'] ?? $_GET['action'] ?? null;

function responder($statusCode, $payload)
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    switch ($action) {
        case 'login':
            $login = trim((string)($_POST['login'] ?? $_GET['login'] ?? ''));
            $senha = (string)($_POST['senha'] ?? $_GET['senha'] ?? '');

            if ($login === '' || $senha === '') {
                responder(400, ['erro' => 'Login e senha são obrigatórios.']);
            }

            $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = ? OR user = ? LIMIT 1');
            $stmt->execute([$login, $login]);
            $usuario = $stmt->fetch();

            if (!$usuario || !password_verify($senha, $usuario['senha_hash'])) {
                responder(401, ['erro' => 'Credenciais inválidas.']);
            }

            unset($usuario['senha_hash']);
            responder(200, ['usuario' => $usuario, 'mensagem' => 'Login realizado com sucesso.']);

        case 'register':
            $email = trim((string)($_POST['email'] ?? $_GET['email'] ?? ''));
            $user = trim((string)($_POST['user'] ?? $_GET['user'] ?? ''));
            $senha = (string)($_POST['senha'] ?? $_GET['senha'] ?? '');

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                responder(400, ['erro' => 'E-mail inválido.']);
            }
            if ($user === '' || strlen($user) < 3) {
                responder(400, ['erro' => 'O nome de usuário deve ter pelo menos 3 caracteres.']);
            }
            if (strlen($senha) < 6) {
                responder(400, ['erro' => 'A senha deve ter pelo menos 6 caracteres.']);
            }

            $check = $pdo->prepare('SELECT id FROM usuarios WHERE email = ? OR user = ? LIMIT 1');
            $check->execute([$email, $user]);
            if ($check->fetch()) {
                responder(409, ['erro' => 'E-mail ou usuário já cadastrados.']);
            }

            $senhaHash = password_hash($senha, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('INSERT INTO usuarios (email, user, senha_hash) VALUES (?, ?, ?)');
            $stmt->execute([$email, $user, $senhaHash]);

            responder(201, [
                'mensagem' => 'Usuário cadastrado com sucesso.',
                'usuario' => [
                    'id' => $pdo->lastInsertId(),
                    'email' => $email,
                    'user' => $user,
                ]
            ]);

        case 'salvarPontuacao':
            $usuarioId = (int)($_POST['usuarioId'] ?? $_GET['usuarioId'] ?? 0);
            $quiz = trim((string)($_POST['quiz'] ?? $_GET['quiz'] ?? ''));
            $pontos = (int)($_POST['pontos'] ?? $_GET['pontos'] ?? 0);

            if ($usuarioId <= 0 || $quiz === '' || $pontos < 0) {
                responder(400, ['erro' => 'Dados da pontuação incompletos.']);
            }

            $stmt = $pdo->prepare('INSERT INTO pontuacoes (usuario_id, quiz, pontos) VALUES (?, ?, ?)');
            $stmt->execute([$usuarioId, $quiz, $pontos]);

            responder(201, ['mensagem' => 'Pontuação salva com sucesso.']);

        case 'pontuacoes':
            $usuarioId = (int)($_POST['usuarioId'] ?? $_GET['usuarioId'] ?? 0);

            if ($usuarioId <= 0) {
                responder(400, ['erro' => 'Usuário não informado.']);
            }

            $stmt = $pdo->prepare('SELECT * FROM pontuacoes WHERE usuario_id = ? ORDER BY data DESC');
            $stmt->execute([$usuarioId]);
            responder(200, ['pontuacoes' => $stmt->fetchAll()]);

        default:
            responder(404, ['erro' => 'Ação inválida.']);
    }
} catch (Throwable $e) {
    responder(500, [
        'erro' => 'Erro interno do servidor.',
        'detalhe' => $e->getMessage(),
    ]);
}