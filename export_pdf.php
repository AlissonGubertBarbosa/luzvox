<?php
require 'db_connection.php';
require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;

$filterStatus = $_GET['status'] ?? '';
$sql = "SELECT * FROM ocorrencia";
if ($filterStatus) {
    $sql .= " WHERE status = :status";
}
$stmt = $pdo->prepare($sql);

if ($filterStatus) {
    $stmt->bindParam(':status', $filterStatus);
}

$stmt->execute();
$ocorrencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Configurar o conteúdo HTML para PDF


$dompdf = new Dompdf();
$html = "<h1>Relatório de Ocorrências</h1>";
$html .= "<table border='1' width='100%' style='border-collapse: collapse;'>
            <tr>
                <th>ID</th><th>Descrição</th><th>Status</th><th>Data</th><th>Usuário</th><th>Latitude</th><th>Longitude</th>
            </tr>";
foreach ($ocorrencias as $ocorrencia) {
    $html .= "<tr>
                <td>{$ocorrencia['id']}</td>
                <td>{$ocorrencia['descricao']}</td>
                <td>{$ocorrencia['status']}</td>
                <td>{$ocorrencia['data']}</td>
                <td>{$ocorrencia['usuario']}</td>
                <td>{$ocorrencia['lat']}</td>
                <td>{$ocorrencia['lng']}</td>
            </tr>";
}
$html .= "</table>";
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("relatorio_ocorrencias.pdf");
