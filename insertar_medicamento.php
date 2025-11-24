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
    <title>Registrar Nuevo Medicamento - Nucleo Diagnóstico</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="Styles/form.css">
</head>
<body>
    <div class="container">
        <div class="form-card">
            <div class="form-header">
                <div class="header-icon" style="background: linear-gradient(135deg, #b69a59 0%, #ad7f44 100%);">
                    <i class="fas fa-pills"></i>
                </div>
                <h2>Registrar Nuevo Medicamento</h2>
                <p class="subtitle">Complete la información del medicamento en inventario</p>
            </div>

            <div class="info-card">
                <i class="fas fa-info-circle"></i>
                <p>Todos los campos marcados con <span style="color: #e74c3c;">*</span> son obligatorios</p>
            </div>

            <form action="Actions/insertar_medicamento_action.php" method="post">
                <div class="form-grid">
                    
                    <div class="form-group full-width">
                        <label class="form-label">
                            <i class="fas fa-prescription-bottle-alt"></i>
                            Nombre
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-prescription-bottle-alt input-icon"></i>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej: Amoxicilina" required>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label">
                            <i class="fas fa-prescription-bottle-alt"></i>
                            Vía de Administración
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-prescription-bottle-alt input-icon"></i>
                            <input type="text" name="via_adm" class="form-control" placeholder="Ej: Oral" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-pills"></i>
                            Presentación
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-pills input-icon"></i>
                            <input type="text" name="presentacion" class="form-control" placeholder="Ej: Tableta, Jarabe" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-calendar-times"></i>
                            Fecha de Caducidad
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-calendar-times input-icon"></i>
                            <input type="date" name="fecha_cad" class="form-control" required>
                        </div>
                        <small class="form-help">
                            <i class="fas fa-info-circle"></i>
                            Fecha límite de uso (AAAA-MM-DD)
                        </small>
                    </div>

                    </div>

                <div class="button-group">
                    <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #b69a59 0%, #ad7f44 100%); box-shadow: 0 4px 16px rgba(188, 129, 26, 0.3);">
                        <i class="fas fa-save"></i>
                        <span>Guardar Medicamento</span>
                    </button>
                    <a href="menu.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        <span>Volver al Menú</span>
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>