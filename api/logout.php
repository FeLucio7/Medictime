<?php
session_start();
session_unset();  // Remove todas as variáveis da sessão
session_destroy(); // Destroi a sessão atual

http_response_code(200);
echo json_encode(['mensagem' => 'Logout realizado com sucesso']);
?>