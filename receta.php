<?php 
session_start();

// Verificar doctor
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'doctor') {
    header("Location: index.php");
    exit();
}

$doctor = $_SESSION['usuario'];
$codigo = $_SESSION['codigo'];

include("conecta.php");

// Obtener medicamentos para el select
$medicamentos = pg_fetch_all(pg_query(
    $conexion,
    "SELECT codigo, nombre, via_adm FROM medicamento ORDER BY nombre ASC"
)) ?: [];

// Obtener próximas citas para el select de pacientes
$citasQuery = pg_query_params(
    $conexion,
    "SELECT c.id_cita, p.codigo, p.nombre 
     FROM citas c 
     INNER JOIN paciente p ON p.codigo = c.id_paciente 
     WHERE c.id_doctor = $1 AND c.fecha >= CURRENT_DATE 
     ORDER BY c.fecha ASC, c.hora ASC",
    [$codigo]
);
$citasPacientes = pg_fetch_all($citasQuery) ?: [];

pg_close($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Generar Receta Médica - Nucleo Diagnóstico</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="Styles/form.css">
<link rel="stylesheet" href="Styles/receta.css">
</head>
<body>
<div class="container">
    <div class="form-card">
        <!-- Encabezado del formulario -->
        <div class="form-header">
            <div class="header-icon">
                <i class="fas fa-file-medical"></i>
            </div>
            <h2>Generar Receta Médica</h2>
            <p class="subtitle">Dr. <?php echo htmlspecialchars($doctor); ?> — Código: <?php echo htmlspecialchars($codigo); ?></p>
        </div>

        <!-- Tarjeta de información -->
        <div class="info-card">
            <i class="fas fa-info-circle"></i>
            <p>Complete la información para generar la receta médica. Todos los campos marcados con <span style="color: #e74c3c;">*</span> son obligatorios.</p>
        </div>

        <!-- Mostrar mensajes de error -->
        <?php if (isset($_GET['error'])): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <p>
                    <?php 
                    switch($_GET['error']) {
                        case 'datos_incompletos':
                            echo 'Por favor complete todos los campos obligatorios.';
                            break;
                        case 'medicamentos_vacios':
                            echo 'Debe agregar al menos un medicamento.';
                            break;
                        case 'medicamentos_incompletos':
                            echo 'Todos los medicamentos deben tener instrucciones completas.';
                            break;
                        case 'error_bd':
                            echo 'Error al guardar la consulta. Intente nuevamente.';
                            break;
                        default:
                            echo 'Error al procesar el formulario.';
                    }
                    ?>
                </p>
            </div>
        <?php endif; ?>

        <!-- Formulario -->
        <form action="Actions/generar_receta.php" method="post">
            <input type="hidden" name="doctor_codigo" value="<?= $codigo; ?>">
            
            <div class="form-section">
                <label class="form-label">
                    <i class="fas fa-user-injured"></i>
                    Seleccionar Paciente
                    <span class="required">*</span>
                </label>
                <div class="input-wrapper">
                    <i class="fas fa-user input-icon"></i>
                    <select name="id_cita" class="form-control" required>
                        <option value="">Seleccione un paciente</option>
                        <?php foreach ($citasPacientes as $cita): ?>
                            <option value="<?= $cita['id_cita']; ?>">
                                <?= htmlspecialchars($cita['nombre']); ?> (Cita #<?= $cita['id_cita']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-section">
                <label class="form-label">
                    <i class="fas fa-stethoscope"></i>
                    Diagnóstico / Valoración Médica
                    <span class="required">*</span>
                </label>
                <div class="input-wrapper">
                    <i class="fas fa-file-medical-alt input-icon"></i>
                    <textarea name="diagnostico" class="form-control" required placeholder="Describa la valoración médica realizada..." rows="4"></textarea>
                </div>
            </div>

            <div class="form-section">
                <label class="form-label">
                    <i class="fas fa-pills"></i>
                    Medicamentos Recetados
                    <span class="required">*</span>
                </label>
                <div class="medications-group">
                    <div class="med-row">
                        <div class="input-wrapper">
                            <i class="fas fa-capsules input-icon"></i>
                            <select name="medicamentos_ids[]" class="form-control" required>
                                <option value="">Seleccione medicamento</option>
                                <?php foreach ($medicamentos as $med): ?>
                                    <option value="<?= $med['codigo']; ?>" data-via="<?= htmlspecialchars($med['via_adm']); ?>">
                                        <?= htmlspecialchars($med['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="input-wrapper">
                            <i class="fas fa-clock input-icon"></i>
                            <input type="text" name="medicamentos_horarios[]" class="form-control" placeholder="Dosis y horario (ej: 1 tableta cada 8h)" required>
                        </div>
                        <button type="button" class="btn-remove-med" aria-label="Quitar medicamento">&times;</button>
                    </div>
                    <button type="button" class="btn-add-med">
                        <i class="fas fa-plus"></i> Agregar Medicamento
                    </button>
                </div>
            </div>

            <div class="form-section">
                <label class="form-label">
                    <i class="fas fa-clipboard-list"></i>
                    Instrucciones Generales y Recomendaciones
                    <span class="required">*</span>
                </label>
                <div class="input-wrapper">
                    <i class="fas fa-notes-medical input-icon"></i>
                    <textarea name="instrucciones_generales" class="form-control" required placeholder="Indicaciones adicionales, cuidados, reposo, etc..." rows="3"></textarea>
                </div>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i>
                    <span>Guardar Consulta</span>
                </button>
                <button type="button" class="btn-print" onclick="window.print()">
                    <i class="fas fa-file-pdf"></i>
                    <span>Vista previa</span>
                </button>
            </div>
        </form>

        <!-- Botón Volver -->
        <div class="button-group" style="margin-top: 20px;">
            <a href="menu_doc.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                <span>Volver al Menú</span>
            </a>
        </div>
    </div>
</div>

<script>
// Gestión de medicamentos dinámicos
document.addEventListener('DOMContentLoaded', function() {
    const group = document.querySelector('.medications-group');
    const addBtn = group.querySelector('.btn-add-med');
    
    // Función para crear nueva fila de medicamento
    const createRow = () => {
        const row = document.createElement('div');
        row.className = 'med-row';
        row.innerHTML = `
            <div class="input-wrapper">
                <i class="fas fa-capsules input-icon"></i>
                <select name="medicamentos_ids[]" class="form-control" required>
                    <option value="">Seleccione medicamento</option>
                    <?php foreach ($medicamentos as $med): ?>
                        <option value="<?= $med['codigo']; ?>" data-via="<?= htmlspecialchars($med['via_adm']); ?>">
                            <?= htmlspecialchars($med['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="input-wrapper">
                <i class="fas fa-clock input-icon"></i>
                <input type="text" name="medicamentos_horarios[]" class="form-control" placeholder="Dosis y horario (ej: 1 tableta cada 8h)" required>
            </div>
            <button type="button" class="btn-remove-med" aria-label="Quitar medicamento">&times;</button>
        `;
        return row;
    };

    // Función para actualizar visibilidad de botones de eliminar
    const updateRemovers = () => {
        const removes = group.querySelectorAll('.btn-remove-med');
        removes.forEach(btn => {
            btn.style.display = removes.length === 1 ? 'none' : 'inline-flex';
        });
    };

    // Event listener para eliminar medicamentos
    group.addEventListener('click', (ev) => {
        if (ev.target.closest('.btn-remove-med')) {
            const rows = group.querySelectorAll('.med-row');
            if (rows.length === 1) return;
            ev.target.closest('.med-row').remove();
            updateRemovers();
        }
    });

    // Event listener para agregar medicamentos
    addBtn.addEventListener('click', () => {
        addBtn.before(createRow());
        updateRemovers();
    });

    // Validación antes de enviar el formulario
    document.querySelector('form').addEventListener('submit', function(e) {
        const medicamentosSelects = this.querySelectorAll('select[name="medicamentos_ids[]"]');
        const medicamentosInputs = this.querySelectorAll('input[name="medicamentos_horarios[]"]');
        
        // Verificar que todos los medicamentos estén completos
        for (let i = 0; i < medicamentosSelects.length; i++) {
            if (!medicamentosSelects[i].value || !medicamentosInputs[i].value.trim()) {
                e.preventDefault();
                alert('Por favor complete todos los medicamentos y sus instrucciones.');
                return;
            }
        }
        
        // Verificar que haya al menos un medicamento
        if (medicamentosSelects.length === 0) {
            e.preventDefault();
            alert('Debe agregar al menos un medicamento.');
            return;
        }
    });

    // Inicializar visibilidad de botones
    updateRemovers();
});
</script>

</body>
</html>