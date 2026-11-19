import React, { useState } from 'react';
import { Taller, Docente, InstrumentoTipo, NivelTipo } from '../types';
import { Search, Filter, Clock, Calendar, User, CheckCircle, AlertCircle, ArrowRight, BookOpen, Music, X } from 'lucide-react';

interface TalleresSectionProps {
  talleres: Taller[];
  docentes: Docente[];
  onSeleccionarTallerParaInscripcion: (tallerId: number) => void;
  onVerPerfilDocente: (docenteId: number) => void;
}

export const TalleresSection: React.FC<TalleresSectionProps> = ({
  talleres,
  docentes,
  onSeleccionarTallerParaInscripcion,
  onVerPerfilDocente
}) => {
  const [busqueda, setBusqueda] = useState('');
  const [instrumentoFiltro, setInstrumentoFiltro] = useState<string>('todos');
  const [nivelFiltro, setNivelFiltro] = useState<string>('todos');
  const [tallerModalDetalle, setTallerModalDetalle] = useState<Taller | null>(null);

  const instrumentosDisponibles: string[] = [
    'todos',
    'Guitarra',
    'Violín',
    'Flauta',
    'Batería',
    'Piano',
    'Teclado',
    'Canto Popular',
    'Ensamble'
  ];

  const nivelesDisponibles: string[] = [
    'todos',
    'Inicial / Sin experiencia',
    'Intermedio',
    'Ensamble Abierto'
  ];

  const talleresFiltrados = talleres.filter((taller) => {
    const coincideBusqueda =
      taller.nombre.toLowerCase().includes(busqueda.toLowerCase()) ||
      taller.descripcion.toLowerCase().includes(busqueda.toLowerCase()) ||
      taller.instrumento.toLowerCase().includes(busqueda.toLowerCase());

    const coincideInstrumento =
      instrumentoFiltro === 'todos' || taller.instrumento === instrumentoFiltro;

    const coincideNivel =
      nivelFiltro === 'todos' || taller.nivel.includes(nivelFiltro);

    return coincideBusqueda && coincideInstrumento && coincideNivel;
  });

  const getDocente = (docenteId: number) => {
    return docentes.find(d => d.id === docenteId);
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">
      {/* CABECERA */}
      <div className="text-center max-w-3xl mx-auto space-y-3">
        <span className="text-xs font-bold text-[#24AB58] uppercase tracking-wider">
          Propuesta Académica 2026
        </span>
        <h1 className="font-carter text-3xl sm:text-5xl text-[#181818]">
          Nuestros Talleres Instrumentales
        </h1>
        <p className="text-sm sm:text-base text-neutral-700 leading-relaxed font-sans">
          Clases grupales reducidas, método práctico desde el primer día y acceso al Banco Solidario de Instrumentos.
        </p>
      </div>

      {/* FILTROS Y BÚSQUEDA */}
      <div className="bg-[#FFFDF5] border-2 border-[#181818] rounded-2xl p-5 shadow-sm space-y-4">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
          {/* Buscador de texto */}
          <div className="relative">
            <Search className="w-4 h-4 text-neutral-400 absolute left-3 top-3" />
            <input
              type="text"
              placeholder="Buscar por instrumento, tema o palabra..."
              value={busqueda}
              onChange={(e) => setBusqueda(e.target.value)}
              className="w-full pl-9 pr-3 py-2 bg-[#FFFAE6] border border-neutral-300 rounded-xl text-xs sm:text-sm text-[#181818] focus:outline-none focus:border-[#24AB58]"
            />
          </div>

          {/* Filtro por Instrumento */}
          <div className="flex items-center gap-2">
            <Filter className="w-4 h-4 text-neutral-500 shrink-0" />
            <select
              value={instrumentoFiltro}
              onChange={(e) => setInstrumentoFiltro(e.target.value)}
              className="w-full py-2 px-3 bg-[#FFFAE6] border border-neutral-300 rounded-xl text-xs sm:text-sm text-[#181818] focus:outline-none focus:border-[#24AB58]"
            >
              <option value="todos">Todos los instrumentos</option>
              {instrumentosDisponibles.filter(i => i !== 'todos').map((inst) => (
                <option key={inst} value={inst}>
                  {inst}
                </option>
              ))}
            </select>
          </div>

          {/* Filtro por Nivel */}
          <div>
            <select
              value={nivelFiltro}
              onChange={(e) => setNivelFiltro(e.target.value)}
              className="w-full py-2 px-3 bg-[#FFFAE6] border border-neutral-300 rounded-xl text-xs sm:text-sm text-[#181818] focus:outline-none focus:border-[#24AB58]"
            >
              <option value="todos">Todos los niveles</option>
              {nivelesDisponibles.filter(n => n !== 'todos').map((nivel) => (
                <option key={nivel} value={nivel}>
                  {nivel}
                </option>
              ))}
            </select>
          </div>
        </div>

        {/* Chips de instrumentos para filtrado rápido */}
        <div className="flex flex-wrap items-center gap-1.5 pt-2 border-t border-neutral-200">
          <span className="text-[11px] font-bold text-neutral-500 uppercase mr-1">Rápido:</span>
          {instrumentosDisponibles.map((inst) => (
            <button
              key={inst}
              onClick={() => setInstrumentoFiltro(inst)}
              className={`px-2.5 py-1 rounded-full text-xs font-semibold transition-all ${
                instrumentoFiltro === inst
                  ? 'bg-[#181818] text-[#F3D84F]'
                  : 'bg-[#FFFAE6] text-neutral-700 hover:bg-neutral-200 border border-neutral-300'
              }`}
            >
              {inst === 'todos' ? 'Mostrar Todos' : inst}
            </button>
          ))}
        </div>
      </div>

      {/* RESULTADOS DE TALLERES */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        {talleresFiltrados.map((taller) => {
          const docente = getDocente(taller.docenteId);
          const tieneVacantes = taller.vacantesDisponibles > 0;

          return (
            <div
              key={taller.id}
              className="bg-[#FFFDF5] border-2 border-[#181818] rounded-2xl overflow-hidden shadow-md flex flex-col justify-between hover:shadow-xl transition-all"
            >
              <div>
                {/* Imagen del taller con badges */}
                <div className="relative h-48 overflow-hidden bg-neutral-900">
                  <img
                    src={taller.imagenUrl}
                    alt={taller.nombre}
                    className="w-full h-full object-cover transform hover:scale-105 transition-transform duration-500"
                    referrerPolicy="no-referrer"
                  />
                  <div className="absolute top-3 left-3 bg-[#F3D84F] text-[#181818] px-3 py-1 rounded-full text-xs font-carter shadow">
                    {taller.instrumento}
                  </div>
                  <div className={`absolute top-3 right-3 px-2.5 py-0.5 rounded-full text-[11px] font-bold shadow ${
                    tieneVacantes ? 'bg-[#24AB58] text-[#FFFAE6]' : 'bg-red-600 text-white'
                  }`}>
                    {tieneVacantes ? `${taller.vacantesDisponibles} vacantes libres` : 'Cupo Completo'}
                  </div>
                </div>

                {/* Contenido del taller */}
                <div className="p-6 space-y-4">
                  <div>
                    <span className="text-[11px] font-bold text-neutral-500 uppercase tracking-wider">
                      Nivel: {taller.nivel} • Edad: +{taller.edadMinima} años
                    </span>
                    <h3 className="font-carter text-xl text-[#181818] mt-1">
                      {taller.nombre}
                    </h3>
                  </div>

                  <p className="text-xs text-neutral-700 leading-relaxed line-clamp-3">
                    {taller.descripcion}
                  </p>

                  <div className="space-y-2 text-xs text-neutral-700 pt-3 border-t border-neutral-200">
                    <div className="flex items-center gap-2">
                      <Calendar className="w-4 h-4 text-[#24AB58] shrink-0" />
                      <span>{taller.diaSemana}</span>
                    </div>

                    <div className="flex items-center gap-2">
                      <Clock className="w-4 h-4 text-[#24AB58] shrink-0" />
                      <span>{taller.horario} ({taller.modalidad})</span>
                    </div>

                    {docente && (
                      <div className="flex items-center justify-between pt-1">
                        <div className="flex items-center gap-2">
                          <User className="w-4 h-4 text-[#181818] shrink-0" />
                          <span className="font-bold">Prof. {docente.nombre} {docente.apellido}</span>
                        </div>
                        <button
                          onClick={() => onVerPerfilDocente(docente.id)}
                          className="text-[11px] text-[#24AB58] underline hover:text-[#181818]"
                        >
                          Ver perfil
                        </button>
                      </div>
                    )}

                    <div className="p-2.5 rounded-xl bg-[#FFFAE6] border border-neutral-200 flex items-center justify-between">
                      <span className="text-[11px] text-neutral-600">
                        Préstamo de instrumento:
                      </span>
                      <span className="font-bold text-xs text-[#24AB58]">
                        {taller.instrumentosPrestamoDisponibles > 0 ? `${taller.instrumentosPrestamoDisponibles} en stock` : 'Consultar'}
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              {/* Botones de acción y arancel */}
              <div className="p-6 pt-0 space-y-3">
                <div className="flex items-center justify-between pt-2 border-t border-neutral-200">
                  <span className="text-xs text-neutral-500">Arancel Mensual:</span>
                  <span className="font-carter text-lg text-[#181818]">
                    ${taller.arancelMensual.toLocaleString('es-AR')}
                  </span>
                </div>

                <div className="grid grid-cols-2 gap-2">
                  <button
                    onClick={() => setTallerModalDetalle(taller)}
                    className="py-2.5 px-3 rounded-xl border border-neutral-300 hover:border-neutral-500 text-xs font-bold text-neutral-800 transition-colors"
                  >
                    Detalles
                  </button>

                  <button
                    onClick={() => onSeleccionarTallerParaInscripcion(taller.id)}
                    className="py-2.5 px-3 rounded-xl bg-[#24AB58] hover:bg-[#1e8f49] text-[#FFFAE6] font-carter text-xs transition-colors flex items-center justify-center gap-1 shadow"
                  >
                    <span>Inscribirme</span>
                    <ArrowRight className="w-3.5 h-3.5" />
                  </button>
                </div>
              </div>
            </div>
          );
        })}
      </div>

      {talleresFiltrados.length === 0 && (
        <div className="text-center py-16 bg-[#FFFDF5] border-2 border-dashed border-neutral-300 rounded-2xl">
          <BookOpen className="w-12 h-12 text-neutral-400 mx-auto mb-3" />
          <h3 className="font-carter text-xl text-neutral-700">No encontramos talleres con ese criterio</h3>
          <p className="text-xs text-neutral-500 mt-1">Probá cambiando el filtro o la búsqueda por texto.</p>
          <button
            onClick={() => {
              setBusqueda('');
              setInstrumentoFiltro('todos');
              setNivelFiltro('todos');
            }}
            className="mt-4 px-4 py-2 bg-[#F3D84F] text-[#181818] font-bold text-xs rounded-lg"
          >
            Limpiar Filtros
          </button>
        </div>
      )}

      {/* MODAL DETALLES DEL TALLER */}
      {tallerModalDetalle && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm animate-fadeIn">
          <div className="bg-[#FFFAE6] border-3 border-[#181818] rounded-2xl max-w-xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div className="flex justify-between items-start">
              <div>
                <span className="px-2.5 py-0.5 rounded-full bg-[#F3D84F] text-[#181818] text-xs font-carter">
                  {tallerModalDetalle.instrumento}
                </span>
                <h3 className="font-carter text-2xl text-[#181818] mt-2">
                  {tallerModalDetalle.nombre}
                </h3>
              </div>
              <button
                onClick={() => setTallerModalDetalle(null)}
                className="p-1.5 rounded-lg text-neutral-600 hover:text-black hover:bg-neutral-200"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            <p className="text-sm text-neutral-700 leading-relaxed">
              {tallerModalDetalle.descripcion}
            </p>

            <div className="bg-[#FFFDF5] border border-neutral-300 rounded-xl p-4 space-y-2 text-xs text-neutral-700">
              <p><strong>Nivel requerido:</strong> {tallerModalDetalle.nivel}</p>
              <p><strong>Edad mínima:</strong> A partir de {tallerModalDetalle.edadMinima} años sin límite superior.</p>
              <p><strong>Días y horarios:</strong> {tallerModalDetalle.diaSemana} de {tallerModalDetalle.horario}</p>
              <p><strong>Modalidad:</strong> {tallerModalDetalle.modalidad}</p>
              <p><strong>Requisitos:</strong> {tallerModalDetalle.requisitos}</p>
              <p><strong>Banco de Instrumentos:</strong> Contamos con {tallerModalDetalle.instrumentosPrestamoDisponibles} instrumentos disponibles para préstamo gratuito a alumnos que no posean el propio.</p>
            </div>

            <div className="flex items-center justify-between pt-2">
              <div>
                <span className="text-xs text-neutral-500">Arancel mensual solidario:</span>
                <p className="font-carter text-xl text-[#181818]">
                  ${tallerModalDetalle.arancelMensual.toLocaleString('es-AR')}
                </p>
              </div>

              <div className="flex gap-2">
                <button
                  onClick={() => setTallerModalDetalle(null)}
                  className="px-4 py-2 text-xs font-bold text-neutral-700 hover:bg-neutral-200 rounded-xl"
                >
                  Volver
                </button>
                <button
                  onClick={() => {
                    const id = tallerModalDetalle.id;
                    setTallerModalDetalle(null);
                    onSeleccionarTallerParaInscripcion(id);
                  }}
                  className="px-5 py-2.5 bg-[#24AB58] hover:bg-[#1e8f49] text-[#FFFAE6] font-carter text-xs rounded-xl shadow"
                >
                  Inscribirme Ahora
                </button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};
