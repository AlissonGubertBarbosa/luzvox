<?php
// Conectar ao banco de dados (ajuste os parâmetros conforme necessário)
$host = 'localhost';
$dbname = 'luzvox'; // Substitua pelo nome do seu banco de dados
$username = 'root';
$password = '';

// Estabelecer a conexão com o banco de dados
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}

// Definir cabeçalho para permitir acesso via CORS e definir tipo de conteúdo como JSON
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

// Buscar todos os markers do banco de dados
$sql = "SELECT * FROM marker";
$stmt = $pdo->query($sql);
$markers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verificar se há registros e retornar como JSON
if ($markers) {
    echo json_encode(['markers' => $markers], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} else {
    echo json_encode(['markers' => []]);  // Retorna um array vazio se não houver registros
}
?>
