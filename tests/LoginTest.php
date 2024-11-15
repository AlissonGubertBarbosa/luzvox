<?php
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        require 'db_connection.php';
        $this->pdo = $pdo;
    }

    public function testLoginSuccess()
    {
        // Criar um usuário temporário
        $email = 'teste@exemplo.com';
        $senha = md5('senha123');
        $tipo = 'usuario';
        $this->pdo->exec("INSERT INTO usuario (nome, email, senha, tipo) VALUES ('Teste', '$email', '$senha', '$tipo')");

        // Simular o POST de login
        $_POST['action'] = 'login';
        $_POST['email'] = $email;
        $_POST['senha'] = 'senha123';
        ob_start();
        include 'login.php';
        ob_end_clean();

        // Verificar se a sessão foi iniciada
        $this->assertNotEmpty($_SESSION['usuario_id']);
        $this->assertEquals('Teste', $_SESSION['usuario_nome']);

        // Limpar o banco
        $this->pdo->exec("DELETE FROM usuario WHERE email = '$email'");
    }

    public function testLoginFailure()
    {
        // Simular o POST de login com credenciais incorretas
        $_POST['action'] = 'login';
        $_POST['email'] = 'invalido@exemplo.com';
        $_POST['senha'] = 'senha123';
        ob_start();
        include 'login.php';
        ob_end_clean();

        // Verificar se a sessão não foi criada
        $this->assertEmpty($_SESSION['usuario_id']);
    }
}

?>