<?php
require_once dirname(__DIR__) . "/config/conectdb.php";

$total = 0;
$res = $conn->query("SELECT COUNT(*) AS total FROM clientes");
if ($res) {
    $row = $res->fetch_assoc();
    $total = (int) $row["total"];
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Clientes | Dashboard Secundario</title>
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary clientes-page">
<div class="app-wrapper">
  <nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">Menu</a>
        </li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="index.php">Volver al Dashboard 1</a>
        </li>
      </ul>
    </div>
  </nav>

  <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
      <a href="index.php" class="brand-link">
        <span class="brand-text fw-light">Clientes App</span>
      </a>
    </div>
    <div class="sidebar-wrapper">
      <nav class="mt-2">
        <ul class="nav sidebar-menu flex-column">
          <li class="nav-item">
            <a href="index.php" class="nav-link">
              <p>Dashboard Principal</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="dashboard2.php" class="nav-link active">
              <p>Dashboard Secundario</p>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

  <main class="app-main">
    <div class="app-content-header">
      <div class="container-fluid">
        <h3 class="mb-0">Dashboard secundario</h3>
      </div>
    </div>
    <div class="app-content">
      <div class="container-fluid">
        <div class="row g-3">
          <div class="col-lg-4">
            <div class="small-box text-bg-info">
              <div class="inner">
                <h3><?= $total ?></h3>
                <p>Clientes actuales</p>
              </div>
            </div>
          </div>
          <div class="col-lg-8">
            <div class="card card-outline card-primary">
              <div class="card-header">
                <h3 class="card-title">Espacio listo para editar</h3>
              </div>
              <div class="card-body">
                <p class="mb-2">Este dashboard esta preparado para que agregues graficas, tablas o reportes.</p>
                <p class="mb-0">Puedes navegar entre ambos dashboards desde el sidebar o la barra superior.</p>
              </div>
              <div class="card-footer">
                <a href="index.php" class="btn btn-primary btn-sm">Ir al Dashboard 1</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<script src="../dist/js/adminlte.min.js"></script>
</body>
</html>
