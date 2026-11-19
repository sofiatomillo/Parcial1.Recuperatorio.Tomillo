import { useState } from 'react';
import { SeccionActiva, Taller, Evento } from './types';
import { DataService } from './services/dataService';
import { Navbar } from './components/Navbar';
import { Footer } from './components/Footer';
import { HomeSection } from './components/HomeSection';
import { NosotrosSection } from './components/NosotrosSection';
import { TalleresSection } from './components/TalleresSection';
import { EventosSection } from './components/EventosSection';
import { DocentesSection } from './components/DocentesSection';
import { InscripcionSection } from './components/InscripcionSection';
import { ConsultasSection } from './components/ConsultasSection';
import { SqlExplorerModal } from './components/SqlExplorerModal';
import { Terminal, Database } from 'lucide-react';

export default function App() {
  const [seccionActiva, setSeccionActiva] = useState<SeccionActiva>('home');
  const [talleres, setTalleres] = useState<Taller[]>(DataService.getTalleres());
  const [eventos, setEventos] = useState<Evento[]>(DataService.getEventos());
  const docentes = DataService.getDocentes();
  const sedes = DataService.getSedes();
  const resenas = DataService.getResenas();
  const preguntasFrecuentes = DataService.getPreguntasFrecuentes();

  // Estados para acciones cruzadas entre vistas
  const [tallerPreseleccionadoId, setTallerPreseleccionadoId] = useState<number | null>(null);
  const [docenteFocoId, setDocenteFocoId] = useState<number | null>(null);
  const [eventoPreseleccionado, setEventoPreseleccionado] = useState<Evento | null>(null);

  // Modal de exploración SQL
  const [sqlModalAbierto, setSqlModalAbierto] = useState(false);
  const [ultimaConsultaSql, setUltimaConsultaSql] = useState<string>('');
  const [mostrarToastSql, setMostrarToastSql] = useState(false);

  const actualizarTalleres = () => {
    setTalleres(DataService.getTalleres());
  };

  const actualizarEventos = () => {
    setEventos(DataService.getEventos());
  };

  const notificarSqlEjecutado = (sql: string) => {
    setUltimaConsultaSql(sql);
    setMostrarToastSql(true);
    setTimeout(() => {
      setMostrarToastSql(false);
    }, 6000);
  };

  // Navegación con preselección de taller
  const handleSeleccionarTallerParaInscripcion = (tallerId: number) => {
    setTallerPreseleccionadoId(tallerId);
    setSeccionActiva('inscripcion');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  // Navegación con preselección de docente
  const handleVerPerfilDocente = (docenteId: number) => {
    setDocenteFocoId(docenteId);
    setSeccionActiva('docentes');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  // Navegación para sacar entradas de evento
  const handleSeleccionarEventoParaEntrada = (evento: Evento) => {
    setEventoPreseleccionado(evento);
    setSeccionActiva('eventos');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  return (
    <div className="min-h-screen flex flex-col bg-[#FFFAE6] text-[#181818] font-sans selection:bg-[#F3D84F] selection:text-[#181818]">
      {/* NAVBAR */}
      <Navbar
        seccionActiva={seccionActiva}
        onCambiarSeccion={setSeccionActiva}
        onAbrirSqlModal={() => setSqlModalAbierto(true)}
      />

      {/* CONTENIDO PRINCIPAL SEGÚN SECCIÓN */}
      <main className="flex-1">
        {seccionActiva === 'home' && (
          <HomeSection
            talleres={talleres}
            eventos={eventos}
            resenas={resenas}
            onCambiarSeccion={setSeccionActiva}
            onSeleccionarTallerParaInscripcion={handleSeleccionarTallerParaInscripcion}
            onSeleccionarEventoParaEntrada={handleSeleccionarEventoParaEntrada}
          />
        )}

        {seccionActiva === 'nosotros' && (
          <NosotrosSection
            sedes={sedes}
            resenas={resenas}
            onCambiarSeccion={setSeccionActiva}
          />
        )}

        {seccionActiva === 'talleres' && (
          <TalleresSection
            talleres={talleres}
            docentes={docentes}
            onSeleccionarTallerParaInscripcion={handleSeleccionarTallerParaInscripcion}
            onVerPerfilDocente={handleVerPerfilDocente}
          />
        )}

        {seccionActiva === 'eventos' && (
          <EventosSection
            eventos={eventos}
            onActualizarEventos={actualizarEventos}
            onNotificarSqlEjecutado={notificarSqlEjecutado}
            eventoPreseleccionadoParaEntrada={eventoPreseleccionado}
            onLimpiarEventoPreseleccionado={() => setEventoPreseleccionado(null)}
          />
        )}

        {seccionActiva === 'docentes' && (
          <DocentesSection
            docentes={docentes}
            talleres={talleres}
            onSeleccionarTallerParaInscripcion={handleSeleccionarTallerParaInscripcion}
            onCambiarSeccion={setSeccionActiva}
            docenteFocoId={docenteFocoId}
          />
        )}

        {seccionActiva === 'inscripcion' && (
          <InscripcionSection
            talleres={talleres}
            sedes={sedes}
            tallerPreseleccionadoId={tallerPreseleccionadoId}
            onActualizarTalleres={actualizarTalleres}
            onNotificarSqlEjecutado={notificarSqlEjecutado}
            onCambiarSeccion={setSeccionActiva}
          />
        )}

        {seccionActiva === 'consultas' && (
          <ConsultasSection
            preguntasFrecuentes={preguntasFrecuentes}
            onNotificarSqlEjecutado={notificarSqlEjecutado}
          />
        )}
      </main>

      {/* TOAST FLOTANTE DE CONSULTA SQL EJECUTADA EN VIVO */}
      {mostrarToastSql && (
        <div className="fixed bottom-5 right-5 z-40 max-w-md bg-[#181818] border-2 border-[#24AB58] rounded-2xl p-4 shadow-2xl text-[#FFFAE6] animate-bounce-short">
          <div className="flex items-start justify-between gap-3">
            <div className="flex items-center gap-2 text-[#24AB58]">
              <Terminal className="w-5 h-5 shrink-0" />
              <span className="font-mono text-xs font-bold uppercase tracking-wider">
                Operación SQL Ejecutada en Vivo
              </span>
            </div>
            <button
              onClick={() => setMostrarToastSql(false)}
              className="text-neutral-400 hover:text-white text-xs"
            >
              ✕
            </button>
          </div>
          <p className="text-[11px] text-neutral-300 mt-1 font-mono truncate">
            {ultimaConsultaSql.split('\n')[2] || ultimaConsultaSql.split('\n')[1] || 'Transacción completada'}
          </p>
          <div className="mt-2.5 flex justify-end">
            <button
              onClick={() => {
                setMostrarToastSql(false);
                setSqlModalAbierto(true);
              }}
              className="px-3 py-1 bg-[#F3D84F] hover:bg-[#e6c83b] text-[#181818] text-xs font-bold rounded-lg transition-colors flex items-center gap-1.5"
            >
              <Database className="w-3.5 h-3.5" />
              <span>Ver Sentencia SQL</span>
            </button>
          </div>
        </div>
      )}

      {/* MODAL EXPLORADOR DE BASE DE DATOS Y CONSULTAS SQL */}
      <SqlExplorerModal
        abierto={sqlModalAbierto}
        onCerrar={() => setSqlModalAbierto(false)}
        consultaEjecutadaReciente={ultimaConsultaSql}
      />

      {/* FOOTER */}
      <Footer
        onCambiarSeccion={setSeccionActiva}
        onAbrirSqlModal={() => setSqlModalAbierto(true)}
      />
    </div>
  );
}
