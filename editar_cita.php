<?php
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['codigo'])) {
    header("Location: index.php");
    exit();
}

include("conecta.php");

// Obtener ID de la cita
$cita_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($cita_id == 0) {
    header("Location: consultar_citas.php");
    exit();
}

// Obtener datos de la cita
$query_cita = "SELECT c.*, p.nombre as nombre_paciente, d.nombre as nombre_doctor, d.especialidad
               FROM citas c
               INNER JOIN paciente p ON c.id_paciente = p.codigo
               INNER JOIN doctor d ON c.id_doctor = d.codigo
               WHERE c.id_cita = $cita_id";

$resultado_cita = pg_query($conexion, $query_cita);

if (!$resultado_cita || pg_num_rows($resultado_cita) == 0) {
    header("Location: consultar_citas.php");
    exit();
}

$cita = pg_fetch_assoc($resultado_cita);

// Verificar que la cita no sea pasada
$hoy = date('Y-m-d');
$ahora = date('H:i:s');
if ($cita['fecha'] < $hoy || ($cita['fecha'] == $hoy && $cita['hora'] < $ahora)) {
    header("Location: consultar_citas.php?error=pasada");
    exit();
}

// Obtener lista de pacientes
$query_pacientes = "SELECT codigo, nombre FROM paciente ORDER BY nombre ASC";
$resultado_pacientes = pg_query($conexion, $query_pacientes);

// Obtener lista de doctores
$query_doctores = "SELECT codigo, nombre, especialidad FROM doctor ORDER BY nombre ASC";
$resultado_doctores = pg_query($conexion, $query_doctores);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Modificar Cita - Nucleo Diagnóstico</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="Styles/form.css">
</head>
<body>
  <div class="container">
    <div class="form-card">
      <!-- Encabezado del formulario -->
      <div class="form-header">
        <div class="header-icon" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);">
          <i class="fas fa-calendar-edit"></i>
        </div>
        <h2>Modificar Cita Médica</h2>
        <p class="subtitle">Actualice la información de la cita</p>
      </div>

      <!-- Info de cita actual -->
      <div class="info-card" style="background: linear-gradient(135deg, #e8daef 0%, #d7bde2 100%); border-left-color: #9b59b6;">
        <i class="fas fa-info-circle" style="color: #9b59b6;"></i>
        <p style="color: #6c3483;">
          <strong>Cita ID: <?php echo $cita['id_cita']; ?></strong><br>
          Paciente actual: <?php echo htmlspecialchars($cita['nombre_paciente']); ?><br>
          Doctor actual: <?php echo htmlspecialchars($cita['nombre_doctor']); ?> - <?php echo htmlspecialchars($cita['especialidad']); ?>
        </p>
      </div>

      <!-- Formulario -->
      <form action="Actions/editar_cita_action.php" method="post">
        <input type="hidden" name="cita_id" value="<?php echo $cita['id_cita']; ?>">
        
        <div class="form-grid">
          <!-- Paciente -->
          <div class="form-group full-width">
            <label class="form-label">
              <i class="fas fa-user-injured"></i>
              Paciente
              <span class="required">*</span>
            </label>
            <div class="input-wrapper">
              <i class="fas fa-user-injured input-icon"></i>
              <select name="id_paciente" class="form-control" required>
                <?php
                if ($resultado_pacientes && pg_num_rows($resultado_pacientes) > 0) {
                  while ($paciente = pg_fetch_assoc($resultado_pacientes)) {
                    $selected = ($paciente['codigo'] == $cita['id_paciente']) ? 'selected' : '';
                    echo '<option value="' . $paciente['codigo'] . '" ' . $selected . '>' . 
                         htmlspecialchars($paciente['nombre']) . ' (ID: ' . $paciente['codigo'] . ')</option>';
                  }
                }
                ?>
              </select>
            </div>
          </div>

          <!-- Doctor -->
          <div class="form-group full-width">
            <label class="form-label">
              <i class="fas fa-user-md"></i>
              Doctor
              <span class="required">*</span>
            </label>
            <div class="input-wrapper">
              <i class="fas fa-user-md input-icon"></i>
              <select name="id_doctor" class="form-control" required>
                <?php
                if ($resultado_doctores && pg_num_rows($resultado_doctores) > 0) {
                  while ($doctor = pg_fetch_assoc($resultado_doctores)) {
                    $selected = ($doctor['codigo'] == $cita['id_doctor']) ? 'selected' : '';
                    echo '<option value="' . $doctor['codigo'] . '" ' . $selected . '>' . 
                         htmlspecialchars($doctor['nombre']) . ' - ' . 
                         htmlspecialchars($doctor['especialidad']) . ' (ID: ' . $doctor['codigo'] . ')</option>';
                  }
                }
                ?>
              </select>
            </div>
          </div>

          <!-- Fecha de la cita -->
          <div class="form-group">
            <label class="form-label">
              <i class="fas fa-calendar-day"></i>
              Nueva Fecha
              <span class="required">*</span>
            </label>
            <div class="input-wrapper">
              <i class="fas fa-calendar-day input-icon"></i>
              <input type="date" name="fecha" class="form-control" 
                     value="<?php echo $cita['fecha']; ?>" 
                     min="<?php echo date('Y-m-d'); ?>" required>
            </div>
          </div>

          <!-- Hora de la cita -->
          <div class="form-group">
            <label class="form-label">
              <i class="fas fa-clock"></i>
              Nueva Hora
              <span class="required">*</span>
            </label>
            <div class="input-wrapper">
              <i class="fas fa-clock input-icon"></i>
              <input type="time" name="hora" class="form-control" 
                     value="<?php echo substr($cita['hora'], 0, 5); ?>" required>
            </div>
            <small class="form-help">
              <i class="fas fa-info-circle"></i>
              Cada cita dura 1 hora
            </small>
          </div>
        </div>

        <!-- Botones -->
        <div class="button-group">
          <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); box-shadow: 0 4px 16px rgba(155, 89, 182, 0.3);">
            <i class="fas fa-save"></i>
            <span>Guardar Cambios</span>
          </button>
          <a href="consultar_citas.php" class="btn btn-secondary">
            <i class="fas fa-times"></i>
            <span>Cancelar</span>
          </a>
        </div>
      </form>
    </div>
  </div>

  <?php
  pg_close($conexion);
  ?>
</body>
</html>
