<?php
session_start();

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'empleado') {
    header("Location: index.php");
    exit();
}

$usuario_nombre_completo = $_SESSION['usuario'];
$usuario_codigo = $_SESSION['codigo'];
$primer_nombre = explode(' ', $usuario_nombre_completo)[0];

include("conecta.php");

$query_pacientes = "SELECT COUNT(*) as total FROM paciente";
$result_pacientes = pg_query($conexion, $query_pacientes);
$total_pacientes = pg_fetch_assoc($result_pacientes)['total'];

$query_citas = "SELECT COUNT(*) as total FROM citas";
$result_citas = pg_query($conexion, $query_citas);
$total_citas = pg_fetch_assoc($result_citas)['total'];

/* NUEVO: total de medicamentos */
$query_medicamento = "SELECT COUNT(*) as total FROM medicamento";
$result_medicamento = pg_query($conexion, $query_medicamento);
$total_medicamento = pg_fetch_assoc($result_medicamento)['total'];

pg_close($conexion);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Menú Empleado - Nucleo Diagnóstico</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="Styles/menu.css">
</head>
<body>

<div class="top-header">
    <div class="user-info">
        <div class="user-avatar">
            <i class="fas fa-user"></i>
        </div>
        <div class="user-details">
            <h3>Bienvenido, <?php echo htmlspecialchars($primer_nombre); ?></h3>
            <p><i class="fas fa-id-badge"></i> Código: <?php echo htmlspecialchars($usuario_codigo); ?></p>
        </div>
    </div>

    <form action="logout.php" method="post">
        <button type="submit" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span>Cerrar Sesión</span>
        </button>
    </form>
</div>

<div class="main-container">

<div class="welcome-card">
    <h1>
        <i class="fas fa-briefcase"></i>
        Panel del Empleado
    </h1>
    <p>Gestione pacientes, citas y medicamentos del área administrativa.</p>
</div>

<div class="menu-grid">

    <!-- Pacientes -->
    <div class="menu-section pacientes">
        <div class="menu-header">
            <div class="menu-icon">
                <i class="fas fa-procedures"></i>
            </div>
            <h2>Pacientes</h2>
        </div>
        <div class="menu-options">
            <a href="insertar_paciente.php" class="menu-link">
                <i class="fas fa-user-plus"></i>
                <span>Registrar Nuevo Paciente</span>
            </a>
            <a href="consultar_pacientes.php" class="menu-link">
                <i class="fas fa-list"></i>
                <span>Ver Lista de Pacientes</span>
            </a>
        </div>
    </div>

    <!-- Citas -->
    <div class="menu-section citas">
        <div class="menu-header">
            <div class="menu-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <h2>Citas Médicas</h2>
        </div>
        <div class="menu-options">
            <a href="insertar_cita.php" class="menu-link">
                <i class="fas fa-calendar-plus"></i>
                <span>Agendar Nueva Cita</span>
            </a>
            <a href="consultar_citas.php" class="menu-link">
                <i class="fas fa-list"></i>
                <span>Ver Registro de Citas</span>
            </a>
        </div>
    </div>

</div>

<!-- NUEVO GRID con los medicamentos (igual que en admin) -->
<div class="menu-grid">

    <div class="menu-section medicamento">
        <div class="menu-header">
            <div class="menu-icon">
                <i class="fas fa-pills"></i>
            </div>
            <h2>Medicamentos</h2>
        </div>
        <div class="menu-options">
            <a href="insertar_medicamento.php" class="menu-link">
                <i class="fas fa-prescription-bottle-alt"></i>
                <span>Agregar nuevo medicamento</span>
            </a>
            <a href="consultar_medicamento.php" class="menu-link">
                <i class="fas fa-list"></i>
                <span>Inventario de medicamentos</span>
            </a>
        </div>
    </div>

</div>

<!-- Estadísticas -->
<div class="stats-grid">

    <div class="stat-card">
        <i class="fas fa-procedures stat-icon"></i>
        <h3><?php echo $total_pacientes; ?></h3>
        <p>Pacientes Registrados</p>
    </div>

    <div class="stat-card">
        <i class="fas fa-calendar-check stat-icon"></i>
        <h3><?php echo $total_citas; ?></h3>
        <p>Citas Agendadas</p>
    </div>

    <div class="stat-card">
        <i class="fas fa-pills stat-icon"></i>
        <h3><?php echo $total_medicamento; ?></h3>
        <p>Medicamentos en Inventario</p>
    </div>

</div>

</body>
</html>