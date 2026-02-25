//editar_ponto.php
<?php
header('Content-Type: text/html; charset=utf-8');
require_once 'config/database.php';
require_once 'config/constants.php';

$currentPage = 'pontos';
$pageTitle = 'Editar Ponto - Conectiva';

$conn = getConnection();

// Buscar ponto
if (!isset($_GET['id'])) {
    header('Location: pontos.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM conectiva WHERE id = ?");
$stmt->execute([$_GET['id']]);
$ponto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ponto) {
    header('Location: pontos.php');
    exit;
}

// Atualizar ponto
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $conn->prepare("
        UPDATE conectiva SET
        localidade = ?, territorio = ?, cidade = ?, endereco = ?,
        latitude = ?, longitude = ?, tipo = ?,
        data_instalacao = ?, observacao = ?, data_atualizacao = CURRENT_TIMESTAMP
        WHERE id = ?
    ");
    
    $stmt->execute([
        $_POST['localidade'],
        $_POST['territorio'],
        $_POST['cidade'],
        $_POST['endereco'],
        $_POST['latitude'] ?: null,
        $_POST['longitude'] ?: null,

        $_POST['tipo'],
        $_POST['data_instalacao'] ?: null,
        $_POST['observacao'],
        $_GET['id']
    ]);
    
    header('Location: pontos.php?success=updated');
    exit;
}

ob_start();
?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="bi bi-pencil"></i> Editar Ponto</h1>
        <a href="pontos.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-geo-alt"></i> Editando: <?php echo htmlspecialchars($ponto['localidade']); ?>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Localidade *</label>
                        <input type="text" name="localidade" class="form-control" value="<?php echo htmlspecialchars($ponto['localidade']); ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Território</label>
                        <select name="territorio" id="territorio" class="form-select" onchange="carregarCidades()">
                            <option value="">Selecione o território</option>
                            <?php foreach(getTerritorios() as $territorio): ?>
                                <option value="<?php echo htmlspecialchars($territorio); ?>" 
                                    <?php echo ($ponto['territorio'] == $territorio) ? 'selected' : ''; ?>>
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
                            <option value="">Selecione a cidade</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Endereço</label>
                        <input type="text" name="endereco" class="form-control" value="<?php echo htmlspecialchars($ponto['endereco'] ?? ''); ?>">
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
                                <option value="<?php echo htmlspecialchars($label); ?>" <?php echo ($ponto['tipo'] == $label) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Data de Instalação</label>
                        <input type="date" name="data_instalacao" class="form-control" value="<?php echo $ponto['data_instalacao']; ?>">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Latitude</label>
                        <input type="text" name="latitude" id="latitude" class="form-control" step="any" value="<?php echo $ponto['latitude']; ?>">
                        <small class="text-muted">Clique no mapa para obter coordenadas</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Longitude</label>
                        <input type="text" name="longitude" id="longitude" class="form-control" step="any" value="<?php echo $ponto['longitude']; ?>">
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
                <textarea name="observacao" class="form-control" rows="3"><?php echo htmlspecialchars($ponto['observacao'] ?? ''); ?></textarea>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="pontos.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check"></i> Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const lat = <?php echo $ponto['latitude'] ?: -12.5797; ?>;
const lng = <?php echo $ponto['longitude'] ?: -41.7007; ?>;

// Dados de territórios e cidades - declarar globalmente
const territorios = <?php echo json_encode($GLOBALS['territorios'], JSON_UNESCAPED_UNICODE); ?>;
const cidadeAtual = '<?php echo addslashes($ponto['cidade'] ?? ''); ?>';
const territorioAtual = '<?php echo addslashes($ponto['territorio'] ?? ''); ?>';

// Variáveis globais
let map;
let marker = null;

// Função global para carregar cidades (chamada pelo onchange do select)
function carregarCidades() {
    const territorioSel = document.getElementById('territorio').value;
    const cidadeSel = document.getElementById('cidade');
    
    // Limpar select
    cidadeSel.innerHTML = '<option value="">Selecione a cidade</option>';
    
    if (territorioSel && territorios[territorioSel]) {
        territorios[territorioSel].forEach(cidade => {
            const option = document.createElement('option');
            option.value = cidade;
            option.textContent = cidade;
            // Selecionar a cidade atual se corresponder
            if (cidade === cidadeAtual) {
                option.selected = true;
            }
            cidadeSel.appendChild(option);
        });
    }
}

// Aguardar carregamento completo da página
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar mapa
    map = L.map('map').setView([lat, lng], <?php echo ($ponto['latitude']) ? 13 : 7; ?>);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    // Adicionar marcador existente se houver coordenadas
    <?php if ($ponto['latitude'] && $ponto['longitude']): ?>
    marker = L.marker([lat, lng]).addTo(map);
    marker.bindPopup('<b>Localização atual</b>').openPopup();
    <?php endif; ?>

    // Carregar cidades do território atual
    if (territorioAtual) {
        carregarCidades();
    }

    // Clicar no mapa para atualizar coordenadas
    map.on('click', function(e) {
        const newLat = e.latlng.lat.toFixed(6);
        const newLng = e.latlng.lng.toFixed(6);
        
        document.getElementById('latitude').value = newLat;
        document.getElementById('longitude').value = newLng;
        
        if (marker) {
            map.removeLayer(marker);
        }
        
        marker = L.marker([newLat, newLng]).addTo(map);
        marker.bindPopup(`<b>Nova localização</b><br>Lat: ${newLat}<br>Lng: ${newLng}`).openPopup();
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