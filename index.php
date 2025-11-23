<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Nucleo Diagnóstico</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="Styles/index.css">
</head>
<body>
  <div class="container">
    <div class="login-card">
      <div class="logo-container">
        <div class="logo-icon">
          <i class="fas fa-hospital-user"></i>
        </div>
        <h2>Nucleo Diagnóstico</h2>
        <p class="subtitle">Bienvenido al sistema de gestión</p>
      </div>

      <!-- Alerta de error -->
      <div class="alert alert-error" id="errorAlert">
        <i class="fas fa-exclamation-circle"></i>
        <span id="errorMessage"></span>
      </div>

      <!-- Alerta de éxito -->
      <div class="alert alert-success" id="successAlert">
        <i class="fas fa-check-circle"></i>
        <span id="successMessage"></span>
      </div>

      <!-- FORMULARIO -->
      <form action="./Actions/validate_login.php" method="post" id="loginForm" novalidate>
        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-id-badge"></i>
            Código de Usuario
          </label>
          <div class="input-wrapper">
            <input 
              type="text" 
              name="usuario" 
              id="usuario"
              class="form-control" 
              placeholder="Ingresa tu código (ej: 1, 2, 3...)"
              required
              autocomplete="username"
              pattern="[0-9]+"
              title="Ingresa solo números"
            >
            <i class="fas fa-id-badge input-icon"></i>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-lock"></i>
            Contraseña
          </label>
          <div class="input-wrapper">
            <input 
              type="password" 
              name="contrasena" 
              id="contrasena"
              class="form-control" 
              placeholder="Ingresa tu contraseña"
              required
              autocomplete="current-password"
            >
            <i class="fas fa-lock input-icon"></i>
            <i class="fas fa-eye password-toggle" id="togglePassword"></i>
          </div>
        </div>

        <button type="submit" class="btn-login" id="loginBtn">
          <span>Iniciar Sesión</span>
          <i class="fas fa-arrow-right"></i>
          <div class="spinner"></div>
        </button>
      </form>

    </div>
  </div>
  <script>
    // Toggle contraseña
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('contrasena');
    togglePassword.addEventListener('click', () => {
      const type = passwordInput.type === "password" ? "text" : "password";
      passwordInput.type = type;
      togglePassword.classList.toggle('fa-eye');
      togglePassword.classList.toggle('fa-eye-slash');
    });

    // Elementos
    const loginForm = document.getElementById('loginForm');
    const usuarioInput = document.getElementById('usuario');
    const loginBtn = document.getElementById('loginBtn');
    const errorAlert = document.getElementById('errorAlert');
    const errorMessage = document.getElementById('errorMessage');
    const successAlert = document.getElementById('successAlert');

    // Mostrar error
    function showError(message) {
      errorMessage.textContent = message;
      errorAlert.classList.add('show');
      successAlert.classList.remove('show');
    }

    // Validaciones
    loginForm.addEventListener('submit', function(e) {
      e.preventDefault();

      const usuario = usuarioInput.value.trim();
      const contrasena = passwordInput.value.trim();

      if (!usuario || !contrasena) {
        showError("Por favor, completa todos los campos");
        return;
      }

      if (contrasena.length < 1) {
        showError("Ingresa una contraseña válida");
        return;
      }

      loginBtn.classList.add('loading');
      loginBtn.disabled = true;

      setTimeout(() => {
        loginForm.submit();
      }, 400);
    });

    // Errores del servidor
    window.addEventListener("DOMContentLoaded", () => {
      const p = new URLSearchParams(window.location.search);
      const error = p.get("error");

      if (error === "invalid") showError("Usuario o contraseña incorrectos.");
      if (error === "empty") showError("Completa todos los campos.");
      if (error === "db") showError("Error de conexión.");
      if (error === "nouser") showError("El usuario no existe.");
    });
  </script>
</body>
</html>