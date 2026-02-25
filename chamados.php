<?php
header('Content-Type: text/html; charset=utf-8');
require_once 'config/database.php';
require_once 'config/constants.php';

$currentPage = 'chamados';
$pageTitle = 'Chamados - Conectiva';

// Habilitar exibição de erros para debug (remover em produção)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = getConnection();

// Processar novo chamado
// Processar novo chamado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'novo') {
    try {
        // DEBUG: Mostrar dados recebidos
        error_log("=== DEBUG CHAMADO ===");
        error_log("POST recebido: " . print_r($_POST, true));
        
        // Converter ponto_id para inteiro
        $ponto_id = (int)$_POST['ponto_id'];
        error_log("Ponto ID convertido: " . $ponto_id);
        
        if ($ponto_id <= 0) {
            error_log("ERRO: Ponto ID inválido");
            header('Location: chamados.php?error=ponto_invalido');
            exit;
        }
        
        // Buscar dados do ponto
        $stmtPonto = $conn->prepare("SELECT cidade, territorio FROM conectiva WHERE id = ?");
        $stmtPonto->execute([$ponto_id]);
        $ponto = $stmtPonto->fetch(PDO::FETCH_ASSOC);
        error_log("Ponto encontrado: " . print_r($ponto, true));
        
        if (!$ponto) {
            error_log("ERRO: Ponto não encontrado no banco");
            header('Location: chamados.php?error=ponto_nao_encontrado');
            exit;
        }
        
        // Garantir que cidade e territorio não sejam nulos
        $cidade = $ponto['cidade'] ?? '';
        $territorio = $ponto['territorio'] ?? '';
        error_log("Cidade: '$cidade', Território: '$territorio'");
        
        // Validar tipo_problema
        if (empty($_POST['tipo_problema'])) {
            error_log("ERRO: Tipo de problema vazio");
            header('Location: chamados.php?error=tipo_problema_vazio');
            exit;
        }
        
        error_log("Tipo problema: " . $_POST['tipo_problema']);
        
        // Tentar inserir
        $stmt = $conn->prepare("
            INSERT INTO conectiva_helpdesk 
            (ponto_id, cidade, territorio, tipo_problema, status, data_abertura) 
            VALUES (?, ?, ?, ?, 'Aberto', CURRENT_TIMESTAMP)
        ");
        
        $dados = [
            $ponto_id,
            $cidade,
            $territorio,
            $_POST['tipo_problema']
        ];
        error_log("Dados para inserção: " . print_r($dados, true));
        
        $resultado = $stmt->execute($dados);
        error_log("Resultado da inserção: " . ($resultado ? 'SUCESSO' : 'FALHA'));
        error_log("Linhas afetadas: " . $stmt->rowCount());
        
        if ($resultado) {
            error_log("=== CHAMADO CRIADO COM SUCESSO ===");
            header('Location: chamados.php?success=created');
            exit;
        } else {
            error_log("ERRO: Execute retornou false");
            $errorInfo = $stmt->errorInfo();
            error_log("ErrorInfo: " . print_r($errorInfo, true));
            header('Location: chamados.php?error=criar_chamado');
            exit;
        }
        
    } catch (PDOException $e) {
        error_log("ERRO PDO: " . $e->getMessage());
        error_log("Código do erro: " . $e->getCode());
        error_log("Stack trace: " . $e->getTraceAsString());
        
        // Mostrar erro na tela temporariamente
        die("ERRO PDO: " . $e->getMessage() . "<br>Código: " . $e->getCode());
        
        header('Location: chamados.php?error=criar_chamado');
        exit;
    } catch (Exception $e) {
        error_log("ERRO GERAL: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        
        // Mostrar erro na tela temporariamente
        die("ERRO GERAL: " . $e->getMessage());
        
        header('Location: chamados.php?error=criar_chamado');
        exit;
    }
}

// Processar adição de observação
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'observacao') {
    try {
        $stmt = $conn->prepare("UPDATE conectiva_helpdesk SET observacao = ? WHERE id = ?");
        $stmt->execute([$_POST['observacao'], $_POST['chamado_id']]);
        header('Location: chamados.php?success=observation');
        exit;
    } catch (Exception $e) {
        error_log("Erro ao adicionar observação: " . $e->getMessage());
        header('Location: chamados.php?error=observacao');
        exit;
    }
}

// Processar fechamento de chamado
if (isset($_GET['fechar'])) {
    $stmt = $conn->prepare("UPDATE conectiva_helpdesk SET status = 'Fechado', data_fechamento = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->execute([$_GET['fechar']]);
    header('Location: chamados.php?success=closed');
    exit;
}

// Buscar chamados com dados do ponto
$stmt = $conn->prepare("
    SELECT h.*, c.localidade
    FROM conectiva_helpdesk h 
    LEFT JOIN conectiva c ON h.ponto_id = c.id 
    ORDER BY h.data_abertura DESC
");
$stmt->execute();
$chamados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar pontos para o formulário
$pontos = $conn->query("SELECT id, localidade, territorio, cidade FROM conectiva ORDER BY localidade")->fetchAll(PDO::FETCH_ASSOC);

// Verificar se deve abrir modal automaticamente
$abrirModal = isset($_GET['action']) && $_GET['action'] == 'novo';
$pontoSelecionado = isset($_GET['ponto_id']) ? $_GET['ponto_id'] : null;

ob_start();
?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="bi bi-headset"></i> Gerenciar Chamados</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNovoChamado">
            <i class="bi bi-plus-circle"></i> Abrir Novo Chamado
        </button>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle"></i>
    <?php
    if ($_GET['success'] == 'created') echo 'Chamado aberto com sucesso!';
    elseif ($_GET['success'] == 'closed') echo 'Chamado fechado com sucesso!';
    elseif ($_GET['success'] == 'observation') echo 'Observação adicionada com sucesso!';
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <i class="bi bi-exclamation-triangle"></i>
    <?php
    if ($_GET['error'] == 'ponto_nao_encontrado') echo 'Ponto não encontrado!';
    elseif ($_GET['error'] == 'criar_chamado') echo 'Erro ao criar chamado. Tente novamente.';
    elseif ($_GET['error'] == 'observacao') echo 'Erro ao adicionar observação. Tente novamente.';
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Filtros -->
<div class="card mb-3">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select class="form-select" id="filtroStatus" onchange="filtrarChamados()">
                    <option value="">Todos</option>
                    <?php foreach(STATUS_CHAMADO as $key => $label): ?>
                        <option value="<?php echo htmlspecialchars($label); ?>"><?php echo htmlspecialchars($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tipo de Problema</label>
                <select class="form-select" id="filtroTipo" onchange="filtrarChamados()">
                    <option value="">Todos</option>
                    <?php foreach(TIPOS_PROBLEMA as $tipo): ?>
                        <option value="<?php echo htmlspecialchars($tipo); ?>"><?php echo htmlspecialchars($tipo); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Buscar</label>
                <input type="text" class="form-control" id="busca" placeholder="Localidade, território, cidade..." onkeyup="filtrarChamados()">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button class="btn btn-secondary w-100" onclick="limparFiltros()">
                    <i class="bi bi-x-circle"></i> Limpar
                </button>
            </div>
        </div>
        <div id="resultadosCount" class="mt-2 text-muted"></div>
    </div>
</div>

<!-- Tabela de Chamados -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-list"></i> Histórico de Chamados
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="tabelaChamados">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Ponto</th>
                        <th>Território</th>
                        <th>Cidade</th>
                        <th>Tipo Problema</th>
                        <th>Status</th>
                        <th>Data Abertura</th>
                        <th>Data Fechamento</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($chamados)): ?>
                    <tr id="emptyRow">
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                            <p class="mt-2">Nenhum chamado registrado</p>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($chamados as $chamado): ?>
                        <tr data-localidade="<?php echo htmlspecialchars(mb_strtolower($chamado['localidade'] ?? '')); ?>"
                            data-territorio="<?php echo htmlspecialchars(mb_strtolower($chamado['territorio'] ?? '')); ?>"
                            data-cidade="<?php echo htmlspecialchars(mb_strtolower($chamado['cidade'] ?? '')); ?>"
                            data-tipo="<?php echo htmlspecialchars($chamado['tipo_problema'] ?? ''); ?>"
                            data-status="<?php echo htmlspecialchars($chamado['status'] ?? ''); ?>">
                            <td><?php echo $chamado['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($chamado['localidade'] ?? 'N/A'); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($chamado['territorio'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($chamado['cidade'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($chamado['tipo_problema'] ?? 'N/A'); ?></td>
                            <td>
                                <span class="badge bg-<?php echo getBadgeClassStatus($chamado['status']); ?>">
                                    <?php echo htmlspecialchars($chamado['status']); ?>
                                </span>
                            </td>
                            <td><?php echo $chamado['data_abertura'] ? date('d/m/Y H:i', strtotime($chamado['data_abertura'])) : 'N/A'; ?></td>
                            <td><?php echo $chamado['data_fechamento'] ? date('d/m/Y H:i', strtotime($chamado['data_fechamento'])) : '-'; ?></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-info" onclick="verDetalhes(<?php echo $chamado['id']; ?>)" title="Ver Detalhes">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <?php if ($chamado['status'] == 'Aberto'): ?>
                                        <button class="btn btn-warning" onclick="adicionarObservacao(<?php echo $chamado['id']; ?>)" title="Adicionar Observação">
                                            <i class="bi bi-chat-left-text"></i>
                                        </button>
                                        <button class="btn btn-success" onclick="fecharChamado(<?php echo $chamado['id']; ?>)" title="Fechar Chamado">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                    <?php endif; ?>
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

<!-- Modal Novo Chamado -->
<div class="modal fade" id="modalNovoChamado" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Abrir Novo Chamado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="novo">
                    
                    <div class="mb-3">
                        <label class="form-label">Ponto de Internet *</label>
                        <select name="ponto_id" class="form-select" required>
                            <option value="">Selecione o ponto</option>
                            <?php foreach ($pontos as $ponto): ?>
                                <option value="<?php echo $ponto['id']; ?>" 
                                    <?php echo ($pontoSelecionado == $ponto['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($ponto['localidade']) . ' - ' . htmlspecialchars($ponto['territorio'] ?? '') . ' - ' . htmlspecialchars($ponto['cidade'] ?? ''); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tipo de Problema *</label>
                        <select name="tipo_problema" class="form-select" required>
                            <option value="">Selecione o tipo</option>
                            <?php foreach(TIPOS_PROBLEMA as $tipo): ?>
                                <option value="<?php echo htmlspecialchars($tipo); ?>"><?php echo htmlspecialchars($tipo); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> A observação pode ser adicionada posteriormente.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check"></i> Abrir Chamado
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Adicionar Observação -->
<div class="modal fade" id="modalObservacao" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-chat-left-text"></i> Adicionar Observação</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="observacao">
                    <input type="hidden" name="chamado_id" id="observacao_chamado_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Observação</label>
                        <textarea name="observacao" id="observacao_texto" class="form-control" rows="5" placeholder="Descreva detalhes sobre o chamado..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check"></i> Salvar Observação
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detalhes -->
<div class="modal fade" id="modalDetalhes" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-info-circle"></i> Detalhes do Chamado</h5>
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
const chamados = <?php echo json_encode($chamados, JSON_UNESCAPED_UNICODE); ?>;
const totalChamados = chamados.length;

document.addEventListener('DOMContentLoaded', function() {
    atualizarContador(totalChamados);
    
    <?php if ($abrirModal): ?>
    // Abrir modal automaticamente se action=novo
    new bootstrap.Modal(document.getElementById('modalNovoChamado')).show();
    <?php endif; ?>
});

function fecharChamado(id) {
    if (confirm('Deseja realmente fechar este chamado?')) {
        window.location.href = `chamados.php?fechar=${id}`;
    }
}

function adicionarObservacao(id) {
    const chamado = chamados.find(c => c.id == id);
    document.getElementById('observacao_chamado_id').value = id;
    document.getElementById('observacao_texto').value = chamado.observacao || '';
    new bootstrap.Modal(document.getElementById('modalObservacao')).show();
}

function verDetalhes(id) {
    const chamado = chamados.find(c => c.id == id);
    
    const html = `
        <div class="row">
            <div class="col-md-6">
                <p><strong>ID:</strong> ${chamado.id}</p>
                <p><strong>Ponto:</strong> ${chamado.localidade || 'N/A'}</p>
                <p><strong>Território:</strong> ${chamado.territorio || 'N/A'}</p>
                <p><strong>Cidade:</strong> ${chamado.cidade || 'N/A'}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Tipo de Problema:</strong> ${chamado.tipo_problema || 'N/A'}</p>
                <p><strong>Status:</strong> <span class="badge bg-${getBadgeClass(chamado.status)}">${chamado.status}</span></p>
                <p><strong>Data Abertura:</strong> ${chamado.data_abertura ? new Date(chamado.data_abertura).toLocaleString('pt-BR') : 'N/A'}</p>
                <p><strong>Data Fechamento:</strong> ${chamado.data_fechamento ? new Date(chamado.data_fechamento).toLocaleString('pt-BR') : '-'}</p>
            </div>
        </div>
        <hr>
        <div>
            <strong>Observação:</strong>
            <p class="mt-2">${chamado.observacao || '<em class="text-muted">Nenhuma observação registrada</em>'}</p>
        </div>
    `;
    
    document.getElementById('detalhesContent').innerHTML = html;
    new bootstrap.Modal(document.getElementById('modalDetalhes')).show();
}

function getBadgeClass(status) {
    const classes = {
        'Aberto': 'danger',
        'Em Andamento': 'warning',
        'Fechado': 'success'
    };
    return classes[status] || 'secondary';
}

function filtrarChamados() {
    const status = document.getElementById('filtroStatus').value.toLowerCase();
    const tipo = document.getElementById('filtroTipo').value.toLowerCase();
    const busca = document.getElementById('busca').value.toLowerCase();
    
    const linhas = document.querySelectorAll('#tabelaChamados tbody tr[data-status]');
    let visibleCount = 0;
    
    linhas.forEach(linha => {
        const localidade = linha.dataset.localidade;
        const territorio = linha.dataset.territorio;
        const cidade = linha.dataset.cidade;
        const tipoLinha = linha.dataset.tipo.toLowerCase();
        const statusLinha = linha.dataset.status.toLowerCase();
        
        let mostrar = true;
        
        // Filtro de status
        if (status && !statusLinha.includes(status)) {
            mostrar = false;
        }
        
        // Filtro de tipo
        if (tipo && !tipoLinha.includes(tipo)) {
            mostrar = false;
        }
        
        // Filtro de busca (localidade, território ou cidade)
        if (busca) {
            const matchBusca = localidade.includes(busca) || 
                              territorio.includes(busca) || 
                              cidade.includes(busca);
            if (!matchBusca) mostrar = false;
        }
        
        linha.style.display = mostrar ? '' : 'none';
        if (mostrar) visibleCount++;
    });
    
    // Gerenciar linha vazia
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
                <td colspan="9" class="text-center text-muted py-4">
                    <i class="bi bi-search" style="font-size: 3rem;"></i>
                    <p class="mt-2">Nenhum chamado encontrado com os filtros aplicados</p>
                </td>
            `;
            document.querySelector('#tabelaChamados tbody').appendChild(noResultRow);
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
    document.getElementById('filtroStatus').value = '';
    document.getElementById('filtroTipo').value = '';
    document.getElementById('busca').value = '';
    filtrarChamados();
}

function atualizarContador(visible) {
    const texto = visible === totalChamados 
        ? `Exibindo ${visible} chamado(s)` 
        : `Exibindo ${visible} de ${totalChamados} chamado(s)`;
    document.getElementById('resultadosCount').textContent = texto;
}
</script>

<?php
$content = ob_get_clean();
include 'templates/layout.php';
?>