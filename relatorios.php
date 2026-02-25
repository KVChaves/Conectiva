<?php
header('Content-Type: text/html; charset=utf-8');
require_once 'config/database.php';
require_once 'config/constants.php';

$currentPage = 'relatorios';
$pageTitle = 'Relatórios - Conectiva';

$conn = getConnection();

// Estatísticas gerais
$stats = [
    'total' => $conn->query("SELECT COUNT(*) FROM conectiva")->fetchColumn(),
    'por_tipo' => $conn->query("SELECT tipo, COUNT(*) as total FROM conectiva GROUP BY tipo")->fetchAll(PDO::FETCH_ASSOC),
    'por_territorio' => $conn->query("SELECT territorio, COUNT(*) as total FROM conectiva WHERE territorio IS NOT NULL GROUP BY territorio ORDER BY total DESC")->fetchAll(PDO::FETCH_ASSOC),
    'chamados_abertos' => $conn->query("SELECT COUNT(*) FROM conectiva_helpdesk WHERE status = 'Aberto'")->fetchColumn(),
    'chamados_total' => $conn->query("SELECT COUNT(*) FROM conectiva_helpdesk")->fetchColumn()
];

// Estatísticas de chamados
$chamados_stats = [
    'por_tipo' => $conn->query("SELECT tipo_problema, COUNT(*) as total FROM conectiva_helpdesk GROUP BY tipo_problema ORDER BY total DESC")->fetchAll(PDO::FETCH_ASSOC),
    'por_territorio' => $conn->query("SELECT territorio, COUNT(*) as total FROM conectiva_helpdesk WHERE territorio IS NOT NULL AND territorio != '' GROUP BY territorio ORDER BY total DESC")->fetchAll(PDO::FETCH_ASSOC)
];

ob_start();
?>

<div class="page-header">
    <h1><i class="bi bi-bar-chart"></i> Relatórios e Dashboards</h1>
</div>

<!-- Cards de Estatísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2 opacity-75">Total de Pontos</h6>
                        <h2 class="card-title mb-0"><?php echo $stats['total']; ?></h2>
                    </div>
                    <i class="bi bi-geo-alt" style="font-size: 3rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2 opacity-75">Territórios</h6>
                        <h2 class="card-title mb-0"><?php echo count($stats['por_territorio']); ?></h2>
                    </div>
                    <i class="bi bi-map" style="font-size: 3rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2 opacity-75">Chamados Abertos</h6>
                        <h2 class="card-title mb-0"><?php echo $stats['chamados_abertos']; ?></h2>
                    </div>
                    <i class="bi bi-exclamation-triangle" style="font-size: 3rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2 opacity-75">Total Chamados</h6>
                        <h2 class="card-title mb-0"><?php echo $stats['chamados_total']; ?></h2>
                    </div>
                    <i class="bi bi-headset" style="font-size: 3rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos de Pontos -->
<div class="row">
    <div class="col-12">
        <h4 class="mb-3"><i class="bi bi-wifi"></i> Análise de Pontos de Internet</h4>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pie-chart"></i> Distribuição por Tipo de Conexão
            </div>
            <div class="card-body">
                <canvas id="chartTipo" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-bar-chart"></i> Pontos por Território
            </div>
            <div class="card-body">
                <canvas id="chartTerritorio" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos de Chamados -->
<div class="row mt-4">
    <div class="col-12">
        <h4 class="mb-3"><i class="bi bi-headset"></i> Análise de Chamados</h4>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pie-chart-fill"></i> Chamados por Tipo de Problema
            </div>
            <div class="card-body">
                <canvas id="chartChamadosTipo" height="300"></canvas>
                <?php if (empty($chamados_stats['por_tipo'])): ?>
                <div class="text-center text-muted py-4">
                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                    <p class="mt-2">Nenhum chamado registrado</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-bar-chart-fill"></i> Top 10 Territórios com Mais Chamados
            </div>
            <div class="card-body">
                <canvas id="chartChamadosTerritorio" height="300"></canvas>
                <?php if (empty($chamados_stats['por_territorio'])): ?>
                <div class="text-center text-muted py-4">
                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                    <p class="mt-2">Nenhum chamado registrado</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Tabelas de Dados -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-table"></i> Detalhamento por Tipo
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th class="text-end">Quantidade</th>
                            <th class="text-end">Percentual</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats['por_tipo'] as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['tipo'] ?? 'Não definido'); ?></td>
                            <td class="text-end"><?php echo $item['total']; ?></td>
                            <td class="text-end">
                                <?php echo round(($item['total'] / $stats['total']) * 100, 1); ?>%
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-table"></i> Top 10 Territórios
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Território</th>
                            <th class="text-end">Quantidade</th>
                            <th class="text-end">Percentual</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($stats['por_territorio'], 0, 10) as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['territorio']); ?></td>
                            <td class="text-end"><?php echo $item['total']; ?></td>
                            <td class="text-end">
                                <?php echo round(($item['total'] / $stats['total']) * 100, 1); ?>%
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Exportação -->
<div class="card mt-4">
    <div class="card-header">
        <i class="bi bi-download"></i> Exportar Relatórios
    </div>
    <div class="card-body">
        <p>Baixe os dados em formato Excel para análise detalhada:</p>
        <div class="btn-group">
            <a href="exportar.php?tipo=pontos" class="btn btn-success">
                <i class="bi bi-file-earmark-excel"></i> Exportar Pontos
            </a>
            <a href="exportar.php?tipo=chamados" class="btn btn-success">
                <i class="bi bi-file-earmark-excel"></i> Exportar Chamados
            </a>
            <a href="exportar.php?tipo=completo" class="btn btn-success">
                <i class="bi bi-file-earmark-excel"></i> Relatório Completo
            </a>
        </div>
    </div>
</div>

<script>
// Dados para os gráficos
const dadosTipo = <?php echo json_encode($stats['por_tipo']); ?>;
const dadosTerritorio = <?php echo json_encode($stats['por_territorio']); ?>;
const dadosChamadosTipo = <?php echo json_encode($chamados_stats['por_tipo']); ?>;
const dadosChamadosTerritorio = <?php echo json_encode($chamados_stats['por_territorio']); ?>;

// Cores para os gráficos (usar constantes)
const cores = <?php echo json_encode(CORES_TIPO); ?>;

// Esperar o DOM carregar completamente
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart === 'undefined') {
        console.error('Chart.js não carregou');
        return;
    }
    
    initCharts();
});

function initCharts() {
    try {
        // Gráfico de Pizza - Tipo de Conexão
        const ctxTipo = document.getElementById('chartTipo');
        if (ctxTipo) {
            new Chart(ctxTipo.getContext('2d'), {
                type: 'pie',
                data: {
                    labels: dadosTipo.map(d => d.tipo || 'Não definido'),
                    datasets: [{
                        data: dadosTipo.map(d => d.total),
                        backgroundColor: dadosTipo.map(d => cores[d.tipo] || '#6c757d'),
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return label + ': ' + value + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }

        // Gráfico de Barras - Território (Top 10)
        const top10Territorios = dadosTerritorio.slice(0, 10);
        const ctxTerritorio = document.getElementById('chartTerritorio');
        if (ctxTerritorio) {
            new Chart(ctxTerritorio.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: top10Territorios.map(d => {
                        // Pegar apenas a primeira parte do território (antes do " - ")
                        const parts = d.territorio.split(' - ');
                        return parts[0];
                    }),
                    datasets: [{
                        label: 'Quantidade de Pontos',
                        data: top10Territorios.map(d => d.total),
                        backgroundColor: '#3498db',
                        borderColor: '#2980b9',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        // Gráfico de Pizza - Chamados por Tipo
        if (dadosChamadosTipo.length > 0) {
            const ctxChamadosTipo = document.getElementById('chartChamadosTipo');
            if (ctxChamadosTipo) {
                new Chart(ctxChamadosTipo.getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels: dadosChamadosTipo.map(d => d.tipo_problema || 'Não especificado'),
                        datasets: [{
                            data: dadosChamadosTipo.map(d => d.total),
                            backgroundColor: [
                                '#FF6384',
                                '#36A2EB',
                                '#FFCE56',
                                '#4BC0C0',
                                '#9966FF',
                                '#FF9F40',
                                '#FF6384',
                                '#C9CBCF'
                            ],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return label + ': ' + value + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }

        // Gráfico de Barras - Chamados por Território (Top 10)
        if (dadosChamadosTerritorio.length > 0) {
            const top10ChamadosTerritorio = dadosChamadosTerritorio.slice(0, 10);
            const ctxChamadosTerritorio = document.getElementById('chartChamadosTerritorio');
            if (ctxChamadosTerritorio) {
                new Chart(ctxChamadosTerritorio.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: top10ChamadosTerritorio.map(d => {
                            // Pegar apenas a primeira parte do território
                            const parts = (d.territorio || 'Não especificado').split(' - ');
                            return parts[0];
                        }),
                        datasets: [{
                            label: 'Quantidade de Chamados',
                            data: top10ChamadosTerritorio.map(d => d.total),
                            backgroundColor: '#e74c3c',
                            borderColor: '#c0392b',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            },
                            x: {
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Chamados: ' + context.parsed.y;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }
    } catch(error) {
        console.error('Erro ao criar gráficos:', error);
    }
}
</script>

<?php
$content = ob_get_clean();
include 'templates/layout.php';
?>