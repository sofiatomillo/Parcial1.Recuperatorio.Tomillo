import React from 'react';
import { SeccionActiva, Taller, Evento, Resena } from '../types';
import { Music, Sparkles, HeartHandshake, ShieldCheck, Users, Calendar, ArrowRight, CheckCircle2, Ticket, Star, BookOpen } from 'lucide-react';

interface HomeSectionProps {
  talleres: Taller[];
  eventos: Evento[];
  resenas: Resena[];
  onCambiarSeccion: (seccion: SeccionActiva) => void;
  onSeleccionarTallerParaInscripcion: (tallerId: number) => void;
  onSeleccionarEventoParaEntrada: (evento: Evento) => void;
}

export const HomeSection: React.FC<HomeSectionProps> = ({
  talleres,
  eventos,
  resenas,
  onCambiarSeccion,
  onSeleccionarTallerParaInscripcion,
  onSeleccionarEventoParaEntrada
}) => {
  const proximoEvento = eventos.find(e => e.estado === 'proximo');
  const talleresDestacados = talleres.filter(t => t.destacado).slice(0, 4);

  return (
    <div className="space-y-16 pb-12">
      {/* HERO SECTION */}
      <section className="relative overflow-hidden bg-[#181818] text-[#FFFAE6] rounded-3xl mx-4 sm:mx-6 lg:mx-8 mt-6 border-4 border-[#F3D84F] shadow-2xl pattern-dots-yellow">
        {/* Fondo decorativo con ondas fluidas abstractas */}
        <div className="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-[#24AB58]/20 blur-3xl pointer-events-none"></div>
        <div className="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-[#F3D84F]/15 blur-2xl pointer-events-none"></div>

        <div className="relative max-w-5xl mx-auto px-6 py-16 sm:py-24 text-center space-y-6">
          <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#24AB58]/30 border border-[#24AB58] text-[#F3D84F] text-xs sm:text-sm font-bold tracking-wider uppercase">
            <Sparkles className="w-4 h-4 text-[#F3D84F]" />
            <span>Inscripciones Ciclo Lectivo 2026</span>
          </div>

          <h1 className="font-carter text-3xl sm:text-5xl lg:text-6xl text-[#FFFAE6] tracking-tight leading-tight">
            Sentí la música, <br />
            <span className="text-[#F3D84F] inline-block mt-1">descubrí tu ritmo.</span>
          </h1>

          <p className="max-w-2xl mx-auto text-base sm:text-lg text-neutral-300 font-sans leading-relaxed">
            Una escuela de música inclusiva en el corazón del barrio. Clases de guitarra, violín, flauta, batería, piano y canto para todas las edades. <strong className="text-[#F3D84F]">¿No tenés instrumento? Te lo prestamos nosotros.</strong>
          </p>

          <div className="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
            <button
              onClick={() => onCambiarSeccion('inscripcion')}
              id="hero-cta-inscribirme"
              className="w-full sm:w-auto px-8 py-3.5 bg-[#F3D84F] hover:bg-[#e6c83b] text-[#181818] font-carter text-lg rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2"
            >
              <span>¡Quiero Inscribirme!</span>
              <ArrowRight className="w-5 h-5 text-[#181818]" />
            </button>

            <button
              onClick={() => onCambiarSeccion('talleres')}
              id="hero-cta-talleres"
              className="w-full sm:w-auto px-7 py-3.5 bg-transparent border-2 border-[#24AB58] text-[#FFFAE6] hover:bg-[#24AB58] font-carter text-base rounded-xl transition-colors flex items-center justify-center gap-2"
            >
              <BookOpen className="w-5 h-5 text-[#F3D84F]" />
              <span>Explorar Talleres 2026</span>
            </button>
          </div>

          {/* Micro badges de confianza */}
          <div className="pt-8 border-t border-neutral-800 grid grid-cols-2 md:grid-cols-4 gap-4 text-xs font-semibold text-neutral-300">
            <div className="flex items-center justify-center gap-2">
              <CheckCircle2 className="w-4 h-4 text-[#24AB58]" />
              <span>Sin examen previo</span>
            </div>
            <div className="flex items-center justify-center gap-2">
              <CheckCircle2 className="w-4 h-4 text-[#F3D84F]" />
              <span>Préstamo de instrumentos</span>
            </div>
            <div className="flex items-center justify-center gap-2">
              <CheckCircle2 className="w-4 h-4 text-[#24AB58]" />
              <span>Desde los 7 años en adelante</span>
            </div>
            <div className="flex items-center justify-center gap-2">
              <CheckCircle2 className="w-4 h-4 text-[#F3D84F]" />
              <span>Aranceles accesibles</span>
            </div>
          </div>
        </div>
      </section>

      {/* PILARES DE VALOR (Respuesta a los dolores del público objetivo del PDF) */}
      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center max-w-2xl mx-auto mb-10">
          <span className="text-xs font-bold text-[#24AB58] uppercase tracking-wider">
            Nuestra Filosofía
          </span>
          <h2 className="font-carter text-2xl sm:text-3xl text-[#181818] mt-1">
            Por qué estudiar en "A Todo Ritmo"
          </h2>
          <p className="text-sm text-neutral-700 mt-2">
            Rompemos las barreras de los conservatorios tradicionales para que el arte sea un espacio de encuentro y disfrute.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          {/* Tarjeta 1 */}
          <div className="bg-[#FFFDF5] border-2 border-[#181818] rounded-2xl p-6 shadow-md hover:border-[#24AB58] transition-all space-y-3">
            <div className="w-12 h-12 rounded-xl bg-[#24AB58] text-[#FFFAE6] flex items-center justify-center">
              <HeartHandshake className="w-6 h-6" />
            </div>
            <h3 className="font-carter text-lg text-[#181818]">Banco de Instrumentos</h3>
            <p className="text-xs text-neutral-700 leading-relaxed">
              No dejes de aprender por no poder comprar un instrumento caro. La escuela cuenta con guitarras, violines, flautas y teclados para su uso gratuito en clase y sala de estudio.
            </p>
          </div>

          {/* Tarjeta 2 */}
          <div className="bg-[#FFFDF5] border-2 border-[#181818] rounded-2xl p-6 shadow-md hover:border-[#F3D84F] transition-all space-y-3">
            <div className="w-12 h-12 rounded-xl bg-[#F3D84F] text-[#181818] flex items-center justify-center">
              <ShieldCheck className="w-6 h-6" />
            </div>
            <h3 className="font-carter text-lg text-[#181818]">Vacantes Reales</h3>
            <p className="text-xs text-neutral-700 leading-relaxed">
              Sin audiciones elitistas ni filtros frustrantes. Todos tenemos derecho a la música; organizamos grupos reducidos por edades para que recibas atención personalizada.
            </p>
          </div>

          {/* Tarjeta 3 */}
          <div className="bg-[#FFFDF5] border-2 border-[#181818] rounded-2xl p-6 shadow-md hover:border-[#24AB58] transition-all space-y-3">
            <div className="w-12 h-12 rounded-xl bg-[#24AB58] text-[#FFFAE6] flex items-center justify-center">
              <Users className="w-6 h-6" />
            </div>
            <h3 className="font-carter text-lg text-[#181818]">Docentes de Vocación</h3>
            <p className="text-xs text-neutral-700 leading-relaxed">
              Profesores graduados de conservatorios nacionales de primer nivel con una pedagogía cercana, alegre y respetuosa de los tiempos de cada estudiante.
            </p>
          </div>

          {/* Tarjeta 4 */}
          <div className="bg-[#FFFDF5] border-2 border-[#181818] rounded-2xl p-6 shadow-md hover:border-[#F3D84F] transition-all space-y-3">
            <div className="w-12 h-12 rounded-xl bg-[#F3D84F] text-[#181818] flex items-center justify-center">
              <Music className="w-6 h-6" />
            </div>
            <h3 className="font-carter text-lg text-[#181818]">Ensambles y Muestras</h3>
            <p className="text-xs text-neutral-700 leading-relaxed">
              No tocás solo en un rincón: desde los primeros meses participás en ensambles colectivos y muestras en vivo para compartir con amigos y familiares.
            </p>
          </div>
        </div>
      </section>

      {/* TALLERES DESTACADOS (Preview de los talleres del sitemap) */}
      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4 mb-8">
          <div>
            <span className="text-xs font-bold text-[#24AB58] uppercase tracking-wider">
              Nuestra Oferta Educativa
            </span>
            <h2 className="font-carter text-2xl sm:text-3xl text-[#181818] mt-1">
              Talleres Destacados
            </h2>
            <p className="text-sm text-neutral-700">
              Conocé las disciplinas más elegidas por nuestros alumnos.
            </p>
          </div>

          <button
            onClick={() => onCambiarSeccion('talleres')}
            className="text-sm font-bold text-[#24AB58] hover:text-[#181818] flex items-center gap-1.5 transition-colors"
          >
            <span>Ver todos los talleres</span>
            <ArrowRight className="w-4 h-4" />
          </button>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          {talleresDestacados.map((taller) => (
            <div
              key={taller.id}
              className="bg-[#FFFDF5] rounded-2xl border-2 border-[#181818] overflow-hidden shadow-md flex flex-col justify-between hover:shadow-xl transition-all"
            >
              <div>
                <div className="relative h-44 overflow-hidden bg-neutral-900">
                  <img
                    src={taller.imagenUrl}
                    alt={taller.nombre}
                    className="w-full h-full object-cover transform hover:scale-105 transition-transform duration-500"
                    referrerPolicy="no-referrer"
                  />
                  <div className="absolute top-3 left-3 bg-[#F3D84F] text-[#181818] px-2.5 py-0.5 rounded-full text-xs font-carter shadow">
                    {taller.instrumento}
                  </div>
                  <div className="absolute top-3 right-3 bg-[#181818]/90 text-[#FFFAE6] px-2 py-0.5 rounded-full text-[11px] font-bold">
                    {taller.nivel.split('/')[0]}
                  </div>
                </div>

                <div className="p-5 space-y-3">
                  <h3 className="font-carter text-lg text-[#181818] leading-tight">
                    {taller.nombre}
                  </h3>

                  <p className="text-xs text-neutral-600 line-clamp-2">
                    {taller.descripcion}
                  </p>

                  <div className="pt-2 border-t border-neutral-200 text-xs space-y-1.5">
                    <p className="text-neutral-700">
                      <strong>Horario:</strong> {taller.diaSemana} ({taller.horario})
                    </p>
                    <div className="flex items-center justify-between">
                      <span className="inline-flex items-center gap-1 text-[#24AB58] font-bold">
                        <span className="w-2 h-2 rounded-full bg-[#24AB58]"></span>
                        {taller.vacantesDisponibles} vacantes libres
                      </span>
                      <span className="font-carter text-sm text-[#181818]">
                        ${taller.arancelMensual.toLocaleString('es-AR')}/mes
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <div className="p-5 pt-0">
                <button
                  onClick={() => onSeleccionarTallerParaInscripcion(taller.id)}
                  className="w-full py-2.5 px-4 bg-[#24AB58] hover:bg-[#1e8f49] text-[#FFFAE6] font-carter text-xs rounded-xl shadow transition-colors flex items-center justify-center gap-1.5"
                >
                  <span>Inscribirme en este Taller</span>
                  <ArrowRight className="w-3.5 h-3.5" />
                </button>
              </div>
            </div>
          ))}
        </div>
      </section>

      {/* DESTACADO DE EVENTO PRÓXIMO */}
      {proximoEvento && (
        <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="bg-gradient-to-r from-[#181818] to-neutral-900 border-3 border-[#24AB58] rounded-3xl p-6 sm:p-10 text-[#FFFAE6] shadow-xl relative overflow-hidden">
            <div className="relative z-10 grid grid-cols-1 lg:grid-cols-3 gap-8 items-center">
              <div className="lg:col-span-2 space-y-4">
                <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#F3D84F] text-[#181818] text-xs font-bold uppercase">
                  <Calendar className="w-3.5 h-3.5" />
                  <span>Próximo Evento Mensual</span>
                </div>

                <h3 className="font-carter text-2xl sm:text-4xl text-[#F3D84F] leading-tight">
                  {proximoEvento.titulo}
                </h3>

                <p className="text-sm text-neutral-300">
                  {proximoEvento.descripcion}
                </p>

                <div className="flex flex-wrap gap-4 text-xs font-semibold text-neutral-300 pt-2">
                  <span className="bg-neutral-800 px-3 py-1.5 rounded-lg border border-neutral-700">
                    📅 Fecha: {proximoEvento.fecha}
                  </span>
                  <span className="bg-neutral-800 px-3 py-1.5 rounded-lg border border-neutral-700">
                    ⏰ {proximoEvento.hora}
                  </span>
                  <span className="bg-neutral-800 px-3 py-1.5 rounded-lg border border-neutral-700">
                    📍 {proximoEvento.lugar}
                  </span>
                  <span className="bg-[#24AB58]/30 text-[#24AB58] px-3 py-1.5 rounded-lg border border-[#24AB58]">
                    🎟️ Entradas disponibles: {proximoEvento.entradasDisponibles}
                  </span>
                </div>
              </div>

              <div className="bg-neutral-900 border border-neutral-700 rounded-2xl p-6 text-center space-y-4">
                <span className="text-xs uppercase text-neutral-400 font-bold">Valor de la Entrada</span>
                <p className="font-carter text-3xl text-[#F3D84F]">
                  {proximoEvento.precioEntrada === 0 ? 'Entrada Libre' : `$${proximoEvento.precioEntrada}`}
                </p>
                <p className="text-[11px] text-neutral-400">
                  Los fondos se destinan íntegramente al mantenimiento del banco solidario de instrumentos.
                </p>
                <button
                  onClick={() => onSeleccionarEventoParaEntrada(proximoEvento)}
                  className="w-full py-3 bg-[#F3D84F] hover:bg-[#e6c83b] text-[#181818] font-carter text-sm rounded-xl shadow-md transition-all flex items-center justify-center gap-2"
                >
                  <Ticket className="w-4 h-4 text-[#181818]" />
                  <span>Sacar Entradas Online</span>
                </button>
              </div>
            </div>
          </div>
        </section>
      )}

      {/* RESEÑAS DE LA COMUNIDAD */}
      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center max-w-xl mx-auto mb-10">
          <span className="text-xs font-bold text-[#24AB58] uppercase tracking-wider">
            Testimonios Reales
          </span>
          <h2 className="font-carter text-2xl sm:text-3xl text-[#181818] mt-1">
            La Música Transforma el Barrio
          </h2>
          <p className="text-sm text-neutral-700">
            Opiniones de nuestros alumnos, familias y egresados.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          {resenas.map((resena) => (
            <div
              key={resena.id}
              className="bg-[#FFFDF5] border-2 border-[#181818] rounded-2xl p-6 shadow-sm flex flex-col justify-between"
            >
              <div className="space-y-3">
                <div className="flex items-center gap-1 text-[#F3D84F]">
                  {[...Array(resena.calificacion)].map((_, i) => (
                    <Star key={i} className="w-4 h-4 fill-[#F3D84F]" />
                  ))}
                </div>
                <p className="text-xs text-neutral-800 italic leading-relaxed">
                  "{resena.comentario}"
                </p>
              </div>

              <div className="pt-4 mt-4 border-t border-neutral-200">
                <p className="font-bold text-sm text-[#181818]">{resena.autor}</p>
                <p className="text-[11px] text-[#24AB58] font-semibold">
                  {resena.rol} • {resena.tallerNombre}
                </p>
              </div>
            </div>
          ))}
        </div>
      </section>

      {/* BANNER CTA FINAL DE INSCRIPCIÓN */}
      <section className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="bg-[#F3D84F] border-4 border-[#181818] rounded-3xl p-8 sm:p-12 text-center space-y-4 shadow-xl pattern-dots-dark">
          <h2 className="font-carter text-2xl sm:text-4xl text-[#181818]">
            ¿Listo para empezar tu viaje musical?
          </h2>
          <p className="max-w-xl mx-auto text-sm sm:text-base text-neutral-900 font-medium">
            Completa la inscripción online en 2 minutos. Si no tenés instrumento, marcá la opción de préstamo solidario y te lo reservamos para tu primera clase.
          </p>
          <div className="pt-2">
            <button
              onClick={() => onCambiarSeccion('inscripcion')}
              className="px-8 py-3.5 bg-[#181818] hover:bg-neutral-800 text-[#F3D84F] font-carter text-base rounded-xl shadow-lg transition-transform transform hover:scale-105"
            >
              Completar Ficha de Inscripción 2026
            </button>
          </div>
        </div>
      </section>
    </div>
  );
};
