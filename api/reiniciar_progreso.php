<?php
// Botón "Reiniciar progreso" del menú: borra las lecciones completadas y el porcentaje (conserva XP y racha)
require_once 'db.php';
exigir_post();
$id = exigir_sesion();
$curso = ID_CURSO;

$s = $conexion->prepare('DELETE FROM leccion_completada WHERE Id_usuario = ?');
$s->bind_param('i', $id);
$s->execute();
$s->close();

$s = $conexion->prepare('UPDATE progreso SET Porcentaje = 0 WHERE Id_usuario = ? AND Id_curso = ?');
$s->bind_param('ii', $id, $curso);
$s->execute();
$s->close();

responder(200, ['exito' => true, 'mensaje' => 'Progreso reiniciado']);
