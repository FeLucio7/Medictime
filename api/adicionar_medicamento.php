<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:8083');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

session_start();

$id_usuario = $_SESSION['id_user'] ?? null;
if (!$id_usuario) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}

require_once 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Dados inválidos']);
    exit;
}

if (empty($data['nome']) || empty($data['horario']) || empty($data['dosagem'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Campos obrigatórios faltando']);
    exit;
}

try {
    $stmt = $pdo->prepare('INSERT INTO medicamento (id_usuario, nome_medicamento, horario, dosagem, observacao) VALUES (:id_usuario, :nome, :horario, :dosagem, :observacao)');
    $stmt->execute([
        ':id_usuario' => $id_usuario,
        ':nome' => $data['nome'],
        ':horario' => $data['horario'],
        ':dosagem' => $data['dosagem'],
        ':observacao' => $data['observacao'] ?? ''
    ]);
    http_response_code(201);
    echo json_encode(['message' => 'Medicamento adicionado com sucesso']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao inserir no banco: ' . $e->getMessage()]);
}
