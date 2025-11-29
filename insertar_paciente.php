<?php
// Proteger la página - verificar sesión
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['codigo'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrar Nuevo Paciente - Nucleo Diagnóstico</title>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="Styles/form.css">
</head>
<body>
  <div class="container">
    <div class="form-card">
      <!-- Encabezado del formulario -->
      <div class="form-header">
        <div class="header-icon" style="background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);">
          <i class="fas fa-procedures"></i>
        </div>
        <h2>Registrar Nuevo Paciente</h2>
        <p class="subtitle">Complete la información del paciente</p>
      </div>

      <!-- Tarjeta de información -->
      <div class="info-card">
        <i class="fas fa-info-circle"></i>
        <p>Todos los campos marcados con <span style="color: #e74c3c;">*</span> son obligatorios</p>
      </div>

      <!-- Formulario -->
      <form action="Actions/insertar_paciente_action.php" method="post">
        <div class="form-grid">
          <!-- Nombre completo -->
          <div class="form-group full-width">
            <label class="form-label">
              <i class="fas fa-user"></i>
              Nombre Completo
              <span class="required">*</span>
            </label>
            <div class="input-wrapper">
              <i class="fas fa-user input-icon"></i>
              <input type="text" name="nombre" class="form-control" placeholder="Ej: María López García" required>
            </div>
          </div>

          <!-- Dirección -->
          <div class="form-group full-width">
            <label class="form-label">
              <i class="fas fa-map-marker-alt"></i>
              Dirección
              <span class="required">*</span>
            </label>
            <div class="input-wrapper">
              <i class="fas fa-map-marker-alt input-icon"></i>
              <input type="text" name="direccion" class="form-control" placeholder="Ej: Av. Revolución #456, Col. Moderna" required>
            </div>
          </div>

          <!-- Teléfono -->
          <div class="form-group">
            <label class="form-label">
              <i class="fas fa-phone"></i>
              Teléfono
              <span class="required">*</span>
            </label>
            <div class="input-wrapper">
              <i class="fas fa-phone input-icon"></i>
              <input type="tel" name="telefono" class="form-control" placeholder="Ej: 3312345678" pattern="[0-9]{10}" required>
            </div>
            <small class="form-help">
              <i class="fas fa-info-circle"></i>
              10 dígitos sin espacios
            </small>
          </div>

          <!-- Fecha de nacimiento -->
          <div class="form-group">
            <label class="form-label">
              <i class="fas fa-calendar"></i>
              Fecha de Nacimiento
              <span class="required">*</span>
            </label>
            <div class="input-wrapper">
              <i class="fas fa-calendar input-icon"></i>
              <input type="date" name="fecha_nac" class="form-control" required>
            </div>
          </div>

          <!-- Sexo -->
          <div class="form-group">
            <label class="form-label">
              <i class="fas fa-venus-mars"></i>
              Sexo
              <span class="required">*</span>
            </label>
            <div class="input-wrapper">
              <i class="fas fa-venus-mars input-icon"></i>
              <select name="sexo" class="form-control" required>
                <option value="">Seleccione...</option>
                <option value="M">Masculino</option>
                <option value="F">Femenino</option>
              </select>
            </div>
          </div>

          <!-- Edad -->
          <div class="form-group">
            <label class="form-label">
              <i class="fas fa-user-clock"></i>
              Edad
              <span class="required">*</span>
            </label>
            <div class="input-wrapper">
              <i class="fas fa-user-clock input-icon"></i>
              <input type="number" name="edad" class="form-control" placeholder="Ej: 35" min="0" max="120" required>
            </div>
            <small class="form-help">
              <i class="fas fa-info-circle"></i>
              Edad en años
            </small>
          </div>

          <!-- Estatura -->
          <div class="form-group">
            <label class="form-label">
              <i class="fas fa-ruler-vertical"></i>
              Estatura (metros)
              <span class="required">*</span>
            </label>
            <div class="input-wrapper">
              <i class="fas fa-ruler-vertical input-icon"></i>
              <input type="number" name="estatura" class="form-control" placeholder="Ej: 1.75" step="0.01" min="0.50" max="2.50" required>
            </div>
            <small class="form-help">
              <i class="fas fa-info-circle"></i>
              Estatura en metros (ej: 1.75)
            </small>
          </div>
        </div>

        <!-- Botones -->
        <div class="button-group">
          <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%); box-shadow: 0 4px 16px rgba(46, 204, 113, 0.3);">
            <i class="fas fa-save"></i>
            <span>Guardar Paciente</span>
          </button>
          <a href="menu_empleado.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            <span>Volver al Menú</span>
          </a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
