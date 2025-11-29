<?php
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['codigo'])) {
    header("Location: ../index.php");
    exit();
}

include("../conecta.php");

$nombre = pg_escape_string($conexion, $_POST['nombre']);
$direccion = pg_escape_string($conexion, $_POST['direccion']);
$telefono = pg_escape_string($conexion, $_POST['telefono']);
$fecha_nac = $_POST['fecha_nacimiento'];
$sexo = $_POST['sexo'];
$sueldo = floatval($_POST['sueldo']);
$turno = pg_escape_string($conexion, $_POST['turno']);
$contrasena = pg_escape_string($conexion, $_POST['contrasena']);

$query = "INSERT INTO empleado (nombre, direccion, telefono, fecha_nac, sexo, sueldo, turno, contrasena)
VALUES ('$nombre', '$direccion', '$telefono', '$fecha_nac', '$sexo', $sueldo, '$turno', '$contrasena')";

$resultado = pg_query($conexion, $query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resultado - Empleado</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../Styles/form.css">
  <link rel="stylesheet" href="../Styles/resultado.css">
</head>
<body>
  <div class="container result-container">
    <div class="form-card result-card">
      <?php
      if ($resultado) {
        echo '<div class="success-icon">';
        echo '<i class="fas fa-check"></i>';
        echo '</div>';
        echo '<h2 class="result-title">¡Empleado Registrado Exitosamente!</h2>';
        echo '<p class="result-message">El empleado ha sido agregado correctamente al sistema</p>';
        
        echo '<div class="success-details">';
        echo '<p><i class="fas fa-user"></i><strong>Nombre:</strong> ' . htmlspecialchars($nombre) . '</p>';
        echo '<p><i class="fas fa-phone"></i><strong>Teléfono:</strong> ' . htmlspecialchars($telefono) . '</p>';
        echo '<p><i class="fas fa-dollar-sign"></i><strong>Sueldo:</strong> $' . number_format($sueldo, 2) . '</p>';
        echo '<p><i class="fas fa-clock"></i><strong>Turno:</strong> ' . htmlspecialchars($turno) . '</p>';
        echo '</div>';
      } else {
        echo '<div class="error-icon">';
        echo '<i class="fas fa-times"></i>';
        echo '</div>';
        echo '<h2 class="result-title">Error al Registrar Empleado</h2>';
        echo '<p class="result-message">No se pudo completar el registro del empleado</p>';
        echo '<div class="error-detail">';
        echo '<p><i class="fas fa-exclamation-triangle"></i><strong>Detalle del error:</strong><br>' . htmlspecialchars(pg_last_error($conexion)) . '</p>';
        echo '</div>';
      }
      pg_close($conexion);
      ?>
      
      <div class="button-group">
        <a href="../insertar_empleado.php" class="btn btn-primary">
          <i class="fas fa-plus"></i>
          <span>Registrar Otro Empleado</span>
        </a>
        <a href="../menu_admin.php" class="btn btn-secondary">
          <i class="fas fa-home"></i>
          <span>Volver al Menú</span>
        </a>
      </div>
    </div>
  </div>
</body>
</html>