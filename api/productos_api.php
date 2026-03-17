<?php
/**
 * API de Productos (CRUD)
 *
 * Responde en JSON y espera peticiones POST con un campo `action`:
 * - action=insert  -> POST: nombre, descripcion, valor, cliente
 * - action=update  -> POST: old_nombre, nombre, descripcion, valor, cliente
 * - action=delete  -> POST: old_nombre
 *
 * Nota: Este archivo intenta adaptarse a posibles variaciones de nombres de columnas
 * en la tabla `productos` (por ejemplo descripcion vs descripción, valor vs precio).
 */
require_once dirname(__DIR__) . '/config/conectdb.php';

// Configurar respuesta JSON
header('Content-Type: application/json');

// Validar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Metodo no permitido']);
    exit;
}

/**
 * Normaliza una cadena para poder comparar nombres de columnas.
 * - quita espacios
 * - intenta remover acentos (iconv)
 * - pasa a minusculas
 * - deja solo [a-z0-9_]
 */
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

/**
 * Busca el nombre real de una columna en la tabla, aceptando varias alternativas.
 *
 * Ejemplo:
 *   find_column_name(['Nombre', 'Descripción'], ['descripcion','descripción'])
 *   -> devuelve 'Descripción'
 */
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

/**
 * Escapa un identificador SQL (nombre de columna) usando backticks.
 * Esto evita errores si la columna tiene caracteres especiales o coincide con una palabra reservada.
 */
function quote_identifier(string $column): string
{
    return '`' . str_replace('`', '``', $column) . '`';
}

// Validar que exista la acción
$action = $_POST['action'] ?? '';
if ($action === '') {
    echo json_encode(['success' => false, 'error' => 'Accion no especificada']);
    exit;
}

// Leer estructura de la tabla para encontrar el nombre real de las columnas esperadas
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
$colCliente = find_column_name($columns, ['cliente', 'cliente_id', 'id_cliente', 'idcliente', 'identificacion_cliente', 'cliente_identificacion']);

if ($colNombre === null || $colDescripcion === null || $colValor === null || $colCliente === null) {
    echo json_encode([
        'success' => false,
        'error' => 'Falta la columna de cliente en la tabla productos. Agrega una columna llamada `cliente` (VARCHAR) y vuelve a intentar.',
    ]);
    exit;
}

// ============================================================================
// ELIMINAR
// ============================================================================
if ($action === 'delete') {
    if (!array_key_exists('old_nombre', $_POST)) {
        echo json_encode(['success' => false, 'error' => 'Clave de producto no proporcionada']);
        exit;
    }

    $oldNombre = (string) $_POST['old_nombre'];

    $oldNombreSql = $conn->real_escape_string($oldNombre);
    $sql = 'DELETE FROM productos WHERE ' . quote_identifier($colNombre) . " = '{$oldNombreSql}' LIMIT 1";
    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Producto eliminado correctamente']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al eliminar: ' . $conn->error]);
    }
    exit;
}

// ============================================================================
// INSERTAR
// ============================================================================
if ($action === 'insert') {
    $nombre = trim((string) ($_POST['nombre'] ?? ''));
    $descripcion = trim((string) ($_POST['descripcion'] ?? ''));
    $valor = trim((string) ($_POST['valor'] ?? ''));
    $cliente = trim((string) ($_POST['cliente'] ?? ''));

    if ($nombre === '' || $descripcion === '' || $valor === '') {
        echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
        exit;
    }

    $nombreSql = $conn->real_escape_string($nombre);
    $descripcionSql = $conn->real_escape_string($descripcion);
    $valorSql = (float) $valor;
    $clienteSql = $conn->real_escape_string($cliente);

    $clienteValueSql = 'NULL';
    if ($cliente !== '') {
        $exists = $conn->query("SELECT identificacion FROM clientes WHERE identificacion = '{$clienteSql}' LIMIT 1");
        if (!$exists || $exists->num_rows === 0) {
            echo json_encode(['success' => false, 'error' => 'El cliente seleccionado no existe']);
            exit;
        }
        $clienteValueSql = "'{$clienteSql}'";
    }

    $sql = 'INSERT INTO productos ('
        . quote_identifier($colNombre) . ', '
        . quote_identifier($colDescripcion) . ', '
        . quote_identifier($colValor) . ', '
        . quote_identifier($colCliente) . ") VALUES ('{$nombreSql}', '{$descripcionSql}', '{$valorSql}', {$clienteValueSql})";

    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Producto agregado correctamente']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al insertar: ' . $conn->error]);
    }
    exit;
}

// ============================================================================
// ACTUALIZAR
// ============================================================================
if ($action === 'update') {
    $oldNombre = trim((string) ($_POST['old_nombre'] ?? ''));
    $nombre = trim((string) ($_POST['nombre'] ?? ''));
    $descripcion = trim((string) ($_POST['descripcion'] ?? ''));
    $valor = trim((string) ($_POST['valor'] ?? ''));
    $cliente = trim((string) ($_POST['cliente'] ?? ''));

    if ($oldNombre === '' || $nombre === '' || $descripcion === '' || $valor === '') {
        echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
        exit;
    }

    $oldNombreSql = $conn->real_escape_string($oldNombre);
    $nombreSql = $conn->real_escape_string($nombre);
    $descripcionSql = $conn->real_escape_string($descripcion);
    $valorSql = (float) $valor;
    $clienteSql = $conn->real_escape_string($cliente);

    $clienteSetSql = quote_identifier($colCliente) . '=NULL';
    if ($cliente !== '') {
        $exists = $conn->query("SELECT identificacion FROM clientes WHERE identificacion = '{$clienteSql}' LIMIT 1");
        if (!$exists || $exists->num_rows === 0) {
            echo json_encode(['success' => false, 'error' => 'El cliente seleccionado no existe']);
            exit;
        }
        $clienteSetSql = quote_identifier($colCliente) . "='{$clienteSql}'";
    }

    $sql = 'UPDATE productos SET '
        . quote_identifier($colNombre) . "='{$nombreSql}', "
        . quote_identifier($colDescripcion) . "='{$descripcionSql}', "
        . quote_identifier($colValor) . "='{$valorSql}', "
        . $clienteSetSql . ' '
        . 'WHERE ' . quote_identifier($colNombre) . "='{$oldNombreSql}' LIMIT 1";

    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Producto actualizado correctamente']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al actualizar: ' . $conn->error]);
    }
    exit;
}

// ============================================================================
// ACCION NO RECONOCIDA
// ============================================================================
echo json_encode(['success' => false, 'error' => 'Accion no valida: ' . $action]);
$conn->close();
?>
