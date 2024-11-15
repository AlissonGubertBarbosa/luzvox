<?php
session_start();
require 'db_connection.php';

// Verificar se o formulário de login foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Verificar se o usuário existe no banco de dados
    $sql = "SELECT * FROM usuario WHERE email = :email AND senha = MD5(:senha)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':senha', $senha);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_tipo'] = $usuario['tipo'];
        header('Location: index.php');
        exit;
    } else {
        $erro = "Email ou senha incorretos!";
    }
}

// Verificar se o formulário de inscrição foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $tipo = $_POST['tipo'];

    // Inserir o novo usuário no banco de dados
    $sql = "INSERT INTO usuario (nome, email, senha, tipo) VALUES (:nome, :email, MD5(:senha), :tipo)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':senha', $senha);
    $stmt->bindParam(':tipo', $tipo);
    $stmt->execute();

    $sucesso = "Cadastro realizado com sucesso! Você pode fazer login agora.";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LuzVox</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .body, html{
            margin-top: 100px;
        }

        .logo {
            margin-top: 50px;
            width: 300px; 
        }
    </style>
</head>
<body>
    
    <div class="container">
        <div class="row justify-content-center">
        <img src="Logo_Login.png" alt="Logo do Site" class="logo">
            <div class="col-md-5">
                <h2 class="mt-5">Login</h2>
                <?php if (isset($erro)): ?>
                    <div class="alert alert-danger"><?= $erro ?></div>
                <?php elseif (isset($sucesso)): ?>
                    <div class="alert alert-success"><?= $sucesso ?></div>
                <?php endif; ?>
                <form method="POST" action="login.php">
                    <input type="hidden" name="action" value="login">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="senha">Senha:</label>
                        <input type="password" name="senha" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Entrar</button>
                    <button type="button" class="btn btn-link" data-toggle="modal" data-target="#registerModal">Inscrever-se</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Inscrição -->
    <div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="login.php">
                    <input type="hidden" name="action" value="register">
                    <div class="modal-header">
                        <h5 class="modal-title" id="registerModalLabel">Inscrever-se</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nome">Nome:</label>
                            <input type="text" class="form-control" name="nome" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="senha">Senha:</label>
                            <input type="password" class="form-control" name="senha" required>
                        </div>
                        <div class="form-group">
                            <label for="tipo">Tipo de Usuário:</label>
                            <select class="form-control" name="tipo" required>
                                <option value="usuario">Usuário</option>
                                <option value="empresa">Empresa</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Inscrever-se</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
