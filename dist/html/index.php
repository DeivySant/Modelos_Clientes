<?php
require_once dirname(__DIR__, 2) . "/config/conectdb.php";
$layoutSidebarFile = dirname(__DIR__) . "/layout/fixed-sidebar.html";
$sidebarHtml = "";
if (is_file($layoutSidebarFile)) {
    $layoutContent = file_get_contents($layoutSidebarFile);
    if ($layoutContent !== false && preg_match('/<aside class="app-sidebar[\\s\\S]*?<\\/aside>/i', $layoutContent, $matches)) {
        $sidebarHtml = $matches[0];    }
}

$resultado = $conn->query("SELECT * FROM clientes ORDER BY nombre ASC");
$clientes = [];
while ($fila = $resultado->fetch_assoc()) {
    $clientes[] = $fila;
}
$conn->close();

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Clientes | Dashboard Principal</title>
  <link rel="stylesheet" href="../css/adminlte.min.css">
  <link rel="stylesheet" href="/clientes/assets/css/style.css">
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
      </ul>
    </div>
  </nav>
  <?= $sidebarHtml ?>

  <main class="app-main">
    <div class="app-content">
      <div class="container-fluid">
        <div class="clientes-main-container">
          <div class="header">
            <h1>Tabla de clientes</h1>
            <button id="btnAgregarCliente" class="btn-add" type="button">Agregar</button>
          </div>

          <div class="card">
            <table>
              <thead>
                <tr>
                  <th>Identificacion</th>
                  <th>Nombre</th>
                  <th>Correo</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody id="tabla">
                <?php if (empty($clientes)): ?>
                  <tr><td colspan="4" class="empty">No hay clientes registrados.</td></tr>
                <?php else: ?>
                  <?php foreach ($clientes as $c): ?>
                  <tr class="fila-cliente"
                      data-identificacion="<?= strtolower(htmlspecialchars($c["identificacion"])) ?>"
                      data-nombre="<?= strtolower(htmlspecialchars($c["nombre"])) ?>">
                    <td><?= htmlspecialchars($c["identificacion"]) ?></td>
                    <td><?= htmlspecialchars($c["nombre"]) ?></td>
                    <td><?= htmlspecialchars($c["correo"]) ?></td>
                    <td>
                      <div class="actions">
                        <button
                          class="btn-icon btn-edit js-editar"
                          type="button"
                          title="Editar"
                          data-id="<?= htmlspecialchars($c["identificacion"], ENT_QUOTES) ?>"
                          data-nombre="<?= htmlspecialchars($c["nombre"], ENT_QUOTES) ?>"
                          data-correo="<?= htmlspecialchars($c["correo"], ENT_QUOTES) ?>"
                        >E</button>
                        <button
                          class="btn-icon btn-delete js-eliminar"
                          type="button"
                          title="Eliminar"
                          data-id="<?= htmlspecialchars($c["identificacion"], ENT_QUOTES) ?>"
                          data-nombre="<?= htmlspecialchars($c["nombre"], ENT_QUOTES) ?>"
                        >X</button>
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
    </div>
  </main>
</div>

<div class="overlay" id="overlay">
  <div class="clientes-modal">
    <h2 id="modalTitulo">Agregar Cliente</h2>

    <div class="form-group">
      <label>Identificacion</label>
      <input type="text" id="inp_id" placeholder="Ej: 12345678">
    </div>

    <div class="form-group">
      <label>Nombre</label>
      <input type="text" id="inp_nombre" placeholder="Ej: Juan Perez">
    </div>

    <div class="form-group">
      <label>Correo</label>
      <input type="email" id="inp_correo" placeholder="Ej: juan@email.com">
    </div>

    <div class="modal-actions">
      <button class="btn-cancel" type="button" onclick="cerrarModal()">Cancelar</button>
      <button class="btn-save" type="button" onclick="guardar()">Guardar</button>
    </div>
  </div>
</div>

<div class="notification-overlay" id="notificationOverlay">
  <div class="notification-modal">
    <div class="notification-icon" id="notificationIcon">
      <svg id="notificationSvg" viewBox="0 0 24 24" fill="none" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"></svg>
    </div>
    <div class="notification-title" id="notificationTitle">Titulo</div>
    <div class="notification-message" id="notificationMessage">Mensaje</div>
    <button class="notification-button" onclick="cerrarNotificacion()">Aceptar</button>
  </div>
</div>

<script src="../js/adminlte.min.js"></script>
<script src="/clientes/assets/js/script.js"></script>
</body>
</html>






