<?php
// Proteger la página - verificar sesión
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['codigo'])) {
    header("Location: index.php");
    exit();
}

include("conecta.php");
$resultado = pg_query($conexion, "SELECT codigo, nombre, via_adm, presentacion, fecha_cad FROM medicamento ORDER BY codigo ASC");

$usuario = $_SESSION['tipo'];
$destino = ($tipo_usuario === 'doctor') ? 'menu_doc.php' : 'menu_empleado.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lista de Pacientes - Nucleo Diagnóstico</title>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="Styles/cons.css">
</head>
<body>
  <div class="container">
    <h2>Inventario de medicamentos</h2>

    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th><i class="fas fa-hashtag"></i> Código</th>
            <th><i class="fas fa-prescription-bottle-alt"></i> Nombre</th>
            <th><i class="fas fas fa-syringe"></i> Vía Administración</th>
            <th><i class="fas fa-pills"></i> Presentación</th>
            <th><i class="fas fa-calendar-times"></i> Fecha Caducidad</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($resultado && pg_num_rows($resultado) > 0) {
            while ($fila = pg_fetch_assoc($resultado)) {
              echo "<tr>";
              echo "<td>" . htmlspecialchars($fila['codigo']) . "</td>";
              echo "<td>" . htmlspecialchars($fila['nombre']) . "</td>";
              echo "<td>" . htmlspecialchars($fila['via_adm']) . "</td>";
              echo "<td>" . htmlspecialchars($fila['presentacion']) . "</td>";
              echo "<td>" . htmlspecialchars($fila['fecha_cad']) . "</td>";
              echo "</tr>";
            }
          } else {
            echo "<tr><td colspan='5'>No hay medicamentos en el inventario</td></tr>";
          }
          pg_close($conexion);
          ?>
        </tbody>
      </table>
    </div>

    <div class="btn-container">
      <a href="<?php echo $destino; ?>" class="back-btn">
        <span>Volver al Menú</span>
      </a>
    </div>
  </div>
</body>
</html>
