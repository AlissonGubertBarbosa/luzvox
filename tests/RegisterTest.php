<?php
use PHPUnit\Framework\TestCase;

class RegisterTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        require 'db_connection.php';
        $this->pdo = $pdo;
    }

    public function testRegisterSuccess()
    {
        // Simular o POST de registro
        $_POST['action'] = 'register';
        $_POST['nome'] = 'Novo Usuário';
        $_POST['email'] = 'novo@exemplo.com';
        $_POST['senha'] = 'senha123';
        $_POST['tipo'] = 'usuario';
        ob_start();
        include 'login.php';
        ob_end_clean();

        // Verificar se o usuário foi inserido no banco
        $stmt = $this->pdo->query("SELECT * FROM usuario WHERE email = 'novo@exemplo.com'");
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertNotEmpty($usuario);
        $this->assertEquals('Novo Usuário', $usuario['nome']);

        // Limpar o banco
        $this->pdo->exec("DELETE FROM usuario WHERE email = 'novo@exemplo.com'");
    }

    public function testRegisterDuplicateEmail()
    {
        // Criar um usuário temporário
        $this->pdo->exec("INSERT INTO usuario (nome, email, senha, tipo) VALUES ('Teste', 'teste@exemplo.com', MD5('senha123'), 'usuario')");

        // Simular o POST de registro com o mesmo email
        $_POST['action'] = 'register';
        $_POST['nome'] = 'Outro Usuário';
        $_POST['email'] = 'teste@exemplo.com';
        $_POST['senha'] = 'senha456';
        $_POST['tipo'] = 'usuario';
        ob_start();
        include 'login.php';
        ob_end_clean();

        // Verificar se o usuário duplicado não foi inserido
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM usuario WHERE email = 'teste@exemplo.com'");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        $this->assertEquals(1, $count);

        // Limpar o banco
        $this->pdo->exec("DELETE FROM usuario WHERE email = 'teste@exemplo.com'");
    }
}

?>