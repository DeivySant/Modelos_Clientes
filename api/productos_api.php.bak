<?php
require_once dirname(__DIR__) . '/config/conectdb.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Metodo no permitido']);
    exit;
}

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

function find_column_name(array $columns, array $targets): ?string
{
    $normalizedColumns = [];
    foreach ($columns as $column) {
        $normalizedColumns[normalize_key($column)] = $column;
    }

    foreach ($targets as $target) {
        $key = normalize_key($target);
        if (array_key_exists($key, $normalizedColumns)) {
            return $normalizedColumns[$key];
        }
    }

    return null;
}

function quote_identifier(string $column): string
{
    return '`' . str_replace('`', '``', $column) . '`';
}

$action = $_POST['action'] ?? '';
if ($action === '') {
    echo json_encode(['success' => false, 'error' => 'Accion no especificada']);
    exit;
}

$columnsResult = $conn->query('SHOW COLUMNS FROM productos');
if (!$columnsResult) {
    echo json_encode(['success' => false, 'error' => 'No se pudo leer la estructura de productos: ' . $conn->error]);
    exit;
}

$columns = [];
while ($row = $columnsResult->fetch_assoc()) {
    $columns[] = (string) $row['Field'];
}

$colNombre = find_column_name($columns, ['nombre']);
$colDescripcion = find_column_name($columns, ['descripcion', 'descripción']);
$colValor = find_column_name($columns, ['valor', 'precio']);

if ($colNombre === null || $colDescripcion === null || $colValor === null) {
    echo json_encode(['success' => false, 'error' => 'No se encontraron columnas esperadas en la tabla productos']);
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'delete') {
    $oldNombre = trim((string) ($_POST['old_nombre'] ?? ''));
    if ($oldNombre === '') {
        echo json_encode(['success' => false, 'error' => 'Clave de producto no proporcionada']);
        exit;
    }

    $oldNombreSql = $conn->real_escape_string($oldNombre);
    $sql = 'DELETE FROM productos WHERE ' . quote_identifier($colNombre) . " = '{$oldNombreSql}' LIMIT 1";
    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Producto eliminado correctamente']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al eliminar: ' . $conn->error]);
    }
    exit;
}

if ($action === 'insert') {
    $nombre = trim((string) ($_POST['nombre'] ?? ''));
    $descripcion = trim((string) ($_POST['descripcion'] ?? ''));
    $valor = trim((string) ($_POST['valor'] ?? ''));

    if ($nombre === '' || $descripcion === '' || $valor === '') {
        echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
        exit;
    }

    $nombreSql = $conn->real_escape_string($nombre);
    $descripcionSql = $conn->real_escape_string($descripcion);
    $valorSql = (float) $valor;

    $sql = 'INSERT INTO productos ('
        . quote_identifier($colNombre) . ', '
        . quote_identifier($colDescripcion) . ', '
        . quote_identifier($colValor) . ") VALUES ('{$nombreSql}', '{$descripcionSql}', '{$valorSql}')";

    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Producto agregado correctamente']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al insertar: ' . $conn->error]);
    }
    exit;
}

if ($action === 'update') {
    $oldNombre = trim((string) ($_POST['old_nombre'] ?? ''));
    $nombre = trim((string) ($_POST['nombre'] ?? ''));
    $descripcion = trim((string) ($_POST['descripcion'] ?? ''));
    $valor = trim((string) ($_POST['valor'] ?? ''));

    if ($oldNombre === '' || $nombre === '' || $descripcion === '' || $valor === '') {
        echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
        exit;
    }

    $oldNombreSql = $conn->real_escape_string($oldNombre);
    $nombreSql = $conn->real_escape_string($nombre);
    $descripcionSql = $conn->real_escape_string($descripcion);
    $valorSql = (float) $valor;

    $sql = 'UPDATE productos SET '
        . quote_identifier($colNombre) . "='{$nombreSql}', "
        . quote_identifier($colDescripcion) . "='{$descripcionSql}', "
        . quote_identifier($colValor) . "='{$valorSql}' "
        . 'WHERE ' . quote_identifier($colNombre) . "='{$oldNombreSql}' LIMIT 1";

    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Producto actualizado correctamente']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al actualizar: ' . $conn->error]);
    }
    exit;
}

echo json_encode(['success' => false, 'error' => 'Accion no valida: ' . $action]);
$conn->close();
?>
