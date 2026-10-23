
<?php
$servidor = "127.0.0.1";
$puerto = "3307";
$usuario = "root";
$contrasena = "";
$baseDatos = "transmetro";

try {
    $conexion = new PDO(
        "mysql:host=$servidor;port=$puerto;dbname=$baseDatos;charset=utf8mb4",
        $usuario,
        $contrasena
    );

    $conexion->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit("No se pudo conectar con la base de datos.");
}
?>
