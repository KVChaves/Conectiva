<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Sistema Conectiva'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
          crossorigin=""/>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: linear-gradient(180deg,rgb(45, 110, 50) 0%,rgb(50, 121, 56) 100%);
            padding: 20px 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }
        .sidebar .logo {
            color: white;
            text-align: center;
            padding: 0 20px 20px;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar .logo h4 {
            margin: 0;
            font-weight: 600;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 25px;
            margin: 5px 0;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: #3498db;
        }
        .sidebar .nav-link.active {
            background: rgba(52, 152, 219, 0.2);
            color: white;
            border-left-color: #3498db;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
        }
        .sidebar-footer {
            margin-top: auto;
            padding: 20px 0;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-footer .nav-link {
            background: rgba(255,255,255,0.05);
            border-left-color: #27ae60;
        }
        .sidebar-footer .nav-link:hover {
            background: rgba(39, 174, 96, 0.2);
            border-left-color: #27ae60;
        }
        .main-content {
            margin-left: 250px;
            padding: 30px;
            min-height: 100vh;
            background: #f8f9fa;
        }
        .page-header {
            background: white;
            padding: 20px 30px;
            margin: -30px -30px 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .page-header h1 {
            margin: 0;
            font-size: 28px;
            color: #2c3e50;
        }
        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        .card-header {
            background: white;
            border-bottom: 2px solid #f0f0f0;
            font-weight: 600;
            color: #2c3e50;
        }
        .btn-primary {
            background: #3498db;
            border-color: #3498db;
        }
        .btn-primary:hover {
            background: #2980b9;
            border-color: #2980b9;
        }
        .badge {
            padding: 6px 12px;
            font-weight: 500;
        }
        .modal-header {
            background: #3498db;
            color: white;
        }
        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <i class="bi bi-router" style="font-size: 2rem;"></i>
            <h4>Conectiva ADAB</h4>
            <small style="color: rgba(255,255,255,0.6);">Gestão de Pontos</small>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link <?php echo ($currentPage ?? '') == 'mapa' ? 'active' : ''; ?>" href="index.php">
                <i class="bi bi-map"></i> Mapa de Pontos
            </a>
            <a class="nav-link <?php echo ($currentPage ?? '') == 'pontos' ? 'active' : ''; ?>" href="pontos.php">
                <i class="bi bi-list-ul"></i> Listar Pontos
            </a>
            <a class="nav-link <?php echo ($currentPage ?? '') == 'relatorios' ? 'active' : ''; ?>" href="relatorios.php">
                <i class="bi bi-bar-chart"></i> Relatórios
            </a>
            <a class="nav-link <?php echo ($currentPage ?? '') == 'chamados' ? 'active' : ''; ?>" href="chamados.php">
                <i class="bi bi-headset"></i> Chamados
            </a>
        </nav>
        
        <!-- Footer da Sidebar -->
        <div class="sidebar-footer">
            <a class="nav-link" href="../patrimonio/index.php">
                <i class="bi bi-pc-display"></i> Patrimônio
            </a>
            <a class="nav-link" href="../conectiva/index.php">
                <i class="bi bi-router"></i> Conectiva
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <?php echo $content ?? ''; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</body>
</html>