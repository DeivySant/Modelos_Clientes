<?php
$host     = "localhost";
$usuario  = "sant";       
$password = "1234";         
$base     = "restaurante";

$conn = new mysqli($host, $usuario, $password, $base);

if ($conn->connect_error) {
    die(json_encode([
        "error" => "Conexión fallida: " . $conn->connect_error
    ]));
}

$conn->set_charset("utf8");
?>