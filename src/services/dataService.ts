import {
  Docente,
  Taller,
  Evento,
  Inscripcion,
  EntradaEvento,
  Consulta,
  PreguntaFrecuente,
  Resena,
  Sede,
  AdminUser,
  ContenidoPagina
} from '../types';
import {
  INITIAL_DOCENTES,
  INITIAL_TALLERES,
  INITIAL_EVENTOS,
  INITIAL_PREGUNTAS_FRECUENTES,
  INITIAL_RESENAS,
  INITIAL_SEDES
} from '../data/initialData';

const STORAGE_KEYS = {
  DOCENTES: 'atr_docentes_v1',
  TALLERES: 'atr_talleres_v1',
  EVENTOS: 'atr_eventos_v1',
  INSCRIPCIONES: 'atr_inscripciones_v1',
  ENTRADAS: 'atr_entradas_v1',
  CONSULTAS: 'atr_consultas_v1',
  SEDES: 'atr_sedes_v1',
  RESENAS: 'atr_resenas_v1',
  FAQS: 'atr_faqs_v1',
  CONTENIDO: 'atr_contenido_v1',
  ADMINS: 'atr_admins_v1',
  ADMIN_SESION: 'atr_admin_sesion_v1',
};

export const INITIAL_CONTENIDO_PAGINA: ContenidoPagina = {
  bannerSuperiorIzq: 'Inscripciones 2026 Abiertas • Préstamo gratuito de instrumentos disponible',
  bannerSuperiorDer: 'Sedes: San Telmo & Almagro',
  heroEtiqueta: 'Inscripciones Ciclo Lectivo 2026',
  heroTitulo1: 'Sentí la música,',
  heroTitulo2: 'descubrí tu ritmo.',
  heroDescripcion:
    'Una escuela de música inclusiva en el corazón del barrio. Clases de guitarra, violín, flauta, batería, piano y canto para todas las edades. ¿No tenés instrumento? Te lo prestamos nosotros.',
  heroImagen: '',
  nosotrosTitulo: 'Escuela de Música "A Todo Ritmo"',
  nosotrosSubtitulo:
    'Nacimos con una convicción innegociable: la música no es un privilegio de pocos, sino un lenguaje colectivo, transformador y comunitario.',
  nosotrosHistoriaTitulo: 'De un taller barrial a una red de formación musical abierta',
  nosotrosHistoriaP1:
    '"A Todo Ritmo" nació en 2018 impulsada por un grupo de músicos y docentes egresados de conservatorios públicos que veían cómo cientos de chicos, jóvenes y adultos quedaban afuera de la formación musical por no conseguir vacantes en instituciones saturadas o por no poder costear un instrumento propio.',
  nosotrosHistoriaP2:
    'Comenzamos con una sala prestada en San Telmo y cinco guitarras donadas. Hoy contamos con dos sedes activas, más de 200 alumnos regulares, un Banco Solidario con más de 60 instrumentos en comodato y un cuerpo docente apasionado por la enseñanza popular.',
  nosotrosHistoriaImagen:
    'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=800&q=80',
  nosotrosCita: '"Aprender un instrumento te da una voz que las palabras no alcanzan."',
  contactoWhatsapp: '(011) 15-5829-1022 (Lunes a Viernes de 10 a 20 hs)',
  contactoEmail: 'consultas@atodoritmo.edu.ar',
};

const INITIAL_ADMINS: AdminUser[] = [
  {
    id: 1,
    usuario: 'admin',
    nombreCompleto: 'Administrador General',
    email: 'admin@atodoritmo.edu.ar',
    password: 'Admin2026!',
    rol: 'superadmin',
    activo: true,
    ultimoAcceso: null,
  },
];

export class DataService {
  private static getStored<T>(key: string, fallback: T): T {
    try {
      const item = localStorage.getItem(key);
      return item ? JSON.parse(item) : fallback;
    } catch {
      return fallback;
    }
  }

  private static setStored<T>(key: string, value: T): void {
    try {
      localStorage.setItem(key, JSON.stringify(value));
    } catch (e) {
      console.error('Error saving to localStorage', e);
    }
  }

  // --- AUTENTICACIÓN & USUARIOS ADMIN ---
  static getAdmins(): AdminUser[] {
    return this.getStored<AdminUser[]>(STORAGE_KEYS.ADMINS, INITIAL_ADMINS);
  }

  static getAdminActual(): Omit<AdminUser, 'password'> | null {
    return this.getStored<Omit<AdminUser, 'password'> | null>(STORAGE_KEYS.ADMIN_SESION, null);
  }

  static autenticarAdmin(identificador: string, password: string): { ok: boolean; admin?: Omit<AdminUser, 'password'>; error?: string } {
    const admins = this.getAdmins();
    const ident = identificador.trim().toLowerCase();
    const encontrado = admins.find(
      (a) => a.usuario.toLowerCase() === ident || a.email.toLowerCase() === ident
    );
    if (!encontrado || encontrado.password !== password) {
      return { ok: false, error: 'Usuario o contraseña incorrectos.' };
    }
    if (!encontrado.activo) {
      return { ok: false, error: 'Tu usuario administrador está desactivado.' };
    }

    const ahora = new Date().toISOString();
    const actualizados = admins.map((a) =>
      a.id === encontrado.id ? { ...a, ultimoAcceso: ahora } : a
    );
    this.setStored(STORAGE_KEYS.ADMINS, actualizados);

    const { password: _pw, ...publicAdmin } = { ...encontrado, ultimoAcceso: ahora };
    this.setStored(STORAGE_KEYS.ADMIN_SESION, publicAdmin);
    return { ok: true, admin: publicAdmin };
  }

  static cerrarSesionAdmin(): void {
    localStorage.removeItem(STORAGE_KEYS.ADMIN_SESION);
  }

  static crearAdmin(data: {
    usuario: string;
    nombreCompleto: string;
    email: string;
    password: string;
    rol: 'superadmin' | 'admin';
  }): { ok: boolean; error?: string; sqlQuery?: string } {
    const admins = this.getAdmins();
    if (admins.some((a) => a.usuario.toLowerCase() === data.usuario.trim().toLowerCase() || a.email.toLowerCase() === data.email.trim().toLowerCase())) {
      return { ok: false, error: 'Ya existe un administrador con ese usuario o email.' };
    }
    const newId = admins.length > 0 ? Math.max(...admins.map((a) => a.id)) + 1 : 1;
    const nuevo: AdminUser = {
      id: newId,
      usuario: data.usuario.trim(),
      nombreCompleto: data.nombreCompleto.trim(),
      email: data.email.trim(),
      password: data.password,
      rol: data.rol,
      activo: true,
      ultimoAcceso: null,
    };
    this.setStored(STORAGE_KEYS.ADMINS, [...admins, nuevo]);
    const sqlQuery = `INSERT INTO administradores (usuario, nombre_completo, email, password_hash, rol, activo)
VALUES ('${nuevo.usuario}', '${nuevo.nombreCompleto}', '${nuevo.email}', '$2y$10$...', '${nuevo.rol}', 1);`;
    return { ok: true, sqlQuery };
  }

  static toggleAdminActivo(id: number): void {
    const admins = this.getAdmins().map((a) => (a.id === id ? { ...a, activo: !a.activo } : a));
    this.setStored(STORAGE_KEYS.ADMINS, admins);
  }

  // --- CONTENIDO EDITABLE DE LA PÁGINA (TEXTOS E IMÁGENES) ---
  static getContenidoPagina(): ContenidoPagina {
    const stored = this.getStored<Partial<ContenidoPagina>>(STORAGE_KEYS.CONTENIDO, {});
    return { ...INITIAL_CONTENIDO_PAGINA, ...stored };
  }

  static guardarContenidoPagina(contenido: ContenidoPagina): string {
    this.setStored(STORAGE_KEYS.CONTENIDO, contenido);
    return `UPDATE configuracion_pagina SET valor = CASE clave
  WHEN 'hero_titulo_1' THEN '${contenido.heroTitulo1.replace(/'/g, "''")}'
  WHEN 'nosotros_historia_imagen' THEN '${contenido.nosotrosHistoriaImagen.slice(0, 80)}'
END;`;
  }

  // --- DOCENTES ---
  static getDocentes(): Docente[] {
    return this.getStored<Docente[]>(STORAGE_KEYS.DOCENTES, INITIAL_DOCENTES);
  }

  static getDocenteById(id: number): Docente | undefined {
    return this.getDocentes().find((d) => d.id === id);
  }

  static guardarDocente(data: Omit<Docente, 'id'> & { id?: number }): { docente: Docente; sqlQuery: string } {
    const lista = this.getDocentes();
    if (data.id && data.id > 0) {
      const actualizado: Docente = { ...data, id: data.id };
      const nuevaLista = lista.map((d) => (d.id === data.id ? actualizado : d));
      this.setStored(STORAGE_KEYS.DOCENTES, nuevaLista);
      return {
        docente: actualizado,
        sqlQuery: `UPDATE docentes SET nombre='${actualizado.nombre}', apellido='${actualizado.apellido}', especialidad='${actualizado.especialidad}', imagen='${actualizado.fotoUrl.slice(0, 60)}' WHERE id=${actualizado.id};`,
      };
    }
    const newId = lista.length > 0 ? Math.max(...lista.map((d) => d.id)) + 1 : 1;
    const nuevo: Docente = { ...data, id: newId };
    this.setStored(STORAGE_KEYS.DOCENTES, [...lista, nuevo]);
    return {
      docente: nuevo,
      sqlQuery: `INSERT INTO docentes (nombre, apellido, especialidad, instrumento_principal, email, activo) VALUES ('${nuevo.nombre}', '${nuevo.apellido}', '${nuevo.especialidad}', '${nuevo.instrumentoPrincipal}', '${nuevo.email}', ${nuevo.activo ? 1 : 0});`,
    };
  }

  static eliminarDocente(id: number): { ok: boolean; error?: string; sqlQuery?: string } {
    const talleres = this.getTalleres();
    if (talleres.some((t) => t.docenteId === id)) {
      return { ok: false, error: 'No se puede eliminar este docente porque tiene talleres asignados.' };
    }
    const nuevaLista = this.getDocentes().filter((d) => d.id !== id);
    this.setStored(STORAGE_KEYS.DOCENTES, nuevaLista);
    return { ok: true, sqlQuery: `DELETE FROM docentes WHERE id = ${id};` };
  }

  // --- TALLERES ---
  static getTalleres(): Taller[] {
    return this.getStored<Taller[]>(STORAGE_KEYS.TALLERES, INITIAL_TALLERES);
  }

  static getTallerById(id: number): Taller | undefined {
    return this.getTalleres().find((t) => t.id === id);
  }

  static guardarTaller(data: Omit<Taller, 'id'> & { id?: number }): { taller: Taller; sqlQuery: string } {
    const lista = this.getTalleres();
    if (data.id && data.id > 0) {
      const actualizado: Taller = { ...data, id: data.id };
      this.setStored(STORAGE_KEYS.TALLERES, lista.map((t) => (t.id === data.id ? actualizado : t)));
      return {
        taller: actualizado,
        sqlQuery: `UPDATE talleres SET nombre='${actualizado.nombre}', docente_id=${actualizado.docenteId}, vacantes_disponibles=${actualizado.vacantesDisponibles}, arancel_mensual=${actualizado.arancelMensual} WHERE id=${actualizado.id};`,
      };
    }
    const newId = lista.length > 0 ? Math.max(...lista.map((t) => t.id)) + 1 : 1;
    const nuevo: Taller = { ...data, id: newId };
    this.setStored(STORAGE_KEYS.TALLERES, [...lista, nuevo]);
    return {
      taller: nuevo,
      sqlQuery: `INSERT INTO talleres (nombre, instrumento, docente_id, nivel, dia_semana, horario, arancel_mensual) VALUES ('${nuevo.nombre}', '${nuevo.instrumento}', ${nuevo.docenteId}, '${nuevo.nivel}', '${nuevo.diaSemana}', '${nuevo.horario}', ${nuevo.arancelMensual});`,
    };
  }

  static eliminarTaller(id: number): string {
    this.setStored(STORAGE_KEYS.TALLERES, this.getTalleres().filter((t) => t.id !== id));
    return `DELETE FROM talleres WHERE id = ${id};`;
  }

  // --- EVENTOS ---
  static getEventos(): Evento[] {
    return this.getStored<Evento[]>(STORAGE_KEYS.EVENTOS, INITIAL_EVENTOS);
  }

  static getEventoById(id: number): Evento | undefined {
    return this.getEventos().find((e) => e.id === id);
  }

  static guardarEvento(data: Omit<Evento, 'id'> & { id?: number }): { evento: Evento; sqlQuery: string } {
    const lista = this.getEventos();
    if (data.id && data.id > 0) {
      const actualizado: Evento = { ...data, id: data.id };
      this.setStored(STORAGE_KEYS.EVENTOS, lista.map((e) => (e.id === data.id ? actualizado : e)));
      return {
        evento: actualizado,
        sqlQuery: `UPDATE eventos SET titulo='${actualizado.titulo}', fecha='${actualizado.fecha}', lugar='${actualizado.lugar}', estado='${actualizado.estado}' WHERE id=${actualizado.id};`,
      };
    }
    const newId = lista.length > 0 ? Math.max(...lista.map((e) => e.id)) + 1 : 1;
    const nuevo: Evento = { ...data, id: newId };
    this.setStored(STORAGE_KEYS.EVENTOS, [...lista, nuevo]);
    return {
      evento: nuevo,
      sqlQuery: `INSERT INTO eventos (titulo, tipo, fecha, hora, lugar, precio_entrada, capacidad_total, entradas_disponibles) VALUES ('${nuevo.titulo}', '${nuevo.tipo}', '${nuevo.fecha}', '${nuevo.hora}', '${nuevo.lugar}', ${nuevo.precioEntrada}, ${nuevo.capacidadTotal}, ${nuevo.entradasDisponibles});`,
    };
  }

  static eliminarEvento(id: number): string {
    this.setStored(STORAGE_KEYS.EVENTOS, this.getEventos().filter((e) => e.id !== id));
    return `DELETE FROM eventos WHERE id = ${id};`;
  }

  // --- SEDES ---
  static getSedes(): Sede[] {
    return this.getStored<Sede[]>(STORAGE_KEYS.SEDES, INITIAL_SEDES);
  }

  static guardarSede(sede: Sede): string {
    const lista = this.getSedes().map((s) => (s.id === sede.id ? sede : s));
    this.setStored(STORAGE_KEYS.SEDES, lista);
    return `UPDATE sedes SET nombre='${sede.nombre}', direccion='${sede.direccion}', telefono='${sede.telefono}' WHERE id=${sede.id};`;
  }

  // --- RESEÑAS ---
  static getResenas(): Resena[] {
    return this.getStored<Resena[]>(STORAGE_KEYS.RESENAS, INITIAL_RESENAS);
  }

  static guardarResena(resena: Omit<Resena, 'id'> & { id?: number }): string {
    const lista = this.getResenas();
    if (resena.id && resena.id > 0) {
      this.setStored(STORAGE_KEYS.RESENAS, lista.map((r) => (r.id === resena.id ? { ...resena, id: resena.id! } : r)));
      return `UPDATE resenas SET autor='${resena.autor}', calificacion=${resena.calificacion} WHERE id=${resena.id};`;
    }
    const newId = lista.length > 0 ? Math.max(...lista.map((r) => r.id)) + 1 : 1;
    this.setStored(STORAGE_KEYS.RESENAS, [...lista, { ...resena, id: newId }]);
    return `INSERT INTO resenas (autor, rol, calificacion, comentario, taller_nombre, fecha) VALUES ('${resena.autor}', '${resena.rol}', ${resena.calificacion}, '...', '${resena.tallerNombre}', '${resena.fecha}');`;
  }

  static eliminarResena(id: number): string {
    this.setStored(STORAGE_KEYS.RESENAS, this.getResenas().filter((r) => r.id !== id));
    return `DELETE FROM resenas WHERE id = ${id};`;
  }

  // --- PREGUNTAS FRECUENTES ---
  static getPreguntasFrecuentes(): PreguntaFrecuente[] {
    return this.getStored<PreguntaFrecuente[]>(STORAGE_KEYS.FAQS, INITIAL_PREGUNTAS_FRECUENTES);
  }

  static guardarFaq(faq: Omit<PreguntaFrecuente, 'id'> & { id?: number }): string {
    const lista = this.getPreguntasFrecuentes();
    if (faq.id && faq.id > 0) {
      this.setStored(STORAGE_KEYS.FAQS, lista.map((f) => (f.id === faq.id ? { ...faq, id: faq.id! } : f)));
      return `UPDATE preguntas_frecuentes SET pregunta='${faq.pregunta}', orden=${faq.orden} WHERE id=${faq.id};`;
    }
    const newId = lista.length > 0 ? Math.max(...lista.map((f) => f.id)) + 1 : 1;
    this.setStored(STORAGE_KEYS.FAQS, [...lista, { ...faq, id: newId }]);
    return `INSERT INTO preguntas_frecuentes (categoria, pregunta, respuesta, orden) VALUES ('${faq.categoria}', '${faq.pregunta}', '...', ${faq.orden});`;
  }

  static eliminarFaq(id: number): string {
    this.setStored(STORAGE_KEYS.FAQS, this.getPreguntasFrecuentes().filter((f) => f.id !== id));
    return `DELETE FROM preguntas_frecuentes WHERE id = ${id};`;
  }

  // --- INSCRIPCIONES / ALUMNOS ---
  static getInscripciones(): Inscripcion[] {
    return this.getStored<Inscripcion[]>(STORAGE_KEYS.INSCRIPCIONES, []);
  }

  static guardarInscripcionAdmin(data: Omit<Inscripcion, 'id' | 'codigoInscripcion' | 'fechaInscripcion'> & { id?: number }): {
    inscripcion: Inscripcion;
    sqlQuery: string;
  } {
    const inscripciones = this.getInscripciones();
    const talleres = this.getTalleres();

    if (data.id && data.id > 0) {
      const existente = inscripciones.find((i) => i.id === data.id);
      const actualizado: Inscripcion = {
        ...data,
        id: data.id,
        codigoInscripcion: existente?.codigoInscripcion || `ATR-${new Date().getFullYear()}-${String(data.id).padStart(4, '0')}`,
        fechaInscripcion: existente?.fechaInscripcion || new Date().toISOString(),
      };
      if (existente && existente.tallerId !== data.tallerId) {
        const tallerAnt = talleres.find((t) => t.id === existente.tallerId);
        const tallerNvo = talleres.find((t) => t.id === data.tallerId);
        if (tallerAnt) tallerAnt.vacantesDisponibles = Math.min(tallerAnt.vacantesTotales, tallerAnt.vacantesDisponibles + 1);
        if (tallerNvo && tallerNvo.vacantesDisponibles > 0) tallerNvo.vacantesDisponibles -= 1;
        this.setStored(STORAGE_KEYS.TALLERES, talleres);
      }
      this.setStored(
        STORAGE_KEYS.INSCRIPCIONES,
        inscripciones.map((i) => (i.id === data.id ? actualizado : i))
      );
      return {
        inscripcion: actualizado,
        sqlQuery: `UPDATE inscripciones SET nombre='${actualizado.nombre}', apellido='${actualizado.apellido}', taller_id=${actualizado.tallerId}, estado='${actualizado.estado}' WHERE id=${actualizado.id};`,
      };
    }

    const newId = inscripciones.length > 0 ? Math.max(...inscripciones.map((i) => i.id)) + 1 : 1;
    const codigoInscripcion = `ATR-${new Date().getFullYear()}-${String(newId).padStart(4, '0')}`;
    const nuevaInscripcion: Inscripcion = {
      ...data,
      id: newId,
      codigoInscripcion,
      fechaInscripcion: new Date().toISOString(),
    };
    const taller = talleres.find((t) => t.id === data.tallerId);
    if (taller && data.estado === 'Confirmada' && taller.vacantesDisponibles > 0) {
      taller.vacantesDisponibles -= 1;
      this.setStored(STORAGE_KEYS.TALLERES, talleres);
    }
    this.setStored(STORAGE_KEYS.INSCRIPCIONES, [...inscripciones, nuevaInscripcion]);
    return {
      inscripcion: nuevaInscripcion,
      sqlQuery: `INSERT INTO inscripciones (codigo_inscripcion, taller_id, nombre, apellido, email, edad, estado) VALUES ('${codigoInscripcion}', ${data.tallerId}, '${data.nombre}', '${data.apellido}', '${data.email}', ${data.edad}, '${data.estado}');`,
    };
  }

  static actualizarEstadoInscripcion(id: number, estado: Inscripcion['estado']): string {
    const lista = this.getInscripciones().map((i) => (i.id === id ? { ...i, estado } : i));
    this.setStored(STORAGE_KEYS.INSCRIPCIONES, lista);
    return `UPDATE inscripciones SET estado = '${estado}' WHERE id = ${id};`;
  }

  static eliminarInscripcion(id: number): string {
    const inscripciones = this.getInscripciones();
    const target = inscripciones.find((i) => i.id === id);
    if (target) {
      const talleres = this.getTalleres();
      const taller = talleres.find((t) => t.id === target.tallerId);
      if (taller) {
        taller.vacantesDisponibles = Math.min(taller.vacantesTotales, taller.vacantesDisponibles + 1);
        this.setStored(STORAGE_KEYS.TALLERES, talleres);
      }
    }
    this.setStored(STORAGE_KEYS.INSCRIPCIONES, inscripciones.filter((i) => i.id !== id));
    return `DELETE FROM inscripciones WHERE id = ${id};`;
  }

  /**
   * Ejecuta la lógica equivalente a:
   * INSERT INTO inscripciones (...) VALUES (...);
   * UPDATE talleres SET vacantes_disponibles = vacantes_disponibles - 1 WHERE id = ?;
   */
  static registrarInscripcion(data: Omit<Inscripcion, 'id' | 'codigoInscripcion' | 'fechaInscripcion' | 'estado'>): {
    inscripcion: Inscripcion;
    sqlQuery: string;
  } {
    const inscripciones = this.getInscripciones();
    const talleres = this.getTalleres();
    const taller = talleres.find((t) => t.id === data.tallerId);

    const newId = inscripciones.length > 0 ? Math.max(...inscripciones.map((i) => i.id)) + 1 : 1;
    const codigoInscripcion = `ATR-${new Date().getFullYear()}-${String(newId).padStart(4, '0')}`;
    const fechaInscripcion = new Date().toISOString();

    const nuevaInscripcion: Inscripcion = {
      ...data,
      id: newId,
      codigoInscripcion,
      fechaInscripcion,
      estado: taller && taller.vacantesDisponibles > 0 ? 'Confirmada' : 'En Lista de Espera',
    };

    if (taller && taller.vacantesDisponibles > 0) {
      taller.vacantesDisponibles -= 1;
      this.setStored(STORAGE_KEYS.TALLERES, talleres);
    }

    inscripciones.push(nuevaInscripcion);
    this.setStored(STORAGE_KEYS.INSCRIPCIONES, inscripciones);

    const sqlQuery = `
-- Transacción de Inscripción de Alumno
START TRANSACTION;

INSERT INTO inscripciones (
  codigo_inscripcion, taller_id, nombre, apellido, email,
  telefono, edad, genero, modalidad, necesita_prestamo_instrumento,
  posee_instrumento_propio, sede_id, horario_seleccionado, estado
) VALUES (
  '${codigoInscripcion}', ${data.tallerId}, '${data.nombre}', '${data.apellido}', '${data.email}',
  '${data.telefono}', ${data.edad}, '${data.genero}', '${data.modalidad}', ${data.necesitaPrestamoInstrumento ? 1 : 0},
  ${data.poseeInstrumentoPropio ? 1 : 0}, ${data.sedeId}, '${data.horarioSeleccionado}', '${nuevaInscripcion.estado}'
);

UPDATE talleres
SET vacantes_disponibles = GREATEST(vacantes_disponibles - 1, 0)
WHERE id = ${data.tallerId};

COMMIT;`;

    return { inscripcion: nuevaInscripcion, sqlQuery };
  }

  // --- ENTRADAS EVENTOS ---
  static getEntradas(): EntradaEvento[] {
    return this.getStored<EntradaEvento[]>(STORAGE_KEYS.ENTRADAS, []);
  }

  static reservarEntradas(data: Omit<EntradaEvento, 'id' | 'codigoTicket' | 'fechaReserva' | 'estado'>): {
    entrada: EntradaEvento;
    sqlQuery: string;
  } {
    const entradas = this.getEntradas();
    const eventos = this.getEventos();
    const evento = eventos.find((e) => e.id === data.eventoId);

    const newId = entradas.length > 0 ? Math.max(...entradas.map((e) => e.id)) + 1 : 1;
    const codigoTicket = `TKT-${data.eventoId}-${Date.now().toString().slice(-5)}`;
    const fechaReserva = new Date().toISOString();

    const nuevaEntrada: EntradaEvento = {
      ...data,
      id: newId,
      codigoTicket,
      fechaReserva,
      estado: 'Reservada',
    };

    if (evento) {
      evento.entradasDisponibles = Math.max(0, evento.entradasDisponibles - data.cantidad);
      if (evento.entradasDisponibles === 0) {
        evento.estado = 'agotado';
      }
      this.setStored(STORAGE_KEYS.EVENTOS, eventos);
    }

    entradas.push(nuevaEntrada);
    this.setStored(STORAGE_KEYS.ENTRADAS, entradas);

    const sqlQuery = `
-- Transacción de Reserva de Entradas para Evento
START TRANSACTION;

INSERT INTO entradas_eventos (
  codigo_ticket, evento_id, nombre_comprador, email_comprador,
  telefono_comprador, cantidad, total_abonado, metodo_pago, estado
) VALUES (
  '${codigoTicket}', ${data.eventoId}, '${data.nombreComprador}', '${data.emailComprador}',
  '${data.telefonoComprador || ''}', ${data.cantidad}, ${data.totalAbonado}, '${data.metodoPago}', 'Reservada'
);

UPDATE eventos
SET entradas_disponibles = GREATEST(entradas_disponibles - ${data.cantidad}, 0)
WHERE id = ${data.eventoId};

COMMIT;`;

    return { entrada: nuevaEntrada, sqlQuery };
  }

  // --- CONSULTAS ---
  static getConsultas(): Consulta[] {
    return this.getStored<Consulta[]>(STORAGE_KEYS.CONSULTAS, []);
  }

  static registrarConsulta(data: Omit<Consulta, 'id' | 'fecha'>): {
    consulta: Consulta;
    sqlQuery: string;
  } {
    const consultas = this.getConsultas();
    const newId = consultas.length > 0 ? Math.max(...consultas.map((c) => c.id)) + 1 : 1;
    const fecha = new Date().toISOString();

    const nuevaConsulta: Consulta = {
      ...data,
      id: newId,
      fecha,
    };

    consultas.push(nuevaConsulta);
    this.setStored(STORAGE_KEYS.CONSULTAS, consultas);

    const sqlQuery = `
INSERT INTO consultas (nombre, email, telefono, motivo, mensaje)
VALUES ('${data.nombre}', '${data.email}', '${data.telefono || ''}', '${data.motivo}', '${data.mensaje.replace(/'/g, "''")}');
`;

    return { consulta: nuevaConsulta, sqlQuery };
  }

  static eliminarConsulta(id: number): void {
    this.setStored(STORAGE_KEYS.CONSULTAS, this.getConsultas().filter((c) => c.id !== id));
  }

  static resetDatabase(): void {
    Object.values(STORAGE_KEYS).forEach((key) => localStorage.removeItem(key));
  }
}
