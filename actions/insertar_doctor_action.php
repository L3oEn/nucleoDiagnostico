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
$especialidad = pg_escape_string($conexion, $_POST['especialidad']);
$fecha_nac = $_POST['fecha_nac'];
$sexo = $_POST['sexo'];
$contrasena = pg_escape_string($conexion, $_POST['contrasena']);

$query = "INSERT INTO doctor (nombre, direccion, telefono, especialidad, fecha_nac, sexo, contrasena)
VALUES ('$nombre', '$direccion', '$telefono', '$especialidad', '$fecha_nac', '$sexo', '$contrasena')";

$resultado = pg_query($conexion, $query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resultado - Doctor</title>
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
        echo '<h2 class="result-title">¡Doctor Registrado Exitosamente!</h2>';
        echo '<p class="result-message">El doctor ha sido agregado correctamente al sistema</p>';
        
        echo '<div class="success-details">';
        echo '<p><i class="fas fa-user-md"></i><strong>Nombre:</strong> ' . htmlspecialchars($nombre) . '</p>';
        echo '<p><i class="fas fa-stethoscope"></i><strong>Especialidad:</strong> ' . htmlspecialchars($especialidad) . '</p>';
        echo '<p><i class="fas fa-phone"></i><strong>Teléfono:</strong> ' . htmlspecialchars($telefono) . '</p>';
        echo '<p><i class="fas fa-map-marker-alt"></i><strong>Dirección:</strong> ' . htmlspecialchars($direccion) . '</p>';
        echo '</div>';
      } else {
        echo '<div class="error-icon">';
        echo '<i class="fas fa-times"></i>';
        echo '</div>';
        echo '<h2 class="result-title">Error al Registrar Doctor</h2>';
        echo '<p class="result-message">No se pudo completar el registro del doctor</p>';
        echo '<div class="error-detail">';
        echo '<p><i class="fas fa-exclamation-triangle"></i><strong>Detalle del error:</strong><br>' . htmlspecialchars(pg_last_error($conexion)) . '</p>';
        echo '</div>';
      }
      pg_close($conexion);
      ?>
      
      <div class="button-group">
        <a href="../insertar_doctor.php" class="btn btn-primary">
          <i class="fas fa-plus"></i>
          <span>Registrar Otro Doctor</span>
        </a>
        <a href="../menu.php" class="btn btn-secondary">
          <i class="fas fa-home"></i>
          <span>Volver al Menú</span>
        </a>
      </div>
    </div>
  </div>
</body>
</html>