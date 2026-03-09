<?php
require_once dirname(__DIR__, 2) . '/config/conectdb.php';

function normalize_key(string $value): string
{
    $value = trim($value);
    if (function_exists('iconv')) {
        $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        if ($converted !== false) {
            $value = $converted;
        }
    }
    $value = strtolower($value);
    return preg_replace('/[^a-z0-9_]/', '', $value) ?? '';
}

function get_field_value(array $row, array $targets): string
{
    $normalizedMap = [];
    foreach ($row as $k => $v) {
        $normalizedMap[normalize_key((string) $k)] = (string) $v;
    }

    foreach ($targets as $target) {
        $key = normalize_key($target);
        if (array_key_exists($key, $normalizedMap)) {
            return $normalizedMap[$key];
        }
    }

    return '';
}

$layoutSidebarFile = dirname(__DIR__) . '/layout/fixed-sidebar.html';
$sidebarHtml = '';
if (is_file($layoutSidebarFile)) {
    $layoutContent = file_get_contents($layoutSidebarFile);
    if ($layoutContent !== false && preg_match('/<aside class="app-sidebar[\\s\\S]*?<\\/aside>/i', $layoutContent, $matches)) {
        $sidebarHtml = $matches[0];
    }
}

$productos = [];
$resultado = $conn->query('SELECT * FROM productos ORDER BY 1 ASC');
if ($resultado) {
    while ($fila = $resultado->fetch_assoc()) {
        $nombre = get_field_value($fila, ['nombre']);
        $descripcion = get_field_value($fila, ['descripcion', 'descripción']);
        $valorRaw = get_field_value($fila, ['valor', 'precio']);
        $valor = is_numeric($valorRaw) ? (float) $valorRaw : 0;

        $productos[] = [
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'valor' => $valor,
            'clave' => $nombre,
        ];
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Productos | Gestion de productos</title>
  <link rel="stylesheet" href="../css/adminlte.min.css">
  <link rel="stylesheet" href="/clientes/assets/css/productos.css">
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
      <ul class="navbar-nav ms-auto"></ul>
    </div>
  </nav>

  <?= $sidebarHtml ?>

  <main class="app-main">
    <div class="app-content">
      <div class="container-fluid">
        <div class="productos-main-container">
          <div class="header">
            <h1>Tabla de productos</h1>
            <button class="btn-add" type="button" onclick="abrirModal()">Agregar</button>
          </div>

          <div class="card">
            <table>
              <thead>
                <tr>
                  <th>Nombre</th>
                  <th>Descripcion</th>
                  <th>Valor</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody id="tabla">
                <?php if (empty($productos)): ?>
                  <tr><td colspan="4" class="empty">No hay productos registrados.</td></tr>
                <?php else: ?>
                  <?php foreach ($productos as $p): ?>
                    <?php
                    $clave = (string) $p['clave'];
                    $nombre = (string) $p['nombre'];
                    $descripcion = (string) $p['descripcion'];
                    $valor = (float) $p['valor'];
                    ?>
                    <tr class="fila-producto"
                        data-nombre="<?= strtolower(htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8')) ?>"
                        data-descripcion="<?= strtolower(htmlspecialchars($descripcion, ENT_QUOTES, 'UTF-8')) ?>">
                      <td><?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></td>
                      <td><?= htmlspecialchars($descripcion, ENT_QUOTES, 'UTF-8') ?></td>
                      <td>$<?= number_format($valor, 2, '.', ',') ?></td>
                      <td>
                        <div class="actions">
                          <button
                            class="btn-icon btn-edit"
                            type="button"
                            title="Editar"
                            onclick='abrirEditar(<?= json_encode($clave, JSON_UNESCAPED_UNICODE) ?>, <?= json_encode($nombre, JSON_UNESCAPED_UNICODE) ?>, <?= json_encode($descripcion, JSON_UNESCAPED_UNICODE) ?>, <?= json_encode($valor) ?>)'
                          >E</button>
                          <button
                            class="btn-icon btn-delete"
                            type="button"
                            title="Eliminar"
                            onclick='abrirEliminar(<?= json_encode($clave, JSON_UNESCAPED_UNICODE) ?>, <?= json_encode($nombre, JSON_UNESCAPED_UNICODE) ?>)'
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
  <div class="productos-modal">
    <h2 id="modalTitulo">Agregar Producto</h2>

    <div class="form-group">
      <label>Nombre</label>
      <input type="text" id="inp_nombre" placeholder="Ej: Pizza Margarita">
    </div>

    <div class="form-group">
      <label>Descripcion</label>
      <textarea id="inp_descripcion" placeholder="Ej: Pizza con tomate, mozzarella y albahaca" rows="3"></textarea>
    </div>

    <div class="form-group">
      <label>Valor</label>
      <input type="number" id="inp_valor" placeholder="Ej: 15000" step="0.01" min="0">
    </div>

    <div class="modal-actions">
      <button class="btn-cancel" type="button" onclick="cerrarModal()">Cancelar</button>
      <button class="btn-save" type="button" onclick="guardar()">Guardar</button>
    </div>
  </div>
</div>

<div class="overlay" id="deleteOverlay">
  <div class="productos-modal delete-modal">
    <h2>Eliminar Producto</h2>
    <p class="delete-text">Seguro que deseas eliminar <strong id="deleteProductName"></strong>?</p>
    <div class="modal-actions">
      <button class="btn-cancel" type="button" onclick="cerrarEliminar()">Cancelar</button>
      <button class="btn-delete-confirm" type="button" onclick="confirmarEliminar()">Eliminar</button>
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
    <button class="notification-button" type="button" onclick="cerrarNotificacion()">Aceptar</button>
  </div>
</div>

<script src="../js/adminlte.min.js"></script>
<script src="/clientes/assets/js/productos.js"></script>
</body>
</html>




