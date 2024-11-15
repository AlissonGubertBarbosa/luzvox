<?php
require_once "../ocorrencia.php";

$_POST['action'] = 'add';
$_POST['descricao'] = 'Teste Manual';
$_POST['status'] = 'Registrada';
$_POST['lat'] = -22.655874;
$_POST['lng'] = -53.857403;

session_start();
$_SESSION['usuario_id'] = 12; // Simular o id do usuário logado
$_SESSION['usuario_tipo'] = 'usuario12'; // Tipo do usuário

require 'ocorrencia.php';
echo "Teste concluído!";
?>