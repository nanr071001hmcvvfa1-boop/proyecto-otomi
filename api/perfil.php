<?php
// GET  -> datos completos del perfil (incluye foto)
// Foto: se sube con subir_foto.php y se quita con eliminar_foto.php
// POST -> { "nombre"?: "...", "avatar"?: 0-7, "meta"?: 10|20|30|50 }
require_once 'db.php';
$id = exigir_sesion();
$curso = ID_CURSO;
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $d = json_decode(file_get_contents('php://input'), true) ?? [];

        if (isset($d['nombre'])) {
            $nombre = trim((string)$d['nombre']);
            if ($nombre === '' || mb_strlen($nombre) > 60) {
                responder(400, ['exito' => false, 'mensaje' => 'El nombre debe tener entre 1 y 60 caracteres']);
            }
            $s = $conexion->prepare('UPDATE usuarios SET Nombre = ? WHERE Id_usuario = ?');
            $s->bind_param('si', $nombre, $id);
            $s->execute(); $s->close();
            $_SESSION['usuario_nombre'] = $nombre;
        }
        if (isset($d['avatar'])) {
            $av = (int)$d['avatar'];
            if ($av < 0 || $av > 7) responder(400, ['exito' => false, 'mensaje' => 'Avatar no válido']);
            $s = $conexion->prepare('UPDATE usuarios SET Avatar = ? WHERE Id_usuario = ?');
            $s->bind_param('ii', $av, $id);
            $s->execute(); $s->close();
        }
        if (isset($d['meta'])) {
            $meta = (int)$d['meta'];
            if (!in_array($meta, [10, 20, 30, 50], true)) responder(400, ['exito' => false, 'mensaje' => 'Meta no válida']);
            $s = $conexion->prepare('UPDATE usuarios SET Meta_diaria = ? WHERE Id_usuario = ?');
            $s->bind_param('ii', $meta, $id);
            $s->execute(); $s->close();
        }
        responder(200, ['exito' => true, 'mensaje' => 'Perfil actualizado']);
    }

    // ---- GET ----
    $s = $conexion->prepare('SELECT Nombre, Email, `País` AS pais, Fecha_registro, Fecha_nac, Foto_perfil, Avatar, Meta_diaria FROM usuarios WHERE Id_usuario = ?');
    $s->bind_param('i', $id);
    $s->execute();
    $u = $s->get_result()->fetch_assoc();
    $s->close();
    if (!$u) { session_destroy(); responder(401, ['exito' => false, 'mensaje' => 'Usuario no encontrado']); }

    $s = $conexion->prepare('SELECT Experiencia AS experiencia, Racha AS racha, Porcentaje AS porcentaje FROM progreso WHERE Id_usuario = ? AND Id_curso = ?');
    $s->bind_param('ii', $id, $curso);
    $s->execute();
    $progreso = $s->get_result()->fetch_assoc() ?: ['experiencia' => 0, 'racha' => 0, 'porcentaje' => 0];
    $s->close();

    $completadas = [];
    $s = $conexion->prepare('SELECT Id_leccion FROM leccion_completada WHERE Id_usuario = ?');
    $s->bind_param('i', $id);
    $s->execute();
    foreach ($s->get_result()->fetch_all(MYSQLI_ASSOC) as $f) $completadas[] = $f['Id_leccion'];
    $s->close();

    $hoy = $conexion->query('SELECT CURDATE() AS d')->fetch_assoc()['d'];

    $actividad = [];
    $s = $conexion->prepare('SELECT Fecha, Xp FROM actividad_diaria WHERE Id_usuario = ? AND Fecha >= CURDATE() - INTERVAL 6 DAY');
    $s->bind_param('i', $id);
    $s->execute();
    foreach ($s->get_result()->fetch_all(MYSQLI_ASSOC) as $f) $actividad[$f['Fecha']] = (int)$f['Xp'];
    $s->close();

    $s = $conexion->prepare('SELECT COUNT(*) AS n FROM actividad_diaria WHERE Id_usuario = ? AND Xp > 0');
    $s->bind_param('i', $id);
    $s->execute();
    $dias = (int)$s->get_result()->fetch_assoc()['n'];
    $s->close();

    responder(200, [
        'exito' => true,
        'usuario' => [
            'nombre' => $u['Nombre'], 'email' => $u['Email'], 'pais' => $u['pais'],
            'fecha_registro' => $u['Fecha_registro'], 'fecha_nac' => $u['Fecha_nac'],
            'foto' => $u['Foto_perfil'] ?: null,   // ruta relativa a /otomi (o null si no tiene)
            'avatar' => (int)$u['Avatar'], 'meta' => (int)$u['Meta_diaria'],
        ],
        'progreso' => $progreso,
        'completadas' => $completadas,
        'total_lecciones' => count(LECCIONES),
        'hoy' => $hoy,
        'actividad' => $actividad,
        'dias_practicados' => $dias,
    ]);
} catch (Throwable $e) {
    error_log('perfil.php: ' . $e->getMessage());
    responder(500, ['exito' => false, 'mensaje' => 'No se pudo leer el perfil. Detalle: ' . $e->getMessage() . ' — si dice "Unknown column" o "doesn\'t exist", importa sql/mejoras.sql y sql/fotos.sql en phpMyAdmin.']);
}
