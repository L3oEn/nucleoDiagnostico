<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Empleado - Nucleo Diagnóstico</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="Styles/form.css">
</head>
<body>
<?php
// Proteger la página - verificar sesión
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['codigo'])) {
    header("Location: index.php");
    exit();
}
?>
    <div class="container">
        <div class="form-card">
            <div class="form-header">
                <div class="header-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h2>Registrar Nuevo Empleado</h2>
                <p class="subtitle">Complete el formulario con los datos del empleado</p>
            </div>

            <div class="info-card">
                <i class="fas fa-info-circle"></i>
                <p>Todos los campos marcados con <span style="color: #e74c3c;">*</span> son obligatorios</p>
            </div>

            <div class="success-message" id="successMessage">
                <i class="fas fa-check-circle"></i>
                <p>¡Empleado registrado exitosamente!</p>
            </div>

            <form action="Actions/insertar_empleado_action.php" method="post" id="empleadoForm">
                <div class="form-grid">
                    <!-- Nombre -->
                    <div class="form-group full-width">
                        <label class="form-label">
                            <i class="fas fa-user"></i>
                            Nombre Completo <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <input 
                                type="text" 
                                name="nombre" 
                                class="form-control" 
                                placeholder="Ej: María García López"
                                required
                            >
                            <i class="fas fa-user input-icon"></i>
                        </div>
                    </div>

                    <!-- Dirección -->
                    <div class="form-group full-width">
                        <label class="form-label">
                            <i class="fas fa-map-marker-alt"></i>
                            Dirección <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <input 
                                type="text" 
                                name="direccion" 
                                class="form-control" 
                                placeholder="Ej: Calle Principal #123, Col. Centro"
                                required
                            >
                            <i class="fas fa-map-marker-alt input-icon"></i>
                        </div>
                    </div>

                    <!-- Teléfono -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-phone"></i>
                            Teléfono <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <input 
                                type="tel" 
                                name="telefono" 
                                class="form-control" 
                                placeholder="Ej: 5512345678"
                                pattern="[0-9]{10}"
                                required
                            >
                            <i class="fas fa-phone input-icon"></i>
                        </div>
                        <p class="form-help">
                            <i class="fas fa-info-circle"></i>
                            10 dígitos sin espacios
                        </p>
                    </div>

                    <!-- Fecha de Nacimiento -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-calendar"></i>
                            Fecha de Nacimiento <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <input 
                                type="date" 
                                name="fecha_nacimiento" 
                                class="form-control" 
                                required
                            >
                            <i class="fas fa-calendar input-icon"></i>
                        </div>
                    </div>

                    <!-- Sexo -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-venus-mars"></i>
                            Sexo <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <select name="sexo" class="form-control" required>
                                <option value="">Seleccionar...</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                            <i class="fas fa-venus-mars input-icon"></i>
                        </div>
                    </div>

                    <!-- Sueldo -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-dollar-sign"></i>
                            Sueldo <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <input 
                                type="number" 
                                name="sueldo" 
                                class="form-control" 
                                placeholder="Ej: 15000.00"
                                step="0.01"
                                min="0"
                                required
                            >
                            <i class="fas fa-dollar-sign input-icon"></i>
                        </div>
                    </div>

                    <!-- Turno -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-clock"></i>
                            Turno <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <select name="turno" class="form-control" required>
                                <option value="">Seleccionar...</option>
                                <option value="Matutino">Matutino (6:00 - 14:00)</option>
                                <option value="Vespertino">Vespertino (14:00 - 22:00)</option>
                                <option value="Nocturno">Nocturno (22:00 - 6:00)</option>
                            </select>
                            <i class="fas fa-clock input-icon"></i>
                        </div>
                    </div>

                    <!-- Contraseña -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-lock"></i>
                            Contraseña <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <input 
                                type="password" 
                                name="contrasena" 
                                class="form-control" 
                                placeholder="Mínimo 6 caracteres"
                                minlength="6"
                                required
                            >
                            <i class="fas fa-lock input-icon"></i>
                        </div>
                        <p class="form-help">
                            <i class="fas fa-shield-alt"></i>
                            Mínimo 6 caracteres
                        </p>
                    </div>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        <span>Guardar Empleado</span>
                    </button>
                    <a href="menu_admin.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        <span>Volver al Menú</span>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Validación y efectos del formulario
        document.getElementById('empleadoForm').addEventListener('submit', function(e) {
        });

        // Animación en los inputs al escribir
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateY(-2px)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'translateY(0)';
            });
        });
    </script>
</body>
</html>
