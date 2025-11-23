<?php
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['codigo'])) {
    header("Location: index.php");
    exit();
}


include("conecta.php");

// Obtener parámetros
$doctor_id = isset($_GET['doctor_id']) ? intval($_GET['doctor_id']) : 0;
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');


if ($doctor_id == 0) {
    die("Doctor no especificado.");
}


// Obtener información del doctor
$query_doctor = "SELECT nombre, especialidad FROM doctor WHERE codigo = $doctor_id";
$result_doctor = pg_query($conexion, $query_doctor);
$doctor = pg_fetch_assoc($result_doctor);


// Consultar citas existentes del doctor en la fecha seleccionada
$query_citas = "SELECT hora FROM citas WHERE id_doctor = $doctor_id AND fecha = '$fecha'";
$result_citas = pg_query($conexion, $query_citas);


$ocupadas = [];
while ($row = pg_fetch_assoc($result_citas)) {
    $ocupadas[] = $row['hora'];
}


// Definir horario de atención
$hora_inicio = 8;  // 08:00 AM
$hora_fin = 20;    // 08:00 PM
$duracion_cita = 1; // 1 hora


$disponibles = [];
for ($h = $hora_inicio; $h < $hora_fin; $h++) {
    $hora_formato = sprintf("%02d:00:00", $h);
    if (!in_array($hora_formato, $ocupadas)) {
        $disponibles[] = $h . ":00";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Disponibilidad del Doctor</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="Styles/form.css">
<style>
    .disponibilidad-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    .disponibilidad-table th, .disponibilidad-table td { border: 1px solid #ccc; padding: 10px; text-align: center; }
    .ocupado { background-color: #f8d7da; color: #721c24; }
    .libre { background-color: #d4edda; color: #155724; cursor: pointer; }
    .btn-back { display: inline-block; margin-top: 20px; padding: 10px 20px; background: linear-gradient(135deg,#9b59b6 0%,#8e44ad 100%); color: #fff; text-decoration: none; border-radius: 5px; }
</style>
</head>
<body>
<div class="container">
    <div class="form-card">
        <div class="form-header">
            <div class="header-icon" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <h2>Disponibilidad del Doctor</h2>
            <p>Doctor: <strong><?php echo htmlspecialchars($doctor['nombre']); ?></strong> (<?php echo htmlspecialchars($doctor['especialidad']); ?>)</p>
            <p>Fecha: <strong><?php echo date('d/m/Y', strtotime($fecha)); ?></strong></p>
        </div>


        <table class="disponibilidad-table">
            <tr>
                <th>Hora</th>
                <th>Estado</th>
            </tr>
            <?php
            for ($h = $hora_inicio; $h < $hora_fin; $h++) {
                $hora_str = sprintf("%02d:00:00", $h);
                $hora_mostrar = sprintf("%02d:00", $h);
                $estado = in_array($hora_str, $ocupadas) ? 'Ocupado' : 'Libre';
                $clase = in_array($hora_str, $ocupadas) ? 'ocupado' : 'libre';
                echo "<tr class='$clase'><td>$hora_mostrar</td><td>$estado</td></tr>";
            }
            ?>
        </table>


        <a href="insertar_cita.php" class="btn-back"><i class="fas fa-arrow-left"></i> Volver a Agendar Cita</a>
    </div>
</div>
</body>
</html>
<?php pg_close($conexion); ?>