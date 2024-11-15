<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LuzVox</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <!-- Importar o Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        .container {
            padding-top: 50px;
        }

        .jumbotron {
            padding: 30px;
            text-align: center;
        }

        .btn-custom {
            margin: 10px;
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
                <li class="nav-item active">
                    <a class="nav-link" href="#">Início</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="ocorrencia.php">Ocorrências</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="usuario.php">Usuários</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Login</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="jumbotron">
            <h1 class="display-4">Bem-vindo ao Sistema de Gestão!</h1>
            <p class="lead">Clique nas opções abaixo para acessar os módulos do sistema:</p>
            <hr class="my-4">
            <a class="btn btn-primary btn-lg btn-custom" href="ocorrencia.php" role="button">Gerenciar Ocorrências</a>
            <a class="btn btn-info btn-lg btn-custom" href="usuario.php" role="button">Gerenciar Usuários</a>
        </div>
    </div>

    <!-- Scripts do Bootstrap e jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
