<?php
require 'db_connection.php';

// Função para adicionar um novo marker
function adicionarMarker($name, $address, $lat, $lng, $type, $cidade) {
    global $pdo;
    $sql = "INSERT INTO marker (name, address, lat, lng, type, cidade) VALUES (:name, :address, :lat, :lng, :type, :cidade)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':lat', $lat);
    $stmt->bindParam(':lng', $lng);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':cidade', $cidade);
    $stmt->execute();
}

// Função para buscar todos os markers
function buscarMarkers() {
    global $pdo;
    $sql = "SELECT * FROM marker";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Função para buscar um marker específico
function buscarMarkerPorId($id) {
    global $pdo;
    $sql = "SELECT * FROM marker WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Função para atualizar um marker
function atualizarMarker($id, $name, $address, $lat, $lng, $type, $cidade) {
    global $pdo;
    $sql = "UPDATE marker SET name = :name, address = :address, lat = :lat, lng = :lng, type = :type, cidade = :cidade WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':lat', $lat);
    $stmt->bindParam(':lng', $lng);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':cidade', $cidade);
    $stmt->execute();
}

// Função para excluir um marker
function excluirMarker($id) {
    global $pdo;
    $sql = "DELETE FROM marker WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
}
?>
