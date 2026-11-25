# Otomí – puesta en marcha (XAMPP)

1. Copia la carpeta `otomi` a `C:\xampp\htdocs\`.
2. En phpMyAdmin importa tu `otomi_rnn.sql` (si no lo tienes ya) y después **`sql/mejoras.sql`** (se puede ejecutar varias veces).
3. Abre **http://localhost/otomi/public/index.html** (no abras los .html con doble clic: PHP necesita el servidor).

## Archivos nuevos
- `api/sesion.php` (usuario + progreso + lecciones hechas), `api/logout.php`, `api/reiniciar_progreso.php`
- `api/db.php`, `login.php`, `registro.php`, `actualizar_progreso.php` (reescritos)
- `public/numeros.html`, `saludos.html`, `familia.html`, `animales.html` (lecciones nuevas)
- `public/vocab.js` (TODO el vocabulario: edítalo aquí), `comun.js`, `leccion.js`, `progreso.js`
- `public/juegos.html` + `juegos.css` (rediseñado), `public/index.html` (rediseñado)
- `sql/mejoras.sql`

## Notas
- Las contraseñas nuevas se guardan con hash; las cuentas antiguas en texto plano se convierten solas al iniciar sesión.
- Audio: si pones archivos `.ogg`/`.mp3` en `/audios` (nombre = campo `audio` de vocab.js) se usan; si no, se usa la voz del navegador.
- Revisa el vocabulario de números, saludos, familia y animales con un hablante: el otomí varía mucho por región.

## Foto de perfil (nuevo)
1. En phpMyAdmin (base `otomi_rnn`) ejecuta **`sql/fotos.sql`** (o `sql/mejoras.sql`, que ya lo incluye). Usa la columna `usuarios.Foto_perfil`, donde se guarda la **ruta** de la imagen (no la imagen dentro de la BD).
2. Las fotos se guardan en `uploads/perfiles/` (se crea sola; `.htaccess` impide ejecutar código ahí).
3. Endpoints:
   - `api/perfil.php` (GET) → ahora devuelve `foto` y `fecha_nac`.
   - `api/subir_foto.php` (POST multipart, campo `foto`) → valida, recorta a 400×400 y guarda.
   - `api/eliminar_foto.php` (POST) → quita la foto y borra el archivo.
   - `api/sesion.php` → también devuelve `usuario.foto` por si quieres mostrarla en otras pantallas (`"../" + foto`).
4. En el perfil: toca el círculo de la foto → **📷 Subir foto** / **🗑 Quitar foto**.
- Si subes fotos muy pesadas: el navegador las reduce solo, pero el tope real lo marcan `upload_max_filesize` y `post_max_size` de `php.ini`.

## XP al terminar lecciones (nuevo)
- **Todas** las lecciones suman XP en la base de datos al terminar y muestran en pantalla el resultado
  (`⭐ +XP · Total · 🔥 Racha · 📊 Curso %`, y «Lección nueva completada» la primera vez). Si falla, aparece «Reintentar».
- Helpers en `public/progreso.js`: `guardarYMostrar(leccion, xp)`, `marcarHecha(leccion)`.
- XP = 10 por acierto (colores, frutas, consonantes, números, saludos, familia, animales, vocales).
- `Vocales.html` ahora es la lección `vocales` (se agregó a `LECCIONES` en `api/db.php` y al menú `men_lecc.html`).
  No hace falta importar ningún SQL nuevo.
- `Perfil.html` muestra XP, nivel, racha, % del curso y una lista de lecciones completadas (✅).
- Botones de regreso: `Vocales.html` y `unidad1.html` (🏠 MENÚ), y en todas las pantallas finales (Volver al menú / Ver mi perfil).

## Diseño unificado y juego de colores
- Todas las páginas usan el mismo diseño oscuro (Nunito, fondo `#16222a`, tarjetas con borde `#36464f`, texto `#e8eef2`, verde `#58cc02`, azul `#1cb0f6`, bordes redondeados). Para cambiar la paleta, busca esos colores en `estilo1.css`, `estilo2.css`, `menu.css`, `estilos.css`, `juegos.css` y `estil.css`. `frutas.html` ahora usa `estilo1.css` (se eliminó `estilo_frutas.css`).
- En `juegos.html`, «Une las parejas» ahora es el juego de colores de la carpeta `juego` (5 niveles: 3, 5, 7, 10 y 12 colores). Arrastra el color a su nombre o tócalos en el celular. Da puntos y XP como los demás juegos.
- Se quitaron las copias duplicadas «(2)» conservando la versión más reciente de cada archivo.

## Responsive
- Todas las páginas se adaptan a celular (desde 320 px), tableta y escritorio (probado hasta 1440 px y en celular acostado), sin desbordes horizontales.
- Cada hoja de estilos termina con un bloque «RESPONSIVE». El ancho de las lecciones se controla con la variable `--ancho` (`estilo1.css` y `estilo2.css`).
- Las barras fijas (arriba y abajo) se alinean al ancho de la página; en `men_lecc.html` el zigzag de la ruta se reduce en pantallas angostas.

## Menú lateral
- `sidebar.css` es el menú lateral del `inicio.html`, compartido. Aparece en las páginas que **no** son lecciones: `men_lecc.html` (Aprender), `Perfil.html`, `juegos.html` y `F.html`. `index.html` conserva su propia versión (decorativa, sin enlaces) porque ahí el usuario aún no inicia sesión.
- Las lecciones (Vocales, Consonantes, Colores, Frutas, Números, Saludos, Familia, Animales, Unidad 1) y el examen no lo llevan, para que ocupen toda la pantalla.
- Para ponerlo en otra página: agrega `class="con-menu"` al `<body>`, enlaza `sidebar.css`, copia el bloque `<nav class="sidebar1">` y marca el enlace de esa página con `class="actual"`.
- En celular y tableta el menú pasa arriba, en filas. El logo lleva a `inicio.html`.

## Cambio de nombres
- `index.html` (la página principal con tradiciones y lugares) ahora se llama **`inicio.html`**.
- `registro.html` (iniciar sesión / crear cuenta) ahora se llama **`index.html`**, así que es la página que se abre al entrar a `http://localhost/otomi/public/`.
- Se actualizaron todos los enlaces, el cierre de sesión (`api/logout.php`) y las redirecciones cuando no hay sesión.
