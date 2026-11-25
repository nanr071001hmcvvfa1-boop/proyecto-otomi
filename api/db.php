<?php
// Conexión común + sesión + utilidades. Todos los endpoints hacen require_once 'db.php'.
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
mysqli_report(MYSQLI_REPORT_OFF);

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

// ---- Ajusta estos datos si tu MySQL/XAMPP es distinto ----
$host = 'localhost';
$usuario_db = 'root';
$password_db = '';
$nombre_db = 'otomi_rnn';
$puerto = 3306;

const ID_CURSO = 100;
// Lecciones que cuentan para el porcentaje del curso (mismos ids que men_lecc.html)
const LECCIONES = ['vocales', 'alfabeto', 'colores', 'frutas', 'numeros', 'saludos', 'familia', 'animales'];

function responder(int $codigo, array $datos): void {
    http_response_code($codigo);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit;
}

function exigir_post(): array {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        responder(405, ['exito' => false, 'mensaje' => 'Método no permitido']);
    }
    return json_decode(file_get_contents('php://input'), true) ?? [];
}

function exigir_sesion(): int {
    if (!isset($_SESSION['usuario_id'])) {
        responder(401, ['exito' => false, 'mensaje' => 'Debes iniciar sesión']);
    }
    return (int)$_SESSION['usuario_id'];
}

// ---- Fotos de perfil ----
const FOTO_SUBDIR = 'uploads/perfiles';                      // relativo a la carpeta /otomi
define('FOTO_DIR', dirname(__DIR__) . '/' . FOTO_SUBDIR);    // ruta física en disco

// Borra del disco una foto anterior (solo si está dentro de uploads/perfiles; nunca confía en la ruta de la BD)
function borrar_foto_archivo(?string $ruta): void {
    if (!$ruta) return;
    $archivo = basename($ruta);
    if (!preg_match('/^u\d+_[a-f0-9]+\.(jpg|png|webp)$/', $archivo)) return;
    $completa = FOTO_DIR . '/' . $archivo;
    if (is_file($completa)) @unlink($completa);
}

$conexion = @new mysqli($host, $usuario_db, $password_db, $nombre_db, $puerto);
if ($conexion->connect_error) {
    responder(500, ['exito' => false, 'mensaje' => 'No se pudo conectar a la base de datos. ¿Está encendido MySQL en XAMPP?']);
}
$conexion->set_charset('utf8mb4');
