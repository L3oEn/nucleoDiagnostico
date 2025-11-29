<?php
// Proteger la página - verificar sesión
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['codigo'])) {
    header("Location: index.php");
    exit();
}

include("conecta.php");

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
  <title>Agendar Nueva Cita - Nucleo Diagnóstico</title>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="Styles/form.css">
</head>
<body>
  <div class="container">
    <div class="form-card">
      <!-- Encabezado del formulario -->
      <div class="form-header">
        <div class="header-icon" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);">
          <i class="fas fa-calendar-check"></i>
        </div>
        <h2>Agendar Nueva Cita</h2>
        <p class="subtitle">Complete la información de la cita médica</p>
      </div>

      <!-- Tarjeta de información -->
      <div class="info-card">
        <i class="fas fa-info-circle"></i>
        <p>Todos los campos marcados con <span style="color: #e74c3c;">*</span> son obligatorios</p>
      </div>

      <!-- Formulario -->
      <form action="Actions/insertar_cita_action.php" method="post">
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
                <option value="">Seleccione un paciente...</option>
                <?php
                if ($resultado_pacientes && pg_num_rows($resultado_pacientes) > 0) {
                  while ($paciente = pg_fetch_assoc($resultado_pacientes)) {
                    echo '<option value="' . $paciente['codigo'] . '">' . 
                         htmlspecialchars($paciente['nombre']) . ' (ID: ' . $paciente['codigo'] . ')</option>';
                  }
                } else {
                  echo '<option value="" disabled>No hay pacientes registrados</option>';
                }
                ?>
              </select>
            </div>
            <small class="form-help">
              <i class="fas fa-info-circle"></i>
              Seleccione el paciente que asistirá a la cita
            </small>
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
                <option value="">Seleccione un doctor...</option>
                <?php
                if ($resultado_doctores && pg_num_rows($resultado_doctores) > 0) {
                  while ($doctor = pg_fetch_assoc($resultado_doctores)) {
                    echo '<option value="' . $doctor['codigo'] . '">' . 
                         htmlspecialchars($doctor['nombre']) . ' - ' . 
                         htmlspecialchars($doctor['especialidad']) . ' (ID: ' . $doctor['codigo'] . ')</option>';
                  }
                } else {
                  echo '<option value="" disabled>No hay doctores registrados</option>';
                }
                ?>
              </select>
            </div>
            <small class="form-help">
              <i class="fas fa-info-circle"></i>
              Seleccione el doctor que atenderá la cita
            </small>
          </div>

          <!-- Fecha de la cita -->
          <div class="form-group">
            <label class="form-label">
              <i class="fas fa-calendar-day"></i>
              Fecha de la Cita
              <span class="required">*</span>
            </label>
            <div class="input-wrapper">
              <i class="fas fa-calendar-day input-icon"></i>
              <input type="date" name="fecha" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <small class="form-help">
              <i class="fas fa-info-circle"></i>
              La fecha debe ser actual o futura
            </small>
          </div>

          <!-- Hora de la cita -->
          <div class="form-group">
            <label class="form-label">
              <i class="fas fa-clock"></i>
              Hora de la Cita
              <span class="required">*</span>
            </label>
            <div class="input-wrapper">
              <i class="fas fa-clock input-icon"></i>
              <input type="time" name="hora" class="form-control" required>
            </div>
            <small class="form-help">
              <i class="fas fa-info-circle"></i>
              Cada cita dura 1 hora. Horario: 8:00 AM - 8:00 PM
            </small>
          </div>
        </div>

        <!-- Botones -->
        <div class="button-group">
          <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); box-shadow: 0 4px 16px rgba(155, 89, 182, 0.3);">
            <i class="fas fa-calendar-plus"></i>
            <span>Agendar Cita</span>
          </button>
          <a href="menu_empleado.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            <span>Volver al Menú</span>
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
