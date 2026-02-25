//adicionar.php
<?php
header('Content-Type: text/html; charset=utf-8');
require_once 'config/database.php';
require_once 'config/constants.php';

$currentPage = 'pontos';
$pageTitle = 'Adicionar Ponto - Conectiva';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = getConnection();
    $stmt = $conn->prepare("
        INSERT INTO conectiva 
        (localidade, territorio, cidade, endereco, latitude, longitude, velocidade, tipo, data_instalacao, observacao, data_criacao) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
    ");
    
    $stmt->execute([
        $_POST['localidade'],
        $_POST['territorio'],
        $_POST['cidade'],
        $_POST['endereco'],
        $_POST['latitude'] ?: null,
        $_POST['longitude'] ?: null,
        $_POST['velocidade'],
        $_POST['tipo'],
        $_POST['data_instalacao'] ?: null,
        $_POST['observacao']
    ]);
    
    header('Location: pontos.php?success=added');
    exit;
}

ob_start();
?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="bi bi-plus-circle"></i> Adicionar Novo Ponto</h1>
        <a href="pontos.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-pencil"></i> Dados do Ponto de Internet
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Localidade *</label>
                        <input type="text" name="localidade" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Território</label>
                        <select name="territorio" id="territorio" class="form-select" onchange="carregarCidades()">
                            <option value="">Selecione o território</option>
                            <?php foreach(getTerritorios() as $territorio): ?>
                                <option value="<?php echo htmlspecialchars($territorio); ?>">
                                    <?php echo htmlspecialchars($territorio); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Cidade</label>
                        <select name="cidade" id="cidade" class="form-select">
                            <option value="">Selecione primeiro o território</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Endereço</label>
                        <input type="text" name="endereco" class="form-control">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Tipo de Conexão</label>
                        <select name="tipo" class="form-select">
                            <option value="">Selecione</option>
                            <?php foreach(TIPOS_CONEXAO as $key => $label): ?>
                                <option value="<?php echo htmlspecialchars($label); ?>"><?php echo htmlspecialchars($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Velocidade</label>
                        <input type="text" name="velocidade" class="form-control" placeholder="Ex: 100 Mbps" list="velocidades">
                        <datalist id="velocidades">
                            <?php foreach(VELOCIDADES_PADRAO as $vel): ?>
                                <option value="<?php echo $vel; ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Data de Instalação</label>
                        <input type="date" name="data_instalacao" class="form-control">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Latitude</label>
                        <input type="text" name="latitude" id="latitude" class="form-control" step="any" placeholder="Ex: -12.9714">
                        <small class="text-muted">Clique no mapa para obter coordenadas</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Longitude</label>
                        <input type="text" name="longitude" id="longitude" class="form-control" step="any" placeholder="Ex: -38.5014">
                        <small class="text-muted">Clique no mapa para obter coordenadas</small>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Mapa de Localização</label>
                <div id="map" style="height: 400px; border: 1px solid #ddd; border-radius: 5px;"></div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Observações</label>
                <textarea name="observacao" class="form-control" rows="3"></textarea>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="pontos.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check"></i> Salvar Ponto
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Dados de territórios e cidades - declarar globalmente
const territorios = <?php echo json_encode($GLOBALS['territorios'], JSON_UNESCAPED_UNICODE); ?>;

// Variáveis globais
let map;
let marker = null;

// Função global para carregar cidades (chamada pelo onchange do select)
function carregarCidades() {
    const territorioSel = document.getElementById('territorio').value;
    const cidadeSel = document.getElementById('cidade');
    
    cidadeSel.innerHTML = '<option value="">Selecione a cidade</option>';
    
    if (territorioSel && territorios[territorioSel]) {
        territorios[territorioSel].forEach(cidade => {
            const option = document.createElement('option');
            option.value = cidade;
            option.textContent = cidade;
            cidadeSel.appendChild(option);
        });
    }
}

// Aguardar carregamento completo da página
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar mapa centrado na Bahia
    map = L.map('map').setView([-12.5797, -41.7007], 7);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    // Clicar no mapa para definir coordenadas
    map.on('click', function(e) {
        const lat = e.latlng.lat.toFixed(6);
        const lng = e.latlng.lng.toFixed(6);
        
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
        
        if (marker) {
            map.removeLayer(marker);
        }
        
        marker = L.marker([lat, lng]).addTo(map);
        marker.bindPopup(`<b>Localização selecionada</b><br>Lat: ${lat}<br>Lng: ${lng}`).openPopup();
    });

    // Atualizar marcador quando digitar coordenadas
    document.getElementById('latitude').addEventListener('change', updateMarker);
    document.getElementById('longitude').addEventListener('change', updateMarker);
});

function updateMarker() {
    const lat = parseFloat(document.getElementById('latitude').value);
    const lng = parseFloat(document.getElementById('longitude').value);
    
    if (!isNaN(lat) && !isNaN(lng)) {
        if (marker) {
            map.removeLayer(marker);
        }
        
        marker = L.marker([lat, lng]).addTo(map);
        map.setView([lat, lng], 13);
    }
}
</script>

<?php
$content = ob_get_clean();
include 'templates/layout.php';
?>