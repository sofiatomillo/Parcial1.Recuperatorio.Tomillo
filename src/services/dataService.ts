import { Docente, Taller, Evento, Inscripcion, EntradaEvento, Consulta, PreguntaFrecuente, Resena, Sede } from '../types';
import { INITIAL_DOCENTES, INITIAL_TALLERES, INITIAL_EVENTOS, INITIAL_PREGUNTAS_FRECUENTES, INITIAL_RESENAS, INITIAL_SEDES } from '../data/initialData';

const STORAGE_KEYS = {
  DOCENTES: 'atr_docentes_v1',
  TALLERES: 'atr_talleres_v1',
  EVENTOS: 'atr_eventos_v1',
  INSCRIPCIONES: 'atr_inscripciones_v1',
  ENTRADAS: 'atr_entradas_v1',
  CONSULTAS: 'atr_consultas_v1',
};

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

  // --- DOCENTES ---
  static getDocentes(): Docente[] {
    return this.getStored<Docente[]>(STORAGE_KEYS.DOCENTES, INITIAL_DOCENTES);
  }

  static getDocenteById(id: number): Docente | undefined {
    return this.getDocentes().find(d => d.id === id);
  }

  // --- TALLERES ---
  static getTalleres(): Taller[] {
    return this.getStored<Taller[]>(STORAGE_KEYS.TALLERES, INITIAL_TALLERES);
  }

  static getTallerById(id: number): Taller | undefined {
    return this.getTalleres().find(t => t.id === id);
  }

  // --- EVENTOS ---
  static getEventos(): Evento[] {
    return this.getStored<Evento[]>(STORAGE_KEYS.EVENTOS, INITIAL_EVENTOS);
  }

  static getEventoById(id: number): Evento | undefined {
    return this.getEventos().find(e => e.id === id);
  }

  // --- SEDES ---
  static getSedes(): Sede[] {
    return INITIAL_SEDES;
  }

  // --- RESEÑAS ---
  static getResenas(): Resena[] {
    return INITIAL_RESENAS;
  }

  // --- PREGUNTAS FRECUENTES ---
  static getPreguntasFrecuentes(): PreguntaFrecuente[] {
    return INITIAL_PREGUNTAS_FRECUENTES;
  }

  // --- INSCRIPCIONES ---
  static getInscripciones(): Inscripcion[] {
    return this.getStored<Inscripcion[]>(STORAGE_KEYS.INSCRIPCIONES, []);
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
    const taller = talleres.find(t => t.id === data.tallerId);

    const newId = inscripciones.length > 0 ? Math.max(...inscripciones.map(i => i.id)) + 1 : 1;
    const codigoInscripcion = `ATR-${new Date().getFullYear()}-${String(newId).padStart(4, '0')}`;
    const fechaInscripcion = new Date().toISOString();

    const nuevaInscripcion: Inscripcion = {
      ...data,
      id: newId,
      codigoInscripcion,
      fechaInscripcion,
      estado: (taller && taller.vacantesDisponibles > 0) ? 'Confirmada' : 'En Lista de Espera'
    };

    // Actualizar vacantes en talleres si hay cupo
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

  /**
   * Ejecuta la lógica equivalente a:
   * INSERT INTO entradas_eventos (...) VALUES (...);
   * UPDATE eventos SET entradas_disponibles = entradas_disponibles - ? WHERE id = ?;
   */
  static reservarEntradas(data: Omit<EntradaEvento, 'id' | 'codigoTicket' | 'fechaReserva' | 'estado'>): {
    entrada: EntradaEvento;
    sqlQuery: string;
  } {
    const entradas = this.getEntradas();
    const eventos = this.getEventos();
    const evento = eventos.find(e => e.id === data.eventoId);

    const newId = entradas.length > 0 ? Math.max(...entradas.map(e => e.id)) + 1 : 1;
    const codigoTicket = `TKT-${data.eventoId}-${Date.now().toString().slice(-5)}`;
    const fechaReserva = new Date().toISOString();

    const nuevaEntrada: EntradaEvento = {
      ...data,
      id: newId,
      codigoTicket,
      fechaReserva,
      estado: 'Reservada'
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
    const newId = consultas.length > 0 ? Math.max(...consultas.map(c => c.id)) + 1 : 1;
    const fecha = new Date().toISOString();

    const nuevaConsulta: Consulta = {
      ...data,
      id: newId,
      fecha
    };

    consultas.push(nuevaConsulta);
    this.setStored(STORAGE_KEYS.CONSULTAS, consultas);

    const sqlQuery = `
INSERT INTO consultas (nombre, email, telefono, motivo, mensaje)
VALUES ('${data.nombre}', '${data.email}', '${data.telefono || ''}', '${data.motivo}', '${data.mensaje.replace(/'/g, "''")}');
`;

    return { consulta: nuevaConsulta, sqlQuery };
  }

  static resetDatabase(): void {
    localStorage.removeItem(STORAGE_KEYS.DOCENTES);
    localStorage.removeItem(STORAGE_KEYS.TALLERES);
    localStorage.removeItem(STORAGE_KEYS.EVENTOS);
    localStorage.removeItem(STORAGE_KEYS.INSCRIPCIONES);
    localStorage.removeItem(STORAGE_KEYS.ENTRADAS);
    localStorage.removeItem(STORAGE_KEYS.CONSULTAS);
  }
}
