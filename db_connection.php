<?php
// Configuração de conexão com o banco de dados
$host = 'localhost';     // Host do banco de dados
$dbname = 'luzvox';   // Nome do banco de dados
$username = 'root';      // Usuário do banco de dados
$password = '';          // Senha do banco de dados (deixe em branco se não houver)

try {
    // Criar uma nova conexão PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>
