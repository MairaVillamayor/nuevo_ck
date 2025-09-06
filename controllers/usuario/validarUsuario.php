<?php
require_once __DIR__ . '/../../config/conexion.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['usuario_nombre'], $_POST['usuario_contraseña'])) {
    $usuario = $_POST['usuario_nombre'];
    $contraseña = $_POST['usuario_contraseña'];
    $pdo = getConexion();
    $consulta = "SELECT u.*, p.perfil_rol 
                FROM usuarios u 
                JOIN perfiles p ON u.RELA_perfil = p.ID_perfil 
                WHERE u.usuario_nombre = :usuario_nombre";
    $stmt = $pdo->prepare($consulta);
    $stmt->execute(['usuario_nombre' => $usuario]);
    $usuario_data = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($usuario_data && password_verify($contraseña, $usuario_data['usuario_contraseña'])) {
        $_SESSION['usuario_id'] = $usuario_data['ID_usuario'];
        $_SESSION['usuario_nombre'] = $usuario_data['usuario_nombre'];
        $_SESSION['perfil_rol'] = $usuario_data['perfil_rol'];
        $_SESSION['perfil_id'] = $usuario_data['RELA_perfil'];
        switch ($usuario_data['RELA_perfil']) {
            case 1: // Administrador
                header("location:../../views/admin/admin_dashboard.php");
                break;
            case 2: // Empleado
                header("location:../../views/empleado/empleado_dashboard.php");
                break;
            case 4: // Gerente
                header("location:../../views/gerente/gerente_dashboard.php");
                break;
            case 3: // Cliente
                header("location:../../views/cliente/interfaz.php");
                break;
            default:
                header("location:../../views/cliente/interfaz.php");
                break;
        }
        exit();
    } else {
        header("location:../../views/usuario/login.php?error=invalid");
        exit();
    }
} else {
    header("Location: ../../views/usuario/login.php");
    exit();
}
