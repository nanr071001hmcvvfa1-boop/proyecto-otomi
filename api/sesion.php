<?php
// Devuelve el usuario con sesión iniciada, su progreso y las lecciones completadas
require_once 'db.php';
$id = exigir_sesion();
$curso = ID_CURSO;

$s = $conexion->prepare('SELECT Nombre, Email, `País` AS pais, Fecha_registro FROM usuarios WHERE Id_usuario = ?');
$s->bind_param('i', $id);
$s->execute();
$u = $s->get_result()->fetch_assoc();
$s->close();
if (!$u) {
    session_destroy();
    responder(401, ['exito' => false, 'mensaje' => 'Usuario no encontrado']);
}

$s = $conexion->prepare('SELECT Experiencia AS experiencia, Racha AS racha, Porcentaje AS porcentaje
                         FROM progreso WHERE Id_usuario = ? AND Id_curso = ?');
$s->bind_param('ii', $id, $curso);
$s->execute();
$progreso = $s->get_result()->fetch_assoc() ?: ['experiencia' => 0, 'racha' => 0, 'porcentaje' => 0];
$s->close();

$completadas = [];
$s = @$conexion->prepare('SELECT Id_leccion FROM leccion_completada WHERE Id_usuario = ?');
if ($s) {
    $s->bind_param('i', $id);
    $s->execute();
    foreach ($s->get_result()->fetch_all(MYSQLI_ASSOC) as $f) $completadas[] = $f['Id_leccion'];
    $s->close();
}

$foto = null;
$s = @$conexion->prepare('SELECT Foto_perfil FROM usuarios WHERE Id_usuario = ?');
if ($s) {
    $s->bind_param('i', $id);
    $s->execute();
    $foto = ($s->get_result()->fetch_assoc())['Foto_perfil'] ?? null;
    $s->close();
}
$u['foto'] = $foto ?: null;

responder(200, ['exito' => true, 'usuario' => ['id' => $id] + $u, 'progreso' => $progreso, 'completadas' => $completadas]);
