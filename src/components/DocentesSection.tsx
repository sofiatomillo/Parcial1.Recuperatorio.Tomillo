import React, { useState } from 'react';
import { Docente, Taller, SeccionActiva } from '../types';
import { Award, Mail, Phone, BookOpen, ChevronDown, ChevronUp, ArrowRight, Music, CheckCircle2 } from 'lucide-react';

interface DocentesSectionProps {
  docentes: Docente[];
  talleres: Taller[];
  onSeleccionarTallerParaInscripcion: (tallerId: number) => void;
  onCambiarSeccion: (seccion: SeccionActiva) => void;
  docenteFocoId?: number | null;
}

export const DocentesSection: React.FC<DocentesSectionProps> = ({
  docentes,
  talleres,
  onSeleccionarTallerParaInscripcion,
  onCambiarSeccion,
  docenteFocoId
}) => {
  const [docenteExpandidoId, setDocenteExpandidoId] = useState<number | null>(
    docenteFocoId || null
  );

  React.useEffect(() => {
    if (docenteFocoId) {
      setDocenteExpandidoId(docenteFocoId);
      const element = document.getElementById(`docente-card-${docenteFocoId}`);
      if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    }
  }, [docenteFocoId]);

  const toggleExpandir = (id: number) => {
    setDocenteExpandidoId(docenteExpandidoId === id ? null : id);
  };

  const getTalleresDeDocente = (docenteId: number) => {
    return talleres.filter(t => t.docenteId === docenteId);
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12">
      {/* CABECERA */}
      <div className="text-center max-w-3xl mx-auto space-y-3">
        <span className="text-xs font-bold text-[#24AB58] uppercase tracking-wider">
          Equipo Pedagógico & Artístico
        </span>
        <h1 className="font-carter text-3xl sm:text-5xl text-[#181818]">
          Nuestro Cuerpo Docente
        </h1>
        <p className="text-sm sm:text-base text-neutral-700 leading-relaxed font-sans">
          Músicos profesionales y pedagogos graduados de conservatorios nacionales (Manuel de Falla, UNA, Castro) con amplia trayectoria escénica y vocación social.
        </p>
      </div>

      {/* GRILLA DE DOCENTES */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        {docentes.map((docente) => {
          const talleresDocente = getTalleresDeDocente(docente.id);
          const estaExpandido = docenteExpandidoId === docente.id;

          return (
            <div
              key={docente.id}
              id={`docente-card-${docente.id}`}
              className={`bg-[#FFFDF5] border-2 rounded-3xl overflow-hidden shadow-md transition-all flex flex-col justify-between ${
                estaExpandido ? 'border-[#24AB58] ring-2 ring-[#24AB58]/30 shadow-xl' : 'border-[#181818]'
              }`}
            >
              <div>
                {/* Foto y encabezado */}
                <div className="relative h-60 overflow-hidden bg-neutral-900">
                  <img
                    src={docente.fotoUrl}
                    alt={`${docente.nombre} ${docente.apellido}`}
                    className="w-full h-full object-cover transform hover:scale-105 transition-transform duration-500"
                    referrerPolicy="no-referrer"
                  />
                  <div className="absolute top-3 left-3 bg-[#F3D84F] text-[#181818] px-3 py-0.5 rounded-full text-xs font-carter shadow">
                    {docente.instrumentoPrincipal}
                  </div>
                  <div className="absolute top-3 right-3 bg-[#181818]/80 text-[#FFFAE6] px-2 py-0.5 rounded-md text-[11px] font-mono">
                    ID #{docente.id}
                  </div>
                </div>

                {/* Datos básicos */}
                <div className="p-6 space-y-3">
                  <div>
                    <h3 className="font-carter text-2xl text-[#181818]">
                      {docente.nombre} {docente.apellido}
                    </h3>
                    <p className="text-xs font-bold text-[#24AB58] mt-0.5">
                      {docente.especialidad}
                    </p>
                  </div>

                  <p className="text-xs text-neutral-700 leading-relaxed">
                    {docente.biografia}
                  </p>

                  {/* Talleres a cargo */}
                  <div className="pt-2">
                    <span className="text-[11px] font-bold text-neutral-600 uppercase tracking-wider block mb-1.5">
                      Talleres que dicta:
                    </span>
                    <div className="space-y-1.5">
                      {talleresDocente.map((t) => (
                        <div
                          key={t.id}
                          className="p-2 rounded-xl bg-[#FFFAE6] border border-neutral-300 flex items-center justify-between text-xs"
                        >
                          <span className="font-semibold text-neutral-800 truncate pr-2">
                            {t.nombre}
                          </span>
                          <button
                            onClick={() => onSeleccionarTallerParaInscripcion(t.id)}
                            className="px-2 py-1 bg-[#24AB58] text-[#FFFAE6] hover:bg-[#1e8f49] text-[10px] font-bold rounded-lg shrink-0 transition-colors"
                          >
                            Inscribirme
                          </button>
                        </div>
                      ))}
                    </div>
                  </div>

                  {/* Botón expandir CV / Trayectoria */}
                  <div className="pt-2">
                    <button
                      onClick={() => toggleExpandir(docente.id)}
                      className="w-full py-2 px-3 rounded-xl bg-[#FFFAE6] border border-neutral-300 hover:border-neutral-500 text-xs font-bold text-neutral-800 flex items-center justify-between transition-colors"
                    >
                      <span className="flex items-center gap-1.5">
                        <Award className="w-3.5 h-3.5 text-[#24AB58]" />
                        <span>{estaExpandido ? 'Ocultar CV & Trayectoria' : 'Ver CV & Trayectoria Académica'}</span>
                      </span>
                      {estaExpandido ? <ChevronUp className="w-4 h-4" /> : <ChevronDown className="w-4 h-4" />}
                    </button>
                  </div>

                  {/* Contenido expandible de CV */}
                  {estaExpandido && (
                    <div className="p-3.5 rounded-xl bg-amber-50/60 border border-amber-200 text-xs text-neutral-800 space-y-2 animate-fadeIn">
                      <p className="font-bold text-[#181818] flex items-center gap-1">
                        <CheckCircle2 className="w-3.5 h-3.5 text-[#24AB58]" />
                        Trayectoria y Formación:
                      </p>
                      <p className="leading-relaxed text-neutral-700">
                        {docente.trayectoriaCv}
                      </p>
                      <div className="pt-2 border-t border-amber-200/80 text-[11px] text-neutral-600 space-y-1">
                        <p className="flex items-center gap-1">
                          <Mail className="w-3 h-3 text-[#181818]" /> {docente.email}
                        </p>
                        <p className="flex items-center gap-1">
                          <Phone className="w-3 h-3 text-[#181818]" /> {docente.telefono}
                        </p>
                      </div>
                    </div>
                  )}
                </div>
              </div>
            </div>
          );
        })}
      </div>

      {/* BANNER COMPLEMENTARIO DE VALOR */}
      <div className="p-8 rounded-3xl bg-[#181818] text-[#FFFAE6] border-4 border-[#F3D84F] shadow-xl text-center space-y-4">
        <h3 className="font-carter text-2xl text-[#F3D84F]">
          ¿Querés formarte con nuestro equipo docente?
        </h3>
        <p className="max-w-xl mx-auto text-xs sm:text-sm text-neutral-300">
          Todas las comisiones tienen vacantes limitadas para asegurar un seguimiento cercano. Inscribite hoy y asegurá tu lugar para el ciclo 2026.
        </p>
        <div>
          <button
            onClick={() => onCambiarSeccion('inscripcion')}
            className="px-6 py-3 bg-[#24AB58] hover:bg-[#1e8f49] text-[#FFFAE6] font-carter text-sm rounded-xl transition-all shadow-md"
          >
            Comenzar Inscripción Online
          </button>
        </div>
      </div>
    </div>
  );
};
