<?php
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['codigo'])) {
    header("Location: ../index.php");
    exit();
}

include("../conecta.php");

// Recibir datos del formulario
$id_paciente = intval($_POST['id_paciente']);
$id_doctor = intval($_POST['id_doctor']);
$fecha = $_POST['fecha'];
$hora = $_POST['hora'];

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
    // Verificar conflictos de horario considerando que cada cita dura 1 hora
    // Una cita ocupa desde la hora seleccionada hasta 1 hora después
    
    // Convertir la hora a timestamp para hacer cálculos
    $hora_inicio = $hora;
    $hora_fin = date('H:i:s', strtotime($hora) + 3600); // +1 hora (3600 segundos)
    
    // Verificar si hay conflictos:
    // 1. Citas que empiezan en el mismo horario
    // 2. Citas que empiezan antes pero terminan después de nuestra hora de inicio
    // 3. Citas que empiezan después pero antes de nuestra hora de fin
    $query_conflicto = "SELECT c.hora, p.nombre as paciente_nombre 
                        FROM citas c 
                        INNER JOIN paciente p ON c.id_paciente = p.codigo
                        WHERE c.id_doctor = $id_doctor 
                        AND c.fecha = '$fecha'
                        AND (
                            -- Caso 1: Misma hora exacta
                            c.hora = '$hora_inicio'
                            OR
                            -- Caso 2: La cita existente empieza antes pero termina después de nuestra hora inicio
                            (c.hora < '$hora_inicio' AND (c.hora + interval '1 hour') > '$hora_inicio')
                            OR
                            -- Caso 3: La cita existente empieza después de nuestro inicio pero antes de nuestro fin
                            (c.hora > '$hora_inicio' AND c.hora < '$hora_fin')
                        )";
    
    $check_conflicto = pg_query($conexion, $query_conflicto);
    
    if (pg_num_rows($check_conflicto) > 0) {
        $conflicto = pg_fetch_assoc($check_conflicto);
        $hora_conflicto = date('h:i A', strtotime($conflicto['hora']));
        $error_message = "Conflicto de horario: El doctor ya tiene una cita con " . 
                        htmlspecialchars($conflicto['paciente_nombre']) . 
                        " a las $hora_conflicto. Las citas duran 1 hora. Por favor, seleccione otro horario.";
    } else {
        // Query para insertar en la tabla citas
        $query = "INSERT INTO citas (id_paciente, id_doctor, fecha, hora)
        VALUES ($id_paciente, $id_doctor, '$fecha', '$hora')";
        
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
  <title>Resultado - Cita</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../Styles/form.css">
  <link rel="stylesheet" href="../Styles/resultado.css">
</head>
<body>
  <div class="container result-container">
    <div class="form-card result-card">
      <?php
      if ($resultado) {
        // Formatear fecha y hora para mostrar
        $fecha_formateada = date('d/m/Y', strtotime($fecha));
        $hora_formateada = date('h:i A', strtotime($hora));
        $hora_fin_formateada = date('h:i A', strtotime($hora) + 3600); // +1 hora
        
        echo '<div class="success-icon" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); box-shadow: 0 6px 20px rgba(155, 89, 182, 0.35);">';
        echo '<i class="fas fa-check"></i>';
        echo '</div>';
        echo '<h2 class="result-title">¡Cita Agendada Exitosamente!</h2>';
        echo '<p class="result-message">La cita médica ha sido registrada correctamente en el sistema</p>';
        
        echo '<div class="success-details">';
        echo '<p><i class="fas fa-user-injured"></i><strong>Paciente:</strong> ' . htmlspecialchars($paciente_info['nombre']) . ' (ID: ' . $id_paciente . ')</p>';
        echo '<p><i class="fas fa-user-md"></i><strong>Doctor:</strong> ' . htmlspecialchars($doctor_info['nombre']) . '</p>';
        echo '<p><i class="fas fa-stethoscope"></i><strong>Especialidad:</strong> ' . htmlspecialchars($doctor_info['especialidad']) . '</p>';
        echo '</div>';
        
        echo '<div class="info-details" style="background: linear-gradient(135deg, #e8daef 0%, #d7bde2 100%); border-left-color: #9b59b6;">';
        echo '<p style="color: #6c3483;"><i class="fas fa-calendar-day" style="color: #9b59b6;"></i><strong>Fecha:</strong> ' . $fecha_formateada . '</p>';
        echo '<p style="color: #6c3483;"><i class="fas fa-clock" style="color: #9b59b6;"></i><strong>Horario:</strong> ' . $hora_formateada . ' - ' . $hora_fin_formateada . ' (1 hora)</p>';
        echo '</div>';
      } else {
       echo '<div class="error-detail">';
      echo '<p><i class="fas fa-exclamation-triangle"></i><strong>Detalle del error:</strong><br>' . htmlspecialchars($error_message) . '</p>';
      echo '<p><a href="../ver_disponibilidad.php?doctor_id=' . $id_doctor . '&fecha=' . $fecha . '" class="btn btn-info"><i class="fas fa-calendar-alt"></i> Ver disponibilidad del doctor</a></p>';
    	echo '</div>';
;
      }
      pg_close($conexion);
      ?>
      
      <div class="button-group">
        <a href="../insertar_cita.php" class="btn btn-primary" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); box-shadow: 0 4px 16px rgba(155, 89, 182, 0.3);">
          <i class="fas fa-calendar-plus"></i>
          <span>Agendar Otra Cita</span>
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
