<?php
session_start();
require 'db_connection.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Processar o formulário para adicionar um novo usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $senha = md5($_POST['senha']);  // Hash da senha para segurança
    $tipo = $_POST['tipo'];

    // Inserir o novo usuário no banco de dados
    $sql = "INSERT INTO usuario (nome, telefone, email, senha, tipo) VALUES (:nome, :telefone, :email, :senha, :tipo)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':senha', $senha);
    $stmt->bindParam(':tipo', $tipo);
    $stmt->execute();

    header('Location: usuario.php');
    exit;
}

// Processar a exclusão de usuário
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $sql = "DELETE FROM usuario WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    header('Location: usuario.php');
    exit;
}

// Processar o formulário de edição de usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $tipo = $_POST['tipo'];

    $sql = "UPDATE usuario SET nome = :nome, telefone = :telefone, email = :email, tipo = :tipo WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':tipo', $tipo);
    $stmt->execute();

    header('Location: usuario.php');
    exit;
}

// Buscar todos os usuários para exibição
$sql = "SELECT * FROM usuario";
$stmt = $pdo->query($sql);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários - LuzVox</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container {
            padding-top: 20px;
        }
        .form-inline {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="index.php"><img src="Logo.png" alt="Logo"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Início</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="ocorrencia.php">Ocorrências</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="usuario.php">Usuários</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="relatorio.php">Relatório</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <h2 class="mt-4">Gerenciamento de Usuários</h2>

        <!-- Formulário para adicionar um novo usuário -->
        <h4>Adicionar Novo Usuário</h4>
        <form method="POST" action="usuario.php" class="form-inline">
            <input type="hidden" name="action" value="add">
            <div class="form-group mx-sm-3 mb-2">
                <input type="text" class="form-control" name="nome" placeholder="Nome" required>
            </div>
            <div class="form-group mx-sm-3 mb-2">
                <input type="text" class="form-control" name="telefone" placeholder="Telefone" required>
            </div>
            <div class="form-group mx-sm-3 mb-2">
                <input type="email" class="form-control" name="email" placeholder="Email" required>
            </div>
            <div class="form-group mx-sm-3 mb-2">
                <input type="password" class="form-control" name="senha" placeholder="Senha" required>
            </div>
            <div class="form-group mx-sm-3 mb-2">
                <select name="tipo" class="form-control" required>
                    <option value="usuario">Usuário</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success mb-2">Adicionar</button>
        </form>

        <hr>

        <!-- Tabela de usuários -->
        <h4>Lista de Usuários</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Telefone</th>
                    <th>Email</th>
                    <th>Tipo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= $usuario['id'] ?></td>
                        <td><?= $usuario['nome'] ?></td>
                        <td><?= $usuario['telefone'] ?></td>
                        <td><?= $usuario['email'] ?></td>
                        <td><?= $usuario['tipo'] ?></td>
                        <td>
                            <!-- Botão para editar (abre o modal de edição) -->
                            <button class="btn btn-primary btn-sm" onclick="editarUsuario(<?= $usuario['id'] ?>, '<?= $usuario['nome'] ?>', '<?= $usuario['telefone'] ?>', '<?= $usuario['email'] ?>', '<?= $usuario['tipo'] ?>')">Editar</button>

                            <!-- Link para excluir usuário -->
                            <a href="usuario.php?delete_id=<?= $usuario['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este usuário?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal de Edição de Usuário -->
    <div class="modal" id="editUserModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="usuarios.php">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Usuário</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="editUserId">
                        <div class="form-group">
                            <label for="editNome">Nome:</label>
                            <input type="text" class="form-control" name="nome" id="editNome" required>
                        </div>
                        <div class="form-group">
                            <label for="editTelefone">Telefone:</label>
                            <input type="text" class="form-control" name="telefone" id="editTelefone" required>
                        </div>
                        <div class="form-group">
                            <label for="editEmail">Email:</label>
                            <input type="email" class="form-control" name="email" id="editEmail" required>
                        </div>
                        <div class="form-group">
                            <label for="editTipo">Tipo:</label>
                            <select name="tipo" class="form-control" id="editTipo" required>
                                <option value="usuario">Usuário</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Salvar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts para Bootstrap e jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // Função para abrir o modal de edição com os dados do usuário
        function editarUsuario(id, nome, telefone, email, tipo) {
            document.getElementById('editUserId').value = id;
            document.getElementById('editNome').value = nome;
            document.getElementById('editTelefone').value = telefone;
            document.getElementById('editEmail').value = email;
            document.getElementById('editTipo').value = tipo;
            $('#editUserModal').modal('show');  // Abrir o modal
        }
    </script>
</body>
</html>
