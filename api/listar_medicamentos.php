<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Credentials: true');

session_start();

$id_usuario = $_SESSION['id_user'] ?? null;
if (!$id_usuario) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}

require_once 'db.php';

try {
    $stmt = $pdo->prepare('SELECT id_medicamento, nome_medicamento, horario, dosagem, observacao FROM medicamento WHERE id_usuario = :id_usuario ORDER BY nome_medicamento');
    $stmt->execute([':id_usuario' => $id_usuario]);
    $medicamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($medicamentos);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao buscar medicamentos: ' . $e->getMessage()]);
}
