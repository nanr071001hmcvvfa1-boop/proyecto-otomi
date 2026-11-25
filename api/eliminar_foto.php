<?php
// POST (sin cuerpo necesario): quita la foto de perfil y vuelve al avatar.
require_once 'db.php';
exigir_post();
$id = exigir_sesion();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $s = $conexion->prepare('SELECT Foto_perfil FROM usuarios WHERE Id_usuario = ?');
    $s->bind_param('i', $id);
    $s->execute();
    $anterior = ($s->get_result()->fetch_assoc())['Foto_perfil'] ?? null;
    $s->close();

    $s = $conexion->prepare('UPDATE usuarios SET Foto_perfil = NULL WHERE Id_usuario = ?');
    $s->bind_param('i', $id);
    $s->execute();
    $s->close();

    borrar_foto_archivo($anterior);
    responder(200, ['exito' => true, 'mensaje' => 'Foto eliminada']);
} catch (Throwable $e) {
    error_log('eliminar_foto.php: ' . $e->getMessage());
    responder(500, ['exito' => false, 'mensaje' => 'No se pudo quitar la foto.']);
}
