<?php
session_start();
include("../conecta.php");

if (!$conexion) {
    header("Location: ../index.php?error=db");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../index.php");
    exit();
}

$codigo = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
$contrasena = isset($_POST['contrasena']) ? trim($_POST['contrasena']) : '';

if ($codigo === "" || $contrasena === "") {
    header("Location: ../index.php?error=empty");
    exit();
}

function verificarLogin($conexion, $tabla, $codigo, $contrasena, $tipo) {

    $query = "SELECT codigo, nombre, contrasena 
              FROM $tabla 
              WHERE codigo = $1 
              LIMIT 1";

    $result = pg_query_params($conexion, $query, array($codigo));

    if ($result && pg_num_rows($result) > 0) {
        $user = pg_fetch_assoc($result);

        if (!$user["contrasena"] || trim($user["contrasena"]) === "") {
            return false;
        }

        if (trim($user["contrasena"]) === trim($contrasena)) {

            $_SESSION["usuario"] = $user["nombre"];
            $_SESSION["codigo"] = $user["codigo"];
            $_SESSION["tipo"] = $tipo;
            $_SESSION["login_time"] = time();

            return true;
        }
    }

    return false;
}
if (verificarLogin($conexion, "empleado", $codigo, $contrasena, "empleado")) {
    header("Location: ../menu.php");
    exit();
}
if (verificarLogin($conexion, "doctor", $codigo, $contrasena, "doctor")) {
    header("Location: ../menu_doc.php");
    exit();
}

header("Location: ../index.php?error=invalid");
exit();

?>