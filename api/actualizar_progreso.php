<?php
// Recibe: { "leccion": "colores", "experienciaGanada": 80, "tipo": "leccion" | "juego" }
require_once 'db.php';
$datos = exigir_post();
$idUsuario = exigir_sesion();

$tipo = (($datos['tipo'] ?? 'leccion') === 'juego') ? 'juego' : 'leccion';
$leccion = preg_replace('/[^a-z_]/', '', strtolower((string)($datos['leccion'] ?? '')));
$tope = $tipo === 'juego' ? 100 : 200;               // el navegador no es de fiar
$xp = max(0, min((int)($datos['experienciaGanada'] ?? 0), $tope));

if ($tipo === 'leccion' && !in_array($leccion, LECCIONES, true)) {
    responder(400, ['exito' => false, 'mensaje' => 'Lección desconocida']);
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$curso = ID_CURSO;
try {
    $conexion->begin_transaction();

    $s = $conexion->prepare('INSERT IGNORE INTO progreso (Id_usuario, Id_curso) VALUES (?, ?)');
    $s->bind_param('ii', $idUsuario, $curso);
    $s->execute();
    $s->close();

    // Racha: mismo día = igual, ayer = +1, antes = vuelve a 1
    $s = $conexion->prepare('SELECT Racha, Ultima_actividad, DATEDIFF(CURDATE(), Ultima_actividad) AS dias
                             FROM progreso WHERE Id_usuario = ? AND Id_curso = ? FOR UPDATE');
    $s->bind_param('ii', $idUsuario, $curso);
    $s->execute();
    $fila = $s->get_result()->fetch_assoc();
    $s->close();

    $racha = (int)$fila['Racha'];
    if ($fila['Ultima_actividad'] === null)      $racha = 1;
    elseif ((int)$fila['dias'] === 0)            $racha = max(1, $racha);
    elseif ((int)$fila['dias'] === 1)            $racha += 1;
    else                                         $racha = 1;

    $nueva = false;   // true solo la primera vez que el usuario termina esa lección
    if ($tipo === 'leccion') {
        $s = $conexion->prepare('INSERT IGNORE INTO leccion_completada (Id_usuario, Id_leccion) VALUES (?, ?)');
        $s->bind_param('is', $idUsuario, $leccion);
        $s->execute();
        $nueva = $s->affected_rows > 0;
        $s->close();
    }

    $s = $conexion->prepare('SELECT COUNT(*) AS n FROM leccion_completada WHERE Id_usuario = ?');
    $s->bind_param('i', $idUsuario);
    $s->execute();
    $hechas = (int)$s->get_result()->fetch_assoc()['n'];
    $s->close();
    $porcentaje = (int)min(100, round($hechas / count(LECCIONES) * 100));

    $s = $conexion->prepare('UPDATE progreso SET Experiencia = Experiencia + ?, Racha = ?, Porcentaje = ?, Ultima_actividad = CURDATE()
                             WHERE Id_usuario = ? AND Id_curso = ?');
    $s->bind_param('iiiii', $xp, $racha, $porcentaje, $idUsuario, $curso);
    $s->execute();
    $s->close();

    // Registro del XP del día (alimenta el perfil)
    $s = $conexion->prepare('INSERT INTO actividad_diaria (Id_usuario, Fecha, Xp) VALUES (?, CURDATE(), ?)
                             ON DUPLICATE KEY UPDATE Xp = Xp + ?');
    $s->bind_param('iii', $idUsuario, $xp, $xp);
    $s->execute();
    $s->close();

    $conexion->commit();

    $s = $conexion->prepare('SELECT Experiencia AS experiencia, Racha AS racha, Porcentaje AS porcentaje
                             FROM progreso WHERE Id_usuario = ? AND Id_curso = ?');
    $s->bind_param('ii', $idUsuario, $curso);
    $s->execute();
    $progreso = $s->get_result()->fetch_assoc();
    $s->close();

    responder(200, ['exito' => true, 'mensaje' => 'Progreso actualizado', 'xp' => $xp, 'nueva' => $nueva, 'progreso' => $progreso]);
} catch (Throwable $e) {
    try { $conexion->rollback(); } catch (Throwable $e2) {}
    error_log('actualizar_progreso.php: ' . $e->getMessage());
    $pista = (stripos($e->getMessage(), 'Unknown column') !== false || stripos($e->getMessage(), "doesn't exist") !== false)
        ? ' Importa sql/mejoras.sql en phpMyAdmin.' : '';
    responder(500, ['exito' => false, 'mensaje' => 'No se pudo guardar el progreso.' . $pista]);
}
