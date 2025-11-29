<?php
session_start();

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'doctor') {
    header("Location: index.php");
    exit();
}

$doctor_codigo = $_SESSION['codigo'];

include("conecta.php");

// Detectar filtro seleccionado
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : 'dia';

// Fechas para filtros
$hoy = date('Y-m-d');
$fecha_inicio_semana = date('Y-m-d', strtotime('monday this week'));
$fecha_fin_semana = date('Y-m-d', strtotime('sunday this week'));
$primer_dia_mes = date('Y-m-01');
$ultimo_dia_mes = date('Y-m-t');

// Cambiar consulta según selección
switch ($filtro) {

    case 'semana':
        $query = "SELECT c.id_cita, c.fecha, c.hora,
                         p.nombre AS nombre_paciente,
                         d.nombre AS nombre_doctor,
                         d.especialidad
                  FROM citas c
                  INNER JOIN paciente p ON c.id_paciente = p.codigo
                  INNER JOIN doctor d ON c.id_doctor = d.codigo
                  WHERE c.id_doctor = $1
                  AND c.fecha BETWEEN $2 AND $3
                  ORDER BY c.fecha ASC, c.hora ASC";

        $params = array($doctor_codigo, $fecha_inicio_semana, $fecha_fin_semana);
        break;

    case 'mes':
        $query = "SELECT c.id_cita, c.fecha, c.hora,
                         p.nombre AS nombre_paciente,
                         d.nombre AS nombre_doctor,
                         d.especialidad
                  FROM citas c
                  INNER JOIN paciente p ON c.id_paciente = p.codigo
                  INNER JOIN doctor d ON c.id_doctor = d.codigo
                  WHERE c.id_doctor = $1
                  AND c.fecha BETWEEN $2 AND $3
                  ORDER BY c.fecha ASC, c.hora ASC";

        $params = array($doctor_codigo, $primer_dia_mes, $ultimo_dia_mes);
        break;

    default: // día
        $query = "SELECT c.id_cita, c.fecha, c.hora,
                         p.nombre AS nombre_paciente,
                         d.nombre AS nombre_doctor,
                         d.especialidad
                  FROM citas c
                  INNER JOIN paciente p ON c.id_paciente = p.codigo
                  INNER JOIN doctor d ON c.id_doctor = d.codigo
                  WHERE c.id_doctor = $1
                  AND c.fecha = $2
                  ORDER BY c.hora ASC";

        $params = array($doctor_codigo, $hoy);
}

$resultado = pg_query_params($conexion, $query, $params);

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mis Citas Médicas</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="Styles/cons.css">
</head>
<body>

<div class="container">
  <h2>Mis Citas Programadas</h2>

  <!-- Filtro -->
  <form method="GET" style="margin-bottom: 20px;">
    <label><strong>Ver citas por: </strong></label>
    <select name="filtro" onchange="this.form.submit()">
      <option value="dia" <?php if($filtro=='dia') echo 'selected'; ?>>Día</option>
      <option value="semana" <?php if($filtro=='semana') echo 'selected'; ?>>Semana</option>
      <option value="mes" <?php if($filtro=='mes') echo 'selected'; ?>>Mes</option>
    </select>
  </form>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Paciente</th>
          <th>Doctor</th>
          <th>Especialidad</th>
          <th>Fecha</th>
          <th>Horario</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($resultado && pg_num_rows($resultado) > 0) {

            $ahora = date('H:i:s');

            while ($fila = pg_fetch_assoc($resultado)) {
                $fecha_cita = $fila['fecha'];
                $hora_cita = $fila['hora'];

                // Estado visual
                if ($fecha_cita < $hoy || ($fecha_cita == $hoy && $hora_cita < $ahora)) {
                    $badge = "<span class='badge badge-pasada'>Pasada</span>";
                } elseif ($fecha_cita == $hoy) {
                    $badge = "<span class='badge badge-hoy'>Hoy</span>";
                } else {
                    $badge = "<span class='badge badge-futura'>Próxima</span>";
                }

                $hora_inicio = date('h:i A', strtotime($hora_cita));
                $hora_fin = date('h:i A', strtotime($hora_cita) + 3600);

                echo "<tr>";
                echo "<td>{$fila['id_cita']}</td>";
                echo "<td>{$fila['nombre_paciente']}</td>";
                echo "<td>{$fila['nombre_doctor']}</td>";
                echo "<td>{$fila['especialidad']}</td>";
                echo "<td>" . date('d/m/Y', strtotime($fecha_cita)) . "</td>";
                echo "<td>$hora_inicio - $hora_fin</td>";
                echo "<td>$badge</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No hay citas en este periodo</td></tr>";
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
