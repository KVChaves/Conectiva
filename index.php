<?php
header('Content-Type: text/html; charset=utf-8');
require_once 'config/database.php';
require_once 'config/constants.php';

$currentPage = 'mapa';
$pageTitle = 'Mapa de Pontos - Conectiva';

// Buscar todos os pontos
$conn = getConnection();
$stmt = $conn->prepare("SELECT * FROM conectiva WHERE latitude IS NOT NULL AND longitude IS NOT NULL");
$stmt->execute();
$pontos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Usar cores das constantes
$coresTipo = CORES_TIPO;

ob_start();
?>

<div class="page-header">
    <h1><i class="bi bi-map"></i> Mapa de Pontos de Internet</h1>
</div>

<!-- Barra de Pesquisa -->
<div class="card mb-3">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchInput" class="form-control" placeholder="Pesquisar por localidade ou cidade...">
                </div>
            </div>
            <div class="col-md-3">
                <select id="filterTipo" class="form-select">
                    <option value="">Todos os tipos</option>
                    <?php foreach($coresTipo as $tipo => $cor): ?>
                        <option value="<?php echo htmlspecialchars($tipo); ?>"><?php echo htmlspecialchars($tipo); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" onclick="filtrarPontos()">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
            </div>
            <div class="col-md-2">
                <button class="btn btn-secondary w-100" onclick="limparFiltros()">
                    <i class="bi bi-x-circle"></i> Limpar
                </button>
            </div>
        </div>
        <div id="resultadosCount" class="mt-2 text-muted"></div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-geo-alt"></i> Pontos na Bahia</span>
        <div>
            <?php foreach($coresTipo as $tipo => $cor): ?>
                <span class="badge" style="background-color: <?php echo $cor; ?>; margin-left: 5px;">
                    <?php echo htmlspecialchars($tipo); ?>
                </span>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="card-body p-0">
        <div id="map" style="height: 600px; width: 100%;"></div>
    </div>
</div>

<!-- Modal Detalhes do Ponto -->
<div class="modal fade" id="modalDetalhes" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-info-circle"></i> Detalhes do Ponto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalhesContent">
                <!-- Conteúdo carregado via JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-warning" onclick="editarPonto()">
                    <i class="bi bi-pencil"></i> Editar
                </button>
                <button type="button" class="btn btn-primary" onclick="abrirChamado()">
                    <i class="bi bi-headset"></i> Abrir Chamado
                </button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'templates/layout.php';
?>

<script>
// Variáveis globais
let map;
let markers = [];
let pontos = <?php echo json_encode($pontos ?? [], JSON_UNESCAPED_UNICODE); ?>;
let pontoAtual = null;
const coresTipo = <?php echo json_encode($coresTipo ?? [], JSON_UNESCAPED_UNICODE); ?>;

// Esperar o Leaflet carregar completamente
document.addEventListener('DOMContentLoaded', function() {
    if (typeof L === 'undefined') {
        console.error('Leaflet não carregou. Tentando novamente...');
        setTimeout(initMap, 1000);
    } else {
        initMap();
    }
    
    // Adicionar evento de Enter na pesquisa
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            filtrarPontos();
        }
    });
});

function initMap() {
    try {
        // Inicializar mapa centrado na Bahia
        map = L.map('map').setView([-12.5797, -41.7007], 7);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        // Adicionar todos os marcadores inicialmente
        adicionarMarcadores(pontos);
        atualizarContador(pontos.length);
        
    } catch(error) {
        console.error('Erro ao inicializar mapa:', error);
        document.getElementById('map').innerHTML = '<div class="alert alert-danger m-3">Erro ao carregar o mapa. Verifique a conexão com a internet.</div>';
    }
}

function adicionarMarcadores(pontosParaMostrar) {
    // Limpar marcadores existentes
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];
    
    if (pontosParaMostrar && pontosParaMostrar.length > 0) {
        pontosParaMostrar.forEach(ponto => {
            if (ponto.latitude && ponto.longitude) {
                const cor = coresTipo[ponto.tipo] || '#6c757d';
                
                const customIcon = L.divIcon({
                    html: `<div style="background-color: ${cor}; width: 12px; height: 12px; border-radius: 50%; border: 1px solid white; box-shadow: 0 1px 3px rgba(0,0,0,0.3);"></div>`,
                    className: 'custom-marker',
                    iconSize: [12, 12],
                    iconAnchor: [6, 6]
                });
                
                const marker = L.marker([ponto.latitude, ponto.longitude], {icon: customIcon})
                    .addTo(map)
                    .on('click', () => mostrarDetalhes(ponto.id));
                
                marker.bindTooltip(`<b>${ponto.localidade}</b><br>${ponto.cidade || 'N/A'}<br>${ponto.tipo || 'N/A'}`, {
                    permanent: false,
                    direction: 'top'
                });
                
                markers.push(marker);
            }
        });
        
        // Ajustar zoom para mostrar todos os marcadores
        if (markers.length > 0) {
            const group = new L.featureGroup(markers);
            map.fitBounds(group.getBounds().pad(0.1));
        }
    }
}

function filtrarPontos() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
    const tipoFiltro = document.getElementById('filterTipo').value;
    
    let pontosFiltrados = pontos;
    
    // Filtrar por texto (localidade ou cidade)
    if (searchTerm) {
        pontosFiltrados = pontosFiltrados.filter(ponto => {
            const localidade = (ponto.localidade || '').toLowerCase();
            const cidade = (ponto.cidade || '').toLowerCase();
            return localidade.includes(searchTerm) || cidade.includes(searchTerm);
        });
    }
    
    // Filtrar por tipo
    if (tipoFiltro) {
        pontosFiltrados = pontosFiltrados.filter(ponto => ponto.tipo === tipoFiltro);
    }
    
    // Atualizar marcadores no mapa
    adicionarMarcadores(pontosFiltrados);
    atualizarContador(pontosFiltrados.length);
    
    // Mostrar mensagem se não houver resultados
    if (pontosFiltrados.length === 0) {
        alert('Nenhum ponto encontrado com os filtros aplicados.');
    }
}

function limparFiltros() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterTipo').value = '';
    adicionarMarcadores(pontos);
    atualizarContador(pontos.length);
    
    // Resetar zoom para visualização completa da Bahia
    map.setView([-12.5797, -41.7007], 7);
}

function atualizarContador(total) {
    const totalPontos = pontos.length;
    const texto = total === totalPontos 
        ? `Exibindo ${total} ponto(s)` 
        : `Exibindo ${total} de ${totalPontos} ponto(s)`;
    document.getElementById('resultadosCount').textContent = texto;
}

function mostrarDetalhes(id) {
    pontoAtual = pontos.find(p => p.id == id);
    
    const html = `
        <div class="row">
            <div class="col-md-6">
                <p><strong>Localidade:</strong> ${pontoAtual.localidade || 'N/A'}</p>
                <p><strong>Território:</strong> ${pontoAtual.territorio || 'N/A'}</p>
                <p><strong>Cidade:</strong> ${pontoAtual.cidade || 'N/A'}</p>
                <p><strong>Endereço:</strong> ${pontoAtual.endereco || 'N/A'}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Tipo:</strong> <span class="badge" style="background-color: ${coresTipo[pontoAtual.tipo] || '#6c757d'}">${pontoAtual.tipo || 'N/A'}</span></p>
               
                <p><strong>Data Instalação:</strong> ${pontoAtual.data_instalacao || 'N/A'}</p>
                <p><strong>Observação:</strong> ${pontoAtual.observacao || 'N/A'}</p>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-6">
                <p><strong>Latitude:</strong> ${pontoAtual.latitude}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Longitude:</strong> ${pontoAtual.longitude}</p>
            </div>
        </div>
    `;
    
    document.getElementById('detalhesContent').innerHTML = html;
    new bootstrap.Modal(document.getElementById('modalDetalhes')).show();
}

function editarPonto() {
    if (pontoAtual) {
        window.location.href = `editar_ponto.php?id=${pontoAtual.id}`;
    }
}

function abrirChamado() {
    if (pontoAtual) {
        window.location.href = `chamados.php?ponto_id=${pontoAtual.id}&action=novo`;
    }
}
</script>