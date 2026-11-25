// =====================================================================
//  VOCABULARIO COMPARTIDO (lecciones + juegos)
//  Para corregir o agregar palabras solo edita este archivo.
//  es: español · ot: respuestas válidas en otomí (la 1ª es la que se muestra)
//  audio: nombre del archivo en /audios (opcional; si no existe se usa la voz del navegador)
//  ⚠ El otomí tiene muchas variantes. Números, saludos y animales/familia vienen de fuentes
//    del Valle del Mezquital y de comunidades en línea; revísalos con tu maestro/hablante.
// =====================================================================
const VOCAB_ALL = {
  colores: {
    titulo: 'Colores', icon: '🎨', items: [
      { es: 'Morado', ot: ['kjuföi'], audio: 'kjuföi', emoji: '🟣' },
      { es: 'Verde', ot: ["k'apaxi", 'paxi'], audio: "k'apaxi", emoji: '🟢' },
      { es: 'Café', ot: ['boxo'], audio: 'boxo', emoji: '🟤' },
      { es: 'Blanco', ot: ["t'axi"], audio: "t'axi", emoji: '⚪' },
      { es: 'Gris', ot: ['gospi', 'mospi'], audio: 'gospi', emoji: '🔘' },
      { es: 'Pinto', ot: ['bindo'], audio: 'bindo', emoji: '🐆' },
      { es: 'Amarillo', ot: ["k'axti"], audio: "k'axti", emoji: '🟡' },
      { es: 'Azul', ot: ["k'angi"], audio: "k'angi", emoji: '🔵' },
      { es: 'Rosa', ot: ['roxa'], audio: 'rojcha', emoji: '🌸' },
      { es: 'Naranja', ot: ['naranxa'], audio: 'naranxa', emoji: '🟠' },
      { es: 'Negro', ot: ["bot'i"], audio: "bot'i", emoji: '⚫' },
      { es: 'Rojo', ot: ['theni', 'thengi'], audio: 'theni', emoji: '🔴' }
    ]
  },
  frutas: {
    titulo: 'Frutas', icon: '🍊', items: [
      { es: 'Zapote', ot: ['mu̱zä'], audio: 'muza', emoji: '🟤' },
      { es: 'Tuna', ot: ['käjä'], audio: 'kaja', emoji: '🌵' },
      { es: 'Plátano', ot: ["z'anxi"], audio: 'zanxi', emoji: '🍌' },
      { es: 'Tejocote', ot: ['pe̱ni'], audio: 'peni', emoji: '🍎' },
      { es: 'Limón', ot: ['limu'], audio: 'limu', emoji: '🍋' },
      { es: 'Fruta', ot: ['ixi'], audio: 'ixi', emoji: '🍇' },
      { es: 'Pera', ot: ['be̱ra'], audio: 'bera', emoji: '🍐' },
      { es: 'Naranja', ot: ['näranxa'], audio: 'naranxa', emoji: '🍊' },
      { es: 'Aguacate', ot: ["t'söni"], audio: 'tsoni', emoji: '🥑' },
      { es: 'Quelite', ot: ["k'ani"], audio: 'kani', emoji: '🌿' },
      { es: 'Melón', ot: ['meló'], audio: 'melo', emoji: '🍈' }
    ]
  },
  numeros: {
    titulo: 'Números', icon: '🔢', items: [
      { es: 'Uno', ot: ["'na"], audio: 'na', emoji: '1️⃣' },
      { es: 'Dos', ot: ['yoho'], audio: 'yoho', emoji: '2️⃣' },
      { es: 'Tres', ot: ['hñu'], audio: 'hnu', emoji: '3️⃣' },
      { es: 'Cuatro', ot: ['goho'], audio: 'goho', emoji: '4️⃣' },
      { es: 'Cinco', ot: ["kït'a"], audio: 'kita', emoji: '5️⃣' },
      { es: 'Seis', ot: ["'rato"], audio: 'rato', emoji: '6️⃣' },
      { es: 'Siete', ot: ['yoto'], audio: 'yoto', emoji: '7️⃣' },
      { es: 'Ocho', ot: ['hñato'], audio: 'hnato', emoji: '8️⃣' },
      { es: 'Nueve', ot: ['gïto'], audio: 'gito', emoji: '9️⃣' },
      { es: 'Diez', ot: ["'rät'a"], audio: 'rata', emoji: '🔟' }
    ]
  },
  saludos: {
    titulo: 'Saludos', icon: '👋', items: [
      { es: 'Buenos días', ot: ["hanin'su"], audio: 'haninsu', emoji: '🌅' },
      { es: 'Muchas gracias', ot: ['ndunthi di jämadi'], audio: 'jamadi', emoji: '🙏' },
      { es: 'Nos vemos pronto', ot: ['ga nzenguahu'], audio: 'nzenguahu', emoji: '👋' },
      { es: 'Regresa pronto', ot: ["mänt'ä gi pengi"], audio: 'pengi', emoji: '🔙' },
      { es: 'Buen provecho', ot: ['nzaki'], audio: 'nzaki', emoji: '🍽️' },
      { es: 'Pase a descansar', ot: ['thogi gi zi tsaya'], audio: 'tsaya', emoji: '🏠' }
    ]
  },
  familia: {
    titulo: 'Familia', icon: '👪', items: [
      { es: 'Mamá', ot: ["m'e"], audio: 'me', emoji: '👩' },
      { es: 'Papá', ot: ["t'a"], audio: 'ta', emoji: '👨' },
      { es: 'Abuelo', ot: ['xita'], audio: 'xita', emoji: '👴' },
      { es: 'Abuela', ot: ['ngande'], audio: 'ngande', emoji: '👵' },
      { es: 'Hermano', ot: ['ida'], audio: 'ida', emoji: '🧒' },
      { es: 'Hijo', ot: ['bätsi'], audio: 'batsi', emoji: '👶' }
    ]
  },
  animales: {
    titulo: 'Animales', icon: '🦎', items: [
      { es: 'Gato', ot: ['mixi'], audio: 'mixi', emoji: '🐱' },
      { es: 'Perro', ot: ["ts'at'yo", "y'o"], audio: 'yo', emoji: '🐶' },
      { es: 'Puerco', ot: ["ts'üdi"], audio: 'tsudi', emoji: '🐷' },
      { es: 'Pájaro', ot: ["ts'ints'u"], audio: 'tsintsu', emoji: '🐦' },
      { es: 'Gallo', ot: ['ndäni'], audio: 'ndani', emoji: '🐓' },
      { es: 'Ratón', ot: ['ngu'], audio: 'ngu', emoji: '🐭' },
      { es: 'Coyote', ot: ["miñ'o"], audio: 'minyo', emoji: '🐺' },
      { es: 'Tecolote', ot: ['tukru'], audio: 'tukru', emoji: '🦉' },
      { es: 'Tlacuache', ot: ['däzü'], audio: 'dazu', emoji: '🐀' },
      { es: 'Toro', ot: ['doro'], audio: 'doro', emoji: '🐂' }
    ]
  }
};
