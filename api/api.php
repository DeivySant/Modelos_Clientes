<?php
require_once "../config/conectdb.php";

// Configurar respuesta JSON
header('Content-Type: application/json');

// Validar que sea una petición POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "error" => "Método no permitido"]);
    exit;
}

// Validar que exista la acción
if (!isset($_POST["action"])) {
    echo json_encode(["success" => false, "error" => "Acción no especificada"]);
    exit;
}

$action = $_POST["action"];

// ═══════════════════════════════════════════════════════════
// ELIMINAR
// ═══════════════════════════════════════════════════════════
if ($action === "delete") {
    if (!isset($_POST["identificacion"])) {
        echo json_encode(["success" => false, "error" => "Identificación no proporcionada"]);
        exit;
    }

    $id  = $conn->real_escape_string($_POST["identificacion"]);
    $sql = "DELETE FROM clientes WHERE identificacion = '$id'";
    
    if ($conn->query($sql)) {
        echo json_encode(["success" => true, "message" => "Cliente eliminado correctamente"]);
    } else {
        echo json_encode(["success" => false, "error" => "Error al eliminar: " . $conn->error]);
    }
    exit;
}

// ═══════════════════════════════════════════════════════════
// INSERTAR
// ═══════════════════════════════════════════════════════════
if ($action === "insert") {
    if (!isset($_POST["identificacion"]) || !isset($_POST["nombre"]) || !isset($_POST["correo"])) {
        echo json_encode(["success" => false, "error" => "Datos incompletos"]);
        exit;
    }

    $id     = $conn->real_escape_string($_POST["identificacion"]);
    $nombre = $conn->real_escape_string($_POST["nombre"]);
    $correo = $conn->real_escape_string($_POST["correo"]);
    
    // Validar que la identificación no exista
    $check = $conn->query("SELECT identificacion FROM clientes WHERE identificacion = '$id'");
    if ($check->num_rows > 0) {
        echo json_encode(["success" => false, "error" => "La identificación ya existe"]);
        exit;
    }
    
    $sql = "INSERT INTO clientes (identificacion, nombre, correo)
            VALUES ('$id', '$nombre', '$correo')";
    
    if ($conn->query($sql)) {
        echo json_encode(["success" => true, "message" => "Cliente agregado correctamente"]);
    } else {
        echo json_encode(["success" => false, "error" => "Error al insertar: " . $conn->error]);
    }
    exit;
}

// ═══════════════════════════════════════════════════════════
// ACTUALIZAR
// ═══════════════════════════════════════════════════════════
if ($action === "update") {
    if (!isset($_POST["identificacion_vieja"]) || !isset($_POST["identificacion"]) || 
        !isset($_POST["nombre"]) || !isset($_POST["correo"])) {
        echo json_encode(["success" => false, "error" => "Datos incompletos"]);
        exit;
    }

    $id_vieja = $conn->real_escape_string($_POST["identificacion_vieja"]);
    $id_nueva = $conn->real_escape_string($_POST["identificacion"]);
    $nombre   = $conn->real_escape_string($_POST["nombre"]);
    $correo   = $conn->real_escape_string($_POST["correo"]);
    
    // Si se cambió la identificación, validar que la nueva no exista
    if ($id_vieja !== $id_nueva) {
        $check = $conn->query("SELECT identificacion FROM clientes WHERE identificacion = '$id_nueva'");
        if ($check->num_rows > 0) {
            echo json_encode(["success" => false, "error" => "La identificación ya existe"]);
            exit;
        }
    }
    
    $sql = "UPDATE clientes 
            SET identificacion='$id_nueva', nombre='$nombre', correo='$correo'
            WHERE identificacion='$id_vieja'";
    
    if ($conn->query($sql)) {
        echo json_encode(["success" => true, "message" => "Cliente actualizado correctamente"]);
    } else {
        echo json_encode(["success" => false, "error" => "Error al actualizar: " . $conn->error]);
    }
    exit;
}

// ═══════════════════════════════════════════════════════════
// ACCIÓN NO RECONOCIDA
// ═══════════════════════════════════════════════════════════
echo json_encode(["success" => false, "error" => "Acción no válida: " . $action]);
$conn->close();
?>