<?php
session_start();
require 'db_connection.php';

// Verificar se o usuário é do tipo empresa ou admin
if (!isset($_SESSION['usuario_tipo']) || !in_array($_SESSION['usuario_tipo'], ['empresa', 'admin'])) {
    header('Location: login.php');
    exit;
}

// Filtrar ocorrências
$filterStatus = $_GET['status'] ?? '';
$filterData = $_GET['data'] ?? '';

$filterStatus = $_GET['status'] ?? '';
$filterData = $_GET['data'] ?? '';

$sql = "SELECT * FROM ocorrencia WHERE 1=1";
if ($filterStatus) {
    $sql .= " AND status = :status";
}

if ($filterData) {
    $sql .= " AND DATEDIFF(NOW(), data) ";

    // Condições baseadas no intervalo de dias selecionado
    if ($filterData === '0-10') {
        $sql .= "BETWEEN 0 AND 10";
    } elseif ($filterData === '11-20') {
        $sql .= "BETWEEN 11 AND 20";
    } elseif ($filterData === '21-30') {
        $sql .= "BETWEEN 21 AND 30";
    } elseif ($filterData === '30+') {
        $sql .= "> 30";
    }
}

$stmt = $pdo->prepare($sql);

if ($filterStatus) {
    $stmt->bindParam(':status', $filterStatus);
}
$stmt->execute();
$ocorrencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contar total de ocorrências por status
$sqlCount = "SELECT status, COUNT(*) as total FROM ocorrencia GROUP BY status";
$stmtCount = $pdo->query($sqlCount);
$statusCounts = $stmtCount->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Ocorrências - LuzVox</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
                <li class="nav-item active">
                    <a class="nav-link" href="ocorrencia.php">Ocorrências</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="usuario.php">Usuários</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="relatorio.php">Relatórios</a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container mt-5">
        <h2>Relatório de Ocorrências</h2>
        
        <!-- Botão de Download -->
        <div class="mb-3">
            <a href="export_pdf.php?status=<?= $filterStatus ?>" class="btn btn-danger">Baixar PDF</a>
        </div>
        <!-- Filtro do relaatório (status e data) -->
        <form method="GET" class="form-inline mb-4">
            <label for="status" class="mr-2">Filtrar por Status:</label>
            <select name="status" class="form-control mr-2" id="status">
                <option value="">Todos</option>
                <option value="Registrada" <?= $filterStatus === 'Registrada' ? 'selected' : '' ?>>Registrada</option>
                <option value="Em Andamento" <?= $filterStatus === 'Em Andamento' ? 'selected' : '' ?>>Em Andamento</option>
                <option value="Finalizada" <?= $filterStatus === 'Finalizada' ? 'selected' : '' ?>>Finalizada</option>
            </select>

            <label for="data" class="mr-2">Filtrar por Data:</label>
            <select name="data" class="form-control mr-2" id="data">
                <option value="">Todos</option>
                <option value="0-10" <?= $filterData === '0-10' ? 'selected' : '' ?>>0 a 10 dias</option>
                <option value="11-20" <?= $filterData === '11-20' ? 'selected' : '' ?>>11 a 20 dias</option>
                <option value="21-30" <?= $filterData === '21-30' ? 'selected' : '' ?>>21 a 30 dias</option>
                <option value="30+" <?= $filterData === '30+' ? 'selected' : '' ?>>30 dias ou mais</option>
            </select>

            <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
        </form>

        <!-- Resumo das Ocorrências -->
        <div class="mb-4">
            <h5>Resumo de Ocorrências:</h5>
            <ul>
                <?php foreach ($statusCounts as $statusCount): ?>
                    <li><?= htmlspecialchars($statusCount['status']) ?>: <?= $statusCount['total'] ?> ocorrências</li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Tabela de Ocorrências -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Descrição</th>
                    <th>Status</th>
                    <th>Data</th>
                    <th>Usuário</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($ocorrencias): ?>
                    <?php foreach ($ocorrencias as $ocorrencia): ?>
                        <tr>
                            <td><?= htmlspecialchars($ocorrencia['id']) ?></td>
                            <td><?= htmlspecialchars($ocorrencia['descricao']) ?></td>
                            <td><?= htmlspecialchars($ocorrencia['status']) ?></td>
                            <td><?= htmlspecialchars($ocorrencia['data']) ?></td>
                            <td><?= htmlspecialchars($ocorrencia['usuario']) ?></td>
                            <td><?= htmlspecialchars($ocorrencia['lat']) ?></td>
                            <td><?= htmlspecialchars($ocorrencia['lng']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Nenhuma ocorrência encontrada.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
