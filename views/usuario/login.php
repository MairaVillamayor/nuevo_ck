<?php
require_once dirname(__DIR__, 2) . '/includes/navegacion.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Cake Party</title>
    <link rel="stylesheet" href="public/css/login.css">
</head>

<body>
    <div class="top-bar">
        ¡Bienvenido a Cake Party! Iniciá sesión para continuar 🎂
    </div>

    <nav class="main-nav">
        <div class="logo">Cake Party</div>
        <ul>
            <li><a href="views/cliente/interfaz.php">Inicio</a></li>
            <li><a href="#">Pasteles</a></li>
            <?php if (!isset($_SESSION['usuario_id'])): ?>
                <li><a href="login.php" class="active">Login</a></li>
            <?php else: ?>
                <li><a href="cliente/interfaz.php">Mi Cuenta</a></li>
                <li><a href="../controllers/usuario/logout.php">Cerrar Sesión</a></li>
            <?php endif; ?>
        </ul>
        <div class="icons">🍰</div>
    </nav>
    <div class="container">
        <div class="card">
            <span class="singup">Iniciar Sesión</span>
            <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid'): ?>
                <div class="alert-error" style="margin-bottom: 20px; padding: 10px; background-color: #ffebee; color: #d32f2f; border-radius: 5px;">
                    ❌ Usuario o contraseña incorrectos
                </div>
            <?php endif; ?>

            <form action="controllers/usuario/validarUsuario.php" method="post" class="login-form" autocomplete="off">
                <div class="inputBox">
                    <input type="text" id="usuario_nombre" name="usuario_nombre" placeholder=" " required>
                    <span>Usuario</span>
                </div>
                <div class="inputBox">
                    <input type="password" id="usuario_contraseña" name="usuario_contraseña" placeholder=" " required>
                    <span>Contraseña</span>
                </div>
                <button type="submit" class="enter">Ingresar</button>
                <p style="font-size: 14px; margin-top: 10px; text-align:center;">¿No tienes cuenta?
                    <a href="views/usuario/registro.php">Regístrate aquí</a>
                </p>
            </form>
        </div>
    </div>
   
    <script>
        // Animación universal para labels flotantes
        document.querySelectorAll('.inputBox input').forEach(input => {
            const toggleFilled = () => {
                if (input.value.trim() !== '') {
                    input.parentNode.classList.add('filled');
                } else {
                    input.parentNode.classList.remove('filled');
                }
            };
            input.addEventListener('input', toggleFilled);
            input.addEventListener('change', toggleFilled);
            input.addEventListener('focus', toggleFilled);
            toggleFilled();
        });
    </script>
</body>

</html>