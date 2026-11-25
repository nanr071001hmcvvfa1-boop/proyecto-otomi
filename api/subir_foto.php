<?php
// POST multipart/form-data con el campo "foto" (jpg, png, webp o gif).
// Valida, recorta a cuadrado 400x400, la guarda en /uploads/perfiles y apunta usuarios.Foto_perfil a ella.
require_once 'db.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, ['exito' => false, 'mensaje' => 'Método no permitido']);
}
$id = exigir_sesion();

const FOTO_MAX_BYTES = 5 * 1024 * 1024;   // 5 MB (tope real = upload_max_filesize de php.ini)
const FOTO_LADO = 400;                     // se guarda 400x400

// Si el archivo supera post_max_size, PHP vacía $_FILES y $_POST
if (empty($_FILES) && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    responder(413, ['exito' => false, 'mensaje' => 'La imagen es demasiado pesada. Prueba con una más pequeña.']);
}
if (!isset($_FILES['foto'])) {
    responder(400, ['exito' => false, 'mensaje' => 'No se recibió ninguna imagen']);
}
$f = $_FILES['foto'];
if ($f['error'] !== UPLOAD_ERR_OK) {
    $msg = in_array($f['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)
        ? 'La imagen es demasiado pesada. Prueba con una más pequeña.'
        : 'No se pudo recibir la imagen (código ' . $f['error'] . ')';
    responder(400, ['exito' => false, 'mensaje' => $msg]);
}
if ($f['size'] <= 0 || $f['size'] > FOTO_MAX_BYTES) {
    responder(400, ['exito' => false, 'mensaje' => 'La imagen debe pesar menos de 5 MB']);
}
if (!is_uploaded_file($f['tmp_name'])) {
    responder(400, ['exito' => false, 'mensaje' => 'Archivo no válido']);
}

// El tipo se comprueba por el CONTENIDO real, no por el nombre ni por lo que diga el navegador
$info = @getimagesize($f['tmp_name']);
$permitidos = [IMAGETYPE_JPEG => 'jpeg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp', IMAGETYPE_GIF => 'gif'];
if (!$info || !isset($permitidos[$info[2]])) {
    responder(400, ['exito' => false, 'mensaje' => 'Formato no válido. Usa JPG, PNG, WEBP o GIF.']);
}
if ($info[0] < 50 || $info[1] < 50) {
    responder(400, ['exito' => false, 'mensaje' => 'La imagen es demasiado pequeña (mínimo 50×50 px)']);
}
if ($info[0] * $info[1] > 40000000) {   // evita "bombas" de descompresión
    responder(400, ['exito' => false, 'mensaje' => 'La imagen tiene una resolución demasiado grande']);
}

if (!is_dir(FOTO_DIR) && !@mkdir(FOTO_DIR, 0755, true)) {
    responder(500, ['exito' => false, 'mensaje' => 'No se pudo crear la carpeta uploads/perfiles (revisa permisos).']);
}

$nombreArchivo = 'u' . $id . '_' . bin2hex(random_bytes(6));   // nombre nuevo cada vez = el navegador no usa caché vieja
$procesada = false;

// Camino principal: GD (recorta, reduce y re-codifica; así se elimina cualquier contenido oculto en el archivo)
if (function_exists('imagecreatetruecolor')) {
    $crear = [
        IMAGETYPE_JPEG => 'imagecreatefromjpeg',
        IMAGETYPE_PNG  => 'imagecreatefrompng',
        IMAGETYPE_WEBP => 'imagecreatefromwebp',
        IMAGETYPE_GIF  => 'imagecreatefromgif',
    ][$info[2]];
    if (function_exists($crear)) {
        $origen = @$crear($f['tmp_name']);
        if ($origen) {
            // Respeta la rotación de fotos tomadas con celular
            if ($info[2] === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
                $exif = @exif_read_data($f['tmp_name']);
                $giro = [3 => 180, 6 => -90, 8 => 90][$exif['Orientation'] ?? 1] ?? 0;
                if ($giro) { $rot = imagerotate($origen, $giro, 0); if ($rot) { imagedestroy($origen); $origen = $rot; } }
            }
            $w = imagesx($origen); $h = imagesy($origen);
            $lado = min($w, $h);
            $lienzo = imagecreatetruecolor(FOTO_LADO, FOTO_LADO);
            imagefill($lienzo, 0, 0, imagecolorallocate($lienzo, 255, 255, 255));   // fondo blanco para PNG/GIF con transparencia
            imagecopyresampled($lienzo, $origen, 0, 0, intdiv($w - $lado, 2), intdiv($h - $lado, 2), FOTO_LADO, FOTO_LADO, $lado, $lado);
            $destino = FOTO_DIR . '/' . $nombreArchivo . '.jpg';
            $procesada = imagejpeg($lienzo, $destino, 85);
            imagedestroy($origen); imagedestroy($lienzo);
            if ($procesada) { $nombreArchivo .= '.jpg'; }
        }
    }
}
// Plan B (PHP sin GD): se guarda tal cual, con extensión fijada por el tipo real
if (!$procesada) {
    $ext = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp', IMAGETYPE_GIF => null][$info[2]];
    if ($ext === null) {
        responder(500, ['exito' => false, 'mensaje' => 'Este servidor no puede procesar GIF. Usa JPG, PNG o WEBP.']);
    }
    $nombreArchivo .= '.' . $ext;
    if (!move_uploaded_file($f['tmp_name'], FOTO_DIR . '/' . $nombreArchivo)) {
        responder(500, ['exito' => false, 'mensaje' => 'No se pudo guardar la imagen (revisa permisos de uploads/perfiles).']);
    }
}

$ruta = FOTO_SUBDIR . '/' . $nombreArchivo;
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $s = $conexion->prepare('SELECT Foto_perfil FROM usuarios WHERE Id_usuario = ?');
    $s->bind_param('i', $id);
    $s->execute();
    $anterior = ($s->get_result()->fetch_assoc())['Foto_perfil'] ?? null;
    $s->close();

    $s = $conexion->prepare('UPDATE usuarios SET Foto_perfil = ? WHERE Id_usuario = ?');
    $s->bind_param('si', $ruta, $id);
    $s->execute();
    $s->close();

    borrar_foto_archivo($anterior);   // ya no hace falta la anterior
    responder(200, ['exito' => true, 'mensaje' => 'Foto actualizada', 'foto' => $ruta]);
} catch (Throwable $e) {
    @unlink(FOTO_DIR . '/' . $nombreArchivo);   // no dejar archivos huérfanos
    error_log('subir_foto.php: ' . $e->getMessage());
    $pista = stripos($e->getMessage(), 'Unknown column') !== false ? ' Importa sql/fotos.sql en phpMyAdmin.' : '';
    responder(500, ['exito' => false, 'mensaje' => 'No se pudo guardar la foto en la base de datos.' . $pista]);
}
