<?php
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['codigo'])) {
    header("Location: index.php");
    exit();
}

include("conecta.php");

// Consulta con JOIN para obtener nombres de paciente y doctor
$query = "SELECT c.id_cita, c.id_paciente, c.id_doctor, c.fecha, c.hora,
          p.nombre as nombre_paciente,
          d.nombre as nombre_doctor, d.especialidad
          FROM citas c
          INNER JOIN paciente p ON c.id_paciente = p.codigo
          INNER JOIN doctor d ON c.id_doctor = d.codigo
          ORDER BY c.fecha DESC, c.hora DESC";

$resultado = pg_query($conexion, $query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lista de Citas - Nucleo Diagnóstico</title>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="Styles/cons.css">
  <style>
    /* Estilos adicionales para estados de citas */
    .cita-pasada {
      background-color: #fff3cd !important;
    }
    .cita-hoy {
      background-color: #d1ecf1 !important;
    }
    .cita-futura {
      background-color: #d4edda !important;
    }
    .badge {
      display: inline-block;
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 0.8rem;
      font-weight: 600;
    }
    .badge-pasada {
      background: #ffc107;
      color: #856404;
    }
    .badge-hoy {
      background: #17a2b8;
      color: white;
    }
    .badge-futura {
      background: #28a745;
      color: white;
    }
    
    /* Botones de acción */
    .action-buttons {
      display: flex;
      gap: 8px;
      justify-content: center;
    }
    
    .btn-action {
      padding: 6px 12px;
      border: none;
      border-radius: 8px;
      font-size: 0.85rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }
    
    .btn-edit {
      background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);
      color: white;
      box-shadow: 0 2px 8px rgba(155, 89, 182, 0.3);
    }
    
    .btn-edit:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(155, 89, 182, 0.4);
    }
    
    .btn-delete {
      background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
      color: white;
      box-shadow: 0 2px 8px rgba(231, 76, 60, 0.3);
    }
    
    .btn-delete:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(231, 76, 60, 0.4);
    }
    
    .btn-action i {
      font-size: 0.9rem;
    }
    
    /* Mensaje de confirmación */
    .confirm-dialog {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: 1000;
      align-items: center;
      justify-content: center;
    }
    
    .confirm-content {
      background: white;
      padding: 30px;
      border-radius: 16px;
      max-width: 400px;
      text-align: center;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }
    
    .confirm-content h3 {
      color: #333;
      margin-bottom: 15px;
    }
    
    .confirm-content p {
      color: #666;
      margin-bottom: 20px;
    }
    
    .confirm-buttons {
      display: flex;
      gap: 10px;
      justify-content: center;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Registro de Citas Médicas</h2>

    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th><i class="fas fa-hashtag"></i> ID</th>
            <th><i class="fas fa-user-injured"></i> Paciente</th>
            <th><i class="fas fa-user-md"></i> Doctor</th>
            <th><i class="fas fa-stethoscope"></i> Especialidad</th>
            <th><i class="fas fa-calendar-day"></i> Fecha</th>
            <th><i class="fas fa-clock"></i> Horario</th>
            <th><i class="fas fa-info-circle"></i> Estado</th>
            <th><i class="fas fa-cog"></i> Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($resultado && pg_num_rows($resultado) > 0) {
            $hoy = date('Y-m-d');
            $ahora = date('H:i:s');
            
            while ($fila = pg_fetch_assoc($resultado)) {
              $fecha_cita = $fila['fecha'];
              $hora_cita = $fila['hora'];
              $clase_estado = '';
              $badge_texto = '';
              $badge_clase = '';
              $puede_modificar = false;
              
              // Determinar el estado de la cita
              if ($fecha_cita < $hoy || ($fecha_cita == $hoy && $hora_cita < $ahora)) {
                $clase_estado = 'cita-pasada';
                $badge_texto = 'Pasada';
                $badge_clase = 'badge-pasada';
                $puede_modificar = false;
              } elseif ($fecha_cita == $hoy) {
                $clase_estado = 'cita-hoy';
                $badge_texto = 'Hoy';
                $badge_clase = 'badge-hoy';
                $puede_modificar = true;
              } else {
                $clase_estado = 'cita-futura';
                $badge_texto = 'Próxima';
                $badge_clase = 'badge-futura';
                $puede_modificar = true;
              }
              
              // Calcular hora de fin (1 hora después)
              $hora_fin = date('h:i A', strtotime($hora_cita) + 3600);
              $hora_inicio = date('h:i A', strtotime($hora_cita));
              
              echo "<tr class='$clase_estado'>";
              echo "<td>" . htmlspecialchars($fila['id_cita']) . "</td>";
              echo "<td>" . htmlspecialchars($fila['nombre_paciente']) . "</td>";
              echo "<td>" . htmlspecialchars($fila['nombre_doctor']) . "</td>";
              echo "<td>" . htmlspecialchars($fila['especialidad']) . "</td>";
              echo "<td>" . date('d/m/Y', strtotime($fila['fecha'])) . "</td>";
              echo "<td>" . $hora_inicio . " - " . $hora_fin . "</td>";
              echo "<td><span class='badge $badge_clase'>$badge_texto</span></td>";
              echo "<td>";
              
              if ($puede_modificar) {
                echo "<div class='action-buttons'>";
                echo "<a href='editar_cita.php?id=" . $fila['id_cita'] . "' class='btn-action btn-edit' title='Modificar cita'>";
                echo "<i class='fas fa-edit'></i><span>Editar</span>";
                echo "</a>";
                echo "<button onclick='confirmarCancelar(" . $fila['id_cita'] . ", \"" . htmlspecialchars($fila['nombre_paciente']) . "\")' class='btn-action btn-delete' title='Cancelar cita'>";
                echo "<i class='fas fa-trash'></i><span>Cancelar</span>";
                echo "</button>";
                echo "</div>";
              } else {
                echo "<span style='color: #999; font-size: 0.85rem;'>No disponible</span>";
              }
              
              echo "</td>";
              echo "</tr>";
            }
          } else {
            echo "<tr><td colspan='8'>No hay citas registradas</td></tr>";
          }
          pg_close($conexion);
          ?>
        </tbody>
      </table>
    </div>

    <div class="btn-container">
      <a href="menu_empleado.php" class="back-btn">
        <span>Volver al Menú</span>
      </a>
    </div>
  </div>

  <!-- Diálogo de confirmación para cancelar -->
  <div id="confirmDialog" class="confirm-dialog">
    <div class="confirm-content">
      <div style="font-size: 3rem; color: #e74c3c; margin-bottom: 15px;">
        <i class="fas fa-exclamation-triangle"></i>
      </div>
      <h3>¿Cancelar esta cita?</h3>
      <p id="confirmMessage"></p>
      <div class="confirm-buttons">
        <button onclick="cancelarCita()" class="btn-action btn-delete">
          <i class="fas fa-check"></i> Sí, cancelar
        </button>
        <button onclick="cerrarDialog()" class="btn-action btn-edit">
          <i class="fas fa-times"></i> No, mantener
        </button>
      </div>
    </div>
  </div>

  <script>
    let citaIdCancelar = null;

    function confirmarCancelar(citaId, nombrePaciente) {
      citaIdCancelar = citaId;
      document.getElementById('confirmMessage').textContent = 
        'Se cancelará la cita del paciente: ' + nombrePaciente;
      document.getElementById('confirmDialog').style.display = 'flex';
    }

    function cerrarDialog() {
      document.getElementById('confirmDialog').style.display = 'none';
      citaIdCancelar = null;
    }

    function cancelarCita() {
      if (citaIdCancelar) {
        window.location.href = 'Actions/cancelar_cita.php?id=' + citaIdCancelar;
      }
    }

    // Cerrar diálogo al hacer clic fuera
    document.getElementById('confirmDialog').addEventListener('click', function(e) {
      if (e.target === this) {
        cerrarDialog();
      }
    });
  </script>
</body>
</html>