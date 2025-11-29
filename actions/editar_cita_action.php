<?php
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['codigo'])) {
    header("Location: ../index.php");
    exit();
}

include("../conecta.php");

// Recibir datos del formulario
$cita_id = intval($_POST['cita_id']);
$id_paciente = intval($_POST['id_paciente']);
$id_doctor = intval($_POST['id_doctor']);
$fecha = $_POST['fecha'];
$hora = $_POST['hora'];

// Verificar que la cita existe
$check_cita = pg_query($conexion, "SELECT * FROM citas WHERE id_cita = $cita_id");
if (!$check_cita || pg_num_rows($check_cita) == 0) {
    header("Location: ../consultar_citas.php?error=noexiste");
    exit();
}

// Verificar que el paciente existe
$check_paciente = pg_query($conexion, "SELECT codigo, nombre FROM paciente WHERE codigo = $id_paciente");
$paciente_existe = pg_num_rows($check_paciente) > 0;
$paciente_info = $paciente_existe ? pg_fetch_assoc($check_paciente) : null;

// Verificar que el doctor existe
$check_doctor = pg_query($conexion, "SELECT codigo, nombre, especialidad FROM doctor WHERE codigo = $id_doctor");
$doctor_existe = pg_num_rows($check_doctor) > 0;
$doctor_info = $doctor_existe ? pg_fetch_assoc($check_doctor) : null;

$error_message = "";
$resultado = false;

if (!$paciente_existe) {
    $error_message = "El paciente especificado ($id_paciente) no existe en el sistema.";
} elseif (!$doctor_existe) {
    $error_message = "El doctor especificado ($id_doctor) no existe en el sistema.";
} else {
    // Verificar conflictos de horario (excluyendo la cita actual)
    $hora_inicio = $hora;
    $hora_fin = date('H:i:s', strtotime($hora) + 3600);
    
    $query_conflicto = "SELECT c.hora, p.nombre as paciente_nombre 
                        FROM citas c 
                        INNER JOIN paciente p ON c.id_paciente = p.codigo
                        WHERE c.id_doctor = $id_doctor 
                        AND c.fecha = '$fecha'
                        AND c.id_cita != $cita_id
                        AND (
                            c.hora = '$hora_inicio'
                            OR
                            (c.hora < '$hora_inicio' AND (c.hora + interval '1 hour') > '$hora_inicio')
                            OR
                            (c.hora > '$hora_inicio' AND c.hora < '$hora_fin')
                        )";
    
    $check_conflicto = pg_query($conexion, $query_conflicto);
    
    if (pg_num_rows($check_conflicto) > 0) {
        $conflicto = pg_fetch_assoc($check_conflicto);
        $hora_conflicto = date('h:i A', strtotime($conflicto['hora']));
        $error_message = "Conflicto de horario: El doctor ya tiene una cita con " . 
                        htmlspecialchars($conflicto['paciente_nombre']) . 
                        " a las $hora_conflicto. Las citas duran 1 hora.";
    } else {
        // Query para actualizar la cita
        $query = "UPDATE citas 
                  SET id_paciente = $id_paciente, 
                      id_doctor = $id_doctor, 
                      fecha = '$fecha', 
                      hora = '$hora'
                  WHERE id_cita = $cita_id";
        
        $resultado = pg_query($conexion, $query);
        
        if (!$resultado) {
            $error_message = pg_last_error($conexion);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resultado - Modificación de Cita</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../Styles/form.css">
  <link rel="stylesheet" href="../Styles/resultado.css">
</head>
<body>
  <div class="container result-container">
    <div class="form-card result-card">
      <?php
      if ($resultado) {
        $fecha_formateada = date('d/m/Y', strtotime($fecha));
        $hora_formateada = date('h:i A', strtotime($hora));
        $hora_fin_formateada = date('h:i A', strtotime($hora) + 3600);
        
        echo '<div class="success-icon" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); box-shadow: 0 6px 20px rgba(155, 89, 182, 0.35);">';
        echo '<i class="fas fa-check"></i>';
        echo '</div>';
        echo '<h2 class="result-title">¡Cita Modificada Exitosamente!</h2>';
        echo '<p class="result-message">Los cambios han sido guardados correctamente</p>';
        
        echo '<div class="success-details">';
        echo '<p><i class="fas fa-hashtag"></i><strong>ID Cita:</strong> ' . $cita_id . '</p>';
        echo '<p><i class="fas fa-user-injured"></i><strong>Paciente:</strong> ' . htmlspecialchars($paciente_info['nombre']) . '</p>';
        echo '<p><i class="fas fa-user-md"></i><strong>Doctor:</strong> ' . htmlspecialchars($doctor_info['nombre']) . '</p>';
        echo '<p><i class="fas fa-stethoscope"></i><strong>Especialidad:</strong> ' . htmlspecialchars($doctor_info['especialidad']) . '</p>';
        echo '</div>';
        
        echo '<div class="info-details" style="background: linear-gradient(135deg, #e8daef 0%, #d7bde2 100%); border-left-color: #9b59b6;">';
        echo '<p style="color: #6c3483;"><i class="fas fa-calendar-day" style="color: #9b59b6;"></i><strong>Nueva Fecha:</strong> ' . $fecha_formateada . '</p>';
        echo '<p style="color: #6c3483;"><i class="fas fa-clock" style="color: #9b59b6;"></i><strong>Nuevo Horario:</strong> ' . $hora_formateada . ' - ' . $hora_fin_formateada . '</p>';
        echo '</div>';
      } else {
        echo '<div class="error-icon">';
        echo '<i class="fas fa-times"></i>';
        echo '</div>';
        echo '<h2 class="result-title">Error al Modificar Cita</h2>';
        echo '<p class="result-message">No se pudieron guardar los cambios</p>';
        echo '<div class="error-detail">';
        echo '<p><i class="fas fa-exclamation-triangle"></i><strong>Detalle del error:</strong><br>' . htmlspecialchars($error_message) . '</p>';
        echo '</div>';
      }
      pg_close($conexion);
      ?>
      
      <div class="button-group">
        <a href="../consultar_citas.php" class="btn btn-primary" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); box-shadow: 0 4px 16px rgba(155, 89, 182, 0.3);">
          <i class="fas fa-list"></i>
          <span>Ver Todas las Citas</span>
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