import React, { useState } from 'react';
import {
  AdminUser,
  ContenidoPagina,
  Docente,
  Evento,
  Inscripcion,
  InstrumentoTipo,
  ModalidadTipo,
  NivelTipo,
  PreguntaFrecuente,
  Resena,
  SeccionActiva,
  Sede,
  Taller
} from '../types';
import { DataService, INITIAL_CONTENIDO_PAGINA } from '../services/dataService';
import {
  Shield,
  LogOut,
  Users,
  BookOpen,
  Calendar,
  GraduationCap,
  FileEdit,
  UserPlus,
  Trash2,
  Edit3,
  Upload,
  CheckCircle2,
  AlertCircle,
  Eye
} from 'lucide-react';

interface AdminSectionProps {
  adminActual: Omit<AdminUser, 'password'> | null;
  onLoginSuccess: (admin: Omit<AdminUser, 'password'>) => void;
  onLogout: () => void;
  docentes: Docente[];
  talleres: Taller[];
  eventos: Evento[];
  sedes: Sede[];
  resenas: Resena[];
  preguntasFrecuentes: PreguntaFrecuente[];
  contenidoPagina: ContenidoPagina;
  onRefrescarTodo: () => void;
  onNotificarSqlEjecutado: (sql: string) => void;
  onCambiarSeccion: (seccion: SeccionActiva) => void;
}

type AdminTab = 'alumnos' | 'docentes' | 'talleres' | 'eventos' | 'contenido' | 'admins';

const INSTRUMENTOS_LISTA: InstrumentoTipo[] = [
  'Guitarra',
  'Violín',
  'Flauta',
  'Batería',
  'Piano',
  'Teclado',
  'Canto Popular',
  'Bajo Eléctrico',
  'Ensamble'
];

const NIVELES_LISTA: NivelTipo[] = [
  'Inicial / Sin experiencia',
  'Intermedio',
  'Avanzado',
  'Ensamble Abierto'
];

const MODALIDADES_LISTA: ModalidadTipo[] = ['Presencial', 'Online', 'Híbrida'];

function leerArchivoComoDataUrl(file: File, onLoaded: (dataUrl: string) => void) {
  const reader = new FileReader();
  reader.onload = () => {
    if (typeof reader.result === 'string') {
      onLoaded(reader.result);
    }
  };
  reader.readAsDataURL(file);
}

export const AdminSection: React.FC<AdminSectionProps> = ({
  adminActual,
  onLoginSuccess,
  onLogout,
  docentes,
  talleres,
  eventos,
  sedes,
  resenas,
  preguntasFrecuentes,
  contenidoPagina,
  onRefrescarTodo,
  onNotificarSqlEjecutado,
  onCambiarSeccion
}) => {
  // Login state
  const [loginIdent, setLoginIdent] = useState('');
  const [loginPass, setLoginPass] = useState('');
  const [loginError, setLoginError] = useState('');

  // Dashboard state
  const [tab, setTab] = useState<AdminTab>('alumnos');
  const [mensajeOk, setMensajeOk] = useState('');
  const [mensajeError, setMensajeError] = useState('');

  // Alumnos state
  const [inscripciones, setInscripciones] = useState<Inscripcion[]>(DataService.getInscripciones());
  const [buscarAlumno, setBuscarAlumno] = useState('');
  const [filtroTaller, setFiltroTaller] = useState<number>(0);
  const [filtroEstado, setFiltroEstado] = useState<string>('');
  const [alumnoEditId, setAlumnoEditId] = useState<number>(0);
  const [alNombre, setAlNombre] = useState('');
  const [alApellido, setAlApellido] = useState('');
  const [alEmail, setAlEmail] = useState('');
  const [alTelefono, setAlTelefono] = useState('');
  const [alEdad, setAlEdad] = useState<number>(18);
  const [alGenero, setAlGenero] = useState<Inscripcion['genero']>('NB');
  const [alTallerId, setAlTallerId] = useState<number>(talleres[0]?.id || 1);
  const [alSedeId, setAlSedeId] = useState<number>(sedes[0]?.id || 1);
  const [alModalidad, setAlModalidad] = useState<ModalidadTipo>('Presencial');
  const [alPrestamo, setAlPrestamo] = useState(false);
  const [alPropio, setAlPropio] = useState(false);
  const [alHorario, setAlHorario] = useState('');
  const [alComentarios, setAlComentarios] = useState('');
  const [alEstado, setAlEstado] = useState<Inscripcion['estado']>('Confirmada');

  // Docentes state
  const [docEditId, setDocEditId] = useState<number>(0);
  const [docNombre, setDocNombre] = useState('');
  const [docApellido, setDocApellido] = useState('');
  const [docEspecialidad, setDocEspecialidad] = useState('');
  const [docInstrumento, setDocInstrumento] = useState('Guitarra');
  const [docBio, setDocBio] = useState('');
  const [docCv, setDocCv] = useState('');
  const [docEmail, setDocEmail] = useState('');
  const [docTelefono, setDocTelefono] = useState('');
  const [docFotoUrl, setDocFotoUrl] = useState('https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=600&q=80');
  const [docActivo, setDocActivo] = useState(true);

  // Talleres state
  const [talEditId, setTalEditId] = useState<number>(0);
  const [talNombre, setTalNombre] = useState('');
  const [talInstrumento, setTalInstrumento] = useState<InstrumentoTipo>('Guitarra');
  const [talDocenteId, setTalDocenteId] = useState<number>(docentes[0]?.id || 1);
  const [talNivel, setTalNivel] = useState<NivelTipo>('Inicial / Sin experiencia');
  const [talEdadMin, setTalEdadMin] = useState<number>(7);
  const [talDia, setTalDia] = useState('');
  const [talHorario, setTalHorario] = useState('');
  const [talVacTot, setTalVacTot] = useState<number>(12);
  const [talVacDisp, setTalVacDisp] = useState<number>(6);
  const [talArancel, setTalArancel] = useState<number>(15000);
  const [talModalidad, setTalModalidad] = useState<ModalidadTipo>('Presencial');
  const [talSedeId, setTalSedeId] = useState<number>(sedes[0]?.id || 1);
  const [talDesc, setTalDesc] = useState('');
  const [talReq, setTalReq] = useState('');
  const [talPrestamo, setTalPrestamo] = useState<number>(5);
  const [talDestacado, setTalDestacado] = useState<boolean>(false);
  const [talImagen, setTalImagen] = useState('https://images.unsplash.com/photo-1510915361894-db8b60106cb1?auto=format&fit=crop&w=800&q=80');

  // Eventos state
  const [evEditId, setEvEditId] = useState<number>(0);
  const [evTitulo, setEvTitulo] = useState('');
  const [evTipo, setEvTipo] = useState<Evento['tipo']>('Muestra de Alumnos');
  const [evFecha, setEvFecha] = useState('2026-11-28');
  const [evHora, setEvHora] = useState('19:00 hs');
  const [evLugar, setEvLugar] = useState('');
  const [evDesc, setEvDesc] = useState('');
  const [evRepertorio, setEvRepertorio] = useState('');
  const [evPrecio, setEvPrecio] = useState<number>(1500);
  const [evCapacidad, setEvCapacidad] = useState<number>(120);
  const [evDisponibles, setEvDisponibles] = useState<number>(80);
  const [evEstado, setEvEstado] = useState<Evento['estado']>('proximo');
  const [evImagen, setEvImagen] = useState('https://images.unsplash.com/photo-1501386761578-eac5c94b800a?auto=format&fit=crop&w=800&q=80');

  // Contenido Página state
  const [cfgForm, setCfgForm] = useState<ContenidoPagina>(contenidoPagina);
  const [sedesForm, setSedesForm] = useState<Sede[]>(sedes);
  const [resenasForm, setResenasForm] = useState<Resena[]>(resenas);
  const [faqsForm, setFaqsForm] = useState<PreguntaFrecuente[]>(preguntasFrecuentes);
  const [consultas, setConsultas] = useState(DataService.getConsultas());

  // Admins state
  const [admins, setAdmins] = useState<AdminUser[]>(DataService.getAdmins());
  const [newAdmUsuario, setNewAdmUsuario] = useState('');
  const [newAdmNombre, setNewAdmNombre] = useState('');
  const [newAdmEmail, setNewAdmEmail] = useState('');
  const [newAdmPass, setNewAdmPass] = useState('');
  const [newAdmRol, setNewAdmRol] = useState<'admin' | 'superadmin'>('admin');

  const notifyOk = (msg: string, sql?: string) => {
    setMensajeError('');
    setMensajeOk(msg);
    if (sql) onNotificarSqlEjecutado(sql);
  };

  const notifyErr = (msg: string) => {
    setMensajeOk('');
    setMensajeError(msg);
  };

  const handleLogin = (e: React.FormEvent) => {
    e.preventDefault();
    const res = DataService.autenticarAdmin(loginIdent, loginPass);
    if (!res.ok || !res.admin) {
      setLoginError(res.error || 'Error de acceso.');
      return;
    }
    setLoginError('');
    setLoginPass('');
    onLoginSuccess(res.admin);
  };

  // --- LOGIN VIEW ---
  if (!adminActual) {
    return (
      <div className="max-w-lg mx-auto px-4 py-12">
        <div className="bg-[#FFFDF5] border-4 border-[#181818] rounded-3xl p-8 shadow-[6px_6px_0px_#181818] space-y-6">
          <div className="text-center space-y-2">
            <div className="w-14 h-14 rounded-2xl bg-[#181818] text-[#F3D84F] flex items-center justify-center mx-auto border-2 border-[#181818] shadow">
              <Shield className="w-7 h-7" />
            </div>
            <span className="inline-block px-3 py-1 rounded-full bg-[#24AB58] text-[#FFFAE6] text-[11px] font-bold uppercase tracking-wider">
              Acceso Restringido
            </span>
            <h1 className="font-carter text-2xl sm:text-3xl text-[#181818]">
              Ingreso Administrador
            </h1>
            <p className="text-xs sm:text-sm text-neutral-600">
              Iniciá sesión para gestionar alumnos, docentes, talleres, eventos y editar textos e imágenes del sitio.
            </p>
          </div>

          {loginError && (
            <div className="p-3 bg-red-100 border-2 border-red-600 text-red-900 rounded-xl text-xs font-bold flex items-center gap-2">
              <AlertCircle className="w-4 h-4 shrink-0" />
              <span>{loginError}</span>
            </div>
          )}

          <form onSubmit={handleLogin} className="space-y-4">
            <div>
              <label className="block text-xs font-bold text-[#181818] mb-1">
                Usuario o Correo Electrónico *
              </label>
              <input
                type="text"
                required
                value={loginIdent}
                onChange={(e) => setLoginIdent(e.target.value)}
                placeholder="admin"
                className="w-full px-4 py-2.5 bg-[#FFFAE6] border-2 border-[#181818] rounded-xl text-sm focus:border-[#24AB58] focus:outline-none"
              />
            </div>

            <div>
              <label className="block text-xs font-bold text-[#181818] mb-1">
                Contraseña *
              </label>
              <input
                type="password"
                required
                value={loginPass}
                onChange={(e) => setLoginPass(e.target.value)}
                placeholder="••••••••"
                className="w-full px-4 py-2.5 bg-[#FFFAE6] border-2 border-[#181818] rounded-xl text-sm focus:border-[#24AB58] focus:outline-none"
              />
            </div>

            <button
              type="submit"
              className="w-full py-3 bg-[#24AB58] hover:bg-[#1e924b] text-[#FFFAE6] font-carter text-base rounded-xl border-2 border-[#181818] shadow-[3px_3px_0px_#181818] transition-transform active:translate-y-0.5"
            >
              Ingresar al Panel Administrador
            </button>
          </form>

          <div className="p-3.5 bg-[#FFFAE6] border-2 border-dashed border-[#181818] rounded-xl text-xs text-neutral-700 space-y-1">
            <p className="font-bold text-[#181818]">Credenciales iniciales en la base de datos:</p>
            <p>Usuario: <code className="bg-neutral-200 px-1.5 py-0.5 rounded font-bold">admin</code> &nbsp;|&nbsp; Contraseña: <code className="bg-neutral-200 px-1.5 py-0.5 rounded font-bold">Admin2026!</code></p>
          </div>
        </div>
      </div>
    );
  }

  // --- HANDLERS ALUMNOS ---
  const resetFormAlumno = () => {
    setAlumnoEditId(0);
    setAlNombre('');
    setAlApellido('');
    setAlEmail('');
    setAlTelefono('');
    setAlEdad(18);
    setAlGenero('NB');
    setAlTallerId(talleres[0]?.id || 1);
    setAlSedeId(sedes[0]?.id || 1);
    setAlModalidad('Presencial');
    setAlPrestamo(false);
    setAlPropio(false);
    setAlHorario('');
    setAlComentarios('');
    setAlEstado('Confirmada');
  };

  const cargarAlumnoEnFormulario = (ins: Inscripcion) => {
    setAlumnoEditId(ins.id);
    setAlNombre(ins.nombre);
    setAlApellido(ins.apellido);
    setAlEmail(ins.email);
    setAlTelefono(ins.telefono);
    setAlEdad(ins.edad);
    setAlGenero(ins.genero);
    setAlTallerId(ins.tallerId);
    setAlSedeId(ins.sedeId);
    setAlModalidad(ins.modalidad);
    setAlPrestamo(ins.necesitaPrestamoInstrumento);
    setAlPropio(ins.poseeInstrumentoPropio);
    setAlHorario(ins.horarioSeleccionado);
    setAlComentarios(ins.comentarios || '');
    setAlEstado(ins.estado);
  };

  const handleGuardarAlumno = (e: React.FormEvent) => {
    e.preventDefault();
    const tallerObj = talleres.find((t) => t.id === alTallerId);
    const horarioFinal = alHorario.trim() || (tallerObj ? `${tallerObj.diaSemana} (${tallerObj.horario})` : 'A confirmar');
    const res = DataService.guardarInscripcionAdmin({
      id: alumnoEditId > 0 ? alumnoEditId : undefined,
      tallerId: alTallerId,
      sedeId: alSedeId,
      nombre: alNombre.trim(),
      apellido: alApellido.trim(),
      email: alEmail.trim(),
      telefono: alTelefono.trim(),
      edad: Number(alEdad),
      genero: alGenero,
      modalidad: alModalidad,
      necesitaPrestamoInstrumento: alPrestamo,
      poseeInstrumentoPropio: alPropio,
      horarioSeleccionado: horarioFinal,
      comentarios: alComentarios.trim(),
      estado: alEstado,
    });
    setInscripciones(DataService.getInscripciones());
    onRefrescarTodo();
    notifyOk(
      alumnoEditId > 0
        ? `Datos de "${res.inscripcion.nombre} ${res.inscripcion.apellido}" modificados.`
        : `Alumno "${res.inscripcion.nombre} ${res.inscripcion.apellido}" cargado (${res.inscripcion.codigoInscripcion}).`,
      res.sqlQuery
    );
    resetFormAlumno();
  };

  const handleCambiarEstadoAlumno = (id: number, estado: Inscripcion['estado']) => {
    const sql = DataService.actualizarEstadoInscripcion(id, estado);
    setInscripciones(DataService.getInscripciones());
    notifyOk('Estado del alumno actualizado.', sql);
  };

  const handleEliminarAlumno = (id: number) => {
    const sql = DataService.eliminarInscripcion(id);
    setInscripciones(DataService.getInscripciones());
    onRefrescarTodo();
    notifyOk('Inscripción eliminada y vacante restaurada.', sql);
  };

  // --- HANDLERS DOCENTES ---
  const resetFormDocente = () => {
    setDocEditId(0);
    setDocNombre('');
    setDocApellido('');
    setDocEspecialidad('');
    setDocInstrumento('Guitarra');
    setDocBio('');
    setDocCv('');
    setDocEmail('');
    setDocTelefono('');
    setDocFotoUrl('https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=600&q=80');
    setDocActivo(true);
  };

  const cargarDocenteEnFormulario = (doc: Docente) => {
    setDocEditId(doc.id);
    setDocNombre(doc.nombre);
    setDocApellido(doc.apellido);
    setDocEspecialidad(doc.especialidad);
    setDocInstrumento(doc.instrumentoPrincipal);
    setDocBio(doc.biografia);
    setDocCv(doc.trayectoriaCv);
    setDocEmail(doc.email);
    setDocTelefono(doc.telefono);
    setDocFotoUrl(doc.fotoUrl);
    setDocActivo(doc.activo);
  };

  const handleGuardarDocente = (e: React.FormEvent) => {
    e.preventDefault();
    const res = DataService.guardarDocente({
      id: docEditId > 0 ? docEditId : undefined,
      nombre: docNombre.trim(),
      apellido: docApellido.trim(),
      especialidad: docEspecialidad.trim(),
      instrumentoPrincipal: docInstrumento.trim(),
      biografia: docBio.trim(),
      trayectoriaCv: docCv.trim(),
      email: docEmail.trim(),
      telefono: docTelefono.trim(),
      fotoUrl: docFotoUrl.trim(),
      activo: docActivo,
    });
    onRefrescarTodo();
    notifyOk(
      docEditId > 0
        ? `Docente "${res.docente.nombre} ${res.docente.apellido}" modificado.`
        : `Nuevo docente "${res.docente.nombre} ${res.docente.apellido}" cargado.`,
      res.sqlQuery
    );
    resetFormDocente();
  };

  const handleEliminarDocente = (id: number) => {
    const res = DataService.eliminarDocente(id);
    if (!res.ok) {
      notifyErr(res.error || 'No se pudo eliminar el docente.');
      return;
    }
    onRefrescarTodo();
    notifyOk('Docente eliminado correctamente.', res.sqlQuery);
  };

  // --- HANDLERS TALLERES ---
  const resetFormTaller = () => {
    setTalEditId(0);
    setTalNombre('');
    setTalInstrumento('Guitarra');
    setTalDocenteId(docentes[0]?.id || 1);
    setTalNivel('Inicial / Sin experiencia');
    setTalEdadMin(7);
    setTalDia('');
    setTalHorario('');
    setTalVacTot(12);
    setTalVacDisp(6);
    setTalArancel(15000);
    setTalModalidad('Presencial');
    setTalSedeId(sedes[0]?.id || 1);
    setTalDesc('');
    setTalReq('');
    setTalPrestamo(5);
    setTalDestacado(false);
    setTalImagen('https://images.unsplash.com/photo-1510915361894-db8b60106cb1?auto=format&fit=crop&w=800&q=80');
  };

  const cargarTallerEnFormulario = (tl: Taller) => {
    setTalEditId(tl.id);
    setTalNombre(tl.nombre);
    setTalInstrumento(tl.instrumento);
    setTalDocenteId(tl.docenteId);
    setTalNivel(tl.nivel);
    setTalEdadMin(tl.edadMinima);
    setTalDia(tl.diaSemana);
    setTalHorario(tl.horario);
    setTalVacTot(tl.vacantesTotales);
    setTalVacDisp(tl.vacantesDisponibles);
    setTalArancel(tl.arancelMensual);
    setTalModalidad(tl.modalidad);
    setTalSedeId(tl.sedeId);
    setTalDesc(tl.descripcion);
    setTalReq(tl.requisitos);
    setTalPrestamo(tl.instrumentosPrestamoDisponibles);
    setTalDestacado(tl.destacado);
    setTalImagen(tl.imagenUrl);
  };

  const handleGuardarTaller = (e: React.FormEvent) => {
    e.preventDefault();
    const res = DataService.guardarTaller({
      id: talEditId > 0 ? talEditId : undefined,
      nombre: talNombre.trim(),
      instrumento: talInstrumento,
      docenteId: talDocenteId,
      nivel: talNivel,
      edadMinima: Number(talEdadMin),
      diaSemana: talDia.trim(),
      horario: talHorario.trim(),
      vacantesTotales: Number(talVacTot),
      vacantesDisponibles: Number(talVacDisp),
      arancelMensual: Number(talArancel),
      modalidad: talModalidad,
      sedeId: talSedeId,
      descripcion: talDesc.trim(),
      requisitos: talReq.trim(),
      instrumentosPrestamoDisponibles: Number(talPrestamo),
      destacado: talDestacado,
      imagenUrl: talImagen.trim(),
    });
    onRefrescarTodo();
    notifyOk(
      talEditId > 0 ? `Taller "${res.taller.nombre}" actualizado.` : `Nuevo taller "${res.taller.nombre}" cargado.`,
      res.sqlQuery
    );
    resetFormTaller();
  };

  const handleEliminarTaller = (id: number) => {
    const sql = DataService.eliminarTaller(id);
    onRefrescarTodo();
    notifyOk('Taller eliminado correctamente.', sql);
  };

  // --- HANDLERS EVENTOS ---
  const resetFormEvento = () => {
    setEvEditId(0);
    setEvTitulo('');
    setEvTipo('Muestra de Alumnos');
    setEvFecha('2026-11-28');
    setEvHora('19:00 hs');
    setEvLugar('');
    setEvDesc('');
    setEvRepertorio('');
    setEvPrecio(1500);
    setEvCapacidad(120);
    setEvDisponibles(80);
    setEvEstado('proximo');
    setEvImagen('https://images.unsplash.com/photo-1501386761578-eac5c94b800a?auto=format&fit=crop&w=800&q=80');
  };

  const cargarEventoEnFormulario = (ev: Evento) => {
    setEvEditId(ev.id);
    setEvTitulo(ev.titulo);
    setEvTipo(ev.tipo);
    setEvFecha(ev.fecha);
    setEvHora(ev.hora);
    setEvLugar(ev.lugar);
    setEvDesc(ev.descripcion);
    setEvRepertorio(ev.repertorio);
    setEvPrecio(ev.precioEntrada);
    setEvCapacidad(ev.capacidadTotal);
    setEvDisponibles(ev.entradasDisponibles);
    setEvEstado(ev.estado);
    setEvImagen(ev.imagenUrl);
  };

  const handleGuardarEvento = (e: React.FormEvent) => {
    e.preventDefault();
    const res = DataService.guardarEvento({
      id: evEditId > 0 ? evEditId : undefined,
      titulo: evTitulo.trim(),
      tipo: evTipo,
      fecha: evFecha,
      hora: evHora.trim(),
      lugar: evLugar.trim(),
      descripcion: evDesc.trim(),
      repertorio: evRepertorio.trim(),
      precioEntrada: Number(evPrecio),
      capacidadTotal: Number(evCapacidad),
      entradasDisponibles: Number(evDisponibles),
      estado: evEstado,
      imagenUrl: evImagen.trim(),
    });
    onRefrescarTodo();
    notifyOk(
      evEditId > 0 ? `Evento "${res.evento.titulo}" modificado.` : `Nuevo evento "${res.evento.titulo}" cargado.`,
      res.sqlQuery
    );
    resetFormEvento();
  };

  const handleEliminarEvento = (id: number) => {
    const sql = DataService.eliminarEvento(id);
    onRefrescarTodo();
    notifyOk('Evento eliminado correctamente.', sql);
  };

  // --- HANDLERS CONTENIDO ---
  const handleGuardarContenido = (e: React.FormEvent) => {
    e.preventDefault();
    const sql = DataService.guardarContenidoPagina(cfgForm);
    onRefrescarTodo();
    notifyOk('Textos e imágenes del sitio actualizados en vivo.', sql);
  };

  const handleRestaurarContenido = () => {
    setCfgForm(INITIAL_CONTENIDO_PAGINA);
    const sql = DataService.guardarContenidoPagina(INITIAL_CONTENIDO_PAGINA);
    onRefrescarTodo();
    notifyOk('Textos e imágenes restaurados a los valores originales.', sql);
  };

  const handleGuardarSede = (sede: Sede) => {
    const sql = DataService.guardarSede(sede);
    onRefrescarTodo();
    notifyOk(`Sede "${sede.nombre}" actualizada.`, sql);
  };

  const handleGuardarResena = (resena: Resena) => {
    const sql = DataService.guardarResena(resena);
    onRefrescarTodo();
    notifyOk(`Testimonio de "${resena.autor}" actualizado.`, sql);
  };

  const handleGuardarFaq = (faq: PreguntaFrecuente) => {
    const sql = DataService.guardarFaq(faq);
    onRefrescarTodo();
    notifyOk('Pregunta frecuente actualizada.', sql);
  };

  const handleCrearAdmin = (e: React.FormEvent) => {
    e.preventDefault();
    if (newAdmPass.length < 6) {
      notifyErr('La contraseña debe tener al menos 6 caracteres.');
      return;
    }
    const res = DataService.crearAdmin({
      usuario: newAdmUsuario,
      nombreCompleto: newAdmNombre,
      email: newAdmEmail,
      password: newAdmPass,
      rol: newAdmRol,
    });
    if (!res.ok) {
      notifyErr(res.error || 'No se pudo crear el administrador.');
      return;
    }
    setAdmins(DataService.getAdmins());
    setNewAdmUsuario('');
    setNewAdmNombre('');
    setNewAdmEmail('');
    setNewAdmPass('');
    notifyOk(`Administrador "${newAdmUsuario}" registrado en la base de datos.`, res.sqlQuery);
  };

  const inscripcionesFiltradas = inscripciones.filter((ins) => {
    if (filtroTaller > 0 && ins.tallerId !== filtroTaller) return false;
    if (filtroEstado && ins.estado !== filtroEstado) return false;
    if (buscarAlumno.trim()) {
      const q = buscarAlumno.trim().toLowerCase();
      const match =
        ins.nombre.toLowerCase().includes(q) ||
        ins.apellido.toLowerCase().includes(q) ||
        ins.email.toLowerCase().includes(q) ||
        ins.codigoInscripcion.toLowerCase().includes(q);
      if (!match) return false;
    }
    return true;
  });

  const inputClass =
    'w-full px-3 py-2 bg-[#FFFDF5] border-2 border-[#181818] rounded-xl text-sm font-sans focus:border-[#24AB58] focus:outline-none';

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
      {/* TOP BAR ADMIN */}
      <div className="bg-[#181818] text-[#FFFAE6] border-4 border-[#F3D84F] rounded-3xl p-6 shadow-[6px_6px_0px_#24AB58] flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
          <span className="inline-block px-2.5 py-0.5 rounded bg-[#24AB58] text-[#FFFAE6] text-[11px] font-bold uppercase tracking-wider mb-1">
            Panel de Control Escolar
          </span>
          <h1 className="font-carter text-2xl sm:text-3xl text-[#F3D84F]">
            Administración — A Todo Ritmo
          </h1>
          <p className="text-xs text-neutral-300 mt-1">
            Sesión iniciada como <strong>{adminActual.nombreCompleto}</strong> (@{adminActual.usuario} • {adminActual.rol})
          </p>
        </div>
        <div className="flex flex-wrap items-center gap-2.5">
          <button
            onClick={() => onCambiarSeccion('home')}
            className="px-4 py-2 rounded-xl bg-[#F3D84F] text-[#181818] border-2 border-[#181818] font-bold text-xs flex items-center gap-1.5 hover:bg-[#e6c83b]"
          >
            <Eye className="w-4 h-4" />
            <span>Ver Sitio Público</span>
          </button>
          <button
            onClick={onLogout}
            className="px-4 py-2 rounded-xl bg-red-600 text-white border-2 border-[#FFFAE6] font-bold text-xs flex items-center gap-1.5 hover:bg-red-700"
          >
            <LogOut className="w-4 h-4" />
            <span>Cerrar Sesión</span>
          </button>
        </div>
      </div>

      {/* MÉTRICAS */}
      <div className="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <div className="bg-[#FFFDF5] border-2 border-[#181818] rounded-2xl p-4 shadow-[3px_3px_0px_#181818]">
          <span className="text-[11px] font-bold uppercase text-neutral-600 block">Alumnos Inscriptos</span>
          <strong className="font-carter text-2xl text-[#24AB58]">{inscripciones.length}</strong>
        </div>
        <div className="bg-[#FFFDF5] border-2 border-[#181818] rounded-2xl p-4 shadow-[3px_3px_0px_#181818]">
          <span className="text-[11px] font-bold uppercase text-neutral-600 block">Docentes</span>
          <strong className="font-carter text-2xl text-[#24AB58]">{docentes.length}</strong>
        </div>
        <div className="bg-[#FFFDF5] border-2 border-[#181818] rounded-2xl p-4 shadow-[3px_3px_0px_#181818]">
          <span className="text-[11px] font-bold uppercase text-neutral-600 block">Talleres</span>
          <strong className="font-carter text-2xl text-[#24AB58]">{talleres.length}</strong>
        </div>
        <div className="bg-[#FFFDF5] border-2 border-[#181818] rounded-2xl p-4 shadow-[3px_3px_0px_#181818]">
          <span className="text-[11px] font-bold uppercase text-neutral-600 block">Eventos</span>
          <strong className="font-carter text-2xl text-[#24AB58]">{eventos.length}</strong>
        </div>
        <div className="bg-[#FFFDF5] border-2 border-[#181818] rounded-2xl p-4 shadow-[3px_3px_0px_#181818] col-span-2 sm:col-span-1">
          <span className="text-[11px] font-bold uppercase text-neutral-600 block">Consultas Web</span>
          <strong className="font-carter text-2xl text-[#24AB58]">{consultas.length}</strong>
        </div>
      </div>

      {/* PESTAÑAS */}
      <div className="flex flex-wrap gap-2">
        {(
          [
            { id: 'alumnos', label: 'Alumnos', icon: <GraduationCap className="w-4 h-4" /> },
            { id: 'docentes', label: 'Docentes', icon: <Users className="w-4 h-4" /> },
            { id: 'talleres', label: 'Talleres', icon: <BookOpen className="w-4 h-4" /> },
            { id: 'eventos', label: 'Eventos', icon: <Calendar className="w-4 h-4" /> },
            { id: 'contenido', label: 'Textos e Imágenes', icon: <FileEdit className="w-4 h-4" /> },
            { id: 'admins', label: 'Usuarios Admin', icon: <Shield className="w-4 h-4" /> },
          ] as { id: AdminTab; label: string; icon: React.ReactNode }[]
        ).map((item) => (
          <button
            key={item.id}
            onClick={() => {
              setTab(item.id);
              setMensajeOk('');
              setMensajeError('');
            }}
            className={`px-4 py-2.5 rounded-xl border-2 border-[#181818] font-bold text-xs sm:text-sm flex items-center gap-2 shadow-[3px_3px_0px_#181818] transition-colors ${
              tab === item.id
                ? 'bg-[#24AB58] text-[#FFFAE6]'
                : 'bg-white text-[#181818] hover:bg-[#F3D84F]/40'
            }`}
          >
            {item.icon}
            <span>{item.label}</span>
          </button>
        ))}
      </div>

      {/* ALERTAS */}
      {mensajeOk && (
        <div className="p-3.5 rounded-2xl bg-emerald-100 border-2 border-[#24AB58] text-emerald-950 font-bold text-sm flex items-center gap-2">
          <CheckCircle2 className="w-5 h-5 text-[#24AB58] shrink-0" />
          <span>{mensajeOk}</span>
        </div>
      )}
      {mensajeError && (
        <div className="p-3.5 rounded-2xl bg-red-100 border-2 border-red-600 text-red-950 font-bold text-sm flex items-center gap-2">
          <AlertCircle className="w-5 h-5 text-red-600 shrink-0" />
          <span>{mensajeError}</span>
        </div>
      )}

      {/* =================================================================
          TAB 1: ALUMNOS (CARGAR, MODIFICAR, VER, ELIMINAR)
          ================================================================= */}
      {tab === 'alumnos' && (
        <div className="space-y-6">
          <section className="bg-white border-2 border-[#181818] rounded-3xl p-6 shadow-[5px_5px_0px_#181818] space-y-4">
            <h2 className="font-carter text-xl text-[#181818]">
              {alumnoEditId > 0 ? `Modificar Alumno #${alumnoEditId}` : 'Cargar Nuevo Alumno Inscripto'}
            </h2>
            <form onSubmit={handleGuardarAlumno} className="space-y-4">
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label className="block text-xs font-bold mb-1">Nombre *</label>
                  <input type="text" required className={inputClass} value={alNombre} onChange={(e) => setAlNombre(e.target.value)} />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Apellido *</label>
                  <input type="text" required className={inputClass} value={alApellido} onChange={(e) => setAlApellido(e.target.value)} />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Email *</label>
                  <input type="email" required className={inputClass} value={alEmail} onChange={(e) => setAlEmail(e.target.value)} />
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label className="block text-xs font-bold mb-1">Teléfono *</label>
                  <input type="text" required className={inputClass} value={alTelefono} onChange={(e) => setAlTelefono(e.target.value)} />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Edad *</label>
                  <input type="number" min={5} max={110} required className={inputClass} value={alEdad} onChange={(e) => setAlEdad(Number(e.target.value))} />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Género</label>
                  <select className={inputClass} value={alGenero} onChange={(e) => setAlGenero(e.target.value as Inscripcion['genero'])}>
                    <option value="Fem">Fem</option>
                    <option value="Masc">Masc</option>
                    <option value="NB">NB</option>
                    <option value="Otro">Otro</option>
                    <option value="Prefiero no decir">Prefiero no decir</option>
                  </select>
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label className="block text-xs font-bold mb-1">Taller *</label>
                  <select className={inputClass} value={alTallerId} onChange={(e) => setAlTallerId(Number(e.target.value))}>
                    {talleres.map((t) => (
                      <option key={t.id} value={t.id}>
                        {t.nombre} ({t.diaSemana})
                      </option>
                    ))}
                  </select>
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Sede *</label>
                  <select className={inputClass} value={alSedeId} onChange={(e) => setAlSedeId(Number(e.target.value))}>
                    {sedes.map((s) => (
                      <option key={s.id} value={s.id}>
                        {s.nombre}
                      </option>
                    ))}
                  </select>
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Estado *</label>
                  <select className={inputClass} value={alEstado} onChange={(e) => setAlEstado(e.target.value as Inscripcion['estado'])}>
                    <option value="Confirmada">Confirmada</option>
                    <option value="Pendiente de Confirmación">Pendiente de Confirmación</option>
                    <option value="En Lista de Espera">En Lista de Espera</option>
                  </select>
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                <div>
                  <label className="block text-xs font-bold mb-1">Modalidad</label>
                  <select className={inputClass} value={alModalidad} onChange={(e) => setAlModalidad(e.target.value as ModalidadTipo)}>
                    {MODALIDADES_LISTA.map((m) => (
                      <option key={m} value={m}>{m}</option>
                    ))}
                  </select>
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Horario Seleccionado (opcional)</label>
                  <input type="text" className={inputClass} value={alHorario} onChange={(e) => setAlHorario(e.target.value)} placeholder="Se autocompleta si queda vacío" />
                </div>
                <div className="space-y-1.5 pt-2 text-xs font-bold">
                  <label className="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" checked={alPrestamo} onChange={(e) => setAlPrestamo(e.target.checked)} />
                    <span>Solicita préstamo solidario de instrumento</span>
                  </label>
                  <label className="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" checked={alPropio} onChange={(e) => setAlPropio(e.target.checked)} />
                    <span>Posee instrumento propio</span>
                  </label>
                </div>
              </div>

              <div>
                <label className="block text-xs font-bold mb-1">Comentarios / Observaciones</label>
                <input type="text" className={inputClass} value={alComentarios} onChange={(e) => setAlComentarios(e.target.value)} />
              </div>

              <div className="flex gap-2">
                <button type="submit" className="px-5 py-2.5 bg-[#24AB58] text-white font-bold text-sm rounded-xl border-2 border-[#181818] shadow-[3px_3px_0px_#181818]">
                  {alumnoEditId > 0 ? 'Guardar Cambios del Alumno' : 'Cargar Alumno'}
                </button>
                {alumnoEditId > 0 && (
                  <button type="button" onClick={resetFormAlumno} className="px-4 py-2.5 bg-[#F3D84F] text-[#181818] font-bold text-sm rounded-xl border-2 border-[#181818]">
                    Cancelar Edición
                  </button>
                )}
              </div>
            </form>
          </section>

          <section className="bg-white border-2 border-[#181818] rounded-3xl p-6 shadow-[5px_5px_0px_#181818] space-y-4">
            <h2 className="font-carter text-xl text-[#181818]">Listado de Alumnos Inscriptos</h2>
            <div className="flex flex-wrap gap-3">
              <input
                type="text"
                placeholder="Buscar alumno, email o código..."
                value={buscarAlumno}
                onChange={(e) => setBuscarAlumno(e.target.value)}
                className="px-3 py-2 bg-[#FFFDF5] border-2 border-[#181818] rounded-xl text-sm flex-1 min-w-[200px]"
              />
              <select
                value={filtroTaller}
                onChange={(e) => setFiltroTaller(Number(e.target.value))}
                className="px-3 py-2 bg-[#FFFDF5] border-2 border-[#181818] rounded-xl text-sm"
              >
                <option value={0}>-- Todos los talleres --</option>
                {talleres.map((t) => (
                  <option key={t.id} value={t.id}>{t.nombre}</option>
                ))}
              </select>
              <select
                value={filtroEstado}
                onChange={(e) => setFiltroEstado(e.target.value)}
                className="px-3 py-2 bg-[#FFFDF5] border-2 border-[#181818] rounded-xl text-sm"
              >
                <option value="">-- Todos los estados --</option>
                <option value="Confirmada">Confirmada</option>
                <option value="Pendiente de Confirmación">Pendiente de Confirmación</option>
                <option value="En Lista de Espera">En Lista de Espera</option>
              </select>
            </div>

            <div className="overflow-x-auto">
              <table className="w-full text-left text-xs sm:text-sm border-collapse">
                <thead>
                  <tr className="bg-[#181818] text-[#F3D84F]">
                    <th className="p-3">Código</th>
                    <th className="p-3">Alumno / Contacto</th>
                    <th className="p-3">Edad</th>
                    <th className="p-3">Taller & Sede</th>
                    <th className="p-3">Estado</th>
                    <th className="p-3">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  {inscripcionesFiltradas.length === 0 ? (
                    <tr>
                      <td colSpan={6} className="p-6 text-center text-neutral-500">
                        No hay alumnos inscriptos todavía. Cargá uno arriba o completá la ficha pública de inscripción.
                      </td>
                    </tr>
                  ) : (
                    inscripcionesFiltradas.map((ins) => {
                      const tObj = talleres.find((t) => t.id === ins.tallerId);
                      const sObj = sedes.find((s) => s.id === ins.sedeId);
                      return (
                        <tr key={ins.id} className="border-b border-neutral-200 hover:bg-[#FFFDF5]">
                          <td className="p-3 font-bold">
                            {ins.codigoInscripcion}
                            <div className="text-[11px] font-normal text-neutral-500">
                              {new Date(ins.fechaInscripcion).toLocaleDateString('es-AR')}
                            </div>
                          </td>
                          <td className="p-3">
                            <strong>{ins.nombre} {ins.apellido}</strong> ({ins.genero})
                            <div className="text-xs text-neutral-600">{ins.email} • {ins.telefono}</div>
                            {ins.necesitaPrestamoInstrumento && (
                              <span className="inline-block mt-1 px-2 py-0.5 rounded-full bg-amber-100 border border-[#181818] text-[10px] font-bold">
                                Solicita préstamo
                              </span>
                            )}
                          </td>
                          <td className="p-3">{ins.edad} años</td>
                          <td className="p-3">
                            <strong>{tObj?.nombre || `Taller #${ins.tallerId}`}</strong>
                            <div className="text-xs text-neutral-600">{sObj?.nombre || 'Sede Central'}</div>
                          </td>
                          <td className="p-3">
                            <select
                              value={ins.estado}
                              onChange={(e) => handleCambiarEstadoAlumno(ins.id, e.target.value as Inscripcion['estado'])}
                              className="px-2 py-1 bg-[#FFFAE6] border border-[#181818] rounded-lg text-xs font-semibold"
                            >
                              <option value="Confirmada">Confirmada</option>
                              <option value="Pendiente de Confirmación">Pendiente de Confirmación</option>
                              <option value="En Lista de Espera">En Lista de Espera</option>
                            </select>
                          </td>
                          <td className="p-3">
                            <div className="flex gap-1.5">
                              <button
                                onClick={() => cargarAlumnoEnFormulario(ins)}
                                className="px-2.5 py-1 bg-[#F3D84F] text-[#181818] border border-[#181818] rounded-lg text-xs font-bold flex items-center gap-1"
                              >
                                <Edit3 className="w-3.5 h-3.5" />
                                <span>Editar</span>
                              </button>
                              <button
                                onClick={() => handleEliminarAlumno(ins.id)}
                                className="px-2.5 py-1 bg-red-100 text-red-800 border border-red-700 rounded-lg text-xs font-bold flex items-center gap-1"
                              >
                                <Trash2 className="w-3.5 h-3.5" />
                              </button>
                            </div>
                          </td>
                        </tr>
                      );
                    })
                  )}
                </tbody>
              </table>
            </div>
          </section>
        </div>
      )}

      {/* =================================================================
          TAB 2: DOCENTES (CARGAR, MODIFICAR, IMAGEN, ELIMINAR)
          ================================================================= */}
      {tab === 'docentes' && (
        <div className="space-y-6">
          <section className="bg-white border-2 border-[#181818] rounded-3xl p-6 shadow-[5px_5px_0px_#181818] space-y-4">
            <h2 className="font-carter text-xl text-[#181818]">
              {docEditId > 0 ? `Modificar Docente #${docEditId}` : 'Cargar Nuevo Docente'}
            </h2>
            <form onSubmit={handleGuardarDocente} className="space-y-4">
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label className="block text-xs font-bold mb-1">Nombre *</label>
                  <input type="text" required className={inputClass} value={docNombre} onChange={(e) => setDocNombre(e.target.value)} />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Apellido *</label>
                  <input type="text" required className={inputClass} value={docApellido} onChange={(e) => setDocApellido(e.target.value)} />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Instrumento Principal *</label>
                  <input type="text" required className={inputClass} value={docInstrumento} onChange={(e) => setDocInstrumento(e.target.value)} />
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label className="block text-xs font-bold mb-1">Especialidad *</label>
                  <input type="text" required className={inputClass} value={docEspecialidad} onChange={(e) => setDocEspecialidad(e.target.value)} />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Email *</label>
                  <input type="email" required className={inputClass} value={docEmail} onChange={(e) => setDocEmail(e.target.value)} />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Teléfono</label>
                  <input type="text" className={inputClass} value={docTelefono} onChange={(e) => setDocTelefono(e.target.value)} />
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-bold mb-1">Biografía *</label>
                  <textarea rows={3} required className={inputClass} value={docBio} onChange={(e) => setDocBio(e.target.value)} />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Trayectoria Pedagógica / CV *</label>
                  <textarea rows={3} required className={inputClass} value={docCv} onChange={(e) => setDocCv(e.target.value)} />
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-3 gap-4 items-center bg-[#FFFAE6] p-4 rounded-2xl border border-[#181818]">
                <div>
                  <label className="block text-xs font-bold mb-1">URL de Imagen</label>
                  <input type="text" className={inputClass} value={docFotoUrl} onChange={(e) => setDocFotoUrl(e.target.value)} />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">O subir foto desde tu equipo</label>
                  <label className="flex items-center justify-center gap-2 px-3 py-2 bg-[#F3D84F] border-2 border-[#181818] rounded-xl text-xs font-bold cursor-pointer">
                    <Upload className="w-4 h-4" />
                    <span>Elegir Imagen</span>
                    <input
                      type="file"
                      accept="image/*"
                      className="hidden"
                      onChange={(e) => {
                        const file = e.target.files?.[0];
                        if (file) leerArchivoComoDataUrl(file, setDocFotoUrl);
                      }}
                    />
                  </label>
                </div>
                <div className="flex items-center gap-4">
                  {docFotoUrl && (
                    <img src={docFotoUrl} alt="Preview" className="w-16 h-16 object-cover rounded-xl border-2 border-[#181818]" referrerPolicy="no-referrer" />
                  )}
                  <label className="flex items-center gap-2 text-xs font-bold cursor-pointer">
                    <input type="checkbox" checked={docActivo} onChange={(e) => setDocActivo(e.target.checked)} />
                    <span>Docente activo</span>
                  </label>
                </div>
              </div>

              <div className="flex gap-2">
                <button type="submit" className="px-5 py-2.5 bg-[#24AB58] text-white font-bold text-sm rounded-xl border-2 border-[#181818] shadow-[3px_3px_0px_#181818]">
                  {docEditId > 0 ? 'Guardar Cambios del Docente' : 'Cargar Docente'}
                </button>
                {docEditId > 0 && (
                  <button type="button" onClick={resetFormDocente} className="px-4 py-2.5 bg-[#F3D84F] text-[#181818] font-bold text-sm rounded-xl border-2 border-[#181818]">
                    Cancelar
                  </button>
                )}
              </div>
            </form>
          </section>

          <section className="bg-white border-2 border-[#181818] rounded-3xl p-6 shadow-[5px_5px_0px_#181818] overflow-x-auto">
            <h2 className="font-carter text-xl text-[#181818] mb-4">Listado de Docentes</h2>
            <table className="w-full text-left text-xs sm:text-sm border-collapse">
              <thead>
                <tr className="bg-[#181818] text-[#F3D84F]">
                  <th className="p-3">Foto</th>
                  <th className="p-3">Docente</th>
                  <th className="p-3">Instrumento / Especialidad</th>
                  <th className="p-3">Estado</th>
                  <th className="p-3">Acciones</th>
                </tr>
              </thead>
              <tbody>
                {docentes.map((doc) => (
                  <tr key={doc.id} className="border-b border-neutral-200 hover:bg-[#FFFDF5]">
                    <td className="p-3">
                      <img src={doc.fotoUrl} alt={doc.nombre} className="w-12 h-12 rounded-xl object-cover border border-[#181818]" referrerPolicy="no-referrer" />
                    </td>
                    <td className="p-3">
                      <strong>{doc.nombre} {doc.apellido}</strong>
                      <div className="text-xs text-neutral-600">{doc.email}</div>
                    </td>
                    <td className="p-3">
                      <strong>{doc.instrumentoPrincipal}</strong>
                      <div className="text-xs text-neutral-600">{doc.especialidad}</div>
                    </td>
                    <td className="p-3">
                      <span className={`px-2 py-0.5 rounded-full text-xs font-bold border border-[#181818] ${doc.activo ? 'bg-emerald-100 text-emerald-900' : 'bg-red-100 text-red-900'}`}>
                        {doc.activo ? 'Activo' : 'Inactivo'}
                      </span>
                    </td>
                    <td className="p-3">
                      <div className="flex gap-1.5">
                        <button onClick={() => cargarDocenteEnFormulario(doc)} className="px-2.5 py-1 bg-[#F3D84F] border border-[#181818] rounded-lg text-xs font-bold">
                          Editar
                        </button>
                        <button onClick={() => handleEliminarDocente(doc.id)} className="px-2.5 py-1 bg-red-100 text-red-800 border border-red-700 rounded-lg text-xs font-bold">
                          Eliminar
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </section>
        </div>
      )}

      {/* =================================================================
          TAB 3: TALLERES (CARGAR, MODIFICAR, IMAGEN, ELIMINAR)
          ================================================================= */}
      {tab === 'talleres' && (
        <div className="space-y-6">
          <section className="bg-white border-2 border-[#181818] rounded-3xl p-6 shadow-[5px_5px_0px_#181818] space-y-4">
            <h2 className="font-carter text-xl text-[#181818]">
              {talEditId > 0 ? `Modificar Taller #${talEditId}` : 'Cargar Nuevo Taller'}
            </h2>
            <form onSubmit={handleGuardarTaller} className="space-y-4">
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label className="block text-xs font-bold mb-1">Nombre del Taller *</label>
                  <input type="text" required className={inputClass} value={talNombre} onChange={(e) => setTalNombre(e.target.value)} />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Instrumento *</label>
                  <select className={inputClass} value={talInstrumento} onChange={(e) => setTalInstrumento(e.target.value as InstrumentoTipo)}>
                    {INSTRUMENTOS_LISTA.map((inst) => (
                      <option key={inst} value={inst}>{inst}</option>
                    ))}
                  </select>
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Docente a Cargo *</label>
                  <select className={inputClass} value={talDocenteId} onChange={(e) => setTalDocenteId(Number(e.target.value))}>
                    {docentes.map((d) => (
                      <option key={d.id} value={d.id}>{d.nombre} {d.apellido}</option>
                    ))}
                  </select>
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label className="block text-xs font-bold mb-1">Nivel</label>
                  <select className={inputClass} value={talNivel} onChange={(e) => setTalNivel(e.target.value as NivelTipo)}>
                    {NIVELES_LISTA.map((nv) => (
                      <option key={nv} value={nv}>{nv}</option>
                    ))}
                  </select>
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Modalidad</label>
                  <select className={inputClass} value={talModalidad} onChange={(e) => setTalModalidad(e.target.value as ModalidadTipo)}>
                    {MODALIDADES_LISTA.map((md) => (
                      <option key={md} value={md}>{md}</option>
                    ))}
                  </select>
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Sede</label>
                  <select className={inputClass} value={talSedeId} onChange={(e) => setTalSedeId(Number(e.target.value))}>
                    {sedes.map((s) => (
                      <option key={s.id} value={s.id}>{s.nombre}</option>
                    ))}
                  </select>
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label className="block text-xs font-bold mb-1">Días de Cursada *</label>
                  <input type="text" required className={inputClass} value={talDia} onChange={(e) => setTalDia(e.target.value)} placeholder="Ej: Martes y Jueves" />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Horario *</label>
                  <input type="text" required className={inputClass} value={talHorario} onChange={(e) => setTalHorario(e.target.value)} placeholder="Ej: 18:00 a 19:30 hs" />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Arancel Mensual ($) *</label>
                  <input type="number" required className={inputClass} value={talArancel} onChange={(e) => setTalArancel(Number(e.target.value))} />
                </div>
              </div>

              <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                  <label className="block text-xs font-bold mb-1">Vacantes Totales</label>
                  <input type="number" className={inputClass} value={talVacTot} onChange={(e) => setTalVacTot(Number(e.target.value))} />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Vacantes Libres</label>
                  <input type="number" className={inputClass} value={talVacDisp} onChange={(e) => setTalVacDisp(Number(e.target.value))} />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Edad Mínima</label>
                  <input type="number" className={inputClass} value={talEdadMin} onChange={(e) => setTalEdadMin(Number(e.target.value))} />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Inst. Préstamo</label>
                  <input type="number" className={inputClass} value={talPrestamo} onChange={(e) => setTalPrestamo(Number(e.target.value))} />
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-bold mb-1">Descripción *</label>
                  <textarea rows={2} required className={inputClass} value={talDesc} onChange={(e) => setTalDesc(e.target.value)} />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Requisitos</label>
                  <textarea rows={2} className={inputClass} value={talReq} onChange={(e) => setTalReq(e.target.value)} />
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-3 gap-4 items-center bg-[#FFFAE6] p-4 rounded-2xl border border-[#181818]">
                <div>
                  <label className="block text-xs font-bold mb-1">URL de Imagen</label>
                  <input type="text" className={inputClass} value={talImagen} onChange={(e) => setTalImagen(e.target.value)} />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">O subir imagen desde tu equipo</label>
                  <label className="flex items-center justify-center gap-2 px-3 py-2 bg-[#F3D84F] border-2 border-[#181818] rounded-xl text-xs font-bold cursor-pointer">
                    <Upload className="w-4 h-4" />
                    <span>Elegir Imagen</span>
                    <input
                      type="file"
                      accept="image/*"
                      className="hidden"
                      onChange={(e) => {
                        const file = e.target.files?.[0];
                        if (file) leerArchivoComoDataUrl(file, setTalImagen);
                      }}
                    />
                  </label>
                </div>
                <div className="flex items-center gap-4">
                  {talImagen && (
                    <img src={talImagen} alt="Preview" className="w-16 h-16 object-cover rounded-xl border-2 border-[#181818]" referrerPolicy="no-referrer" />
                  )}
                  <label className="flex items-center gap-2 text-xs font-bold cursor-pointer">
                    <input type="checkbox" checked={talDestacado} onChange={(e) => setTalDestacado(e.target.checked)} />
                    <span>Destacado en Home</span>
                  </label>
                </div>
              </div>

              <div className="flex gap-2">
                <button type="submit" className="px-5 py-2.5 bg-[#24AB58] text-white font-bold text-sm rounded-xl border-2 border-[#181818] shadow-[3px_3px_0px_#181818]">
                  {talEditId > 0 ? 'Guardar Cambios del Taller' : 'Cargar Taller'}
                </button>
                {talEditId > 0 && (
                  <button type="button" onClick={resetFormTaller} className="px-4 py-2.5 bg-[#F3D84F] text-[#181818] font-bold text-sm rounded-xl border-2 border-[#181818]">
                    Cancelar
                  </button>
                )}
              </div>
            </form>
          </section>

          <section className="bg-white border-2 border-[#181818] rounded-3xl p-6 shadow-[5px_5px_0px_#181818] overflow-x-auto">
            <h2 className="font-carter text-xl text-[#181818] mb-4">Listado de Talleres</h2>
            <table className="w-full text-left text-xs sm:text-sm border-collapse">
              <thead>
                <tr className="bg-[#181818] text-[#F3D84F]">
                  <th className="p-3">Img</th>
                  <th className="p-3">Taller</th>
                  <th className="p-3">Horario</th>
                  <th className="p-3">Vacantes</th>
                  <th className="p-3">Arancel</th>
                  <th className="p-3">Acciones</th>
                </tr>
              </thead>
              <tbody>
                {talleres.map((tl) => (
                  <tr key={tl.id} className="border-b border-neutral-200 hover:bg-[#FFFDF5]">
                    <td className="p-3">
                      <img src={tl.imagenUrl} alt={tl.nombre} className="w-12 h-12 rounded-xl object-cover border border-[#181818]" referrerPolicy="no-referrer" />
                    </td>
                    <td className="p-3">
                      <strong>{tl.nombre}</strong>
                      <div className="text-xs text-neutral-600">{tl.instrumento} • {tl.nivel}</div>
                    </td>
                    <td className="p-3">
                      {tl.diaSemana}
                      <div className="text-xs text-neutral-600">{tl.horario}</div>
                    </td>
                    <td className="p-3">{tl.vacantesDisponibles} / {tl.vacantesTotales}</td>
                    <td className="p-3 font-bold">${tl.arancelMensual.toLocaleString('es-AR')}</td>
                    <td className="p-3">
                      <div className="flex gap-1.5">
                        <button onClick={() => cargarTallerEnFormulario(tl)} className="px-2.5 py-1 bg-[#F3D84F] border border-[#181818] rounded-lg text-xs font-bold">
                          Editar
                        </button>
                        <button onClick={() => handleEliminarTaller(tl.id)} className="px-2.5 py-1 bg-red-100 text-red-800 border border-red-700 rounded-lg text-xs font-bold">
                          Eliminar
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </section>
        </div>
      )}

      {/* =================================================================
          TAB 4: EVENTOS (CARGAR, MODIFICAR, IMAGEN, ELIMINAR)
          ================================================================= */}
      {tab === 'eventos' && (
        <div className="space-y-6">
          <section className="bg-white border-2 border-[#181818] rounded-3xl p-6 shadow-[5px_5px_0px_#181818] space-y-4">
            <h2 className="font-carter text-xl text-[#181818]">
              {evEditId > 0 ? `Modificar Evento #${evEditId}` : 'Cargar Nuevo Evento'}
            </h2>
            <form onSubmit={handleGuardarEvento} className="space-y-4">
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label className="block text-xs font-bold mb-1">Título del Evento *</label>
                  <input type="text" required className={inputClass} value={evTitulo} onChange={(e) => setEvTitulo(e.target.value)} />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Tipo *</label>
                  <select className={inputClass} value={evTipo} onChange={(e) => setEvTipo(e.target.value as Evento['tipo'])}>
                    <option value="Muestra de Alumnos">Muestra de Alumnos</option>
                    <option value="Concierto Abierto">Concierto Abierto</option>
                    <option value="Masterclass">Masterclass</option>
                    <option value="Jam Session">Jam Session</option>
                  </select>
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Estado</label>
                  <select className={inputClass} value={evEstado} onChange={(e) => setEvEstado(e.target.value as Evento['estado'])}>
                    <option value="proximo">Próximo</option>
                    <option value="previo">Previo / Archivo</option>
                    <option value="agotado">Agotado</option>
                  </select>
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label className="block text-xs font-bold mb-1">Fecha *</label>
                  <input type="date" required className={inputClass} value={evFecha} onChange={(e) => setEvFecha(e.target.value)} />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Hora *</label>
                  <input type="text" required className={inputClass} value={evHora} onChange={(e) => setEvHora(e.target.value)} />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Lugar *</label>
                  <input type="text" required className={inputClass} value={evLugar} onChange={(e) => setEvLugar(e.target.value)} />
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label className="block text-xs font-bold mb-1">Precio Entrada ($)</label>
                  <input type="number" className={inputClass} value={evPrecio} onChange={(e) => setEvPrecio(Number(e.target.value))} />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Capacidad Total</label>
                  <input type="number" className={inputClass} value={evCapacidad} onChange={(e) => setEvCapacidad(Number(e.target.value))} />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Entradas Disponibles</label>
                  <input type="number" className={inputClass} value={evDisponibles} onChange={(e) => setEvDisponibles(Number(e.target.value))} />
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-bold mb-1">Descripción *</label>
                  <textarea rows={2} required className={inputClass} value={evDesc} onChange={(e) => setEvDesc(e.target.value)} />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Repertorio *</label>
                  <textarea rows={2} required className={inputClass} value={evRepertorio} onChange={(e) => setEvRepertorio(e.target.value)} />
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-3 gap-4 items-center bg-[#FFFAE6] p-4 rounded-2xl border border-[#181818]">
                <div>
                  <label className="block text-xs font-bold mb-1">URL de Imagen</label>
                  <input type="text" className={inputClass} value={evImagen} onChange={(e) => setEvImagen(e.target.value)} />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">O subir imagen desde tu equipo</label>
                  <label className="flex items-center justify-center gap-2 px-3 py-2 bg-[#F3D84F] border-2 border-[#181818] rounded-xl text-xs font-bold cursor-pointer">
                    <Upload className="w-4 h-4" />
                    <span>Elegir Imagen</span>
                    <input
                      type="file"
                      accept="image/*"
                      className="hidden"
                      onChange={(e) => {
                        const file = e.target.files?.[0];
                        if (file) leerArchivoComoDataUrl(file, setEvImagen);
                      }}
                    />
                  </label>
                </div>
                <div>
                  {evImagen && (
                    <img src={evImagen} alt="Preview" className="w-24 h-16 object-cover rounded-xl border-2 border-[#181818]" referrerPolicy="no-referrer" />
                  )}
                </div>
              </div>

              <div className="flex gap-2">
                <button type="submit" className="px-5 py-2.5 bg-[#24AB58] text-white font-bold text-sm rounded-xl border-2 border-[#181818] shadow-[3px_3px_0px_#181818]">
                  {evEditId > 0 ? 'Guardar Cambios del Evento' : 'Cargar Evento'}
                </button>
                {evEditId > 0 && (
                  <button type="button" onClick={resetFormEvento} className="px-4 py-2.5 bg-[#F3D84F] text-[#181818] font-bold text-sm rounded-xl border-2 border-[#181818]">
                    Cancelar
                  </button>
                )}
              </div>
            </form>
          </section>

          <section className="bg-white border-2 border-[#181818] rounded-3xl p-6 shadow-[5px_5px_0px_#181818] overflow-x-auto">
            <h2 className="font-carter text-xl text-[#181818] mb-4">Agenda de Eventos</h2>
            <table className="w-full text-left text-xs sm:text-sm border-collapse">
              <thead>
                <tr className="bg-[#181818] text-[#F3D84F]">
                  <th className="p-3">Img</th>
                  <th className="p-3">Fecha</th>
                  <th className="p-3">Evento</th>
                  <th className="p-3">Entradas</th>
                  <th className="p-3">Acciones</th>
                </tr>
              </thead>
              <tbody>
                {eventos.map((ev) => (
                  <tr key={ev.id} className="border-b border-neutral-200 hover:bg-[#FFFDF5]">
                    <td className="p-3">
                      <img src={ev.imagenUrl} alt={ev.titulo} className="w-12 h-12 rounded-xl object-cover border border-[#181818]" referrerPolicy="no-referrer" />
                    </td>
                    <td className="p-3">
                      <strong>{ev.fecha}</strong>
                      <div className="text-xs text-neutral-600">{ev.hora}</div>
                    </td>
                    <td className="p-3">
                      <strong>{ev.titulo}</strong>
                      <div className="text-xs text-neutral-600">{ev.tipo} • {ev.lugar}</div>
                    </td>
                    <td className="p-3">{ev.entradasDisponibles} disp. ({ev.precioEntrada === 0 ? 'Gratis' : `$${ev.precioEntrada}`})</td>
                    <td className="p-3">
                      <div className="flex gap-1.5">
                        <button onClick={() => cargarEventoEnFormulario(ev)} className="px-2.5 py-1 bg-[#F3D84F] border border-[#181818] rounded-lg text-xs font-bold">
                          Editar
                        </button>
                        <button onClick={() => handleEliminarEvento(ev.id)} className="px-2.5 py-1 bg-red-100 text-red-800 border border-red-700 rounded-lg text-xs font-bold">
                          Eliminar
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </section>
        </div>
      )}

      {/* =================================================================
          TAB 5: TEXTOS E IMÁGENES DE LA PÁGINA
          ================================================================= */}
      {tab === 'contenido' && (
        <div className="space-y-6">
          <section className="bg-white border-2 border-[#181818] rounded-3xl p-6 shadow-[5px_5px_0px_#181818] space-y-5">
            <div className="flex flex-wrap justify-between items-center gap-2">
              <h2 className="font-carter text-xl text-[#181818]">
                Modificar Textos e Imágenes del Sitio
              </h2>
              <button
                type="button"
                onClick={handleRestaurarContenido}
                className="px-3 py-1.5 bg-neutral-200 border border-[#181818] rounded-lg text-xs font-bold"
              >
                Restaurar Textos Originales
              </button>
            </div>

            <form onSubmit={handleGuardarContenido} className="space-y-4">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-bold mb-1">Barra Superior (Anuncio Izquierdo)</label>
                  <input className={inputClass} value={cfgForm.bannerSuperiorIzq} onChange={(e) => setCfgForm({ ...cfgForm, bannerSuperiorIzq: e.target.value })} />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Barra Superior (Sedes Derecho)</label>
                  <input className={inputClass} value={cfgForm.bannerSuperiorDer} onChange={(e) => setCfgForm({ ...cfgForm, bannerSuperiorDer: e.target.value })} />
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label className="block text-xs font-bold mb-1">Etiqueta Hero (Home)</label>
                  <input className={inputClass} value={cfgForm.heroEtiqueta} onChange={(e) => setCfgForm({ ...cfgForm, heroEtiqueta: e.target.value })} />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Título Hero Línea 1</label>
                  <input className={inputClass} value={cfgForm.heroTitulo1} onChange={(e) => setCfgForm({ ...cfgForm, heroTitulo1: e.target.value })} />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Título Hero Línea 2 (Amarillo)</label>
                  <input className={inputClass} value={cfgForm.heroTitulo2} onChange={(e) => setCfgForm({ ...cfgForm, heroTitulo2: e.target.value })} />
                </div>
              </div>

              <div>
                <label className="block text-xs font-bold mb-1">Descripción Hero (Home)</label>
                <textarea rows={2} className={inputClass} value={cfgForm.heroDescripcion} onChange={(e) => setCfgForm({ ...cfgForm, heroDescripcion: e.target.value })} />
              </div>

              <div className="grid grid-cols-1 md:grid-cols-3 gap-4 items-center bg-[#FFFAE6] p-4 rounded-2xl border border-[#181818]">
                <div>
                  <label className="block text-xs font-bold mb-1">Imagen Hero Home (URL opcional)</label>
                  <input className={inputClass} value={cfgForm.heroImagen} onChange={(e) => setCfgForm({ ...cfgForm, heroImagen: e.target.value })} placeholder="Dejar vacío para ocultar o pegar URL" />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Subir Imagen Hero</label>
                  <label className="flex items-center justify-center gap-2 px-3 py-2 bg-[#F3D84F] border-2 border-[#181818] rounded-xl text-xs font-bold cursor-pointer">
                    <Upload className="w-4 h-4" />
                    <span>Subir Imagen</span>
                    <input
                      type="file"
                      accept="image/*"
                      className="hidden"
                      onChange={(e) => {
                        const file = e.target.files?.[0];
                        if (file) leerArchivoComoDataUrl(file, (url) => setCfgForm((prev) => ({ ...prev, heroImagen: url })));
                      }}
                    />
                  </label>
                </div>
                <div className="flex items-center gap-2">
                  {cfgForm.heroImagen ? (
                    <>
                      <img src={cfgForm.heroImagen} alt="Hero" className="w-20 h-14 object-cover rounded-lg border border-[#181818]" referrerPolicy="no-referrer" />
                      <button type="button" onClick={() => setCfgForm({ ...cfgForm, heroImagen: '' })} className="text-xs text-red-700 font-bold underline">Quitar</button>
                    </>
                  ) : (
                    <span className="text-xs text-neutral-500">Sin imagen extra en Hero</span>
                  )}
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-bold mb-1">Título Principal (Nosotros)</label>
                  <input className={inputClass} value={cfgForm.nosotrosTitulo} onChange={(e) => setCfgForm({ ...cfgForm, nosotrosTitulo: e.target.value })} />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Título Historia (Nosotros)</label>
                  <input className={inputClass} value={cfgForm.nosotrosHistoriaTitulo} onChange={(e) => setCfgForm({ ...cfgForm, nosotrosHistoriaTitulo: e.target.value })} />
                </div>
              </div>

              <div>
                <label className="block text-xs font-bold mb-1">Subtítulo Cabecera (Nosotros)</label>
                <textarea rows={2} className={inputClass} value={cfgForm.nosotrosSubtitulo} onChange={(e) => setCfgForm({ ...cfgForm, nosotrosSubtitulo: e.target.value })} />
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-bold mb-1">Historia Párrafo 1</label>
                  <textarea rows={3} className={inputClass} value={cfgForm.nosotrosHistoriaP1} onChange={(e) => setCfgForm({ ...cfgForm, nosotrosHistoriaP1: e.target.value })} />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Historia Párrafo 2</label>
                  <textarea rows={3} className={inputClass} value={cfgForm.nosotrosHistoriaP2} onChange={(e) => setCfgForm({ ...cfgForm, nosotrosHistoriaP2: e.target.value })} />
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-3 gap-4 items-center bg-[#FFFAE6] p-4 rounded-2xl border border-[#181818]">
                <div>
                  <label className="block text-xs font-bold mb-1">Imagen Principal Historia (Nosotros)</label>
                  <input className={inputClass} value={cfgForm.nosotrosHistoriaImagen} onChange={(e) => setCfgForm({ ...cfgForm, nosotrosHistoriaImagen: e.target.value })} />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">O subir nueva foto Historia</label>
                  <label className="flex items-center justify-center gap-2 px-3 py-2 bg-[#F3D84F] border-2 border-[#181818] rounded-xl text-xs font-bold cursor-pointer">
                    <Upload className="w-4 h-4" />
                    <span>Elegir Imagen</span>
                    <input
                      type="file"
                      accept="image/*"
                      className="hidden"
                      onChange={(e) => {
                        const file = e.target.files?.[0];
                        if (file) leerArchivoComoDataUrl(file, (url) => setCfgForm((prev) => ({ ...prev, nosotrosHistoriaImagen: url })));
                      }}
                    />
                  </label>
                </div>
                <div>
                  {cfgForm.nosotrosHistoriaImagen && (
                    <img src={cfgForm.nosotrosHistoriaImagen} alt="Nosotros" className="w-24 h-16 object-cover rounded-xl border-2 border-[#181818]" referrerPolicy="no-referrer" />
                  )}
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label className="block text-xs font-bold mb-1">Cita destacada (Nosotros)</label>
                  <input className={inputClass} value={cfgForm.nosotrosCita} onChange={(e) => setCfgForm({ ...cfgForm, nosotrosCita: e.target.value })} />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">WhatsApp de Admisiones</label>
                  <input className={inputClass} value={cfgForm.contactoWhatsapp} onChange={(e) => setCfgForm({ ...cfgForm, contactoWhatsapp: e.target.value })} />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Email Institucional</label>
                  <input className={inputClass} value={cfgForm.contactoEmail} onChange={(e) => setCfgForm({ ...cfgForm, contactoEmail: e.target.value })} />
                </div>
              </div>

              <button type="submit" className="px-6 py-2.5 bg-[#24AB58] text-white font-bold text-sm rounded-xl border-2 border-[#181818] shadow-[3px_3px_0px_#181818]">
                Guardar Todos los Textos e Imágenes
              </button>
            </form>
          </section>

          {/* Sedes */}
          <section className="bg-white border-2 border-[#181818] rounded-3xl p-6 shadow-[5px_5px_0px_#181818] space-y-4">
            <h2 className="font-carter text-xl text-[#181818]">Modificar Sedes Físicas</h2>
            {sedesForm.map((sede, idx) => (
              <div key={sede.id} className="p-4 bg-[#FFFDF5] border border-neutral-300 rounded-2xl space-y-3">
                <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
                  <div>
                    <label className="block text-xs font-bold mb-1">Nombre</label>
                    <input className={inputClass} value={sede.nombre} onChange={(e) => setSedesForm((prev) => prev.map((s, i) => (i === idx ? { ...s, nombre: e.target.value } : s)))} />
                  </div>
                  <div>
                    <label className="block text-xs font-bold mb-1">Dirección</label>
                    <input className={inputClass} value={sede.direccion} onChange={(e) => setSedesForm((prev) => prev.map((s, i) => (i === idx ? { ...s, direccion: e.target.value } : s)))} />
                  </div>
                  <div>
                    <label className="block text-xs font-bold mb-1">Horarios</label>
                    <input className={inputClass} value={sede.horariosAtencion} onChange={(e) => setSedesForm((prev) => prev.map((s, i) => (i === idx ? { ...s, horariosAtencion: e.target.value } : s)))} />
                  </div>
                </div>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <div>
                    <label className="block text-xs font-bold mb-1">Colectivos</label>
                    <input className={inputClass} value={sede.colectivos} onChange={(e) => setSedesForm((prev) => prev.map((s, i) => (i === idx ? { ...s, colectivos: e.target.value } : s)))} />
                  </div>
                  <div>
                    <label className="block text-xs font-bold mb-1">Subte</label>
                    <input className={inputClass} value={sede.subte} onChange={(e) => setSedesForm((prev) => prev.map((s, i) => (i === idx ? { ...s, subte: e.target.value } : s)))} />
                  </div>
                </div>
                <button type="button" onClick={() => handleGuardarSede(sede)} className="px-4 py-1.5 bg-[#F3D84F] border-2 border-[#181818] rounded-xl text-xs font-bold">
                  Guardar Sede
                </button>
              </div>
            ))}
          </section>

          {/* Testimonios y FAQs */}
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <section className="bg-white border-2 border-[#181818] rounded-3xl p-6 shadow-[5px_5px_0px_#181818] space-y-4">
              <h2 className="font-carter text-xl text-[#181818]">Testimonios (Home)</h2>
              {resenasForm.map((res, idx) => (
                <div key={res.id} className="p-3 bg-[#FFFDF5] border border-neutral-300 rounded-2xl space-y-2">
                  <div className="grid grid-cols-2 gap-2">
                    <input className={inputClass} value={res.autor} onChange={(e) => setResenasForm((prev) => prev.map((r, i) => (i === idx ? { ...r, autor: e.target.value } : r)))} />
                    <input className={inputClass} value={res.tallerNombre} onChange={(e) => setResenasForm((prev) => prev.map((r, i) => (i === idx ? { ...r, tallerNombre: e.target.value } : r)))} />
                  </div>
                  <textarea rows={2} className={inputClass} value={res.comentario} onChange={(e) => setResenasForm((prev) => prev.map((r, i) => (i === idx ? { ...r, comentario: e.target.value } : r)))} />
                  <button type="button" onClick={() => handleGuardarResena(res)} className="px-3 py-1 bg-[#F3D84F] border border-[#181818] rounded-lg text-xs font-bold">
                    Actualizar Testimonio
                  </button>
                </div>
              ))}
            </section>

            <section className="bg-white border-2 border-[#181818] rounded-3xl p-6 shadow-[5px_5px_0px_#181818] space-y-4">
              <h2 className="font-carter text-xl text-[#181818]">Preguntas Frecuentes (Consultas)</h2>
              {faqsForm.map((faq, idx) => (
                <div key={faq.id} className="p-3 bg-[#FFFDF5] border border-neutral-300 rounded-2xl space-y-2">
                  <input className={inputClass} value={faq.pregunta} onChange={(e) => setFaqsForm((prev) => prev.map((f, i) => (i === idx ? { ...f, pregunta: e.target.value } : f)))} />
                  <textarea rows={2} className={inputClass} value={faq.respuesta} onChange={(e) => setFaqsForm((prev) => prev.map((f, i) => (i === idx ? { ...f, respuesta: e.target.value } : f)))} />
                  <button type="button" onClick={() => handleGuardarFaq(faq)} className="px-3 py-1 bg-[#F3D84F] border border-[#181818] rounded-lg text-xs font-bold">
                    Actualizar FAQ
                  </button>
                </div>
              ))}
            </section>
          </div>

          {/* Consultas Recibidas */}
          <section className="bg-white border-2 border-[#181818] rounded-3xl p-6 shadow-[5px_5px_0px_#181818] space-y-3">
            <h2 className="font-carter text-xl text-[#181818]">Mensajes de Consulta Recibidos</h2>
            {consultas.length === 0 ? (
              <p className="text-xs text-neutral-500">Todavía no se registraron mensajes desde la sección Consultas.</p>
            ) : (
              <div className="space-y-2">
                {consultas.map((c) => (
                  <div key={c.id} className="p-3 bg-[#FFFDF5] border border-neutral-300 rounded-xl flex justify-between items-start gap-4 text-xs">
                    <div>
                      <strong>{c.nombre}</strong> ({c.email}) — <span className="text-[#24AB58] font-bold">{c.motivo}</span>
                      <p className="text-neutral-700 mt-1">{c.mensaje}</p>
                    </div>
                    <button
                      onClick={() => {
                        DataService.eliminarConsulta(c.id);
                        setConsultas(DataService.getConsultas());
                      }}
                      className="px-2 py-1 bg-red-100 text-red-700 border border-red-600 rounded-lg font-bold"
                    >
                      Borrar
                    </button>
                  </div>
                ))}
              </div>
            )}
          </section>
        </div>
      )}

      {/* =================================================================
          TAB 6: USUARIOS ADMINISTRADORES
          ================================================================= */}
      {tab === 'admins' && (
        <div className="space-y-6">
          <section className="bg-white border-2 border-[#181818] rounded-3xl p-6 shadow-[5px_5px_0px_#181818] space-y-4">
            <h2 className="font-carter text-xl text-[#181818] flex items-center gap-2">
              <UserPlus className="w-5 h-5 text-[#24AB58]" />
              <span>Cargar Nuevo Usuario Administrador</span>
            </h2>
            <form onSubmit={handleCrearAdmin} className="space-y-4">
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label className="block text-xs font-bold mb-1">Usuario *</label>
                  <input type="text" required className={inputClass} value={newAdmUsuario} onChange={(e) => setNewAdmUsuario(e.target.value)} placeholder="ej: sofia.admin" />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Nombre Completo *</label>
                  <input type="text" required className={inputClass} value={newAdmNombre} onChange={(e) => setNewAdmNombre(e.target.value)} placeholder="Sofía Tomillo" />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Email *</label>
                  <input type="email" required className={inputClass} value={newAdmEmail} onChange={(e) => setNewAdmEmail(e.target.value)} placeholder="admin@atodoritmo.edu.ar" />
                </div>
              </div>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-bold mb-1">Contraseña (mínimo 6 caracteres) *</label>
                  <input type="password" required minLength={6} className={inputClass} value={newAdmPass} onChange={(e) => setNewAdmPass(e.target.value)} />
                </div>
                <div>
                  <label className="block text-xs font-bold mb-1">Rol</label>
                  <select className={inputClass} value={newAdmRol} onChange={(e) => setNewAdmRol(e.target.value as 'admin' | 'superadmin')}>
                    <option value="admin">Administrador</option>
                    <option value="superadmin">Super Administrador</option>
                  </select>
                </div>
              </div>
              <button type="submit" className="px-5 py-2.5 bg-[#24AB58] text-white font-bold text-sm rounded-xl border-2 border-[#181818] shadow-[3px_3px_0px_#181818]">
                Registrar Administrador
              </button>
            </form>
          </section>

          <section className="bg-white border-2 border-[#181818] rounded-3xl p-6 shadow-[5px_5px_0px_#181818] overflow-x-auto">
            <h2 className="font-carter text-xl text-[#181818] mb-4">Administradores Cargados</h2>
            <table className="w-full text-left text-xs sm:text-sm border-collapse">
              <thead>
                <tr className="bg-[#181818] text-[#F3D84F]">
                  <th className="p-3">Usuario</th>
                  <th className="p-3">Nombre</th>
                  <th className="p-3">Email</th>
                  <th className="p-3">Rol</th>
                  <th className="p-3">Estado</th>
                </tr>
              </thead>
              <tbody>
                {admins.map((ad) => (
                  <tr key={ad.id} className="border-b border-neutral-200">
                    <td className="p-3 font-bold">@{ad.usuario}</td>
                    <td className="p-3">{ad.nombreCompleto}</td>
                    <td className="p-3">{ad.email}</td>
                    <td className="p-3 uppercase text-xs font-bold">{ad.rol}</td>
                    <td className="p-3">
                      <button
                        disabled={ad.id === adminActual.id}
                        onClick={() => {
                          DataService.toggleAdminActivo(ad.id);
                          setAdmins(DataService.getAdmins());
                        }}
                        className={`px-3 py-1 rounded-lg border border-[#181818] text-xs font-bold ${
                          ad.activo ? 'bg-emerald-100 text-emerald-900' : 'bg-red-100 text-red-900'
                        } disabled:opacity-60`}
                      >
                        {ad.activo ? 'Activo' : 'Inactivo'}
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </section>
        </div>
      )}
    </div>
  );
};
