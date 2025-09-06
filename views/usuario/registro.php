<?php
require_once '../../config/conexion.php';
require_once "C:/laragon/www/nuevo_ck/includes/navegacion.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Usuario</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/registro.css">
</head>

<body>
    <div class="top-bar">
        ¡Bienvenido a Cake Party! Registrá tu cuenta
    </div>

    <nav class="main-nav">
        <div class="logo">Cake Party</div>
        <ul>
            <li><a href="../cliente/interfaz.php">Inicio</a></li>
            <li><a href="#">Pasteles</a></li>
            <?php if (!isset($_SESSION['usuario_id'])): ?>
                <li><a href="login.php" class="active">Login</a></li>
            <?php else: ?>
                <li><a href="../cliente/interfaz.php">Mi Cuenta</a></li>
                <li><a href="../../controllers/usuario/logout.php">Cerrar Sesión</a></li>
            <?php endif; ?>
        </ul>
        <div class="icons">🎂</div>
    </nav>

    <div class="container">
        <div class="card">
            <span class="singup">Registrar Usuario</span>
            <form action="../../controllers/usuario/alta_usuario.php" method="POST" autocomplete="off" onsubmit="return validarFormulario()" novalidate>
                <div class="inputBox">
                    <input type="text" id="persona_nombre" name="persona_nombre" placeholder=" " required>
                    <span>Nombre</span>
                    <div id="persona_nombre-error" class="ajax-error" style="display:none;"></div>
                </div>
                <div class="inputBox">
                    <input type="text" id="persona_apellido" name="persona_apellido" placeholder=" " required>
                    <span>Apellido</span>
                    <div id="persona_apellido-error" class="ajax-error" style="display:none;"></div>
                </div>
                <div class="inputBox" id="fecha-nacimiento-box">
                    <input type="date" id="persona_fecha_nacimiento" name="persona_fecha_nacimiento" required max="2011-12-31">
                    <span>Fecha de Nacimiento</span>
                    <div id="persona_fecha_nacimiento-ajax-error" class="ajax-error" style="display:none;"></div>
                </div>
                <div class="inputBox">
                    <input type="text" id="persona_direccion" name="persona_direccion" placeholder=" " required>
                    <span>Dirección</span>
                    <div id="persona_direccion-ajax-error" class="ajax-error" style="display:none;"></div>
                </div>
                <div class="inputBox">
                    <input type="text" id="usuario_nombre" name="usuario_nombre" placeholder=" " required>
                    <span>Nombre de Usuario</span>
                </div>
                <div class="inputBox">
                    <input type="email" id="usuario_correo_electronico" name="usuario_correo_electronico" placeholder=" " required>
                    <span>Correo Electrónico</span>
                </div>
                <div class="inputBox">
                    <input type="password" id="usuario_contraseña" name="usuario_contraseña" placeholder=" " required>
                    <span>Contraseña</span>
                    <div id="password-length-error" class="ajax-error" style="display:none;"></div>
                </div>
                <div class="inputBox">
                    <input type="password" id="confirmar_contraseña" name="confirmar_contraseña" placeholder=" " required>
                    <span>Confirmar Contraseña</span>
                    <div id="password-match-error" class="ajax-error" style="display:none;"></div>
                </div>
                <div class="inputBox">
                    <input type="tel" id="usuario_numero_de_celular" name="usuario_numero_de_celular" placeholder=" ">
                    <span>Número de Celular (opcional)</span>
                </div>
                <input type="hidden" name="RELA_perfil" value="3">
                <div id="password-error" class="password-error" style="display: none;"></div>
                <button type="submit" class="enter" name="registro" onclick="return validarFormulario()">Registrar</button>
                <p style="font-size: 14px; margin-top: 10px; text-align:center;">¿Ya tenés cuenta?
                    <a href="login.php">Iniciá sesión</a>
                </p>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // FORZAR clase filled en fecha inmediatamente
        const fechaBox = document.getElementById('fecha-nacimiento-box');
        if (fechaBox) {
            fechaBox.classList.add('filled');
        }
        
        // Animación universal para labels
        document.querySelectorAll('.inputBox input').forEach(input => {
            const toggleFilled = () => {
                const isDate = input.type === 'date';
                const hasValue = input.value.trim() !== '';
                
                if (isDate) {
                    input.parentNode.classList.add('filled');
                } else if (hasValue) {
                    input.parentNode.classList.add('filled');
                } else {
                    input.parentNode.classList.remove('filled');
                }
            };
            
            input.addEventListener('input', toggleFilled);
            input.addEventListener('change', toggleFilled);
            input.addEventListener('focus', toggleFilled);
            input.addEventListener('blur', toggleFilled);
            
            toggleFilled();
        });
        
        // Validación AJAX - usuario
        document.getElementById('usuario_nombre').addEventListener('blur', function() {
            const valor = this.value.trim();
            if (valor.length < 3) {
                mostrarErrorAjax('usuario_nombre', 'El usuario debe tener al menos 3 caracteres.', true);
                usuarioDisponible = false;
                window.registroAjaxError = true;
                return;
            }
            fetch('../../controllers/usuario/validar_registro.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'usuario_nombre=' + encodeURIComponent(valor)
            })
            .then(res => res.json())
            .then(data => {
                mostrarErrorAjax('usuario_nombre', data.message, !data.success);
                usuarioDisponible = data.success;
                window.registroAjaxError = !data.success;
            });
        });

        // Validación AJAX - email
        document.getElementById('usuario_correo_electronico').addEventListener('blur', function() {
            const valor = this.value.trim();
            if (!valor.match(/^\S+@\S+\.\S+$/)) {
                mostrarErrorAjax('usuario_correo_electronico', 'Correo electrónico no válido.', true);
                emailDisponible = false;
                window.registroAjaxError = true;
                return;
            }
            fetch('../../controllers/usuario/validar_registro.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'usuario_correo_electronico=' + encodeURIComponent(valor)
            })
            .then(res => res.json())
            .then(data => {
                mostrarErrorAjax('usuario_correo_electronico', data.message, !data.success);
                emailDisponible = data.success;
                window.registroAjaxError = !data.success;
            });
        });

        // Validación en tiempo real de contraseña
        const passInput = document.getElementById('usuario_contraseña');
        const passConfirmInput = document.getElementById('confirmar_contraseña');
        const passLengthErrorDiv = document.getElementById('password-length-error');
        const passMatchErrorDiv = document.getElementById('password-match-error');
        function validarPasswordRealtime() {
            const pass = passInput.value;
            const confirm = passConfirmInput.value;
            let error = false;
            if (pass.length > 0 && pass.length < 8) {
                passLengthErrorDiv.textContent = 'La contraseña debe tener al menos 8 caracteres';
                passLengthErrorDiv.style.display = 'block';
                error = true;
            } else {
                passLengthErrorDiv.textContent = '';
                passLengthErrorDiv.style.display = 'none';
            }
            if (pass && confirm && pass !== confirm) {
                passMatchErrorDiv.textContent = 'Las contraseñas no coinciden';
                passMatchErrorDiv.style.display = 'block';
                error = true;
            } else {
                passMatchErrorDiv.textContent = '';
                passMatchErrorDiv.style.display = 'none';
            }
            window.registroAjaxError = error;
        }
        passInput.addEventListener('input', validarPasswordRealtime);
        passConfirmInput.addEventListener('input', validarPasswordRealtime);

        // Validación personalizada en blur para Nombre y Apellido
        const nombreInput = document.getElementById('persona_nombre');
        const apellidoInput = document.getElementById('persona_apellido');
        nombreInput.addEventListener('blur', function () {
            const valor = this.value.trim();
            mostrarErrorAjax('persona_nombre', valor ? '' : 'El nombre es obligatorio.', true);
        });
        apellidoInput.addEventListener('blur', function () {
            const valor = this.value.trim();
            mostrarErrorAjax('persona_apellido', valor ? '' : 'El apellido es obligatorio.', true);
        });
    });

    function validarFormulario() {
        // Validaciones personalizadas
        const nombre = document.getElementById('persona_nombre').value.trim();
        const apellido = document.getElementById('persona_apellido').value.trim();
        const direccion = document.getElementById('persona_direccion').value.trim();
        const fechaNacimiento = document.getElementById('persona_fecha_nacimiento').value;
        const usuarioNombre = document.getElementById('usuario_nombre').value.trim();
        const email = document.getElementById('usuario_correo_electronico').value.trim();
        const contraseña = document.getElementById('usuario_contraseña').value;
        const confirmar = document.getElementById('confirmar_contraseña').value;
        const errorDiv = document.getElementById('password-error');
        let hayErrores = false;

        // Validar nombre
        if (!nombre) {
            mostrarErrorAjax('persona_nombre', 'El nombre es obligatorio.', true);
            hayErrores = true;
        } else {
            mostrarErrorAjax('persona_nombre', '', false);
        }

        // Validar apellido
        if (!apellido) {
            mostrarErrorAjax('persona_apellido', 'El apellido es obligatorio.', true);
            hayErrores = true;
        } else {
            mostrarErrorAjax('persona_apellido', '', false);
        }

        // Validar dirección
        if (!direccion) {
            mostrarErrorAjax('persona_direccion', 'La dirección es obligatoria.', true);
            hayErrores = true;
        } else {
            mostrarErrorAjax('persona_direccion', '', false);
        }

        // Validar fecha de nacimiento
        if (!fechaNacimiento) {
            mostrarErrorAjax('persona_fecha_nacimiento', 'La fecha de nacimiento es obligatoria.', true);
            hayErrores = true;
        } else {
            // Validar que la fecha no sea futura
            const fechaActual = new Date();
            const fechaNac = new Date(fechaNacimiento);
            if (fechaNac > fechaActual) {
                mostrarErrorAjax('persona_fecha_nacimiento', 'La fecha de nacimiento no puede ser futura.', true);
                hayErrores = true;
            } else {
                // Validar edad mínima (por ejemplo, 13 años)
                const edadMinima = new Date();
                edadMinima.setFullYear(edadMinima.getFullYear() - 13);
                if (fechaNac > edadMinima) {
                    mostrarErrorAjax('persona_fecha_nacimiento', 'Debes tener al menos 13 años para registrarte.', true);
                    hayErrores = true;
                } else {
                    mostrarErrorAjax('persona_fecha_nacimiento', '', false);
                }
            }
        }

        // Validar nombre de usuario
        if (!usuarioNombre) {
            mostrarErrorAjax('usuario_nombre', 'El nombre de usuario es obligatorio.', true);
            hayErrores = true;
        } else if (usuarioNombre.length < 3) {
            mostrarErrorAjax('usuario_nombre', 'El usuario debe tener al menos 3 caracteres.', true);
            hayErrores = true;
        } else {
            mostrarErrorAjax('usuario_nombre', '', false);
        }

        // Validar email
        if (!email) {
            mostrarErrorAjax('usuario_correo_electronico', 'El correo electrónico es obligatorio.', true);
            hayErrores = true;
        } else if (!email.match(/^\S+@\S+\.\S+$/)) {
            mostrarErrorAjax('usuario_correo_electronico', 'Correo electrónico no válido.', true);
            hayErrores = true;
        } else {
            mostrarErrorAjax('usuario_correo_electronico', '', false);
        }

        // Validar contraseña
        if (!contraseña) {
            errorDiv.textContent = 'La contraseña es obligatoria';
            errorDiv.style.display = 'block';
            hayErrores = true;
        } else if (contraseña.length < 8) {
            errorDiv.textContent = 'La contraseña debe tener al menos 8 caracteres';
            errorDiv.style.display = 'block';
            hayErrores = true;
        } else if (contraseña !== confirmar) {
            errorDiv.textContent = 'Las contraseñas no coinciden';
            errorDiv.style.display = 'block';
            hayErrores = true;
        } else {
            errorDiv.style.display = 'none';
        }

        // Validar confirmar contraseña
        if (!confirmar) {
            errorDiv.textContent = 'Debe confirmar la contraseña';
            errorDiv.style.display = 'block';
            hayErrores = true;
        }

        if (window.registroAjaxError || hayErrores) {
            return false;
        }
        return true;
    }

    function mostrarErrorAjax(idInput, mensaje, esError) {
        let div = document.getElementById(idInput + '-ajax-error');
        if (!div) {
            div = document.createElement('div');
            div.id = idInput + '-ajax-error';
            div.className = 'ajax-error';
            document.getElementById(idInput).parentNode.appendChild(div);
        }
        div.textContent = mensaje;
        div.style.color = esError ? '#d32f2f' : '#388e3c';
        div.style.fontSize = '13px';
        div.style.marginTop = '3px';
        div.style.display = mensaje ? 'block' : 'none';
    }

    let usuarioDisponible = false;
    let emailDisponible = false;
    window.registroAjaxError = false;
    </script>
    <style>
    .ajax-error {
        margin-top: 3px;
        font-size: 13px;
        color: #d32f2f;
        display: none;
    }
    </style>
</body>

</html>