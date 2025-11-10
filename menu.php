<?php
session_start();

// Si el usuario viene del formulario, lo guardamos en sesión
if (isset($_POST['usuario'])) {
  $_SESSION['usuario'] = $_POST['usuario'];
}

// Si no hay sesión activa, redirigir al login
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Menú del Administrador</title>
  <link rel="stylesheet" href="Styles/menu.css">
</head>
<body>

<!-- muestra el usuario y permite salir-->
  <div class="barra-superior">
    <span>Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['usuario']); ?></strong></span>
    <form action="logout.php" method="post">
      <button type="submit" class="logout-btn">Cerrar sesión</button>
    </form>
  </div>

  <div class="card">
    <h2>Menú del Administrador</h2>

    <!-- === MENÚ EMPLEADOS === -->
<div class="menu-section">
  <button id="empleadosBtn" class="menu-btn">Menú de Empleados</button>
  <div id="submenuEmpleados" class="submenu">
    <a href="insertar_empleado.php">Insertar Empleado</a>
    <a href="consultar_empleados.php">Consultas Generales</a>
  </div>
</div>

<div class="menu-section">
  <button id="doctoresBtn" class="menu-btn">Menú de Doctores</button>
  <div id="submenuDoctores" class="submenu">
    <a href="insertar_doctor.php">Insertar Doctor</a>
    <a href="consultar_doctores.php">Consultas Generales</a>
  </div>
</div>

  </div>

  <script>
    const empleadosBtn = document.getElementById("empleadosBtn");
    const doctoresBtn = document.getElementById("doctoresBtn");
    const submenuEmpleados = document.getElementById("submenuEmpleados");
    const submenuDoctores = document.getElementById("submenuDoctores");

    empleadosBtn.addEventListener("click", () => {
      submenuEmpleados.style.display =
        submenuEmpleados.style.display === "grid" ? "none" : "grid";
    });

    doctoresBtn.addEventListener("click", () => {
      submenuDoctores.style.display =
        submenuDoctores.style.display === "grid" ? "none" : "grid";
    });
  </script>
</body>
</html>
