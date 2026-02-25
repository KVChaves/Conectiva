//pontos.php
<?php
header('Content-Type: text/html; charset=utf-8');
require_once 'config/database.php';
require_once 'config/constants.php';

$currentPage = 'pontos';
$pageTitle = 'Listar Pontos - Conectiva';

// Processar exclusão
if (isset($_GET['delete'])) {
    $conn = getConnection();
    $stmt = $conn->prepare("DELETE FROM conectiva WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: pontos.php?success=deleted');
    exit;
}

// Buscar todos os pontos
$conn = getConnection();
$stmt = $conn->prepare("SELECT * FROM conectiva ORDER BY data_criacao DESC");
$stmt->execute();
$pontos = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="bi bi-list-ul"></i> Lista de Pontos</h1>
        <a href="adicionar_ponto.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Adicionar Ponto
        </a>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle"></i>
    <?php
    if ($_GET['success'] == 'deleted') echo 'Ponto excluído com sucesso!';
    elseif ($_GET['success'] == 'added') echo 'Ponto adicionado com sucesso!';
    elseif ($_GET['success'] == 'updated') echo 'Ponto atualizado com sucesso!';
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Barra de Pesquisa e Filtros -->
<div class="card mb-3">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchInput" class="form-control" placeholder="Pesquisar por localidade, território ou cidade...">
                </div>
            </div>
            <div class="col-md-3">
                <select id="filterTipo" class="form-select">
                    <option value="">Todos os tipos</option>
                    <?php foreach(TIPOS_CONEXAO as $key => $label): ?>
                        <option value="<?php echo htmlspecialchars($label); ?>"><?php echo htmlspecialchars($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select id="filterTerritorio" class="form-select">
                    <option value="">Todos os territórios</option>
                    <?php foreach(getTerritorios() as $territorio): ?>
                        <option value="<?php echo htmlspecialchars($territorio); ?>"><?php echo htmlspecialchars($territorio); ?></option>
                    <?php endforeach; ?>
                </select>
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
        <span><i class="bi bi-table"></i> Total de Pontos: <span id="totalPontos"><?php echo count($pontos); ?></span></span>
        <div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="tabelaPontos">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Localidade</th>
                        <th>Território</th>
                        <th>Cidade</th>
                        <th>Tipo</th>
                        
                        <th>Data Instalação</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody id="tabelaBody">
                    <?php if (empty($pontos)): ?>
                    <tr id="emptyRow">
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                            <p class="mt-2">Nenhum ponto cadastrado</p>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($pontos as $ponto): ?>
                        <tr data-id="<?php echo $ponto['id']; ?>" 
                            data-localidade="<?php echo htmlspecialchars(mb_strtolower($ponto['localidade'] ?? '')); ?>"
                            data-territorio="<?php echo htmlspecialchars(mb_strtolower($ponto['territorio'] ?? '')); ?>"
                            data-cidade="<?php echo htmlspecialchars(mb_strtolower($ponto['cidade'] ?? '')); ?>"
                            data-tipo="<?php echo htmlspecialchars($ponto['tipo'] ?? ''); ?>">
                            <td><?php echo $ponto['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($ponto['localidade'] ?? 'N/A'); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($ponto['territorio'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($ponto['cidade'] ?? 'N/A'); ?></td>
                            <td>
                                <?php
                                $cores = [
                                    'Fibra' => 'success',
                                    'Rádio' => 'primary',
                                    'Satélite' => 'warning',
                                    'Móvel' => 'info',
                                    'Outros' => 'secondary'
                                ];
                                $tipoNormalizado = $ponto['tipo'] ?? '';
                                $badgeClass = 'secondary';
                                foreach ($cores as $tipo => $classe) {
                                    if (stripos($tipoNormalizado, $tipo) !== false || $tipoNormalizado === $tipo) {
                                        $badgeClass = $classe;
                                        break;
                                    }
                                }
                                ?>
                                <span class="badge bg-<?php echo $badgeClass; ?>">
                                    <?php echo htmlspecialchars($ponto['tipo'] ?? 'N/A'); ?>
                                </span>
                            </td>
                            
                            <td><?php echo $ponto['data_instalacao'] ? date('d/m/Y', strtotime($ponto['data_instalacao'])) : 'N/A'; ?></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-info" onclick="detalhar(<?php echo $ponto['id']; ?>)" title="Detalhar">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <a href="editar_ponto.php?id=<?php echo $ponto['id']; ?>" class="btn btn-warning" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-danger" onclick="confirmarExclusao(<?php echo $ponto['id']; ?>)" title="Excluir">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detalhes -->
<div class="modal fade" id="modalDetalhes" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-info-circle"></i> Detalhes do Ponto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalhesContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script>
const pontos = <?php echo json_encode($pontos, JSON_UNESCAPED_UNICODE); ?>;
const totalPontos = pontos.length;

// Adicionar eventos de filtro em tempo real
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('searchInput').addEventListener('keyup', filtrarTabela);
    document.getElementById('filterTipo').addEventListener('change', filtrarTabela);
    document.getElementById('filterTerritorio').addEventListener('change', filtrarTabela);
    
    atualizarContador(totalPontos);
});

function filtrarTabela() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
    const tipoFiltro = document.getElementById('filterTipo').value;
    const territorioFiltro = document.getElementById('filterTerritorio').value.toLowerCase();
    
    const linhas = document.querySelectorAll('#tabelaBody tr[data-id]');
    let visibleCount = 0;
    
    linhas.forEach(linha => {
        const localidade = linha.dataset.localidade;
        const territorio = linha.dataset.territorio;
        const cidade = linha.dataset.cidade;
        const tipo = linha.dataset.tipo;
        
        let mostrar = true;
        
        // Filtro de busca (localidade, território ou cidade)
        if (searchTerm) {
            const matchSearch = localidade.includes(searchTerm) || 
                               territorio.includes(searchTerm) || 
                               cidade.includes(searchTerm);
            if (!matchSearch) mostrar = false;
        }
        
        // Filtro de tipo
        if (tipoFiltro && tipo !== tipoFiltro) {
            mostrar = false;
        }
        
        // Filtro de território
        if (territorioFiltro && territorio !== territorioFiltro) {
            mostrar = false;
        }
        
        linha.style.display = mostrar ? '' : 'none';
        if (mostrar) visibleCount++;
    });
    
    // Mostrar/ocultar mensagem de vazio
    const emptyRow = document.getElementById('emptyRow');
    if (emptyRow) {
        emptyRow.style.display = 'none';
    }
    
    // Adicionar linha de "nenhum resultado" se necessário
    if (visibleCount === 0 && linhas.length > 0) {
        let noResultRow = document.getElementById('noResultRow');
        if (!noResultRow) {
            noResultRow = document.createElement('tr');
            noResultRow.id = 'noResultRow';
            noResultRow.innerHTML = `
                <td colspan="8" class="text-center text-muted py-4">
                    <i class="bi bi-search" style="font-size: 3rem;"></i>
                    <p class="mt-2">Nenhum ponto encontrado com os filtros aplicados</p>
                </td>
            `;
            document.getElementById('tabelaBody').appendChild(noResultRow);
        } else {
            noResultRow.style.display = '';
        }
    } else {
        const noResultRow = document.getElementById('noResultRow');
        if (noResultRow) {
            noResultRow.style.display = 'none';
        }
    }
    
    atualizarContador(visibleCount);
}

function limparFiltros() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterTipo').value = '';
    document.getElementById('filterTerritorio').value = '';
    filtrarTabela();
}

function atualizarContador(visible) {
    document.getElementById('totalPontos').textContent = visible;
    const texto = visible === totalPontos 
        ? `Exibindo ${visible} ponto(s)` 
        : `Exibindo ${visible} de ${totalPontos} ponto(s)`;
    document.getElementById('resultadosCount').textContent = texto;
}

function detalhar(id) {
    const ponto = pontos.find(p => p.id == id);
    
    const html = `
        <div class="row">
            <div class="col-md-6">
                <p><strong>ID:</strong> ${ponto.id}</p>
                <p><strong>Localidade:</strong> ${ponto.localidade || 'N/A'}</p>
                <p><strong>Território:</strong> ${ponto.territorio || 'N/A'}</p>
                <p><strong>Cidade:</strong> ${ponto.cidade || 'N/A'}</p>
                <p><strong>Endereço:</strong> ${ponto.endereco || 'N/A'}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Tipo:</strong> ${ponto.tipo || 'N/A'}</p>
                
                <p><strong>Latitude:</strong> ${ponto.latitude || 'N/A'}</p>
                <p><strong>Longitude:</strong> ${ponto.longitude || 'N/A'}</p>
                <p><strong>Data Instalação:</strong> ${ponto.data_instalacao || 'N/A'}</p>
            </div>
        </div>
        <hr>
        <p><strong>Observação:</strong> ${ponto.observacao || 'Nenhuma observação'}</p>
    `;
    
    document.getElementById('detalhesContent').innerHTML = html;
    new bootstrap.Modal(document.getElementById('modalDetalhes')).show();
}

function confirmarExclusao(id) {
    if (confirm('Tem certeza que deseja excluir este ponto? Esta ação não pode ser desfeita.')) {
        window.location.href = `pontos.php?delete=${id}`;
    }
}
</script>

<?php
$content = ob_get_clean();
include 'templates/layout.php';
?>