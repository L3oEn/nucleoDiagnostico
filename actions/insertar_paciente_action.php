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
$fecha_nac = $_POST['fecha_nac'];
$sexo = $_POST['sexo'];
$edad = intval($_POST['edad']);
$estatura = floatval($_POST['estatura']);

$query = "INSERT INTO paciente (nombre, direccion, telefono, fecha_nac, sexo, edad, estatura)
VALUES ('$nombre', '$direccion', '$telefono', '$fecha_nac', '$sexo', $edad, $estatura)";

$resultado = pg_query($conexion, $query);
$error_message = $resultado ? "" : pg_last_error($conexion);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resultado - Paciente</title>
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
        echo '<h2 class="result-title">¡Paciente Registrado Exitosamente!</h2>';
        echo '<p class="result-message">El paciente ha sido agregado correctamente al sistema médico</p>';
        
        echo '<div class="success-details">';
        echo '<p><i class="fas fa-user"></i><strong>Nombre:</strong> ' . htmlspecialchars($nombre) . '</p>';
        echo '<p><i class="fas fa-phone"></i><strong>Teléfono:</strong> ' . htmlspecialchars($telefono) . '</p>';
        echo '<p><i class="fas fa-calendar"></i><strong>Fecha de nacimiento:</strong> ' . htmlspecialchars($fecha_nac) . '</p>';
        echo '<p><i class="fas fa-user-clock"></i><strong>Edad:</strong> ' . $edad . ' años</p>';
        echo '<p><i class="fas fa-ruler-vertical"></i><strong>Estatura:</strong> ' . number_format($estatura, 2) . ' m</p>';
        echo '</div>';
      } else {
        echo '<div class="error-icon">';
        echo '<i class="fas fa-times"></i>';
        echo '</div>';
        echo '<h2 class="result-title">Error al Registrar Paciente</h2>';
        echo '<p class="result-message">No se pudo completar el registro del paciente</p>';
        echo '<div class="error-detail">';
        echo '<p><i class="fas fa-exclamation-triangle"></i><strong>Detalle del error:</strong><br>' . htmlspecialchars($error_message) . '</p>';
        echo '</div>';
      }
      pg_close($conexion);
      ?>
      
      <div class="button-group">
        <a href="../insertar_paciente.php" class="btn btn-primary" style="background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%); box-shadow: 0 4px 16px rgba(46, 204, 113, 0.3);">
          <i class="fas fa-plus"></i>
          <span>Registrar Otro Paciente</span>
        </a>
        <a href="../menu_empleado.php" class="btn btn-secondary">
          <i class="fas fa-home"></i>
          <span>Volver al Menú</span>
        </a>
      </div>
    </div>
  </div>
</body>
</html>