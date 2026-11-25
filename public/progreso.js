// Comunicación con el servidor para guardar el progreso.
//   <script src="progreso.js"></script>
//   guardarProgreso('colores', 80)              -> lección terminada
//   guardarProgreso('juego_memorama', 30, 'juego') -> puntos de un juego
const API_BASE = '../api/';

async function guardarProgreso(leccion, xp, tipo = 'leccion') {
    try {
        const r = await fetch(API_BASE + 'actualizar_progreso.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ leccion, experienciaGanada: xp, tipo })
        });
        const d = await r.json().catch(() => ({ exito: false, mensaje: 'Respuesta no válida del servidor' }));
        d.status = r.status;
        return d;
    } catch (e) {
        return { exito: false, mensaje: 'Sin conexión con el servidor', status: 0 };
    }
}

async function obtenerSesion() {
    try {
        const r = await fetch(API_BASE + 'sesion.php', { credentials: 'same-origin' });
        const d = await r.json().catch(() => ({ exito: false }));
        d.status = r.status;
        return d;
    } catch (e) {
        return { exito: false, status: 0 };
    }
}

// Texto para mostrar al terminar (resultado de guardarProgreso)
function textoGuardado(d, xp) {
    if (d && d.exito) {
        const ganado = (d.xp ?? xp);
        return (d.nueva ? '✅ ¡Lección nueva completada! · ' : '') +
            `⭐ +${ganado} XP · Total: ${d.progreso.experiencia} XP · 🔥 Racha: ${d.progreso.racha} · 📊 Curso: ${d.progreso.porcentaje}%`;
    }
    if (d && d.status === 401) return `Inicia sesión para guardar tu progreso`;
    return 'No se pudo guardar el progreso (' + ((d && d.mensaje) || 'error') + ')';
}

// Marca la lección como hecha en el navegador (el menú la pinta en verde al instante)
function marcarHecha(leccion) {
    try {
        const d = JSON.parse(localStorage.getItem('otomi_done') || '[]');
        if (!d.includes(leccion)) { d.push(leccion); localStorage.setItem('otomi_done', JSON.stringify(d)); }
    } catch (e) { }
}

// Guarda el XP en la base de datos y escribe el resultado dentro del elemento #elId.
// Si falla, muestra un enlace "Reintentar"; si no hay sesión, un enlace para entrar.
async function guardarYMostrar(leccion, xp, elId = 'guardado', tipo = 'leccion') {
    const el = document.getElementById(elId);
    if (el) el.textContent = 'Guardando tu progreso…';
    const d = await guardarProgreso(leccion, xp, tipo);
    if (d.exito && tipo === 'leccion') marcarHecha(leccion);
    if (el) {
        let extra = '';
        if (d.status === 401) extra = ' · <a href="index.html">Entrar</a>';
        else if (!d.exito) extra = ' · <a href="#" id="' + elId + '-reintento">Reintentar</a>';
        el.innerHTML = textoGuardado(d, xp) + extra;
        const r = document.getElementById(elId + '-reintento');
        if (r) r.onclick = e => { e.preventDefault(); guardarYMostrar(leccion, xp, elId, tipo); };
    }
    return d;
}
