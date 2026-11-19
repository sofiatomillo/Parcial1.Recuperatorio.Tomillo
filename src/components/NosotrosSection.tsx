import React from 'react';
import { SeccionActiva, Sede, Resena, ContenidoPagina } from '../types';
import { MapPin, Users, Heart, Phone, Mail, Clock, Bus, Train, ArrowRight } from 'lucide-react';

interface NosotrosSectionProps {
  sedes: Sede[];
  resenas: Resena[];
  contenidoPagina: ContenidoPagina;
  onCambiarSeccion: (seccion: SeccionActiva) => void;
}

export const NosotrosSection: React.FC<NosotrosSectionProps> = ({
  sedes,
  contenidoPagina,
  onCambiarSeccion
}) => {
  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-16">
      {/* CABECERA */}
      <div className="text-center max-w-3xl mx-auto space-y-3">
        <span className="text-xs font-bold text-[#24AB58] uppercase tracking-wider">
          Quiénes Somos
        </span>
        <h1 className="font-carter text-3xl sm:text-5xl text-[#181818]">
          {contenidoPagina.nosotrosTitulo}
        </h1>
        <p className="text-sm sm:text-base text-neutral-700 leading-relaxed font-sans">
          {contenidoPagina.nosotrosSubtitulo}
        </p>
      </div>

      {/* HISTORIA Y MISIÓN */}
      <section className="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
        <div className="space-y-4">
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#F3D84F]/30 border border-[#F3D84F] text-xs font-bold text-[#181818]">
            <Heart className="w-3.5 h-3.5 text-[#24AB58]" />
            <span>Nuestra Historia</span>
          </div>

          <h2 className="font-carter text-2xl sm:text-3xl text-[#181818] leading-tight">
            {contenidoPagina.nosotrosHistoriaTitulo}
          </h2>

          <p className="text-sm text-neutral-700 leading-relaxed">
            {contenidoPagina.nosotrosHistoriaP1}
          </p>

          <p className="text-sm text-neutral-700 leading-relaxed">
            {contenidoPagina.nosotrosHistoriaP2}
          </p>

          <div className="grid grid-cols-3 gap-4 pt-2">
            <div className="p-4 bg-[#FFFDF5] border border-neutral-300 rounded-xl text-center">
              <span className="font-carter text-2xl text-[#24AB58]">+200</span>
              <p className="text-[11px] text-neutral-600 font-bold uppercase mt-1">Alumnos Activos</p>
            </div>
            <div className="p-4 bg-[#FFFDF5] border border-neutral-300 rounded-xl text-center">
              <span className="font-carter text-2xl text-[#181818]">+60</span>
              <p className="text-[11px] text-neutral-600 font-bold uppercase mt-1">Instrumentos en Préstamo</p>
            </div>
            <div className="p-4 bg-[#FFFDF5] border border-neutral-300 rounded-xl text-center">
              <span className="font-carter text-2xl text-[#24AB58]">100%</span>
              <p className="text-[11px] text-neutral-600 font-bold uppercase mt-1">Sin Examen Excluyente</p>
            </div>
          </div>
        </div>

        <div className="relative">
          <div className="rounded-3xl overflow-hidden border-4 border-[#181818] shadow-2xl">
            <img
              src={contenidoPagina.nosotrosHistoriaImagen}
              alt="Alumnos de A Todo Ritmo"
              className="w-full h-96 object-cover"
              referrerPolicy="no-referrer"
            />
          </div>
          <div className="absolute -bottom-6 -left-6 bg-[#F3D84F] border-2 border-[#181818] rounded-2xl p-4 shadow-lg max-w-xs">
            <p className="font-carter text-xs text-[#181818]">
              {contenidoPagina.nosotrosCita}
            </p>
          </div>
        </div>
      </section>

      {/* CARRERAS Y TRAYECTOS FORMATIVOS */}
      <section className="bg-[#FFFDF5] border-2 border-[#181818] rounded-3xl p-6 sm:p-10 space-y-8 shadow-md">
        <div className="text-center max-w-2xl mx-auto">
          <span className="text-xs font-bold text-[#24AB58] uppercase tracking-wider">
            Propuesta Pedagógica
          </span>
          <h2 className="font-carter text-2xl sm:text-3xl text-[#181818] mt-1">
            Trayectos Formativos y Niveles
          </h2>
          <p className="text-xs sm:text-sm text-neutral-700 mt-2">
            Nuestros programas se adaptan al ritmo de aprendizaje de cada etapa de la vida.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          {/* Trayecto 1 */}
          <div className="p-6 rounded-2xl bg-[#FFFAE6] border-2 border-neutral-300 space-y-3">
            <span className="px-2.5 py-1 rounded bg-[#24AB58] text-[#FFFAE6] text-xs font-carter">
              7 a 11 Años
            </span>
            <h3 className="font-carter text-lg text-[#181818]">Iniciación Musical Infantil</h3>
            <p className="text-xs text-neutral-700 leading-relaxed">
              Juego, exploración tímbrica, canciones infantiles y primeras notas en violín, teclado o percusión mediante métodos sensoriales y amables.
            </p>
          </div>

          {/* Trayecto 2 */}
          <div className="p-6 rounded-2xl bg-[#FFFAE6] border-2 border-neutral-300 space-y-3">
            <span className="px-2.5 py-1 rounded bg-[#F3D84F] text-[#181818] text-xs font-carter">
              12 Años en Adelante
            </span>
            <h3 className="font-carter text-lg text-[#181818]">Trayecto Joven y Adultos</h3>
            <p className="text-xs text-neutral-700 leading-relaxed">
              Formación instrumental intensiva (Guitarra, Batería, Flauta, Canto, Piano). Práctica de repertorio popular, armonía práctica e improvisación.
            </p>
          </div>

          {/* Trayecto 3 */}
          <div className="p-6 rounded-2xl bg-[#FFFAE6] border-2 border-neutral-300 space-y-3">
            <span className="px-2.5 py-1 rounded bg-[#181818] text-[#F3D84F] text-xs font-carter">
              Abierto a la Comunidad
            </span>
            <h3 className="font-carter text-lg text-[#181818]">Ensambles Comunitarios</h3>
            <p className="text-xs text-neutral-700 leading-relaxed">
              El espacio de práctica orquestal donde confluyen todos los talleres para montar repertorios de rock nacional, cumbia, tango y folklore para tocar en vivo.
            </p>
          </div>
        </div>
      </section>

      {/* NUESTRAS SEDES CON DETALLES DE ACCESO */}
      <section className="space-y-6">
        <div className="text-center max-w-xl mx-auto">
          <span className="text-xs font-bold text-[#24AB58] uppercase tracking-wider">
            Ubicaciones
          </span>
          <h2 className="font-carter text-2xl sm:text-3xl text-[#181818] mt-1">
            Conocé Nuestras Sedes Físicas
          </h2>
          <p className="text-xs sm:text-sm text-neutral-700">
            Salas equipadas, insonorizadas y con instrumentos disponibles para estudiar.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
          {sedes.map((sede) => (
            <div
              key={sede.id}
              className="bg-[#FFFDF5] border-2 border-[#181818] rounded-2xl p-6 sm:p-8 space-y-5 shadow-md"
            >
              <div className="flex items-center justify-between pb-3 border-b border-neutral-200">
                <div>
                  <h3 className="font-carter text-xl text-[#181818]">{sede.nombre}</h3>
                  <p className="text-xs text-[#24AB58] font-bold flex items-center gap-1 mt-0.5">
                    <MapPin className="w-3.5 h-3.5" /> {sede.direccion} ({sede.barrio})
                  </p>
                </div>
                <span className="w-8 h-8 rounded-full bg-[#F3D84F] text-[#181818] flex items-center justify-center font-bold text-xs">
                  {sede.id}
                </span>
              </div>

              <div className="space-y-2.5 text-xs text-neutral-700">
                <div className="flex items-start gap-2">
                  <Clock className="w-4 h-4 text-[#181818] shrink-0 mt-0.5" />
                  <div>
                    <span className="font-bold">Horarios:</span> {sede.horariosAtencion}
                  </div>
                </div>

                <div className="flex items-start gap-2">
                  <Phone className="w-4 h-4 text-[#181818] shrink-0 mt-0.5" />
                  <div>
                    <span className="font-bold">Teléfono:</span> {sede.telefono}
                  </div>
                </div>

                <div className="flex items-start gap-2">
                  <Mail className="w-4 h-4 text-[#181818] shrink-0 mt-0.5" />
                  <div>
                    <span className="font-bold">Email:</span> {sede.email}
                  </div>
                </div>

                <div className="pt-2 border-t border-neutral-200 space-y-1.5">
                  <div className="flex items-start gap-2">
                    <Bus className="w-4 h-4 text-[#24AB58] shrink-0 mt-0.5" />
                    <div>
                      <span className="font-bold">Líneas de colectivo:</span> {sede.colectivos}
                    </div>
                  </div>
                  <div className="flex items-start gap-2">
                    <Train className="w-4 h-4 text-[#24AB58] shrink-0 mt-0.5" />
                    <div>
                      <span className="font-bold">Estaciones de Subte:</span> {sede.subte}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          ))}
        </div>
      </section>

      {/* CTA INTERMEDIO HACIA DOCENTES E INSCRIPCIÓN */}
      <div className="flex flex-col sm:flex-row justify-center items-center gap-4 pt-4">
        <button
          onClick={() => onCambiarSeccion('docentes')}
          className="w-full sm:w-auto px-6 py-3 bg-[#181818] hover:bg-neutral-800 text-[#FFFAE6] font-carter text-sm rounded-xl transition-colors flex items-center justify-center gap-2"
        >
          <Users className="w-4 h-4 text-[#F3D84F]" />
          <span>Conocer a los Docentes</span>
        </button>

        <button
          onClick={() => onCambiarSeccion('inscripcion')}
          className="w-full sm:w-auto px-6 py-3 bg-[#24AB58] hover:bg-[#1e8f49] text-[#FFFAE6] font-carter text-sm rounded-xl transition-colors flex items-center justify-center gap-2 shadow"
        >
          <span>Ir a la Ficha de Inscripción</span>
          <ArrowRight className="w-4 h-4" />
        </button>
      </div>
    </div>
  );
};
