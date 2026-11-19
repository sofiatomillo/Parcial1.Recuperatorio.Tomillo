export type InstrumentoTipo = 
  | 'Guitarra' 
  | 'Violín' 
  | 'Flauta' 
  | 'Batería' 
  | 'Piano' 
  | 'Teclado' 
  | 'Canto Popular' 
  | 'Bajo Eléctrico'
  | 'Ensamble';

export type NivelTipo = 
  | 'Inicial / Sin experiencia' 
  | 'Intermedio' 
  | 'Avanzado' 
  | 'Ensamble Abierto';

export type ModalidadTipo = 'Presencial' | 'Online' | 'Híbrida';

export interface Docente {
  id: number;
  nombre: string;
  apellido: string;
  especialidad: string;
  instrumentoPrincipal: string;
  biografia: string;
  trayectoriaCv: string;
  email: string;
  telefono: string;
  fotoUrl: string;
  activo: boolean;
}

export interface Taller {
  id: number;
  nombre: string;
  instrumento: InstrumentoTipo;
  docenteId: number;
  nivel: NivelTipo;
  edadMinima: number;
  diaSemana: string;
  horario: string;
  vacantesTotales: number;
  vacantesDisponibles: number;
  arancelMensual: number;
  modalidad: ModalidadTipo;
  sedeId: number;
  descripcion: string;
  requisitos: string;
  instrumentosPrestamoDisponibles: number;
  destacado: boolean;
  imagenUrl: string;
}

export interface Evento {
  id: number;
  titulo: string;
  tipo: 'Muestra de Alumnos' | 'Concierto Abierto' | 'Masterclass' | 'Jam Session';
  fecha: string; // YYYY-MM-DD
  hora: string;
  lugar: string;
  descripcion: string;
  repertorio: string;
  precioEntrada: number;
  capacidadTotal: number;
  entradasDisponibles: number;
  estado: 'proximo' | 'previo' | 'agotado';
  imagenUrl: string;
}

export interface Inscripcion {
  id: number;
  codigoInscripcion: string;
  tallerId: number;
  nombre: string;
  apellido: string;
  email: string;
  telefono: string;
  edad: number;
  genero: 'Masc' | 'Fem' | 'NB' | 'Otro' | 'Prefiero no decir';
  modalidad: ModalidadTipo;
  necesitaPrestamoInstrumento: boolean;
  poseeInstrumentoPropio: boolean;
  sedeId: number;
  horarioSeleccionado: string;
  comentarios?: string;
  estado: 'Confirmada' | 'Pendiente de Confirmación' | 'En Lista de Espera';
  fechaInscripcion: string;
}

export interface EntradaEvento {
  id: number;
  codigoTicket: string;
  eventoId: number;
  nombreComprador: string;
  emailComprador: string;
  telefonoComprador?: string;
  cantidad: number;
  totalAbonado: number;
  metodoPago: 'Efectivo en Puerta' | 'Transferencia' | 'Beca Comunitaria';
  estado: 'Reservada' | 'Abonada' | 'Cancelada';
  fechaReserva: string;
}

export interface Consulta {
  id: number;
  nombre: string;
  email: string;
  telefono?: string;
  motivo: string;
  mensaje: string;
  fecha: string;
}

export interface PreguntaFrecuente {
  id: number;
  categoria: 'Inscripciones' | 'Instrumentos y Préstamos' | 'Cuotas y Becas' | 'Niveles y Edades';
  pregunta: string;
  respuesta: string;
  orden: number;
}

export interface Resena {
  id: number;
  autor: string;
  rol: 'Alumno/a' | 'Familiar' | 'Egresado/a';
  calificacion: number;
  comentario: string;
  tallerNombre: string;
  fecha: string;
}

export interface Sede {
  id: number;
  nombre: string;
  direccion: string;
  barrio: string;
  telefono: string;
  email: string;
  horariosAtencion: string;
  colectivos: string;
  subte: string;
}

export interface AdminUser {
  id: number;
  usuario: string;
  nombreCompleto: string;
  email: string;
  password?: string;
  rol: 'superadmin' | 'admin';
  activo: boolean;
  ultimoAcceso: string | null;
}

export interface ContenidoPagina {
  bannerSuperiorIzq: string;
  bannerSuperiorDer: string;
  heroEtiqueta: string;
  heroTitulo1: string;
  heroTitulo2: string;
  heroDescripcion: string;
  heroImagen: string;
  nosotrosTitulo: string;
  nosotrosSubtitulo: string;
  nosotrosHistoriaTitulo: string;
  nosotrosHistoriaP1: string;
  nosotrosHistoriaP2: string;
  nosotrosHistoriaImagen: string;
  nosotrosCita: string;
  contactoWhatsapp: string;
  contactoEmail: string;
}

export type SeccionActiva = 
  | 'home' 
  | 'nosotros' 
  | 'talleres' 
  | 'eventos' 
  | 'docentes' 
  | 'inscripcion' 
  | 'consultas'
  | 'admin';
