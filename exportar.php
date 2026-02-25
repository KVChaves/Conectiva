<?php
header('Content-Type: text/html; charset=utf-8');
require_once 'config/database.php';

if (!isset($_GET['tipo'])) {
    die('Tipo de exportação não especificado');
}

$conn = getConnection();
$tipo = $_GET['tipo'];
$filename = '';
$data = [];

// Determinar qual relatório exportar
switch ($tipo) {
    case 'pontos':
        $filename = 'relatorio_pontos_' . date('Y-m-d') . '.csv';
        $stmt = $conn->query("SELECT * FROM conectiva ORDER BY id");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $headers = ['ID', 'Localidade', 'Territorio', 'Cidade', 'Endereco', 'Latitude', 'Longitude', 'Tipo', 'Data Instalacao', 'Observacao', 'Data Criacao'];
        $fields = ['id', 'localidade', 'territorio', 'cidade', 'endereco', 'latitude', 'longitude', 'tipo', 'data_instalacao', 'observacao', 'data_criacao'];
        break;
        
    case 'chamados':
        $filename = 'relatorio_chamados_' . date('Y-m-d') . '.csv';
        $stmt = $conn->query("
            SELECT h.id, c.localidade, c.territorio, c.cidade, h.tipo_problema, h.status, h.data_abertura, h.data_fechamento
            FROM conectiva_helpdesk h
            LEFT JOIN conectiva c ON h.ponto_id = c.id
            ORDER BY h.id DESC
        ");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $headers = ['ID', 'Localidade', 'Territorio', 'Cidade', 'Tipo Problema', 'Status', 'Data Abertura', 'Data Fechamento'];
        $fields = ['id', 'localidade', 'territorio', 'cidade', 'tipo_problema', 'status', 'data_abertura', 'data_fechamento'];
        break;
        
    case 'completo':
        $filename = 'relatorio_completo_' . date('Y-m-d') . '.csv';
        $stmt = $conn->query("
            SELECT 
                c.id,
                c.localidade,
                c.territorio,
                c.cidade,
                c.endereco,
                c.tipo,
    
                c.data_instalacao,
                COUNT(h.id) as total_chamados,
                SUM(CASE WHEN h.status = 'Aberto' THEN 1 ELSE 0 END) as chamados_abertos
            FROM conectiva c
            LEFT JOIN conectiva_helpdesk h ON c.id = h.ponto_id
            GROUP BY c.id
            ORDER BY c.id
        ");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $headers = ['ID', 'Localidade', 'Territorio', 'Cidade', 'Endereco', 'Tipo', 'Data Instalacao', 'Total Chamados', 'Chamados Abertos'];
        $fields = ['id', 'localidade', 'territorio', 'cidade', 'endereco', 'tipo', 'data_instalacao', 'total_chamados', 'chamados_abertos'];
        break;
        
    default:
        die('Tipo de exportação inválido');
}

// Configurar headers para download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// Criar output
$output = fopen('php://output', 'w');

// Escrever cabeçalho
fputcsv($output, $headers, ';');

// Escrever dados
foreach ($data as $row) {
    $line = [];
    foreach ($fields as $field) {
        $line[] = $row[$field] ?? '';
    }
    fputcsv($output, $line, ';');
}

fclose($output);
exit;
?>
