import React, { useState } from 'react';
import { Taller, Sede, Inscripcion, SeccionActiva } from '../types';
import { DataService } from '../services/dataService';
import { UserCheck, CheckCircle2, AlertCircle, FileText, Heart, Shield, Music, Printer, Calendar, Clock, MapPin, ArrowRight } from 'lucide-react';

interface InscripcionSectionProps {
  talleres: Taller[];
  sedes: Sede[];
  tallerPreseleccionadoId?: number | null;
  onActualizarTalleres: () => void;
  onNotificarSqlEjecutado: (sql: string) => void;
  onCambiarSeccion: (seccion: SeccionActiva) => void;
}

export const InscripcionSection: React.FC<InscripcionSectionProps> = ({
  talleres,
  sedes,
  tallerPreseleccionadoId,
  onActualizarTalleres,
  onNotificarSqlEjecutado,
  onCambiarSeccion
}) => {
  // Estado del formulario
  const [tallerId, setTallerId] = useState<number>(
    tallerPreseleccionadoId || (talleres[0]?.id ?? 1)
  );
  const [nombre, setNombre] = useState('');
  const [apellido, setApellido] = useState('');
  const [email, setEmail] = useState('');
  const [telefono, setTelefono] = useState('');
  const [edad, setEdad] = useState<number>(14);
  const [genero, setGenero] = useState<'Masc' | 'Fem' | 'NB' | 'Otro' | 'Prefiero no decir'>('NB');
  const [modalidad, setModalidad] = useState<'Presencial' | 'Online' | 'Híbrida'>('Presencial');
  const [sedeId, setSedeId] = useState<number>(1);
  const [necesitaPrestamo, setNecesitaPrestamo] = useState(true);
  const [poseeInstrumento, setPoseeInstrumento] = useState(false);
  const [comentarios, setComentarios] = useState('');

  const [comprobante, setComprobante] = useState<Inscripcion | null>(null);
  const [errorMensaje, setErrorMensaje] = useState('');

  // Si cambia la preselección externa
  React.useEffect(() => {
    if (tallerPreseleccionadoId) {
      setTallerId(tallerPreseleccionadoId);
    }
  }, [tallerPreseleccionadoId]);

  const tallerSeleccionado = talleres.find(t => t.id === tallerId);
  const sedeSeleccionada = sedes.find(s => s.id === sedeId);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setErrorMensaje('');

    if (!nombre.trim() || !apellido.trim() || !email.trim() || !telefono.trim()) {
      setErrorMensaje('Por favor completa todos los campos obligatorios (*).');
      return;
    }

    if (edad < 7) {
      setErrorMensaje('La edad mínima para inscribirse es de 7 años cumplidos.');
      return;
    }

    if (!tallerSeleccionado) {
      setErrorMensaje('Por favor selecciona un taller válido.');
      return;
    }

    const { inscripcion, sqlQuery } = DataService.registrarInscripcion({
      tallerId: tallerSeleccionado.id,
      nombre: nombre.trim(),
      apellido: apellido.trim(),
      email: email.trim(),
      telefono: telefono.trim(),
      edad,
      genero,
      modalidad,
      necesitaPrestamoInstrumento: necesitaPrestamo,
      poseeInstrumentoPropio: poseeInstrumento,
      sedeId,
      horarioSeleccionado: `${tallerSeleccionado.diaSemana} (${tallerSeleccionado.horario})`,
      comentarios: comentarios.trim()
    });

    setComprobante(inscripcion);
    onActualizarTalleres();
    onNotificarSqlEjecutado(sqlQuery);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  const handleNuevaInscripcion = () => {
    setComprobante(null);
    setNombre('');
    setApellido('');
    setEmail('');
    setTelefono('');
    setComentarios('');
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12">
      {/* CABECERA */}
      <div className="text-center max-w-3xl mx-auto space-y-3">
        <span className="text-xs font-bold text-[#24AB58] uppercase tracking-wider">
          Ciclo Lectivo 2026
        </span>
        <h1 className="font-carter text-3xl sm:text-5xl text-[#181818]">
          Ficha de Inscripción Online
        </h1>
        <p className="text-sm sm:text-base text-neutral-700 leading-relaxed font-sans">
          Completá el formulario para matricularte. Si no disponés de instrumento propio, podés solicitar uno de nuestro Banco Solidario sin costo.
        </p>
      </div>

      {comprobante ? (
        /* COMPROBANTE OFICIAL DE INSCRIPCIÓN EMITIDO */
        <div className="max-w-3xl mx-auto bg-[#FFFDF5] border-4 border-[#24AB58] rounded-3xl p-6 sm:p-10 shadow-2xl space-y-6 animate-fadeIn">
          <div className="flex flex-col sm:flex-row items-center justify-between pb-6 border-b-2 border-neutral-200 gap-4">
            <div className="flex items-center gap-3">
              <div className="w-12 h-12 rounded-2xl bg-[#24AB58] text-[#FFFAE6] flex items-center justify-center font-bold shadow">
                <CheckCircle2 className="w-7 h-7" />
              </div>
              <div>
                <span className="font-carter text-xl sm:text-2xl text-[#181818] block">
                  ¡Inscripción Confirmada!
                </span>
                <span className="text-xs text-[#24AB58] font-bold">
                  Escuela de Música "A Todo Ritmo"
                </span>
              </div>
            </div>

            <div className="text-right sm:text-right">
              <span className="text-[11px] uppercase font-bold text-neutral-500 block">
                Código de Matrícula
              </span>
              <span className="font-mono text-lg font-bold text-[#181818] bg-[#F3D84F] px-3 py-1 rounded-lg inline-block">
                {comprobante.codigoInscripcion}
              </span>
            </div>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs text-neutral-800">
            <div className="p-4 bg-[#FFFAE6] rounded-2xl border border-neutral-300 space-y-1.5">
              <span className="font-bold text-[#24AB58] uppercase text-[11px] block">
                Datos del Alumno/a
              </span>
              <p><strong>Nombre:</strong> {comprobante.nombre} {comprobante.apellido}</p>
              <p><strong>Edad:</strong> {comprobante.edad} años</p>
              <p><strong>Email:</strong> {comprobante.email}</p>
              <p><strong>Teléfono:</strong> {comprobante.telefono}</p>
              <p><strong>Género:</strong> {comprobante.genero}</p>
            </div>

            <div className="p-4 bg-[#FFFAE6] rounded-2xl border border-neutral-300 space-y-1.5">
              <span className="font-bold text-[#24AB58] uppercase text-[11px] block">
                Taller Asignado
              </span>
              <p><strong>Taller:</strong> {tallerSeleccionado?.nombre}</p>
              <p><strong>Instrumento:</strong> {tallerSeleccionado?.instrumento}</p>
              <p><strong>Horario:</strong> {comprobante.horarioSeleccionado}</p>
              <p><strong>Modalidad:</strong> {comprobante.modalidad}</p>
              <p><strong>Sede:</strong> {sedeSeleccionada?.nombre}</p>
            </div>
          </div>

          {/* Estado del Préstamo de Instrumento */}
          <div className={`p-4 rounded-2xl border-2 flex items-start gap-3 text-xs ${
            comprobante.necesitaPrestamoInstrumento
              ? 'bg-amber-50 border-[#F3D84F] text-amber-900'
              : 'bg-emerald-50 border-[#24AB58] text-emerald-900'
          }`}>
            <Heart className="w-5 h-5 shrink-0 text-[#24AB58] mt-0.5" />
            <div>
              <p className="font-bold">
                {comprobante.necesitaPrestamoInstrumento
                  ? 'Préstamo de Instrumento Solidario Solicitado'
                  : 'Cuenta con Instrumento Propio'}
              </p>
              <p className="mt-0.5 text-neutral-700">
                {comprobante.necesitaPrestamoInstrumento
                  ? 'Te hemos reservado un instrumento para el primer día de clase. El docente te explicará el cuidado y el comodato de estudio.'
                  : 'Podrás llevar tu propio instrumento para las clases presenciales.'}
              </p>
            </div>
          </div>

          {/* Pasos siguientes */}
          <div className="p-4 rounded-2xl bg-neutral-900 text-[#FFFAE6] text-xs space-y-2">
            <p className="font-bold text-[#F3D84F]">¿Cuáles son los pasos siguientes?</p>
            <ol className="list-decimal pl-4 space-y-1 text-neutral-300">
              <li>Te enviamos este comprobante por correo a <strong>{comprobante.email}</strong>.</li>
              <li>Presentar antes de la primera clase fotocopia de DNI y apto físico simple en secretaría.</li>
              <li>El arancel se abona en secretaría del 1 al 10 de cada mes o por transferencia bancaria.</li>
            </ol>
          </div>

          <div className="flex flex-col sm:flex-row justify-between items-center gap-3 pt-4 border-t border-neutral-200">
            <button
              onClick={() => window.print()}
              className="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-neutral-400 hover:border-black text-xs font-bold text-neutral-800 flex items-center justify-center gap-2"
            >
              <Printer className="w-4 h-4" />
              <span>Imprimir / Guardar Comprobante</span>
            </button>

            <button
              onClick={handleNuevaInscripcion}
              className="w-full sm:w-auto px-6 py-2.5 bg-[#24AB58] hover:bg-[#1e8f49] text-[#FFFAE6] font-carter text-xs rounded-xl shadow"
            >
              Realizar Otra Inscripción
            </button>
          </div>
        </div>
      ) : (
        /* FORMULARIO Y REQUISITOS */
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
          {/* Columna Izquierda: Requisitos, Documentación y Sede */}
          <div className="lg:col-span-5 space-y-6">
            <div className="bg-[#FFFDF5] border-2 border-[#181818] rounded-3xl p-6 sm:p-8 space-y-6 shadow-md">
              <div className="flex items-center gap-2 text-xs font-bold text-[#24AB58] uppercase">
                <Shield className="w-4 h-4" />
                <span>Requisitos de Inscripción</span>
              </div>

              <div className="space-y-4 text-xs text-neutral-700">
                <div className="flex items-start gap-3">
                  <div className="w-6 h-6 rounded-full bg-[#F3D84F] text-[#181818] flex items-center justify-center font-bold text-xs shrink-0">
                    1
                  </div>
                  <div>
                    <strong className="text-neutral-900 block">Edad Mínima:</strong>
                    A partir de los 7 años cumplidos. No hay límite superior de edad: jóvenes, adultos y personas mayores son siempre bienvenidos.
                  </div>
                </div>

                <div className="flex items-start gap-3">
                  <div className="w-6 h-6 rounded-full bg-[#F3D84F] text-[#181818] flex items-center justify-center font-bold text-xs shrink-0">
                    2
                  </div>
                  <div>
                    <strong className="text-neutral-900 block">Nivel Musical:</strong>
                    ¡No se requieren conocimientos previos! Los talleres iniciales comienzan desde cero con una pedagogía integradora.
                  </div>
                </div>

                <div className="flex items-start gap-3">
                  <div className="w-6 h-6 rounded-full bg-[#F3D84F] text-[#181818] flex items-center justify-center font-bold text-xs shrink-0">
                    3
                  </div>
                  <div>
                    <strong className="text-neutral-900 block">Documentación Necesaria:</strong>
                    DNI original y copia (frente y dorso) y certificado médico básico de aptitud física (para menores de edad, firma del tutor).
                  </div>
                </div>

                <div className="flex items-start gap-3">
                  <div className="w-6 h-6 rounded-full bg-[#24AB58] text-[#FFFAE6] flex items-center justify-center font-bold text-xs shrink-0">
                    ★
                  </div>
                  <div>
                    <strong className="text-neutral-900 block">Banco Solidario de Instrumentos:</strong>
                    Si no tenés instrumento en casa, la escuela te presta uno en comodato durante las clases para que nunca te quedes sin tocar.
                  </div>
                </div>
              </div>

              {/* Taller Preseleccionado Banner */}
              {tallerSeleccionado && (
                <div className="p-4 rounded-2xl bg-[#FFFAE6] border-2 border-[#F3D84F] text-xs space-y-1.5">
                  <span className="font-bold text-[#181818] uppercase tracking-wider text-[11px] block">
                    Taller Seleccionado:
                  </span>
                  <p className="font-carter text-base text-[#181818]">
                    {tallerSeleccionado.nombre}
                  </p>
                  <p className="text-neutral-600">
                    📅 {tallerSeleccionado.diaSemana} • ⏰ {tallerSeleccionado.horario}
                  </p>
                  <p className="text-[#24AB58] font-bold">
                    Cupos disponibles: {tallerSeleccionado.vacantesDisponibles} vacantes
                  </p>
                  <p className="text-[#181818] font-bold">
                    Arancel: ${tallerSeleccionado.arancelMensual.toLocaleString('es-AR')} / mes
                  </p>
                </div>
              )}
            </div>
          </div>

          {/* Columna Derecha: Formulario Completo */}
          <div className="lg:col-span-7 bg-[#FFFDF5] border-3 border-[#181818] rounded-3xl p-6 sm:p-10 shadow-xl">
            <form onSubmit={handleSubmit} className="space-y-6">
              <div className="border-b border-neutral-200 pb-4">
                <h3 className="font-carter text-2xl text-[#181818]">
                  Paso 1: Datos Personales del Postulante
                </h3>
                <p className="text-xs text-neutral-600 mt-1">
                  Ingresá los datos del alumno que tomará las clases.
                </p>
              </div>

              {errorMensaje && (
                <div className="p-3.5 bg-red-100 border border-red-400 text-red-700 text-xs rounded-xl flex items-center gap-2">
                  <AlertCircle className="w-4 h-4 shrink-0" />
                  <span>{errorMensaje}</span>
                </div>
              )}

              {/* Nombre y Apellido */}
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                  <label className="block font-bold text-neutral-800 mb-1">Nombre *</label>
                  <input
                    type="text"
                    required
                    placeholder="Ej: Sofía"
                    value={nombre}
                    onChange={(e) => setNombre(e.target.value)}
                    className="w-full p-2.5 bg-[#FFFAE6] border border-neutral-300 rounded-xl focus:border-[#24AB58] focus:outline-none"
                  />
                </div>

                <div>
                  <label className="block font-bold text-neutral-800 mb-1">Apellido *</label>
                  <input
                    type="text"
                    required
                    placeholder="Ej: Tomillo"
                    value={apellido}
                    onChange={(e) => setApellido(e.target.value)}
                    className="w-full p-2.5 bg-[#FFFAE6] border border-neutral-300 rounded-xl focus:border-[#24AB58] focus:outline-none"
                  />
                </div>
              </div>

              {/* Email y Teléfono */}
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                  <label className="block font-bold text-neutral-800 mb-1">Correo Electrónico *</label>
                  <input
                    type="email"
                    required
                    placeholder="ejemplo@correo.com"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    className="w-full p-2.5 bg-[#FFFAE6] border border-neutral-300 rounded-xl focus:border-[#24AB58] focus:outline-none"
                  />
                </div>

                <div>
                  <label className="block font-bold text-neutral-800 mb-1">Teléfono / WhatsApp *</label>
                  <input
                    type="tel"
                    required
                    placeholder="Ej: 11 5544-3322"
                    value={telefono}
                    onChange={(e) => setTelefono(e.target.value)}
                    className="w-full p-2.5 bg-[#FFFAE6] border border-neutral-300 rounded-xl focus:border-[#24AB58] focus:outline-none"
                  />
                </div>
              </div>

              {/* Edad y Género */}
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                  <label className="block font-bold text-neutral-800 mb-1">Edad (Mínimo 7 años) *</label>
                  <input
                    type="number"
                    min={7}
                    max={100}
                    required
                    value={edad}
                    onChange={(e) => setEdad(Number(e.target.value))}
                    className="w-full p-2.5 bg-[#FFFAE6] border border-neutral-300 rounded-xl focus:border-[#24AB58] focus:outline-none"
                  />
                </div>

                <div>
                  <label className="block font-bold text-neutral-800 mb-1">Género *</label>
                  <select
                    value={genero}
                    onChange={(e) => setGenero(e.target.value as any)}
                    className="w-full p-2.5 bg-[#FFFAE6] border border-neutral-300 rounded-xl focus:border-[#24AB58] focus:outline-none"
                  >
                    <option value="Fem">Femenino</option>
                    <option value="Masc">Masculino</option>
                    <option value="NB">No Binario (NB)</option>
                    <option value="Otro">Otro</option>
                    <option value="Prefiero no decir">Prefiero no decir</option>
                  </select>
                </div>
              </div>

              <div className="border-t border-neutral-200 pt-6">
                <h3 className="font-carter text-2xl text-[#181818]">
                  Paso 2: Elección de Taller, Modalidad y Sede
                </h3>
              </div>

              {/* Taller a Cursar */}
              <div className="text-xs">
                <label className="block font-bold text-neutral-800 mb-1">Taller de Instrumento *</label>
                <select
                  value={tallerId}
                  onChange={(e) => setTallerId(Number(e.target.value))}
                  className="w-full p-3 bg-[#FFFAE6] border border-neutral-300 rounded-xl font-semibold text-neutral-800 focus:border-[#24AB58] focus:outline-none"
                >
                  {talleres.map((t) => (
                    <option key={t.id} value={t.id}>
                      {t.nombre} ({t.instrumento}) — {t.diaSemana} {t.horario} — {t.vacantesDisponibles} vacantes
                    </option>
                  ))}
                </select>
              </div>

              {/* Modalidad y Sede */}
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                  <label className="block font-bold text-neutral-800 mb-1">Modalidad de Cursada *</label>
                  <select
                    value={modalidad}
                    onChange={(e) => setModalidad(e.target.value as any)}
                    className="w-full p-2.5 bg-[#FFFAE6] border border-neutral-300 rounded-xl focus:border-[#24AB58] focus:outline-none"
                  >
                    <option value="Presencial">Presencial en Aula</option>
                    <option value="Online">Online en Vivo</option>
                    <option value="Híbrida">Híbrida (Presencial + Virtual)</option>
                  </select>
                </div>

                <div>
                  <label className="block font-bold text-neutral-800 mb-1">Sede Preferida *</label>
                  <select
                    value={sedeId}
                    onChange={(e) => setSedeId(Number(e.target.value))}
                    className="w-full p-2.5 bg-[#FFFAE6] border border-neutral-300 rounded-xl focus:border-[#24AB58] focus:outline-none"
                  >
                    {sedes.map((s) => (
                      <option key={s.id} value={s.id}>
                        {s.nombre} ({s.barrio})
                      </option>
                    ))}
                  </select>
                </div>
              </div>

              {/* BANCO SOLIDARIO DE INSTRUMENTOS (Clave de la propuesta del PDF) */}
              <div className="p-4 bg-[#FFFAE6] border-2 border-[#181818] rounded-2xl space-y-3">
                <span className="font-carter text-sm text-[#181818] flex items-center gap-2">
                  <Heart className="w-4 h-4 text-[#24AB58]" />
                  <span>Banco Solidario de Instrumentos</span>
                </span>

                <div className="space-y-2 text-xs">
                  <label className="flex items-center gap-2.5 cursor-pointer">
                    <input
                      type="checkbox"
                      checked={necesitaPrestamo}
                      onChange={(e) => setNecesitaPrestamo(e.target.checked)}
                      className="w-4 h-4 accent-[#24AB58] rounded"
                    />
                    <span className="font-semibold text-neutral-800">
                      Solicito que la escuela me preste un instrumento durante las clases (Sin costo adicional).
                    </span>
                  </label>

                  <label className="flex items-center gap-2.5 cursor-pointer">
                    <input
                      type="checkbox"
                      checked={poseeInstrumento}
                      onChange={(e) => setPoseeInstrumento(e.target.checked)}
                      className="w-4 h-4 accent-[#24AB58] rounded"
                    />
                    <span className="text-neutral-700">
                      Cuento con instrumento propio en mi hogar para practicar.
                    </span>
                  </label>
                </div>
              </div>

              {/* Comentarios o Dudas */}
              <div className="text-xs">
                <label className="block font-bold text-neutral-800 mb-1">Comentarios o Experiencia Previa (Opcional)</label>
                <textarea
                  rows={2}
                  placeholder="Contanos si tocás algún otro instrumento o si tenés alguna necesidad específica..."
                  value={comentarios}
                  onChange={(e) => setComentarios(e.target.value)}
                  className="w-full p-2.5 bg-[#FFFAE6] border border-neutral-300 rounded-xl focus:border-[#24AB58] focus:outline-none"
                />
              </div>

              {/* Botón Enviar */}
              <div className="pt-2">
                <button
                  type="submit"
                  className="w-full py-3.5 bg-[#24AB58] hover:bg-[#1e8f49] text-[#FFFAE6] font-carter text-base rounded-xl shadow-lg transition-transform transform active:scale-98 flex items-center justify-center gap-2"
                >
                  <UserCheck className="w-5 h-5" />
                  <span>Confirmar e Iniciar Cursada</span>
                </button>
                <p className="text-[11px] text-center text-neutral-500 mt-2">
                  Al enviar la ficha, se reserva la vacante y se genera el comprobante digital al instante.
                </p>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};
