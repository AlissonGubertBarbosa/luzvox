<?php
require 'db_connection.php';
require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Configurar opções do Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

// Capturar informações adicionais
session_start();
$autor = $_SESSION['usuario_nome'] ?? 'Desconhecido';
$dataHoraEmissao = date('d/m/Y H:i:s');
$filterStatus = $_GET['status'] ?? 'Todos';

// Consulta ao banco com filtros
$sql = "SELECT * FROM ocorrencia";
if ($filterStatus || $filterStatus == "Todos") {
    $sql .= " WHERE status = :status";
}
$stmt = $pdo->prepare($sql);

if ($filterStatus || $filterStatus == "Todos") {
    $stmt->bindParam(':status', $filterStatus);
}

$stmt->execute();
$ocorrencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular o total de ocorrências e páginas
$totalOcorrencias = count($ocorrencias);
$ocorrenciasPorPagina = 20; // Número arbitrário de registros por página
$totalPaginas = ceil($totalOcorrencias / $ocorrenciasPorPagina);

// Configurar o conteúdo HTML para PDF
$html = "<h1> LUZVOX </h1>";
$html .= "<h3 style='text-align: center;'>Relatório de Ocorrências</h3>";
$html .= "<hr><hr>";
$html .= "<h4>Dados de emissão do Relatório</h4>";
$html .= "<p style='text-align: center';><strong>Autor:</strong> {$autor}  <strong>Data de Emissão:</strong> {$dataHoraEmissao}</p>";
$html .= "<p style='text-align: center';><strong>Filtro Utilizado:</strong> Status {$filterStatus}  <strong>Total de Ocorrências:</strong> {$totalOcorrencias}    <strong>Total de Páginas:</strong> {$totalPaginas}</p>";
$html .= "<hr><hr>";

$html .= "<table border='1' width='100%' style='border-collapse: collapse; margin-top: 20px;'>
            <tr>
                <th>ID</th>
                <th>Descrição</th>
                <th>Status</th>
                <th>Data</th>
                <th>Usuário</th>
                <th>Latitude</th>
                <th>Longitude</th>
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

// Adicionar espaço para assinatura
$html .= "<div style='margin-top: 50px;'>
            <p style=' text-align: center;'><strong>Assinatura:</strong></p>
            <div style='width: 500px; height: 100px; border: 1px solid #000; margin: 0 auto; text-align: center;'></div>
            <p style=' text-align: center;'>{$autor}</p>
         </div>";

// Renderizar o PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("relatorio_ocorrencias.pdf");
