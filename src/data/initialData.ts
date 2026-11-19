import { Docente, Taller, Evento, PreguntaFrecuente, Resena, Sede } from '../types';

export const INITIAL_SEDES: Sede[] = [
  {
    id: 1,
    nombre: 'Sede Central - San Telmo',
    direccion: 'Defensa 842',
    barrio: 'San Telmo, CABA',
    telefono: '(011) 4361-9820',
    email: 'santelmo@atodoritmo.edu.ar',
    horariosAtencion: 'Lunes a Viernes: 14:00 a 21:00 hs | Sábados: 09:30 a 19:00 hs',
    colectivos: 'Líneas 24, 29, 64, 86, 152',
    subte: 'Línea C (Estación San Juan) y Línea E (Estación Bolívar)'
  },
  {
    id: 2,
    nombre: 'Sede Anexo Comunitario - Almagro',
    direccion: 'Av. Corrientes 4120',
    barrio: 'Almagro, CABA',
    telefono: '(011) 4862-5510',
    email: 'almagro@atodoritmo.edu.ar',
    horariosAtencion: 'Lunes a Viernes: 15:00 a 20:30 hs | Sábados: 10:00 a 16:30 hs',
    colectivos: 'Líneas 19, 26, 71, 92, 127, 168',
    subte: 'Línea B (Estación Medrano)'
  }
];

export const INITIAL_DOCENTES: Docente[] = [
  {
    id: 1,
    nombre: 'Julián',
    apellido: 'Morales',
    especialidad: 'Guitarra Criolla, Eléctrica y Ensamble',
    instrumentoPrincipal: 'Guitarra',
    biografia: 'Guitarrista y arreglador con más de 14 años de experiencia en docencia popular. Cree fervientemente que la música es un derecho humano y no un privilegio.',
    trayectoriaCv: 'Licenciado en Artes Musicales (UNA). Guitarrista sesionista en festivales nacionales como Cosquín y Baradero. Coordinador del programa de orquestas infanto-juveniles de Buenos Aires.',
    email: 'jmorales@atodoritmo.edu.ar',
    telefono: '(011) 15-5829-1022',
    fotoUrl: 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=600&q=80',
    activo: true
  },
  {
    id: 2,
    nombre: 'Camila',
    apellido: 'Santillán',
    especialidad: 'Violín Clásico y Popular',
    instrumentoPrincipal: 'Violín',
    biografia: 'Violinista formada en el método Suzuki y repertorio latinoamericano. Apasionada por desmitificar el violín como un instrumento inalcanzable.',
    trayectoriaCv: 'Egresada del Conservatorio Superior de Música Manuel de Falla. Miembro de la Orquesta Típica Sans Souci y docente de violín infantil desde 2017.',
    email: 'csantillan@atodoritmo.edu.ar',
    telefono: '(011) 15-4991-3044',
    fotoUrl: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=600&q=80',
    activo: true
  },
  {
    id: 3,
    nombre: 'Facundo',
    apellido: 'Pérez',
    especialidad: 'Batería, Percusión y Ritmos Afroamericanos',
    instrumentoPrincipal: 'Batería',
    biografia: 'Baterista y percusionista apasionado por el pulso, la cadencia y el juego en equipo. Especialista en desbloquear el miedo al tempo.',
    trayectoriaCv: 'Formado con Pipi Piazzolla y quintetos de jazz porteño. Más de 10 discos grabados como baterista independiente. Docente en A Todo Ritmo desde sus inicios.',
    email: 'fperez@atodoritmo.edu.ar',
    telefono: '(011) 15-6677-8899',
    fotoUrl: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=600&q=80',
    activo: true
  },
  {
    id: 4,
    nombre: 'Lucía',
    apellido: 'Herrera',
    especialidad: 'Piano, Teclado y Armonía Funcional',
    instrumentoPrincipal: 'Piano',
    biografia: 'Pianista y compositora. Su enfoque pedagógico fusiona la lectura accesible con la improvisación y el acompañamiento de canciones populares.',
    trayectoriaCv: 'Profesorado Nacional de Música en Piano. Pianista de música de cámara y ensambles de jazz. Especialista en adaptaciones para alumnos principiantes de todas las edades.',
    email: 'lherrera@atodoritmo.edu.ar',
    telefono: '(011) 15-3211-9080',
    fotoUrl: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=600&q=80',
    activo: true
  },
  {
    id: 5,
    nombre: 'Mariano',
    apellido: 'Díaz',
    especialidad: 'Flauta Traversa y Vientos Andinos',
    instrumentoPrincipal: 'Flauta',
    biografia: 'Flautista versátil que abarca desde la música clásica hasta el folclore norteño y el tango.',
    trayectoriaCv: 'Conservatorio Juan José Castro. Integrante del ensamble Vientos del Sur. Tallerista comunitario en centros barriales durante 8 años.',
    email: 'mdiaz@atodoritmo.edu.ar',
    telefono: '(011) 15-7766-4433',
    fotoUrl: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=600&q=80',
    activo: true
  },
  {
    id: 6,
    nombre: 'Valeria',
    apellido: 'Corvalán',
    especialidad: 'Canto Popular, Técnica Vocal y Coro',
    instrumentoPrincipal: 'Canto Popular',
    biografia: 'Cantante, fonoaudióloga y preparadora vocal. Fomenta el canto consciente, la confianza corporal y la expresión auténtica.',
    trayectoriaCv: 'Lic. en Fonoaudiología (UBA). Cantante de música rioplatense con 3 producciones discográficas. Directora del Coro Comunitario A Todo Ritmo.',
    email: 'vcorvalan@atodoritmo.edu.ar',
    telefono: '(011) 15-8833-2211',
    fotoUrl: 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=600&q=80',
    activo: true
  }
];

export const INITIAL_TALLERES: Taller[] = [
  {
    id: 1,
    nombre: 'Taller de Guitarra Criolla y Popular',
    instrumento: 'Guitarra',
    docenteId: 1,
    nivel: 'Inicial / Sin experiencia',
    edadMinima: 7,
    diaSemana: 'Martes y Jueves',
    horario: '17:30 a 19:00 hs',
    vacantesTotales: 12,
    vacantesDisponibles: 4,
    arancelMensual: 14500,
    modalidad: 'Presencial',
    sedeId: 1,
    descripcion: 'Aprende tus primeros acordes, rasgueos folclóricos, ritmos de rock y canciones populares desde la primera clase, sin necesidad de saber teoría previa.',
    requisitos: 'No requiere conocimientos previos. Si no tienes guitarra, la escuela te presta una para las clases.',
    instrumentosPrestamoDisponibles: 6,
    destacado: true,
    imagenUrl: 'https://images.unsplash.com/photo-1510915361894-db8b60106cb1?auto=format&fit=crop&w=800&q=80'
  },
  {
    id: 2,
    nombre: 'Taller de Violín: Primeras Cuerdas',
    instrumento: 'Violín',
    docenteId: 2,
    nivel: 'Inicial / Sin experiencia',
    edadMinima: 7,
    diaSemana: 'Lunes y Miércoles',
    horario: '18:00 a 19:30 hs',
    vacantesTotales: 10,
    vacantesDisponibles: 3,
    arancelMensual: 15500,
    modalidad: 'Presencial',
    sedeId: 1,
    descripcion: 'Postura corporal relajada, agarre del arco, afinación y melodías sencillas. Clases dinámicas y grupales donde el juego y el oído son protagonistas.',
    requisitos: 'Edad mínima 7 años. Contamos con violines de distintos tamaños (1/2, 3/4 y 4/4) disponibles para usar en el aula.',
    instrumentosPrestamoDisponibles: 5,
    destacado: true,
    imagenUrl: 'https://images.unsplash.com/photo-1612225330812-01a9c6b355ec?auto=format&fit=crop&w=800&q=80'
  },
  {
    id: 3,
    nombre: 'Taller de Batería y Pulso Rítmico',
    instrumento: 'Batería',
    docenteId: 3,
    nivel: 'Inicial / Sin experiencia',
    edadMinima: 8,
    diaSemana: 'Viernes',
    horario: '16:00 a 18:00 hs',
    vacantesTotales: 8,
    vacantesDisponibles: 2,
    arancelMensual: 16000,
    modalidad: 'Presencial',
    sedeId: 1,
    descripcion: 'Coordinación motriz, rudimentos básicos, ritmos de rock, funk y cumbia. Práctica directa en sets acústicos y pads de práctica insonorizados.',
    requisitos: 'Solo necesitas traer muchas ganas de tocar. Las baquetas y las baterías son provistas por la escuela.',
    instrumentosPrestamoDisponibles: 4,
    destacado: true,
    imagenUrl: 'https://images.unsplash.com/photo-1519892300165-cb5542fb47c7?auto=format&fit=crop&w=800&q=80'
  },
  {
    id: 4,
    nombre: 'Taller de Piano y Teclado Creativo',
    instrumento: 'Piano',
    docenteId: 4,
    nivel: 'Inicial / Sin experiencia',
    edadMinima: 8,
    diaSemana: 'Miércoles y Viernes',
    horario: '18:30 a 20:00 hs',
    vacantesTotales: 10,
    vacantesDisponibles: 5,
    arancelMensual: 15500,
    modalidad: 'Presencial',
    sedeId: 1,
    descripcion: 'Conoce el teclado, lectura fluida en clave de sol y fa sin presiones, armonía de canciones y acompañamiento.',
    requisitos: 'No requiere piano en casa para arrancar; disponemos de salas de estudio gratuitas para alumnos.',
    instrumentosPrestamoDisponibles: 8,
    destacado: true,
    imagenUrl: 'https://images.unsplash.com/photo-1520523839898-50712825e617?auto=format&fit=crop&w=800&q=80'
  },
  {
    id: 5,
    nombre: 'Taller de Flauta Traversa y Melodías',
    instrumento: 'Flauta',
    docenteId: 5,
    nivel: 'Inicial / Sin experiencia',
    edadMinima: 9,
    diaSemana: 'Sábados',
    horario: '10:30 a 12:30 hs',
    vacantesTotales: 8,
    vacantesDisponibles: 4,
    arancelMensual: 14000,
    modalidad: 'Presencial',
    sedeId: 2,
    descripcion: 'Emisión del sonido, respiración diafragmática, digitación ágil y repertorio de música de películas y folclore.',
    requisitos: 'Sin conocimientos previos. Disponibilidad de flautas para préstamo durante las clases.',
    instrumentosPrestamoDisponibles: 5,
    destacado: false,
    imagenUrl: 'https://images.unsplash.com/photo-1579783900882-c0d3dad7b119?auto=format&fit=crop&w=800&q=80'
  },
  {
    id: 6,
    nombre: 'Taller de Teclado y Sintetizadores',
    instrumento: 'Teclado',
    docenteId: 4,
    nivel: 'Intermedio',
    edadMinima: 12,
    diaSemana: 'Jueves',
    horario: '19:00 a 20:30 hs',
    vacantesTotales: 8,
    vacantesDisponibles: 3,
    arancelMensual: 15000,
    modalidad: 'Híbrida',
    sedeId: 2,
    descripcion: 'Sonidos modernos, síntesis analógica básica, arreglos pop y pads ambientales para tocar en bandas.',
    requisitos: 'Manejo básico de acordes y escalas mayores.',
    instrumentosPrestamoDisponibles: 6,
    destacado: false,
    imagenUrl: 'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?auto=format&fit=crop&w=800&q=80'
  },
  {
    id: 7,
    nombre: 'Taller de Canto Popular y Expresión Vocal',
    instrumento: 'Canto Popular',
    docenteId: 6,
    nivel: 'Inicial / Sin experiencia',
    edadMinima: 10,
    diaSemana: 'Lunes y Jueves',
    horario: '19:00 a 20:30 hs',
    vacantesTotales: 15,
    vacantesDisponibles: 6,
    arancelMensual: 14000,
    modalidad: 'Presencial',
    sedeId: 1,
    descripcion: 'Descubre tu voz natural sin forzar la garganta. Respiración, afinación, soltura escénica y ensamble a varias voces.',
    requisitos: 'Ganas de cantar y perder la vergüenza. Todos los timbres son bienvenidos.',
    instrumentosPrestamoDisponibles: 0,
    destacado: true,
    imagenUrl: 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?auto=format&fit=crop&w=800&q=80'
  },
  {
    id: 8,
    nombre: 'Gran Ensamble Comunitario A Todo Ritmo',
    instrumento: 'Ensamble',
    docenteId: 1,
    nivel: 'Ensamble Abierto',
    edadMinima: 10,
    diaSemana: 'Sábados',
    horario: '15:00 a 18:00 hs',
    vacantesTotales: 25,
    vacantesDisponibles: 8,
    arancelMensual: 9500,
    modalidad: 'Presencial',
    sedeId: 1,
    descripcion: 'El corazón de la escuela. Guitarras, vientos, teclados, percusión y voces unidas en arreglos orquestales colectivos de rock nacional y folklore.',
    requisitos: 'Haber cursado al menos 3 meses de cualquier instrumento o tener nociones elementales.',
    instrumentosPrestamoDisponibles: 10,
    destacado: true,
    imagenUrl: 'https://images.unsplash.com/photo-1465847899084-d164df4dedc6?auto=format&fit=crop&w=800&q=80'
  }
];

export const INITIAL_EVENTOS: Evento[] = [
  {
    id: 1,
    titulo: 'Gran Muestra de Primavera: Todos al Escenario',
    tipo: 'Muestra de Alumnos',
    fecha: '2026-10-17',
    hora: '18:30 hs',
    lugar: 'Auditorio Sala Mayor - Sede San Telmo (Defensa 842)',
    descripcion: 'Presentación en vivo de los ensambles de iniciales y avanzados. Los alumnos de guitarra, batería, violín, flauta y coro mostrarán los proyectos trabajados durante el cuatrimestre.',
    repertorio: 'Rock nacional (Charly García, Spinetta, Los Redondos), candombe rioplatense, piezas clásicas adaptadas y composiciones colectivas de los alumnos.',
    precioEntrada: 1500,
    capacidadTotal: 140,
    entradasDisponibles: 38,
    estado: 'proximo',
    imagenUrl: 'https://images.unsplash.com/photo-1501386761578-eac5c94b800a?auto=format&fit=crop&w=800&q=80'
  },
  {
    id: 2,
    titulo: 'Concierto Solidario: Instrumentos para Todos',
    tipo: 'Concierto Abierto',
    fecha: '2026-11-07',
    hora: '20:00 hs',
    lugar: 'Teatro Comunitario Almagro (Av. Corrientes 4120)',
    descripcion: 'Concierto benéfico abierto a todo el barrio a cargo de los docentes de la escuela e invitados especiales para recaudar fondos para el banco de instrumentos gratuitos.',
    repertorio: 'Jazz fusión, bossa nova, folklore argentino y canciones tradicionales en formato acústico.',
    precioEntrada: 2000,
    capacidadTotal: 180,
    entradasDisponibles: 64,
    estado: 'proximo',
    imagenUrl: 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?auto=format&fit=crop&w=800&q=80'
  },
  {
    id: 3,
    titulo: 'Masterclass Abierta: "El Ritmo en el Cuerpo"',
    tipo: 'Masterclass',
    fecha: '2026-11-21',
    hora: '11:00 hs',
    lugar: 'Patio Abierto - Sede San Telmo',
    descripcion: 'Taller participativo de percusión corporal y polirritmias dictado por Facundo Pérez. Para alumnos, familiares y vecinos de todas las edades.',
    repertorio: 'Claves afroamericanas, chacarera, candombe y juegos de coordinación colectiva.',
    precioEntrada: 0,
    capacidadTotal: 60,
    entradasDisponibles: 19,
    estado: 'proximo',
    imagenUrl: 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?auto=format&fit=crop&w=800&q=80'
  },
  {
    id: 4,
    titulo: 'Muestra de Mitad de Año: Primeros Pasos Sonoros',
    tipo: 'Muestra de Alumnos',
    fecha: '2026-07-11',
    hora: '19:00 hs',
    lugar: 'Auditorio Sala Mayor - San Telmo',
    descripcion: 'Concierto de cierre de ciclo de invierno con la participación de más de 80 alumnos que subieron al escenario por primera vez en sus vidas.',
    repertorio: 'Obras de iniciación al violín Suzuki, melodías populares en teclado, ensambles de percusión y coro.',
    precioEntrada: 1200,
    capacidadTotal: 140,
    entradasDisponibles: 0,
    estado: 'previo',
    imagenUrl: 'https://images.unsplash.com/photo-1465847899084-d164df4dedc6?auto=format&fit=crop&w=800&q=80'
  },
  {
    id: 5,
    titulo: 'Jam Session Barrial de Otoño',
    tipo: 'Jam Session',
    fecha: '2026-05-16',
    hora: '21:00 hs',
    lugar: 'Patio de Ensayo - Sede Almagro',
    descripcion: 'Encuentro de improvisación donde docentes, alumnos y músicos del barrio compartieron escenario en un ambiente distendido y fraterno.',
    repertorio: 'Standards de blues clásico, milongas orquestadas y standards de swing.',
    precioEntrada: 800,
    capacidadTotal: 90,
    entradasDisponibles: 0,
    estado: 'previo',
    imagenUrl: 'https://images.unsplash.com/photo-1511192336575-5a79af67a629?auto=format&fit=crop&w=800&q=80'
  }
];

export const INITIAL_PREGUNTAS_FRECUENTES: PreguntaFrecuente[] = [
  {
    id: 1,
    categoria: 'Instrumentos y Préstamos',
    pregunta: '¿Qué pasa si no tengo instrumento propio?',
    respuesta: 'En "A Todo Ritmo" ningún alumno se queda afuera por no tener instrumento. Disponemos de un Banco Solidario de Instrumentos que te presta guitarras, violines, flautas y teclados para su uso en clase y para practicar en nuestras salas de estudio sin costo adicional.',
    orden: 1
  },
  {
    id: 2,
    categoria: 'Inscripciones',
    pregunta: '¿Hay examen de ingreso o audición eliminatoria?',
    respuesta: '¡No! Rechazamos los filtros excluyentes. La escuela está abierta a personas desde los 7 años en adelante, sin importar si nunca antes tocaste un instrumento. El único requisito es tener curiosidad y ganas de aprender.',
    orden: 2
  },
  {
    id: 3,
    categoria: 'Cuotas y Becas',
    pregunta: '¿Qué facilidades de pago o becas ofrecen?',
    respuesta: 'Mantenemos aranceles comunitarios y accesibles. Si tu familia atraviesa dificultades económicas, contamos con un programa de becas parciales y totales sustentadas por la cooperadora y eventos solidarios. Consulta en secretaría de manera confidencial.',
    orden: 3
  },
  {
    id: 4,
    categoria: 'Niveles y Edades',
    pregunta: '¿A partir de qué edad se puede ingresar? ¿Hay límite de edad?',
    respuesta: 'Recibimos chicos y chicas a partir de los 7 años. No hay límite superior de edad: tenemos cursos con alumnos de 15, 30, 50 y más de 75 años compartiendo proyectos sonoros.',
    orden: 4
  },
  {
    id: 5,
    categoria: 'Inscripciones',
    pregunta: '¿Qué documentación necesito para inscribirme?',
    respuesta: 'Para completar la inscripción solo necesitas DNI (frente y dorso) del alumno (y del adulto responsable si es menor de 18 años), ficha médica simple de aptitud física y completar el formulario online.',
    orden: 5
  },
  {
    id: 6,
    categoria: 'Instrumentos y Préstamos',
    pregunta: '¿Puedo llevarme el instrumento a mi casa?',
    respuesta: 'Alumnos regulares con más de 2 meses de cursada pueden solicitar el préstamo rotativo a domicilio de instrumentos de menor porte (como violines infantiles y guitarras) mediante el convenio de comodato solidario.',
    orden: 6
  }
];

export const INITIAL_RESENAS: Resena[] = [
  {
    id: 1,
    autor: 'Esteban Méndez',
    rol: 'Alumno/a',
    calificacion: 5,
    comentario: 'Empecé de cero a los 42 años sin tener guitarra. Julián y la escuela me prestaron una y en 3 meses ya estaba tocando zambas y rock nacional en el ensamble. El clima humano es insuperable.',
    tallerNombre: 'Guitarra Criolla y Popular',
    fecha: 'Agosto 2026'
  },
  {
    id: 2,
    autor: 'Marcela V. (mamá de Sofía, 9 años)',
    rol: 'Familiar',
    calificacion: 5,
    comentario: 'En otras escuelas de música nos pedían comprar un violín carísimo antes de empezar. En A Todo Ritmo Camila le adaptó un violín 1/2 de la escuela con una dulzura y paciencia admirable. ¡Mi hija no para de sonreír!',
    tallerNombre: 'Violín: Primeras Cuerdas',
    fecha: 'Julio 2026'
  },
  {
    id: 3,
    autor: 'Lucas Benítez',
    rol: 'Egresado/a',
    calificacion: 5,
    comentario: 'La cuota es realmente accesible y no sentís esa competencia tóxica que hay en otros conservatorios. Acá aprendés a escuchar al de al lado y a tocar en grupo.',
    tallerNombre: 'Batería y Pulso Rítmico',
    fecha: 'Junio 2026'
  }
];
