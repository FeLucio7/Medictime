<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE');
header('Access-Control-Allow-Headers: Content-Type');

session_start();

$id_usuario = $_SESSION['id_user'] ?? null;
if (!$id_usuario) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}
// Pega o id do medicamento a excluir
$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    http_response_code(400);
    echo json_encode(['error' => 'ID inválido']);
    exit;
}

require_once 'db.php';

try {
    // Verifica se o medicamento pertence ao usuário
    $stmt = $pdo->prepare('SELECT id_medicamento FROM medicamento WHERE id_medicamento = :id AND id_usuario = :id_usuario');
    $stmt->execute([':id' => $id, ':id_usuario' => $id_usuario]);
    if ($stmt->rowCount() === 0) {
        http_response_code(403);
        echo json_encode(['error' => 'Medicamento não encontrado ou sem permissão']);
        exit;
    }

    // Deleta o medicamento
    $stmt = $pdo->prepare('DELETE FROM medicamento WHERE id_medicamento = :id');
    $stmt->execute([':id' => $id]);

    echo json_encode(['message' => 'Medicamento excluído com sucesso']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro no servidor: ' . $e->getMessage()]);
}
