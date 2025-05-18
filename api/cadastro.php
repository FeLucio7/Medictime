<?php
// CORS headers
header("Access-Control-Allow-Origin: *"); // ou use o domínio exato por segurança
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Lida com requisição pré-vôo
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

header('Content-Type: application/json');

require_once 'db.php'; // conexão PDO em $pdo

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $required = ['nome', 'data_nascimento', 'celular', 'email', 'usuario', 'senha'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "Campo obrigatório ausente: $field"]);
            exit;
        }
    }

    // Validação básica do email
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Email inválido.']);
        exit;
    }
    
    // Validação simples da data no formato YYYY-MM-DD
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['data_nascimento'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Data de nascimento inválida. Use formato YYYY-MM-DD.']);
        exit;
    }

    // Exemplo simples de validação do celular (números e opcional +)
    if (!preg_match('/^\+?\d{8,15}$/', $data['celular'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Número de celular inválido.']);
        exit;
    }

    $nome = trim($data['nome']);
    $data_nascimento = $data['data_nascimento'];
    $celular = trim($data['celular']);
    $email = trim($data['email']);
    $usuario = trim($data['usuario']);
    $senha = $data['senha'];

    // Confere se já existe usuário
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE usuario = :usuario");
    $stmt->execute([':usuario' => $usuario]);
    if ($stmt->fetchColumn() > 0) {
        http_response_code(409);
        echo json_encode(['error' => 'Nome de usuário já existe.']);
        exit;
    }

    // Criptografa a senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO usuarios (nome, data_nascimento, celular, email, usuario, senha)
        VALUES (:nome, :data_nascimento, :celular, :email, :usuario, :senha)
    ");

    $success = $stmt->execute([
        ':nome' => $nome,
        ':data_nascimento' => $data_nascimento,
        ':celular' => $celular,
        ':email' => $email,
        ':usuario' => $usuario,
        ':senha' => $senha_hash,
    ]);

    if ($success) {
        http_response_code(201);
        echo json_encode(['message' => 'Usuário cadastrado com sucesso!']);
    } else {
        throw new Exception("Erro ao inserir no banco.");
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro no servidor: ' . $e->getMessage()]);
}
?>
