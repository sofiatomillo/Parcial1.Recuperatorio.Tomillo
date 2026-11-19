import React from 'react';
import { SeccionActiva } from '../types';
import { Music, MapPin, Phone, Mail, Clock, Heart, ArrowUp } from 'lucide-react';

interface FooterProps {
  onCambiarSeccion: (seccion: SeccionActiva) => void;
  onAbrirSqlModal: () => void;
}

export const Footer: React.FC<FooterProps> = ({ onCambiarSeccion, onAbrirSqlModal }) => {
  const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  return (
    <footer className="bg-[#181818] text-[#FFFAE6] pt-12 pb-8 border-t-4 border-[#24AB58]">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
          {/* Columna 1: Identidad y Misión */}
          <div className="space-y-4">
            <div className="flex items-center gap-3">
              <div className="w-10 h-10 rounded-xl bg-[#F3D84F] flex items-center justify-center text-[#181818]">
                <Music className="w-6 h-6" />
              </div>
              <span className="font-carter text-2xl text-[#F3D84F]">A Todo Ritmo</span>
            </div>
            <p className="text-sm text-neutral-300 leading-relaxed">
              Escuela de música barrial e inclusiva. Creemos que el arte y la música pertenecen a todas las personas. Aprende guitarra, violín, flauta, batería, piano y canto sin barreras económicas.
            </p>
            <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#24AB58]/20 border border-[#24AB58] text-xs text-[#24AB58] font-bold">
              <Heart className="w-3.5 h-3.5 fill-[#24AB58]" />
              <span>Banco Solidario de Instrumentos</span>
            </div>
          </div>

          {/* Columna 2: Navegación Rápida */}
          <div>
            <h4 className="font-carter text-lg text-[#F3D84F] mb-4">Navegación</h4>
            <ul className="space-y-2 text-sm">
              <li>
                <button
                  onClick={() => onCambiarSeccion('home')}
                  className="text-neutral-300 hover:text-[#F3D84F] transition-colors"
                >
                  Inicio
                </button>
              </li>
              <li>
                <button
                  onClick={() => onCambiarSeccion('nosotros')}
                  className="text-neutral-300 hover:text-[#F3D84F] transition-colors"
                >
                  Sobre la Escuela
                </button>
              </li>
              <li>
                <button
                  onClick={() => onCambiarSeccion('talleres')}
                  className="text-neutral-300 hover:text-[#F3D84F] transition-colors"
                >
                  Talleres e Instrumentos
                </button>
              </li>
              <li>
                <button
                  onClick={() => onCambiarSeccion('eventos')}
                  className="text-neutral-300 hover:text-[#F3D84F] transition-colors"
                >
                  Eventos y Conciertos
                </button>
              </li>
              <li>
                <button
                  onClick={() => onCambiarSeccion('docentes')}
                  className="text-neutral-300 hover:text-[#F3D84F] transition-colors"
                >
                  Cuerpo Docente
                </button>
              </li>
              <li>
                <button
                  onClick={() => onCambiarSeccion('inscripcion')}
                  className="text-[#24AB58] font-bold hover:underline"
                >
                  Inscripciones Online 2026
                </button>
              </li>
              <li>
                <button
                  onClick={() => onCambiarSeccion('consultas')}
                  className="text-neutral-300 hover:text-[#F3D84F] transition-colors"
                >
                  Preguntas Frecuentes & Consultas
                </button>
              </li>
            </ul>
          </div>

          {/* Columna 3: Sedes y Contacto */}
          <div>
            <h4 className="font-carter text-lg text-[#F3D84F] mb-4">Nuestras Sedes</h4>
            <div className="space-y-3 text-sm text-neutral-300">
              <div>
                <p className="font-bold text-[#FFFAE6] flex items-center gap-1.5">
                  <MapPin className="w-4 h-4 text-[#24AB58]" /> Sede San Telmo:
                </p>
                <p className="text-xs text-neutral-400 pl-5.5">Defensa 842, CABA</p>
                <p className="text-xs text-neutral-400 pl-5.5">Tel: (011) 4361-9820</p>
              </div>

              <div>
                <p className="font-bold text-[#FFFAE6] flex items-center gap-1.5">
                  <MapPin className="w-4 h-4 text-[#24AB58]" /> Anexo Almagro:
                </p>
                <p className="text-xs text-neutral-400 pl-5.5">Av. Corrientes 4120, CABA</p>
                <p className="text-xs text-neutral-400 pl-5.5">Tel: (011) 4862-5510</p>
              </div>

              <div className="pt-2 border-t border-neutral-800">
                <p className="flex items-center gap-1.5 text-xs text-neutral-400">
                  <Clock className="w-3.5 h-3.5 text-[#F3D84F]" /> Lun a Vie 14:00 - 21:00 hs | Sáb 09:30 - 19:00 hs
                </p>
                <p className="flex items-center gap-1.5 text-xs text-neutral-400 mt-1">
                  <Mail className="w-3.5 h-3.5 text-[#F3D84F]" /> contacto@atodoritmo.edu.ar
                </p>
              </div>
            </div>
          </div>

          {/* Columna 4: Parcial ISEC y Lógica de Datos */}
          <div className="p-4 rounded-xl bg-neutral-900 border border-neutral-800 space-y-3">
            <h4 className="font-carter text-sm text-[#F3D84F] uppercase tracking-wider">
              Parcial 2 - ISEC
            </h4>
            <p className="text-xs text-neutral-400 leading-relaxed">
              Programación / Desarrollo Web<br />
              <strong className="text-neutral-200">Tomillo Sofía</strong> — 3er Año — División A — TM<br />
              <span className="text-[#24AB58] font-semibold">Caso 3: Escuela de Música "A Todo Ritmo"</span>
            </p>
            <div className="pt-2">
              <button
                onClick={onAbrirSqlModal}
                className="w-full py-2 px-3 rounded-lg bg-[#F3D84F] hover:bg-[#e2c638] text-[#181818] font-bold text-xs flex items-center justify-center gap-1.5 transition-colors shadow-sm"
              >
                <span>Inspeccionar Estructura SQL (.sql)</span>
              </button>
            </div>
          </div>
        </div>

        {/* Barra de copyright y subida */}
        <div className="pt-8 border-t border-neutral-800 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-neutral-400">
          <p>© {new Date().getFullYear()} Escuela de Música "A Todo Ritmo". Todos los derechos reservados.</p>
          <div className="flex items-center gap-4">
            <button
              onClick={onAbrirSqlModal}
              className="hover:text-[#F3D84F] underline cursor-pointer"
            >
              Esquema de Consultas SQL
            </button>
            <button
              onClick={scrollToTop}
              className="p-2 rounded-lg bg-neutral-800 hover:bg-[#24AB58] text-[#FFFAE6] transition-colors"
              title="Volver arriba"
              aria-label="Volver arriba"
            >
              <ArrowUp className="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </footer>
  );
};
