// Utilidades compartidas por leccion.js y juegos.html
const MASCOT_IMG = '../img/f.png';
const FRASES = ['¡Muy bien!', '¡Excelente!', '¡Lo lograste!', '¡Increíble!', '¡Perfecto!', '¡Sigue así!', '¡Eres un crack!', '¡Qué bárbaro!', '¡Fantástico!', '¡Dá ra ma ts’ïnt’ï! 🌟'];
const FRASES_FINAL = ['¡Felicidades!', '¡Eres increíble!', '¡Lección completada!'];
const COLORES_CONFETI = ['#58cc02', '#1cb0f6', '#ffc800', '#ff4b4b', '#ce82ff', '#ff9600'];

const shuffle = a => [...a].sort(() => Math.random() - .5);
// Normaliza para comparar: sin acentos, sin apóstrofos, minúsculas
const norm = s => String(s).normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/['’‘`´]/g, '').toLowerCase().replace(/[^a-z0-9 ]/g, '').replace(/\s+/g, ' ').trim();
const esc = s => String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/"/g, '&quot;');
const todasLasPalabras = () => Object.values(VOCAB_ALL).flatMap(c => c.items);

function mascotHTML() { return `<div class="cele-mascot"><img src="${MASCOT_IMG}" alt="¡Felicidades!"></div>`; }

function confetti(n = 30) {
    for (let i = 0; i < n; i++) {
        const c = document.createElement('div');
        c.className = 'cele-confetti';
        c.style.left = Math.random() * 100 + 'vw';
        c.style.background = COLORES_CONFETI[Math.floor(Math.random() * COLORES_CONFETI.length)];
        c.style.animationDuration = (1.5 + Math.random() * 1.5) + 's';
        c.style.animationDelay = (Math.random() * .4) + 's';
        document.body.appendChild(c);
        setTimeout(() => c.remove(), 3600);
    }
}

function celebrate() {
    const frase = FRASES[Math.floor(Math.random() * FRASES.length)];
    const el = document.createElement('div');
    el.className = 'cele-box';
    el.innerHTML = mascotHTML() + `<div class="cele-bubble">${frase}</div>`;
    document.body.appendChild(el);
    confetti(24);
    setTimeout(() => el.remove(), 1250);
}

function confettiFinal() {
    confetti(60);
    let rondas = 0;
    const t = setInterval(() => { confetti(35); if (++rondas >= 4) clearInterval(t); }, 900);
}

// Mensaje inferior verde/rojo (igual que en las lecciones)
function feedback(texto, ok, ms = 1200) {
    const fb = document.createElement('div');
    fb.className = 'feedback ' + (ok ? 'good' : 'bad');
    fb.innerText = texto;
    document.body.appendChild(fb);
    setTimeout(() => fb.remove(), ms);
    return fb;
}

// Audio: intenta ../audios/<audio>.ogg y .mp3; si no existen usa la voz del navegador
let _audioActual = null;
function reproducir(item) {
    if (_audioActual) { _audioActual.pause(); _audioActual = null; }
    const voz = () => {
        try {
            const u = new SpeechSynthesisUtterance(item.ot[0].replace(/['’]/g, ''));
            u.lang = 'es-MX'; u.rate = .8;
            speechSynthesis.cancel(); speechSynthesis.speak(u);
        } catch (e) { }
    };
    if (!item.audio) return voz();
    const a = new Audio(`../audios/${item.audio}.ogg`);
    _audioActual = a;
    a.addEventListener('error', () => {
        const m = new Audio(`../audios/${item.audio}.mp3`);
        _audioActual = m;
        m.addEventListener('error', voz);
        m.play().catch(voz);
    });
    a.play().catch(() => { /* el listener 'error' se encarga del respaldo */ });
}
