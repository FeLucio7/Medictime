<?php
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Ajuste conforme necessário para produção
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'db.php';

try {
    $usuario = $_POST['usuario'] ?? null;
    $senha = $_POST['senha'] ?? null;

    if (empty($usuario) || empty($senha)) {
        http_response_code(400);
        echo json_encode(['error' => 'Dados incompletos.']);
        exit;
    }

    $usuario = trim($usuario);

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = :usuario");
    $stmt->execute([':usuario' => $usuario]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user_data) {
        http_response_code(404);
        echo json_encode(['error' => 'Usuário não encontrado.']);
        header('Location: ../public/login.html');

        exit;
    }

    if (!password_verify($senha, $user_data['senha'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Usuário ou senha inválidos.']);
        header('Location: ../public/login.html');

        exit;
    }

    $_SESSION['id_user'] = $user_data['id_user'];
    $_SESSION['usuario'] = $user_data['usuario'];
    $_SESSION['nome'] = $user_data['nome']; // se existir
    http_response_code(200);

    header('Location: ../public/index.html');
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro no servidor: ' . $e->getMessage()]);
}
