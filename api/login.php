<?php
require_once 'db.php';
$datos = exigir_post();

$correo = trim($datos['correo'] ?? '');
$contrasena = (string)($datos['contrasena'] ?? '');
if ($correo === '' || $contrasena === '') {
    responder(400, ['exito' => false, 'mensaje' => 'Correo y contraseña son obligatorios']);
}

$stmt = $conexion->prepare('SELECT Id_usuario, Nombre, `Contraseña` FROM usuarios WHERE Email = ?');
$stmt->bind_param('s', $correo);
$stmt->execute();
$u = $stmt->get_result()->fetch_assoc();
$stmt->close();

$ok = false;
if ($u) {
    $guardada = (string)$u['Contraseña'];
    if (preg_match('/^\$2[aby]\$/', $guardada)) {
        $ok = password_verify($contrasena, $guardada);
    } else {
        // Cuentas antiguas con contraseña en texto plano: se aceptan una vez y se convierten a hash
        $ok = hash_equals($guardada, $contrasena);
        if ($ok) {
            $nuevo = password_hash($contrasena, PASSWORD_DEFAULT);
            $up = $conexion->prepare('UPDATE usuarios SET `Contraseña` = ? WHERE Id_usuario = ?');
            $up->bind_param('si', $nuevo, $u['Id_usuario']);
            $up->execute();
            $up->close();
        }
    }
}

if (!$ok) {
    responder(401, ['exito' => false, 'mensaje' => 'Correo o contraseña incorrectos']);
}

session_regenerate_id(true);
$_SESSION['usuario_id'] = (int)$u['Id_usuario'];
$_SESSION['usuario_nombre'] = $u['Nombre'];

// Garantiza que exista su fila de progreso
$curso = ID_CURSO;
$p = $conexion->prepare('INSERT IGNORE INTO progreso (Id_usuario, Id_curso) VALUES (?, ?)');
if (!$p) {
    error_log('login.php: ' . $conexion->error);
    responder(500, ['exito' => false, 'mensaje' => 'La tabla progreso está dañada o no existe. Importa sql/reparar_progreso.sql en phpMyAdmin.']);
}
$p->bind_param('ii', $u['Id_usuario'], $curso);
$p->execute();
$p->close();

responder(200, [
    'exito' => true,
    'mensaje' => 'Bienvenido, ' . $u['Nombre'],
    'usuario' => ['id' => (int)$u['Id_usuario'], 'nombre' => $u['Nombre']],
]);
