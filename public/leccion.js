// Motor genérico de lección (mismo diseño y funciones que colores.html / frutas.html).
// La página define antes:  const CATEGORIA = 'numeros';  y carga vocab.js, comun.js, progreso.js.
const CAT = VOCAB_ALL[CATEGORIA];
const VOCAB = CAT.items;
const XP_ACIERTO = 10;
const PERSONA_SVG = `<svg class="person" viewBox="0 0 110 130" xmlns="http://www.w3.org/2000/svg" aria-label="Mujer otomí">
                    <!-- Blusa blanca bordada -->
                    <path d="M10 130 Q10 84 55 84 Q100 84 100 130 Z" fill="#fff" stroke="#d9d9d9" stroke-width="2"/>
                    <!-- Cuello -->
                    <rect x="48" y="70" width="14" height="18" fill="#b87a48"/>
                    <!-- Bordado del cuello (estilo tenango) -->
                    <path d="M30 93 Q55 112 80 93" stroke="#e63946" stroke-width="4" fill="none" stroke-linecap="round"/>
                    <circle cx="36" cy="98" r="4" fill="#ff5fa2"/><circle cx="46" cy="105" r="4" fill="#ff9600"/>
                    <circle cx="55" cy="107" r="4" fill="#1cb0f6"/><circle cx="64" cy="105" r="4" fill="#ff9600"/>
                    <circle cx="74" cy="98" r="4" fill="#ff5fa2"/>
                    <circle cx="41" cy="102" r="2" fill="#58cc02"/><circle cx="69" cy="102" r="2" fill="#58cc02"/>
                    <!-- Flores bordadas en la blusa -->
                    <circle cx="28" cy="120" r="4" fill="#ff5fa2"/><circle cx="28" cy="120" r="1.5" fill="#ffd900"/>
                    <path d="M22 124 Q25 118 28 124" fill="#58cc02"/>
                    <circle cx="82" cy="120" r="4" fill="#ff9600"/><circle cx="82" cy="120" r="1.5" fill="#ffd900"/>
                    <path d="M76 124 Q79 118 82 124" fill="#58cc02"/>
                    <!-- Trenzas -->
                    <path d="M30 46 Q18 82 30 114" stroke="#1f1712" stroke-width="10" fill="none" stroke-linecap="round"/>
                    <path d="M80 46 Q92 82 80 114" stroke="#1f1712" stroke-width="10" fill="none" stroke-linecap="round"/>
                    <rect x="25" y="110" width="10" height="9" rx="2" fill="#e63946"/>
                    <rect x="75" y="110" width="10" height="9" rx="2" fill="#e63946"/>
                    <!-- Rostro -->
                    <circle cx="55" cy="48" r="27" fill="#c68b59"/>
                    <!-- Cabello con raya al centro -->
                    <path d="M27 48 Q27 15 55 15 Q83 15 83 48 Q72 28 55 28 Q38 28 27 48 Z" fill="#1f1712"/>
                    <path d="M55 15 L55 28" stroke="#c68b59" stroke-width="1.5"/>
                    <!-- Rasgos -->
                    <circle cx="44" cy="52" r="3" fill="#1f1712"/><circle cx="66" cy="52" r="3" fill="#1f1712"/>
                    <circle cx="38" cy="60" r="4" fill="#e8836b" opacity=".45"/><circle cx="72" cy="60" r="4" fill="#e8836b" opacity=".45"/>
                    <path d="M46 64 Q55 72 64 64" stroke="#7a3b25" stroke-width="3" fill="none" stroke-linecap="round"/>
                </svg>`;

let lvl = 0, lives = 5, puntos = 0;
let list = nuevaLista();

function nuevaLista() { return shuffle(VOCAB).slice(0, Math.min(8, VOCAB.length)); }

// Si la categoría tiene pocas palabras, completa las opciones con palabras de otras categorías
function opcionesPara(v) {
    let otros = shuffle(VOCAB.filter(x => x.es !== v.es));
    if (otros.length < 3) otros = otros.concat(shuffle(todasLasPalabras().filter(x => x.ot[0] !== v.ot[0] && !VOCAB.includes(x))));
    return shuffle([v, ...otros.slice(0, 3)]);
}

function finaleAnimation() {
    try {
        const d = JSON.parse(localStorage.getItem('otomi_done') || '[]');
        if (!d.includes(CATEGORIA)) { d.push(CATEGORIA); localStorage.setItem('otomi_done', JSON.stringify(d)); }
    } catch (e) { }

    const app = document.getElementById('app');
    const titulo = app.innerHTML;
    app.innerHTML = `<div class="cele-finale">${mascotHTML()}${titulo}
        <p>${FRASES_FINAL[Math.floor(Math.random() * FRASES_FINAL.length)]}</p>
        <p id="guardado" style="font-size:16px">Guardando tu progreso…</p>
        <button class="cele-restart" onclick="location.reload()">PRACTICAR DE NUEVO</button>
        <button class="cele-restart sec" onclick="location.href='men_lecc.html'">VOLVER AL MENÚ</button>
        <button class="cele-restart sec" onclick="location.href='Perfil.html'">VER MI PERFIL</button></div>`;
    confettiFinal();
    guardarYMostrar(CATEGORIA, puntos);
}

function reiniciar() { lives = 5; lvl = 0; puntos = 0; list = nuevaLista(); }

function render() {
    document.getElementById('fill').style.width = (lvl / list.length * 100) + '%';
    document.getElementById('lives').innerText = lives;

    if (lvl >= list.length) {
        document.getElementById('app').innerHTML = `<h1>¡Terminaste! 🎉<br>${list.length} palabras de «${CAT.titulo.toLowerCase()}» en otomí aprendidas</h1>`;
        finaleAnimation();
        document.querySelector('.btn').style.display = 'none';
        return;
    }

    const v = list[lvl];
    document.getElementById('app').innerHTML = `
        <h1>¿Cómo se dice <span style="color:#1cb0f6">${esc(v.es)}</span> en otomí?</h1>
        <div class="scene">
            ${PERSONA_SVG}
            <div class="bubble">${v.emoji || ''} ${esc(v.es)} <button class="audio-btn" id="playAudio" aria-label="Escuchar">🔊</button></div>
        </div>
        <div id="opts"></div>`;

    document.getElementById('playAudio').onclick = () => reproducir(v);

    document.getElementById('opts').innerHTML = opcionesPara(v)
        .map(o => `<div class="card" data-v="${esc(o.ot[0])}" data-i="${VOCAB.includes(o) ? VOCAB.indexOf(o) : -1}">${esc(o.ot[0])}</div>`).join('');

    let sel = '';
    const opciones = todasLasPalabras();
    document.querySelectorAll('.card').forEach(c => c.onclick = () => {
        document.querySelectorAll('.card').forEach(x => x.classList.remove('sel'));
        c.classList.add('sel');
        sel = c.dataset.v;
        const it = opciones.find(x => x.ot[0] === sel);
        if (it) reproducir(it);
        document.getElementById('check').disabled = false;
    });

    // SALTAR: pierde una vida y la pregunta vuelve al final. No se puede saltar la última pendiente.
    const skip = document.getElementById('skip');
    skip.disabled = lvl >= list.length - 1;
    skip.onclick = () => {
        feedback('Saltaste · Era: ' + v.ot[0] + ' · −1 ❤️', false);
        skip.disabled = true;
        document.getElementById('check').disabled = true;
        lives--;
        list.push(list.splice(lvl, 1)[0]);
        setTimeout(() => {
            if (lives <= 0) { alert('Sin vidas'); reiniciar(); }
            render();
        }, 1200);
    };

    document.getElementById('check').onclick = () => {
        const ok = v.ot.map(x => x.toLowerCase()).includes(sel.toLowerCase().trim());
        feedback(ok ? '¡Bien! Es ' + v.ot[0] : 'Era: ' + v.ot[0], ok);
        if (ok) { reproducir(v); celebrate(); puntos += XP_ACIERTO; }
        document.getElementById('check').disabled = true;
        skip.disabled = true;
        setTimeout(() => {
            if (ok) { lvl++; }
            else { lives--; if (lives <= 0) { alert('Sin vidas'); reiniciar(); } }
            render();
        }, 1200);
    };
}
render();
