<?php
require 'db_connection.php';
require 'Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'ID')
            ->setCellValue('B1', 'Descrição')
            ->setCellValue('C1', 'Status');

// Adicionar dados às células
$row = 2;
foreach ($ocorrencias as $ocorrencia) {
    $objPHPExcel->getActiveSheet()
        ->setCellValue("A$row", $ocorrencia['id'])
        ->setCellValue("B$row", $ocorrencia['descricao'])
        ->setCellValue("C$row", $ocorrencia['status']);
    $row++;
}

// Definir cabeçalho de download e saída do arquivo
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="relatorio_ocorrencias.xlsx"');
header('Cache-Control: max-age=0');
$writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$writer->save('php://output');

/*require 'vendor/autoload.php'; // Carregar PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

// Criar planilha
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Relatório de Ocorrências");

// Cabeçalhos
$sheet->setCellValue('A1', 'ID');
$sheet->setCellValue('B1', 'Descrição');
$sheet->setCellValue('C1', 'Status');
$sheet->setCellValue('D1', 'Data');
$sheet->setCellValue('E1', 'Usuário');
$sheet->setCellValue('F1', 'Latitude');
$sheet->setCellValue('G1', 'Longitude');

// Preencher dados
$row = 2;
foreach ($ocorrencias as $ocorrencia) {
    $sheet->setCellValue("A$row", $ocorrencia['id']);
    $sheet->setCellValue("B$row", $ocorrencia['descricao']);
    $sheet->setCellValue("C$row", $ocorrencia['status']);
    $sheet->setCellValue("D$row", $ocorrencia['data']);
    $sheet->setCellValue("E$row", $ocorrencia['usuario']);
    $sheet->setCellValue("F$row", $ocorrencia['lat']);
    $sheet->setCellValue("G$row", $ocorrencia['lng']);
    $row++;
}

// Gerar arquivo Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Relatorio_Ocorrencias.xlsx"');
$writer->save("php://output");
exit;
?>
*/