<?php
require_once 'db.php';
require_once 'paises.php';
$datos = exigir_post();

$nombre     = trim($datos['nombre'] ?? '');
$correo     = trim($datos['correo'] ?? '');
$contrasena = (string)($datos['contrasena'] ?? '');
$fecha_nac  = trim($datos['fecha_nac'] ?? '');
$pais       = trim($datos['pais'] ?? '');

if ($nombre === '' || $correo === '' || $contrasena === '' || $fecha_nac === '' || $pais === '') {
    responder(400, ['exito' => false, 'mensaje' => 'Todos los campos son obligatorios']);
}
if (mb_strlen($nombre) > 60)  responder(400, ['exito' => false, 'mensaje' => 'El nombre es demasiado largo (máx. 60)']);
if (!in_array($pais, PAISES_PERMITIDOS, true)) {
    responder(400, ['exito' => false, 'mensaje' => 'Selecciona un país de la lista']);
}
if (mb_strlen($correo) > 100 || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    responder(400, ['exito' => false, 'mensaje' => 'El correo no es válido']);
}
if (strlen($contrasena) < 6) {
    responder(400, ['exito' => false, 'mensaje' => 'La contraseña debe tener al menos 6 caracteres']);
}
$f = DateTime::createFromFormat('Y-m-d', $fecha_nac);
if (!$f || $f->format('Y-m-d') !== $fecha_nac) {
    responder(400, ['exito' => false, 'mensaje' => 'La fecha de nacimiento no es válida']);
}
$hoy = new DateTime('today');
if ($f > (clone $hoy)->modify('-10 years')) {
    responder(400, ['exito' => false, 'mensaje' => 'Debes tener al menos 10 años para registrarte']);
}
if ($f < (clone $hoy)->modify('-120 years')) {
    responder(400, ['exito' => false, 'mensaje' => 'La fecha de nacimiento no es válida']);
}

$stmt = $conexion->prepare('SELECT Id_usuario FROM usuarios WHERE Email = ?');
$stmt->bind_param('s', $correo);
$stmt->execute();
$existe = $stmt->get_result()->num_rows > 0;
$stmt->close();
if ($existe) {
    responder(409, ['exito' => false, 'mensaje' => 'Ese correo ya está registrado']);
}

$hash = password_hash($contrasena, PASSWORD_DEFAULT);
$stmt = $conexion->prepare(
    'INSERT INTO usuarios (Nombre, Email, `Contraseña`, Fecha_registro, Fecha_nac, `País`) VALUES (?, ?, ?, NOW(), ?, ?)'
);
$stmt->bind_param('sssss', $nombre, $correo, $hash, $fecha_nac, $pais);
if (!$stmt->execute()) {
    error_log('registro.php: ' . $stmt->error);
    responder(500, ['exito' => false, 'mensaje' => 'No se pudo crear la cuenta']);
}
$nuevo_id = $conexion->insert_id;
$stmt->close();

$curso = ID_CURSO;
$p = $conexion->prepare('INSERT IGNORE INTO progreso (Id_usuario, Id_curso) VALUES (?, ?)');
if (!$p) {
    error_log('registro.php: ' . $conexion->error);
    responder(500, ['exito' => false, 'mensaje' => 'La tabla progreso está dañada o no existe. Importa sql/reparar_progreso.sql en phpMyAdmin.']);
}
$p->bind_param('ii', $nuevo_id, $curso);
$p->execute();
$p->close();

// Deja la sesión iniciada para entrar directo
session_regenerate_id(true);
$_SESSION['usuario_id'] = $nuevo_id;
$_SESSION['usuario_nombre'] = $nombre;

responder(200, ['exito' => true, 'mensaje' => 'Usuario registrado correctamente', 'usuario' => ['id' => $nuevo_id, 'nombre' => $nombre]]);
