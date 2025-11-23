<?php
include("conecta.php");

$query = "SELECT * FROM doctor ORDER BY codigo ASC";
$resultado = pg_query($conexion, $query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Consulta General de Doctores</title>
  <link rel="stylesheet" href="Styles/cons.css">
</head>
<body>
  <div class="container">
    <h2>Consulta General de Doctores</h2>
    <table>
      <thead>
        <tr>
          <th>Código</th>
          <th>Nombre</th>
          <th>Dirección</th>
          <th>Teléfono</th>
          <th>Especialidad</th>
          <th>Fecha de Nacimiento</th>
          <th>Sexo</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($resultado && pg_num_rows($resultado) > 0) {
          while ($row = pg_fetch_assoc($resultado)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['codigo']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
            echo "<td>" . htmlspecialchars($row['direccion']) . "</td>";
            echo "<td>" . htmlspecialchars($row['telefono']) . "</td>";
            echo "<td>" . htmlspecialchars($row['especialidad']) . "</td>";
            echo "<td>" . htmlspecialchars($row['fecha_nac']) . "</td>";
            echo "<td>" . htmlspecialchars($row['sexo']) . "</td>";
            echo "</tr>";
          }
        } else {
          echo "<tr><td colspan='9'>No hay doctores registrados</td></tr>";
        }
        pg_close($conexion);
        ?>
      </tbody>
    </table>

    <div class="btn-container">
      <a href="menu.php" class="back-btn">Volver al menú</a>
    </div>
  </div>
</body>
</html>