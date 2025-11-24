<?php
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['codigo'])) {
    header("Location: ../index.php");
    exit();
}

include("../conecta.php");

$nombre = pg_escape_string($conexion, $_POST['nombre']);
$via_adm = pg_escape_string($conexion, $_POST['via_adm']);
$presentacion = pg_escape_string($conexion, $_POST['presentacion']);
$fecha_cad = $_POST['fecha_cad'];

$query = "INSERT INTO medicamento (nombre, via_adm, presentacion, fecha_cad)
VALUES ('$nombre', '$via_adm', '$presentacion', '$fecha_cad')";

$resultado = pg_query($conexion, $query);
$error_message = $resultado ? "" : pg_last_error($conexion);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resultado - Medicamento</title>
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
        echo '<i class="fas fa-pills"></i>';
        echo '</div>';
        echo '<h2 class="result-title">¡Medicamento Registrado Exitosamente!</h2>';
        echo '<p class="result-message">El medicamento ha sido agregado correctamente al inventario.</p>';
        
        echo '<div class="success-details">';
        echo '<p><i class="fas fa-pills"></i><strong>Nombre:</strong> ' . htmlspecialchars($nombre) . '</p>';
        echo '<p><i class="fas fa-prescription-bottle-alt"></i><strong>Vía de Administración:</strong> ' . htmlspecialchars($via_adm) . '</p>';
        echo '<p><i class="fas fa-pills"></i><strong>Presentación:</strong> ' . htmlspecialchars($presentacion) . '</p>';
        echo '<p><i class="fas fa-calendar-times"></i><strong>Fecha de caducidad:</strong> ' . htmlspecialchars($fecha_cad) . '</p>';
        echo '</div>';
      } else {
        echo '<div class="error-icon">';
        echo '<i class="fas fa-times"></i>';
        echo '</div>';
        echo '<h2 class="result-title">Error al Registrar el medicamento</h2>';
        echo '<p class="result-message">No se pudo completar el registro del medicamento</p>';
        echo '<div class="error-detail">';
        echo '<p><i class="fas fa-exclamation-triangle"></i><strong>Detalle del error:</strong><br>' . htmlspecialchars($error_message) . '</p>';
        echo '</div>';
      }
      pg_close($conexion);
      ?>
      
      <div class="button-group">
        <a href="../insertar_medicamento.php" class="btn btn-primary" style="background: linear-gradient(135deg, #b69a59 0%, #ad7f44 100%); box-shadow: 0 4px 16px rgba(188, 129, 26, 0.3);">
          <i class="fas fa-plus"></i>
          <span>Registrar Otro Medicamento</span>
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