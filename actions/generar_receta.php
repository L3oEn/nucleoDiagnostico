<?php
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['codigo']) || !isset($_SESSION['tipo'])) {
    header("Location: ../index.php");
    exit();
}

include("../conecta.php");

// Obtener datos del formulario
$id_cita = isset($_POST['id_cita']) ? intval($_POST['id_cita']) : 0;
$doctor_codigo = isset($_POST['doctor_codigo']) ? intval($_POST['doctor_codigo']) : 0;
$diagnostico = isset($_POST['diagnostico']) ? trim($_POST['diagnostico']) : '';
$instrucciones_generales = isset($_POST['instrucciones_generales']) ? trim($_POST['instrucciones_generales']) : '';

// Validaciones básicas
if ($id_cita <= 0 || $doctor_codigo <= 0 || empty($diagnostico)) {
    header("Location: ../receta.php?error=datos_incompletos");
    exit();
}

// Obtener medicamentos del formulario
$medicamentos_ids = isset($_POST['medicamentos_ids']) ? $_POST['medicamentos_ids'] : [];
$medicamentos_horarios = isset($_POST['medicamentos_horarios']) ? $_POST['medicamentos_horarios'] : [];

// Validar que haya al menos un medicamento
if (empty($medicamentos_ids) || empty($medicamentos_horarios)) {
    header("Location: ../receta.php?error=medicamentos_vacios");
    exit();
}

// Validar que todos los medicamentos tengan horario y viceversa
if (count($medicamentos_ids) !== count($medicamentos_horarios)) {
    header("Location: ../receta.php?error=medicamentos_incompletos");
    exit();
}

// Iniciar transacción
pg_query($conexion, "BEGIN");

try {
    // 1. Insertar la consulta principal - SOLO EL PRIMER MEDICAMENTO
    // (según tu estructura de tabla que solo permite un medicamento por consulta)
    $id_medicamento_principal = intval($medicamentos_ids[0]);
    
    $query_consulta = "
        INSERT INTO consulta (id_cita, diagnostico, id_medicamento) 
        VALUES ($1, $2, $3) 
        RETURNING id_consulta
    ";
    
    $result_consulta = pg_query_params($conexion, $query_consulta, [
        $id_cita, 
        $diagnostico,
        $id_medicamento_principal
    ]);
    
    if (!$result_consulta) {
        throw new Exception("Error al insertar consulta: " . pg_last_error($conexion));
    }
    
    $row_consulta = pg_fetch_assoc($result_consulta);
    $id_consulta = $row_consulta['id_consulta'];
    
    // 2. Si hay más medicamentos, necesitarías una tabla intermedia
    // Pero según tu estructura actual, solo se guarda el primer medicamento
    
    // 3. Obtener datos completos para el PDF
    $query_datos = "
        SELECT 
            c.id_cita,
            c.fecha,
            c.hora,
            p.codigo AS paciente_codigo,
            p.nombre AS paciente_nombre,
            p.direccion AS paciente_direccion,
            p.telefono AS paciente_telefono,
            p.fecha_nac AS paciente_fecha_nac,
            p.sexo AS paciente_sexo,
            p.estatura AS paciente_estatura,
            d.codigo AS doctor_codigo,
            d.nombre AS doctor_nombre,
            d.especialidad AS doctor_especialidad,
            d.telefono AS doctor_telefono,
            con.id_consulta,
            con.diagnostico,
            m.nombre AS medicamento_nombre,
            m.via_adm AS medicamento_via
        FROM citas c
        INNER JOIN paciente p ON p.codigo = c.id_paciente
        INNER JOIN doctor d ON d.codigo = c.id_doctor
        INNER JOIN consulta con ON con.id_cita = c.id_cita
        INNER JOIN medicamento m ON m.codigo = con.id_medicamento
        WHERE c.id_cita = $1 AND con.id_consulta = $2
    ";
    
    $result_datos = pg_query_params($conexion, $query_datos, [$id_cita, $id_consulta]);
    $datos_consulta = pg_fetch_assoc($result_datos);
    
    if (!$datos_consulta) {
        throw new Exception("Error al obtener datos de la consulta");
    }
    
    // Preparar medicamentos para mostrar en el PDF (todos los que se ingresaron)
    $medicamentos_combinados = [];
    for ($i = 0; $i < count($medicamentos_ids); $i++) {
        $id_medicamento = intval($medicamentos_ids[$i]);
        $instrucciones = trim($medicamentos_horarios[$i]);
        
        if ($id_medicamento > 0 && !empty($instrucciones)) {
            $query_medicamento = "SELECT nombre, via_adm FROM medicamento WHERE codigo = $1";
            $result_med = pg_query_params($conexion, $query_medicamento, [$id_medicamento]);
            $medicamento = pg_fetch_assoc($result_med);
            
            if ($medicamento) {
                $medicamentos_combinados[] = [
                    'nombre' => $medicamento['nombre'],
                    'via_adm' => $medicamento['via_adm'],
                    'instrucciones' => $instrucciones
                ];
            }
        }
    }
    
    // Función para calcular edad
    function calcularEdad($fecha_nac) {
        $nacimiento = new DateTime($fecha_nac);
        $hoy = new DateTime();
        $edad = $hoy->diff($nacimiento);
        return $edad->y;
    }
    
    // Preparar datos para la vista
    $edad_paciente = calcularEdad($datos_consulta['paciente_fecha_nac']);
    $sexo_paciente = $datos_consulta['paciente_sexo'] === 'M' ? 'Masculino' : 'Femenino';
    
    // Confirmar transacción
    pg_query($conexion, "COMMIT");
    pg_close($conexion);
    
} catch (Exception $e) {
    // Revertir transacción en caso de error
    pg_query($conexion, "ROLLBACK");
    pg_close($conexion);
    header("Location: ../receta.php?error=error_bd");
    exit();
}

// Mostrar el PDF/HTML
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Receta Médica - La salud es primero</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../Styles/form.css">
    <link rel="stylesheet" href="../Styles/receta.css">
</head>
<body>
<div class="container">
    <div class="form-card">
        <!-- Encabezado de la receta -->
        <div class="receta-header">
            <div class="receta-logo">
                <i class="fas fa-hospital-user"></i>
            </div>
            <h1 class="receta-title">La salud es primero</h1>
            <p class="receta-subtitle">Núcleo de Diagnóstico - Receta Médica</p>
        </div>

        <!-- Información de la consulta guardada -->
        <div class="consulta-info">
            <i class="fas fa-check-circle"></i>
            <strong>Consulta guardada exitosamente</strong> - ID: <?= $datos_consulta['id_consulta'] ?>
        </div>

        <!-- Información de la cita -->
        <div class="info-item" style="justify-content: center; background: #e3f2fd;">
            <i class="fas fa-hashtag"></i>
            <strong>Consulta #<?= $datos_consulta['id_consulta'] ?></strong> | 
            <i class="fas fa-calendar-alt"></i>
            <?= date('d/m/Y', strtotime($datos_consulta['fecha'])) ?> | 
            <i class="fas fa-clock"></i>
            <?= substr($datos_consulta['hora'], 0, 5) ?>
        </div>

        <!-- Información del paciente -->
        <h3 class="section-title">
            <i class="fas fa-user-injured"></i>
            Datos del Paciente
        </h3>
        <div class="patient-info">
            <div class="info-item">
                <i class="fas fa-user"></i>
                <div>
                    <strong>Nombre:</strong><br>
                    <?= htmlspecialchars($datos_consulta['paciente_nombre']) ?>
                </div>
            </div>
            <div class="info-item">
                <i class="fas fa-venus-mars"></i>
                <div>
                    <strong>Sexo/Edad:</strong><br>
                    <?= $sexo_paciente ?> / <?= $edad_paciente ?> años
                </div>
            </div>
            <div class="info-item">
                <i class="fas fa-ruler-vertical"></i>
                <div>
                    <strong>Estatura:</strong><br>
                    <?= number_format($datos_consulta['paciente_estatura'], 2) ?> m
                </div>
            </div>
            <div class="info-item">
                <i class="fas fa-phone"></i>
                <div>
                    <strong>Teléfono:</strong><br>
                    <?= htmlspecialchars($datos_consulta['paciente_telefono']) ?>
                </div>
            </div>
            <div class="info-item" style="grid-column: 1 / -1;">
                <i class="fas fa-map-marker-alt"></i>
                <div>
                    <strong>Dirección:</strong><br>
                    <?= htmlspecialchars($datos_consulta['paciente_direccion']) ?>
                </div>
            </div>
        </div>

        <!-- Información del doctor -->
        <h3 class="section-title">
            <i class="fas fa-user-md"></i>
            Datos del Médico Tratante
        </h3>
        <div class="doctor-info">
            <div class="info-item">
                <i class="fas fa-user-md"></i>
                <div>
                    <strong>Doctor:</strong><br>
                    Dr. <?= htmlspecialchars($datos_consulta['doctor_nombre']) ?>
                </div>
            </div>
            <div class="info-item">
                <i class="fas fa-stethoscope"></i>
                <div>
                    <strong>Especialidad:</strong><br>
                    <?= htmlspecialchars($datos_consulta['doctor_especialidad']) ?>
                </div>
            </div>
            <div class="info-item">
                <i class="fas fa-phone"></i>
                <div>
                    <strong>Teléfono:</strong><br>
                    <?= htmlspecialchars($datos_consulta['doctor_telefono']) ?>
                </div>
            </div>
        </div>

        <!-- Diagnóstico -->
        <h3 class="section-title">
            <i class="fas fa-notes-medical"></i>
            Diagnóstico y Valoración Médica
        </h3>
        <div class="diagnostico-box">
            <?= nl2br(htmlspecialchars($datos_consulta['diagnostico'])) ?>
        </div>

        <!-- Medicamentos recetados -->
        <h3 class="section-title">
            <i class="fas fa-pills"></i>
            Medicamentos Recetados
        </h3>
        <div class="medicamentos-list">
            <?php if (!empty($medicamentos_combinados)): ?>
                <?php foreach ($medicamentos_combinados as $medicamento): ?>
                    <div class="medicamento-item">
                        <i class="fas fa-capsules"></i>
                        <div class="medicamento-content">
                            <div class="medicamento-nombre">
                                <?= htmlspecialchars($medicamento['nombre']) ?>
                                <?php if (!empty($medicamento['via_adm'])): ?>
                                    <small style="color: #666;">(Vía: <?= htmlspecialchars($medicamento['via_adm']) ?>)</small>
                                <?php endif; ?>
                            </div>
                            <div class="medicamento-instrucciones">
                                <strong>Instrucciones:</strong> <?= htmlspecialchars($medicamento['instrucciones']) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="info-item">
                    <i class="fas fa-info-circle"></i>
                    No se recetaron medicamentos
                </div>
            <?php endif; ?>
        </div>

        <!-- Instrucciones generales -->
        <?php if (!empty($instrucciones_generales)): ?>
        <h3 class="section-title">
            <i class="fas fa-clipboard-list"></i>
            Instrucciones y Recomendaciones Generales
        </h3>
        <div class="instrucciones-box">
            <?= nl2br(htmlspecialchars($instrucciones_generales)) ?>
        </div>
        <?php endif; ?>

        <!-- Firma -->
        <div class="signature-area">
            <div class="signature-line"></div>
            <p><strong>Dr. <?= htmlspecialchars($datos_consulta['doctor_nombre']) ?></strong></p>
            <p><?= htmlspecialchars($datos_consulta['doctor_especialidad']) ?></p>
            <p>Cédula Profesional: <?= htmlspecialchars($datos_consulta['doctor_codigo']) ?></p>
        </div>

        <!-- Nota del pie -->
        <div class="footer-note">
            <i class="fas fa-info-circle"></i>
            Esta receta es válida por 30 días a partir de la fecha de emisión. 
            Conserve este documento para futuras consultas.
        </div>

        <!-- Botones de acción -->
        <div class="button-group no-print">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-file-pdf"></i>
                <span>Imprimir/Guardar PDF</span>
            </button>
            <a href="../receta.php" class="btn btn-secondary">
                <i class="fas fa-plus"></i>
                <span>Generar Otra Receta</span>
            </a>
            <a href="../menu_doc.php" class="btn btn-secondary">
                <i class="fas fa-home"></i>
                <span>Volver al Menú</span>
            </a>
        </div>
    </div>
</div>

<script>
</script>
</body>
</html>