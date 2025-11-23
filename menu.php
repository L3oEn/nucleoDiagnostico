<?php
session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['codigo'])) {
    header("Location: index.php");
    exit();
}

$usuario_nombre_completo = $_SESSION['usuario'];
$usuario_codigo = $_SESSION['codigo'];

// Extraer solo el primer nombre (hasta el primer espacio)
$primer_nombre = explode(' ', $usuario_nombre_completo)[0];

include("conecta.php");

$query_empleados = "SELECT COUNT(*) as total FROM empleado";
$result_empleados = pg_query($conexion, $query_empleados);
$total_empleados = pg_fetch_assoc($result_empleados)['total'];

$query_doctores = "SELECT COUNT(*) as total FROM doctor";
$result_doctores = pg_query($conexion, $query_doctores);
$total_doctores = pg_fetch_assoc($result_doctores)['total'];

$query_pacientes = "SELECT COUNT(*) as total FROM paciente";
$result_pacientes = pg_query($conexion, $query_pacientes);
$total_pacientes = pg_fetch_assoc($result_pacientes)['total'];

pg_close($conexion);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Administrador - Nucleo Diagnóstico</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="Styles/menu.css">
</head>
<body>
  <!-- Header superior -->
  <div class="top-header">
    <div class="user-info">
      <div class="user-avatar">
        <i class="fas fa-user-shield"></i>
      </div>
      <div class="user-details">
        <h3>Bienvenida, <?php echo htmlspecialchars($primer_nombre); ?></h3>
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
    <!-- Tarjeta de bienvenida -->
    <div class="welcome-card">
      <h1>
        <i class="fas fa-hospital"></i>
        Panel de Administración
      </h1>
      <p>Gestiona empleados, doctores y pacientes desde un solo lugar</p>
    </div>

    <!-- Grid de menús -->
    <div class="menu-grid">
      <!-- Menú Empleados -->
      <div class="menu-section empleados">
        <div class="menu-header">
          <div class="menu-icon">
            <i class="fas fa-users"></i>
          </div>
          <h2>Empleados</h2>
        </div>
        <div class="menu-options">
          <a href="insertar_empleado.php" class="menu-link">
            <i class="fas fa-user-plus"></i>
            <span>Registrar Nuevo Empleado</span>
          </a>
          <a href="consultar_empleados.php" class="menu-link">
            <i class="fas fa-list"></i>
            <span>Ver Lista de Empleados</span>
          </a>
        </div>
      </div>

      <!-- Menú Doctores -->
      <div class="menu-section doctores">
        <div class="menu-header">
          <div class="menu-icon">
            <i class="fas fa-user-md"></i>
          </div>
          <h2>Doctores</h2>
        </div>
        <div class="menu-options">
          <a href="insertar_doctor.php" class="menu-link">
            <i class="fas fa-user-plus"></i>
            <span>Registrar Nuevo Doctor</span>
          </a>
          <a href="consultar_doctores.php" class="menu-link">
            <i class="fas fa-list"></i>
            <span>Ver Lista de Doctores</span>
          </a>
        </div>
      </div>

      <!-- Menú Pacientes -->
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
    </div>

    <!-- Menú Citas -->
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

    <!-- Tarjetas de estadísticas con consultas dinámicas -->
    <div class="stats-grid">
      <div class="stat-card">
        <i class="fas fa-users stat-icon"></i>
        <h3><?php echo $total_empleados; ?></h3>
        <p>Total Empleados</p>
      </div>
      <div class="stat-card">
        <i class="fas fa-user-md stat-icon"></i>
        <h3><?php echo $total_doctores; ?></h3>
        <p>Doctores Activos</p>
      </div>
      <div class="stat-card">
        <i class="fas fa-procedures stat-icon"></i>
        <h3><?php echo $total_pacientes; ?></h3>
        <p>Pacientes Registrados</p>
      </div>
    </div>
  </div>
</body>
</html>