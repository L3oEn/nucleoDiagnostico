<?php
session_start();

// Verificar que solo doctores accedan
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'doctor') {
    header("Location: index.php");
    exit();
}

$doctor_codigo = $_SESSION['codigo'];

include("conecta.php");

// Consulta SOLO pacientes del doctor, (sin repetir, para eso el DISTRICT)
$query = "
SELECT DISTINCT 
    p.codigo, 
    p.nombre, 
    p.direccion, 
    p.telefono, 
    p.fecha_nac, 
    p.sexo, 
    p.edad, 
    p.estatura
FROM paciente p
INNER JOIN citas c ON c.id_paciente = p.codigo
WHERE c.id_doctor = $1
ORDER BY p.codigo ASC
";

$resultado = pg_query_params($conexion, $query, array($doctor_codigo));

if (!$resultado) {
    die('<strong>Error en la consulta:</strong> ' . pg_last_error($conexion));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mis Pacientes - Nucleo Diagnóstico</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="Styles/cons.css">
</head>
<body>
  <div class="container">
    <h2>Mis Pacientes</h2>
    

    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th><i class="fas fa-hashtag"></i> Código</th>
            <th><i class="fas fa-user"></i> Nombre</th>
            <th><i class="fas fa-map-marker-alt"></i> Dirección</th>
            <th><i class="fas fa-phone"></i> Teléfono</th>
            <th><i class="fas fa-calendar"></i> Fecha Nac.</th>
            <th><i class="fas fa-venus-mars"></i> Sexo</th>
            <th><i class="fas fa-user-clock"></i> Edad</th>
            <th><i class="fas fa-ruler-vertical"></i> Estatura</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($resultado && pg_num_rows($resultado) > 0) {
            while ($fila = pg_fetch_assoc($resultado)) {
              echo "<tr>";
              echo "<td>" . htmlspecialchars($fila['codigo']) . "</td>";
              echo "<td>" . htmlspecialchars($fila['nombre']) . "</td>";
              echo "<td>" . htmlspecialchars($fila['direccion']) . "</td>";
              echo "<td>" . htmlspecialchars($fila['telefono']) . "</td>";
              echo "<td>" . htmlspecialchars($fila['fecha_nac']) . "</td>";
              echo "<td>" . htmlspecialchars($fila['sexo']) . "</td>";
              echo "<td>" . htmlspecialchars($fila['edad']) . " años</td>";
              echo "<td>" . number_format($fila['estatura'], 2) . " m</td>";
              echo "</tr>";
            }
          } else {
            echo "<tr><td colspan='8'>No hay pacientes asignados a ti</td></tr>";
          }

          pg_close($conexion);
          ?>
        </tbody>
      </table>
    </div>

    <div class="btn-container">
      <a href="menu_doc.php" class="back-btn">
        <span>Volver al Menú</span>
      </a>
    </div>

  </div>
</body>
</html>

