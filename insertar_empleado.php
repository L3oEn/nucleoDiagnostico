<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Insertar Empleada</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="formulario">
    <h2>Insertar Nueva Empleada</h2>
    <form action="insertar_empleado_action.php" method="post">
      <input type="text" name="nombre" placeholder="Nombre completo" required>
      <input type="text" name="direccion" placeholder="Dirección" required>
      <input type="text" name="telefono" placeholder="Teléfono" required>
      <input type="date" name="fecha_nacimiento" required>
      <input type="text" name="sexo" placeholder="Sexo (M/F)" required>
      <input type="number" name="sueldo" step="0.01" placeholder="Sueldo" required>
      <input type="text" name="turno" placeholder="Turno" required>
      <input type="password" name="contrasena" placeholder="Contraseña" required>
      <button type="submit">Guardar Empleada</button>
    </form>
  </div>
</body>
</html>