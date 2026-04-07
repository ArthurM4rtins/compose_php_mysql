<?php
header("Content-Type: application/json");

// 1. Configuração do Banco de Dados
$host = 'mysql';
$user = 'meu_usuario';
$pass = 'minha_senha';
$db = 'meu_banco';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(["error" => "Erro na conexão: " . $conn->connect_error]));
}

// Cria a tabela automaticamente se não existir para facilitar o teste
$conn->query("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL
)");

// 2. Leitura da Requisição
$method = $_SERVER['REQUEST_METHOD'];
$requestUri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$scriptName = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$apiPath = array_slice($requestUri, count($scriptName));

// 3. Roteamento da API
if ($method == 'GET' && count($apiPath) == 1 && $apiPath[0] == 'users') {
    // Listar todos os usuários
    $result = $conn->query("SELECT * FROM users");
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    echo json_encode($users);

} elseif ($method == 'POST' && count($apiPath) == 1 && $apiPath[0] == 'users') {
    // Criar novo usuário
    $input = json_decode(file_get_contents("php://input"), true);
    
    $stmt = $conn->prepare("INSERT INTO users (nome, email) VALUES (?, ?)");
    $stmt->bind_param("ss", $input['nome'], $input['email']);
    
    if ($stmt->execute()) {
        echo json_encode(["message" => "Usuário criado", "id" => $stmt->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao criar usuário"]);
    }
    $stmt->close();

} elseif ($method == 'GET' && count($apiPath) == 2 && $apiPath[0] == 'users') {
    // Buscar usuário específico
    $id = intval($apiPath[1]);
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode($row);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Usuário não encontrado"]);
    }
    $stmt->close();

} elseif ($method == 'PUT' && count($apiPath) == 2 && $apiPath[0] == 'users') {
    // Atualizar usuário
    $id = intval($apiPath[1]);
    $input = json_decode(file_get_contents("php://input"), true);
    
    $stmt = $conn->prepare("UPDATE users SET nome = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $input['nome'], $input['email'], $id);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        echo json_encode(["message" => "Usuário atualizado com sucesso"]);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Usuário não encontrado ou sem alterações"]);
    }
    $stmt->close();

} elseif ($method == 'DELETE' && count($apiPath) == 2 && $apiPath[0] == 'users') {
    // Deletar usuário
    $id = intval($apiPath[1]);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        echo json_encode(["message" => "Usuário deletado"]);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Usuário não encontrado"]);
    }
    $stmt->close();

} else {
    http_response_code(404);
    echo json_encode(["error" => "Rota não encontrada"]);
}

$conn->close();
?>