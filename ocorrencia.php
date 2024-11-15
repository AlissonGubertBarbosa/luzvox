<?php
session_start();
require 'db_connection.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_tipo'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$tipo_usuario = $_SESSION['usuario_tipo'];

// Processar o formulário para adicionar, alterar, finalizar uma ocorrência ou avaliar uma solução
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $descricao = $_POST['descricao'] ?? null;
    $status = $_POST['status'] ?? null;
    $lat = $_POST['lat'] ?? null;
    $lng = $_POST['lng'] ?? null;
    $id = $_POST['id'] ?? null;
    $descricao_solucao = $_POST['descricao_solucao'] ?? null;
    $avaliacao = $_POST['avaliacao'] ?? null;

    if ($_POST['action'] === 'add' && ($tipo_usuario === 'usuario' || $tipo_usuario === 'admin')) {
        $sql = "INSERT INTO ocorrencia (descricao, status, data, usuario, lat, lng) VALUES (:descricao, :status, NOW(), :usuario, :lat, :lng)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':usuario', $usuario_id);
        $stmt->bindParam(':lat', $lat);
        $stmt->bindParam(':lng', $lng);
        $stmt->execute();
    }

    if ($_POST['action'] === 'alterar_status' && $tipo_usuario === 'empresa') {
        $sql = "UPDATE ocorrencia SET status = :status WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    if ($_POST['action'] === 'finalizar_ocorrencia' && $tipo_usuario === 'empresa') {
        $sql = "UPDATE ocorrencia SET status = 'Finalizada', descricao_solucao = :descricao_solucao WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':descricao_solucao', $descricao_solucao);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    if ($_POST['action'] === 'avaliar' && $tipo_usuario === 'usuario') {
        $sql = "UPDATE ocorrencia SET avaliacao = :avaliacao WHERE id = :id AND usuario = :usuario";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':avaliacao', $avaliacao);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':usuario', $usuario_id);
        $stmt->execute();
    }

    header('Location: ocorrencia.php');
    exit;
}

// Processar a exclusão de ocorrência
if (isset($_GET['delete_id']) && ($tipo_usuario === 'usuario' || $tipo_usuario === 'admin')) {
    $id = $_GET['delete_id'];
    $sql = "DELETE FROM ocorrencia WHERE id = :id AND (usuario = :usuario OR :tipo_usuario = 'admin')";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':usuario', $usuario_id);
    $stmt->bindParam(':tipo_usuario', $tipo_usuario);
    $stmt->execute();

    header('Location: ocorrencia.php');
    exit;
}

// Buscar todas as ocorrências para exibição
if ($tipo_usuario === 'usuario') {
    $sql = "SELECT o.*, u.nome as usuario_nome FROM ocorrencia o LEFT JOIN usuario u ON o.usuario = u.id WHERE o.usuario = :usuario";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':usuario', $usuario_id);
} else {
    $sql = "SELECT o.*, u.nome as usuario_nome FROM ocorrencia o LEFT JOIN usuario u ON o.usuario = u.id";
    $stmt = $pdo->query($sql);
}
$stmt->execute();
$ocorrencias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ocorrências - LuzVox</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container {
            padding-top: 20px;
        }
        #map, #modalMap {
            height: 400px;
            width: 100%;
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
                <li class="nav-item active">
                    <a class="nav-link" href="ocorrencia.php">Ocorrências</a>
                </li>
                <?php if ($tipo_usuario === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="usuario.php">Usuários</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="relatorio.php">Relatório</a>
                    </li>
                <?php endif; ?>
                
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <h2 class="mt-4">Gerenciamento de Ocorrências</h2>

        <?php if ($tipo_usuario === 'usuario' || $tipo_usuario === 'admin'): ?>
            <button class="btn btn-success mb-3" data-toggle="modal" data-target="#addOcorrenciaModal">Adicionar Nova Ocorrência</button>
        <?php endif; ?>

        <hr>

        <h4>Lista de Ocorrências</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Descrição</th>
                    <th>Status</th>
                    <th>Data</th>
                    <th>Usuário</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ocorrencias as $ocorrencia): ?>
                    <tr>
                        <td><?= $ocorrencia['id'] ?></td>
                        <td><?= $ocorrencia['descricao'] ?></td>
                        <td><?= $ocorrencia['status'] ?></td>
                        <td><?= $ocorrencia['data'] ?></td>
                        <td><?= $ocorrencia['usuario_nome'] ?></td>
                        <td>
                        <?php if ($tipo_usuario === 'empresa' || $tipo_usuario === 'admin'): ?>
                            <?php if ($ocorrencia['status'] === 'Registrada'): ?>
                                <form method="POST" action="ocorrencia.php" style="display:inline;">
                                    <input type="hidden" name="action" value="alterar_status">
                                    <input type="hidden" name="id" value="<?= $ocorrencia['id'] ?>">
                                    <input type="hidden" name="status" value="Em Andamento">
                                    <button type="submit" class="btn btn-info btn-sm">Iniciar Andamento</button>
                                </form>
                            <?php endif; ?>

                            <?php if ($ocorrencia['status'] === 'Em Andamento' || $tipo_usuario === 'admin'): ?>
                                <form method="POST" action="ocorrencia.php" style="display:inline;">
                                    <input type="hidden" name="action" value="alterar_status">
                                    <input type="hidden" name="id" value="<?= $ocorrencia['id'] ?>">
                                    <input type="hidden" name="status" value="Registrada">
                                    <button type="submit" class="btn btn-warning btn-sm">Voltar para Aberto</button>
                                </form>
                                <button class="btn btn-success btn-sm" onclick="abrirModalSolucao(<?= $ocorrencia['id'] ?>)">Finalizar Ocorrência</button>
                            <?php endif; ?>
                            <?php endif; ?>

                            <?php if ($tipo_usuario === 'usuario' && $ocorrencia['status'] === 'Finalizada' && $ocorrencia['avaliacao'] === null): ?>
                                <button class="btn btn-primary btn-sm" onclick="abrirModalAvaliacao(<?= $ocorrencia['id'] ?>)">Avaliar</button>
                            
                            <?php elseif ($tipo_usuario === 'usuario' && $ocorrencia['status'] === 'Finalizada' && $ocorrencia['avaliacao'] !== null): ?>
                                <span class="badge badge-info">Avaliação: <?= $ocorrencia['avaliacao'] ?>/10</span>
                            <?php endif; ?>
                                                        
                            <?php if ($tipo_usuario === 'admin'): ?>
                                <a href="ocorrencia.php?delete_id=<?= $ocorrencia['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir esta ocorrência?')">Excluir</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div id="map"></div>
    </div>

    <!-- Modal para adicionar ocorrência -->
    <div class="modal" id="addOcorrenciaModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="ocorrencia.php">
                    <div class="modal-header">
                        <h5 class="modal-title">Adicionar Ocorrência</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="lat" id="addLat">
                        <input type="hidden" name="lng" id="addLng">
                        <div class="form-group">
                            <label for="descricao">Descrição:</label>
                            <input type="text" class="form-control" name="descricao" required>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="status" value="Registrada">
                        </div>
                        <div id="modalMap" style="height: 400px;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Adicionar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para finalizar ocorrência -->
    <div class="modal" id="finalizarModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="ocorrencia.php">
                    <div class="modal-header">
                        <h5 class="modal-title">Finalizar Ocorrência</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="finalizar_ocorrencia">
                        <input type="hidden" name="id" id="finalizarOcorrenciaId">
                        <div class="form-group">
                            <label for="descricaoSolucao">Descrição da Solução:</label>
                            <textarea class="form-control" name="descricao_solucao" id="descricaoSolucao" required></textarea>
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

    <!-- Modal para avaliar ocorrência -->
    <div class="modal" id="avaliarModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="ocorrencia.php">
                    <div class="modal-header">
                        <h5 class="modal-title">Avaliar Ocorrência</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="avaliar">
                        <input type="hidden" name="id" id="avaliarOcorrenciaId">
                        <div class="form-group">
                            <label for="avaliacao">Nota de Satisfação:</label>
                            <div id="avaliacao">
                                <?php for ($i = 0; $i <= 10; $i++): ?>
                                    <label class="radio-inline">
                                        <input type="radio" name="avaliacao" value="<?= $i ?>    " required> <?= $i ?>
                                    </label>
                                <?php endfor; ?>
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

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC7YrSBQ22t46cVdnWf5f7j83Tuvr4Rznw&callback=initMap"></script>

    <script>
        var map;
        var modalMap;
        var marker = null;

        function initMap() {
            // Inicializa o mapa principal
            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: -22.655874, lng: -53.857403 }, // Coordenadas iniciais padrão
                zoom: 15,
                mapTypeId: google.maps.MapTypeId.SATELLITE
            });
            
            // Inicializa o mapa no modal para adicionar ocorrências
            modalMap = new google.maps.Map(document.getElementById('modalMap'), {
                center: { lat: -22.655874, lng: -53.857403 }, // Coordenadas iniciais padrão
                zoom: 15,
                mapTypeId: google.maps.MapTypeId.SATELLITE
            });

            // Função para obter a localização pelo IP e centralizar o mapa
            fetchLocationByIP();
        }

        function fetchLocationByIP() {
            // Chamada para a Google Geolocation API
            fetch('https://www.googleapis.com/geolocation/v1/geolocate?key=AIzaSyC7YrSBQ22t46cVdnWf5f7j83Tuvr4Rznw', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                // Verifica se a API retornou uma localização
                if (data && data.location) {
                    const lat = data.location.lat;
                    const lng = data.location.lng;

                    // Centraliza o mapa na localização obtida
                    map.setCenter({ lat, lng });
                    modalMap.setCenter({ lat, lng });

                    // Adiciona um marcador na localização obtida
                    new google.maps.Marker({
                        position: { lat, lng },
                        map: map,
                        title: 'Sua localização'
                    });
                } else {
                    console.error('Erro ao obter localização pelo IP.');
                }
            })
            .catch(error => console.error('Erro ao chamar a Geolocation API:', error));

            modalMap.addListener('click', function(event) {
                var latLng = event.latLng;

                if (marker) {
                    marker.setPosition(latLng);
                } else {
                    marker = new google.maps.Marker({
                        position: latLng,
                        map: modalMap,
                        draggable: true
                    });
                }

                document.getElementById('addLat').value = latLng.lat();
                document.getElementById('addLng').value = latLng.lng();

                marker.addListener('dragend', function() {
                    var newPosition = marker.getPosition();
                    document.getElementById('addLat').value = newPosition.lat();
                    document.getElementById('addLng').value = newPosition.lng();
                });
            });

            // Adicionar os marcadores das ocorrências no mapa principal
            <?php foreach ($ocorrencias as $ocorrencia): ?>
                new google.maps.Marker({
                    position: { lat: <?= $ocorrencia['lat'] ?>, lng: <?= $ocorrencia['lng'] ?> },
                    map: map,
                    title: '<?= $ocorrencia['descricao'] ?>'
                });
            <?php endforeach; ?>
        }

        function abrirModalSolucao(id) {
            document.getElementById('finalizarOcorrenciaId').value = id;
            $('#finalizarModal').modal('show');
        }

        function abrirModalAvaliacao(id) {
            document.getElementById('avaliarOcorrenciaId').value = id;
            $('#avaliarModal').modal('show');
        }

        function abrirModalSolucao(id) {
            document.getElementById('finalizarOcorrenciaId').value = id;
            $('#finalizarModal').modal('show');
        }
    </script>
</body>
</html>
