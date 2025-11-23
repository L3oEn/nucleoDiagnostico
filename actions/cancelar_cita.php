<?php
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['codigo'])) {
    header("Location: ../index.php");
    exit();
}

include("../conecta.php");

// Obtener ID de la cita
$cita_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($cita_id == 0) {
    header("Location: ../consultar_citas.php?error=invalid");
    exit();
}

// Obtener información de la cita antes de eliminarla
$query_info = "SELECT c.*, p.nombre as nombre_paciente, d.nombre as nombre_doctor
               FROM citas c
               INNER JOIN paciente p ON c.id_paciente = p.codigo
               INNER JOIN doctor d ON c.id_doctor = d.codigo
               WHERE c.id_cita = $cita_id";
$resultado_info = pg_query($conexion, $query_info);

if (!$resultado_info || pg_num_rows($resultado_info) == 0) {
    header("Location: ../consultar_citas.php?error=notfound");
    exit();
}

$cita_info = pg_fetch_assoc($resultado_info);

// Eliminar la cita
$query_delete = "DELETE FROM citas WHERE id_cita = $cita_id";
$resultado = pg_query($conexion, $query_delete);
$error_message = $resultado ? "" : pg_last_error($conexion);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resultado - Cancelación de Cita</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../Styles/form.css">
  <link rel="stylesheet" href="../Styles/resultado.css">
</head>
<body>
  <div class="container result-container">
    <div class="form-card result-card">
      <?php
      if ($resultado) {
        $fecha_formateada = date('d/m/Y', strtotime($cita_info['fecha']));
        $hora_formateada = date('h:i A', strtotime($cita_info['hora']));
        
        echo '<div class="warning-icon">';
        echo '<i class="fas fa-calendar-times"></i>';
        echo '</div>';
        echo '<h2 class="result-title">Cita Cancelada</h2>';
        echo '<p class="result-message">La cita médica ha sido cancelada del sistema</p>';
        
        echo '<div class="error-detail" style="background: linear-gradient(135deg, #fff3cd 0%, #ffe8a1 100%);">';
        echo '<p><i class="fas fa-info-circle"></i><strong>Información de la cita cancelada:</strong></p>';
        echo '<p style="margin-top: 10px;"><strong>ID:</strong> ' . $cita_id . '</p>';
        echo '<p><strong>Paciente:</strong> ' . htmlspecialchars($cita_info['nombre_paciente']) . '</p>';
        echo '<p><strong>Doctor:</strong> ' . htmlspecialchars($cita_info['nombre_doctor']) . '</p>';
        echo '<p><strong>Fecha:</strong> ' . $fecha_formateada . '</p>';
        echo '<p><strong>Hora:</strong> ' . $hora_formateada . '</p>';
        echo '</div>';
      } else {
        echo '<div class="error-icon">';
        echo '<i class="fas fa-times"></i>';
        echo '</div>';
        echo '<h2 class="result-title">Error al Cancelar Cita</h2>';
        echo '<p class="result-message">No se pudo cancelar la cita</p>';
        echo '<div class="error-detail">';
        echo '<p><i class="fas fa-exclamation-triangle"></i><strong>Detalle del error:</strong><br>' . htmlspecialchars($error_message) . '</p>';
        echo '</div>';
      }
      pg_close($conexion);
      ?>
      
      <div class="button-group">
        <a href="../insertar_cita.php" class="btn btn-primary" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); box-shadow: 0 4px 16px rgba(155, 89, 182, 0.3);">
          <i class="fas fa-calendar-plus"></i>
          <span>Agendar Nueva Cita</span>
        </a>
        <a href="../consultar_citas.php" class="btn btn-secondary">
          <i class="fas fa-list"></i>
          <span>Ver Citas</span>
        </a>
      </div>
    </div>
  </div>
</body>
</html>