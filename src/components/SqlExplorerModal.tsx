import React, { useState } from 'react';
import { Database, Table, Code2, X, Copy, Check, Terminal, Play, Layers } from 'lucide-react';
import { DataService } from '../services/dataService';

interface SqlExplorerModalProps {
  abierto: boolean;
  onCerrar: () => void;
  consultaEjecutadaReciente?: string;
}

export const SqlExplorerModal: React.FC<SqlExplorerModalProps> = ({
  abierto,
  onCerrar,
  consultaEjecutadaReciente
}) => {
  const [pestaña, setPestaña] = useState<'esquema' | 'consultas' | 'archivo_sql'>('esquema');
  const [copiado, setCopiado] = useState(false);
  const [tablaSeleccionada, setTablaSeleccionada] = useState<string>('talleres');

  if (!abierto) return null;

  const tablasInfo: Record<string, { descripcion: string; columnas: { nombre: string; tipo: string; clave: string; desc: string }[] }> = {
    talleres: {
      descripcion: 'Cursos de instrumentos musicales ofertados por la escuela, vacantes y cupos.',
      columnas: [
        { nombre: 'id', tipo: 'INT AUTO_INCREMENT', clave: 'PK', desc: 'Identificador único del taller' },
        { nombre: 'nombre', tipo: 'VARCHAR(150)', clave: '', desc: 'Nombre descriptivo del taller' },
        { nombre: 'instrumento', tipo: 'VARCHAR(100)', clave: '', desc: 'Guitarra, Violín, Flauta, Batería, Piano, etc.' },
        { nombre: 'docente_id', tipo: 'INT', clave: 'FK -> docentes.id', desc: 'Profesor responsable a cargo' },
        { nombre: 'nivel', tipo: "ENUM('Inicial', 'Intermedio', 'Avanzado', 'Ensamble')", clave: '', desc: 'Grado de conocimiento previo' },
        { nombre: 'edad_minima', tipo: 'INT DEFAULT 7', clave: '', desc: 'A partir de 7 años' },
        { nombre: 'dia_semana', tipo: 'VARCHAR(50)', clave: '', desc: 'Días de cursada' },
        { nombre: 'horario', tipo: 'VARCHAR(50)', clave: '', desc: 'Franja horaria del taller' },
        { nombre: 'vacantes_totales', tipo: 'INT', clave: '', desc: 'Capacidad máxima del aula' },
        { nombre: 'vacantes_disponibles', tipo: 'INT', clave: '', desc: 'Cupos libres en tiempo real' },
        { nombre: 'arancel_mensual', tipo: 'DECIMAL(10,2)', clave: '', desc: 'Costo social mensual accesible' },
        { nombre: 'modalidad', tipo: "ENUM('Presencial', 'Online', 'Híbrida')", clave: '', desc: 'Modalidad de dictado' },
        { nombre: 'instrumentos_prestamo_disponibles', tipo: 'INT', clave: '', desc: 'Stock del banco solidario de instrumentos' }
      ]
    },
    docentes: {
      descripcion: 'Equipo de profesores y formadores musicales de la escuela.',
      columnas: [
        { nombre: 'id', tipo: 'INT AUTO_INCREMENT', clave: 'PK', desc: 'Identificador del docente' },
        { nombre: 'nombre', tipo: 'VARCHAR(100)', clave: '', desc: 'Nombre del docente' },
        { nombre: 'apellido', tipo: 'VARCHAR(100)', clave: '', desc: 'Apellido del docente' },
        { nombre: 'especialidad', tipo: 'VARCHAR(150)', clave: '', desc: 'Especialidad instrumental' },
        { nombre: 'instrumento_principal', tipo: 'VARCHAR(100)', clave: '', desc: 'Instrumento primario' },
        { nombre: 'biografia', tipo: 'TEXT', clave: '', desc: 'Resumen biográfico y pedagógico' },
        { nombre: 'trayectoria_cv', tipo: 'TEXT', clave: '', desc: 'Estudios, conservatorios y experiencia' },
        { nombre: 'email', tipo: 'VARCHAR(100)', clave: '', desc: 'Correo electrónico institucional' }
      ]
    },
    inscripciones: {
      descripcion: 'Registros de alumnos inscriptos y gestión de banco de instrumentos.',
      columnas: [
        { nombre: 'id', tipo: 'INT AUTO_INCREMENT', clave: 'PK', desc: 'Identificador de inscripción' },
        { nombre: 'codigo_inscripcion', tipo: 'VARCHAR(50) UNIQUE', clave: 'UQ', desc: 'Código de comprobante (ATR-2026-XXXX)' },
        { nombre: 'taller_id', tipo: 'INT', clave: 'FK -> talleres.id', desc: 'Taller al que se matricula' },
        { nombre: 'nombre', tipo: 'VARCHAR(100)', clave: '', desc: 'Nombre del alumno' },
        { nombre: 'apellido', tipo: 'VARCHAR(100)', clave: '', desc: 'Apellido del alumno' },
        { nombre: 'email', tipo: 'VARCHAR(150)', clave: '', desc: 'Email de contacto' },
        { nombre: 'telefono', tipo: 'VARCHAR(50)', clave: '', desc: 'Teléfono o WhatsApp' },
        { nombre: 'edad', tipo: 'INT', clave: '', desc: 'Edad del postulante (7 a 90+)' },
        { nombre: 'genero', tipo: "ENUM('Masc', 'Fem', 'NB', ...)", clave: '', desc: 'Género' },
        { nombre: 'necesita_prestamo_instrumento', tipo: 'BOOLEAN', clave: '', desc: 'Solicita instrumento solidario' },
        { nombre: 'estado', tipo: "ENUM('Confirmada', 'En Lista de Espera')", clave: '', desc: 'Estado de la vacante' }
      ]
    },
    eventos: {
      descripcion: 'Muestras de alumnos, conciertos barriales y masterclasses.',
      columnas: [
        { nombre: 'id', tipo: 'INT AUTO_INCREMENT', clave: 'PK', desc: 'Identificador del evento' },
        { nombre: 'titulo', tipo: 'VARCHAR(200)', clave: '', desc: 'Nombre del evento musical' },
        { nombre: 'tipo', tipo: "ENUM('Muestra', 'Concierto', ...)", clave: '', desc: 'Tipo de encuentro' },
        { nombre: 'fecha', tipo: 'DATE', clave: '', desc: 'Fecha de realización' },
        { nombre: 'hora', tipo: 'VARCHAR(20)', clave: '', desc: 'Hora de inicio' },
        { nombre: 'repertorio', tipo: 'TEXT', clave: '', desc: 'Obras y canciones del concierto' },
        { nombre: 'precio_entrada', tipo: 'DECIMAL(10,2)', clave: '', desc: 'Valor del bono o $0 para libre' },
        { nombre: 'entradas_disponibles', tipo: 'INT', clave: '', desc: 'Capacidad restante de la sala' }
      ]
    },
    entradas_eventos: {
      descripcion: 'Reservas y tickets generados para los conciertos y muestras.',
      columnas: [
        { nombre: 'id', tipo: 'INT AUTO_INCREMENT', clave: 'PK', desc: 'Identificador del ticket' },
        { nombre: 'codigo_ticket', tipo: 'VARCHAR(50) UNIQUE', clave: 'UQ', desc: 'Código único de entrada (TKT-XX-XXXX)' },
        { nombre: 'evento_id', tipo: 'INT', clave: 'FK -> eventos.id', desc: 'Evento asociado' },
        { nombre: 'nombre_comprador', tipo: 'VARCHAR(150)', clave: '', desc: 'Nombre de quien reserva' },
        { nombre: 'cantidad', tipo: 'INT', clave: '', desc: 'Cantidad de butacas reservadas' },
        { nombre: 'total_abonado', tipo: 'DECIMAL(10,2)', clave: '', desc: 'Total' }
      ]
    }
  };

  const consultasSqlEjemplo = [
    {
      titulo: '1. Listar Talleres con Docente Asignado (JOIN)',
      proposito: 'Alimentar la cartelera de talleres en la vista principal con datos del profesor.',
      query: `SELECT 
    t.id, t.nombre AS taller, t.instrumento, t.nivel, t.horario, 
    t.vacantes_disponibles, t.arancel_mensual,
    CONCAT(d.nombre, ' ', d.apellido) AS docente_responsable,
    d.especialidad
FROM talleres t
INNER JOIN docentes d ON t.docente_id = d.id
WHERE t.vacantes_disponibles > 0
ORDER BY t.instrumento ASC;`
    },
    {
      titulo: '2. Registro de Alumno y Descuento de Vacante (Transacción ACID)',
      proposito: 'Garantizar que al matricular un alumno no se sobrevenda el aula.',
      query: `START TRANSACTION;

INSERT INTO inscripciones (
    codigo_inscripcion, taller_id, nombre, apellido, email,
    telefono, edad, modalidad, necesita_prestamo_instrumento, horario_seleccionado
) VALUES (
    'ATR-2026-0012', 1, 'Mateo', 'Gómez', 'mateo@ejemplo.com',
    '1155443322', 14, 'Presencial', TRUE, 'Martes y Jueves 17:30 a 19:00 hs'
);

UPDATE talleres 
SET vacantes_disponibles = vacantes_disponibles - 1 
WHERE id = 1 AND vacantes_disponibles > 0;

COMMIT;`
    },
    {
      titulo: '3. Próximos Eventos con Entradas Disponibles',
      proposito: 'Listar muestras y conciertos que tengan cupos para venta y reserva.',
      query: `SELECT 
    id, titulo, tipo, fecha, hora, lugar, repertorio, precio_entrada, entradas_disponibles
FROM eventos
WHERE estado = 'proximo' AND fecha >= CURRENT_DATE
ORDER BY fecha ASC;`
    },
    {
      titulo: '4. Estadísticas del Banco de Instrumentos Prestados',
      proposito: 'Reporte para la cooperadora sobre demanda de guitarras, violines y flautas.',
      query: `SELECT 
    t.instrumento,
    COUNT(i.id) AS total_solicitudes_prestamo,
    t.instrumentos_prestamo_disponibles AS stock_actual
FROM inscripciones i
JOIN talleres t ON i.taller_id = t.id
WHERE i.necesita_prestamo_instrumento = TRUE
GROUP BY t.instrumento, t.instrumentos_prestamo_disponibles;`
    }
  ];

  const archivoSqlCompleto = `-- =========================================================================
-- PARCIAL 2 PROGRAMACIÓN/DESARROLLO WEB - ISEC
-- Caso 3: Escuela de Música "A Todo Ritmo"
-- Base de Datos Relacional y Consultas
-- =========================================================================

CREATE DATABASE IF NOT EXISTS a_todo_ritmo;
USE a_todo_ritmo;

CREATE TABLE sedes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    telefono VARCHAR(50) NOT NULL,
    horarios_atencion VARCHAR(150) NOT NULL
);

CREATE TABLE docentes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    especialidad VARCHAR(150) NOT NULL,
    instrumento_principal VARCHAR(100) NOT NULL,
    biografia TEXT NOT NULL,
    trayectoria_cv TEXT NOT NULL,
    email VARCHAR(100) NOT NULL,
    foto_url VARCHAR(255),
    activo BOOLEAN DEFAULT TRUE
);

CREATE TABLE talleres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    instrumento VARCHAR(100) NOT NULL,
    docente_id INT NOT NULL,
    nivel ENUM('Inicial / Sin experiencia', 'Intermedio', 'Avanzado', 'Ensamble') NOT NULL,
    edad_minima INT DEFAULT 7,
    dia_semana VARCHAR(50) NOT NULL,
    horario VARCHAR(50) NOT NULL,
    vacantes_totales INT NOT NULL,
    vacantes_disponibles INT NOT NULL,
    arancel_mensual DECIMAL(10, 2) NOT NULL,
    modalidad ENUM('Presencial', 'Online', 'Híbrida') DEFAULT 'Presencial',
    sede_id INT DEFAULT 1,
    descripcion TEXT NOT NULL,
    instrumentos_prestamo_disponibles INT DEFAULT 5,
    FOREIGN KEY (docente_id) REFERENCES docentes(id),
    FOREIGN KEY (sede_id) REFERENCES sedes(id)
);

CREATE TABLE eventos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    tipo ENUM('Muestra de Alumnos', 'Concierto Abierto', 'Masterclass', 'Jam Session') NOT NULL,
    fecha DATE NOT NULL,
    hora VARCHAR(20) NOT NULL,
    lugar VARCHAR(150) NOT NULL,
    repertorio TEXT NOT NULL,
    precio_entrada DECIMAL(10, 2) DEFAULT 0.00,
    capacidad_total INT NOT NULL,
    entradas_disponibles INT NOT NULL,
    estado ENUM('proximo', 'previo', 'agotado') DEFAULT 'proximo'
);

CREATE TABLE inscripciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_inscripcion VARCHAR(50) UNIQUE NOT NULL,
    taller_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    telefono VARCHAR(50) NOT NULL,
    edad INT NOT NULL,
    modalidad ENUM('Presencial', 'Online', 'Híbrida') NOT NULL,
    necesita_prestamo_instrumento BOOLEAN DEFAULT FALSE,
    horario_seleccionado VARCHAR(100) NOT NULL,
    estado ENUM('Pendiente de Confirmación', 'Confirmada', 'En Lista de Espera') DEFAULT 'Confirmada',
    fecha_inscripcion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (taller_id) REFERENCES talleres(id)
);

CREATE TABLE entradas_eventos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_ticket VARCHAR(50) UNIQUE NOT NULL,
    evento_id INT NOT NULL,
    nombre_comprador VARCHAR(150) NOT NULL,
    email_comprador VARCHAR(150) NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    total_abonado DECIMAL(10, 2) NOT NULL,
    fecha_reserva TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (evento_id) REFERENCES eventos(id)
);

CREATE TABLE consultas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    telefono VARCHAR(50),
    motivo VARCHAR(150) NOT NULL,
    mensaje TEXT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
`;

  const copiarAlPortapapeles = (texto: string) => {
    navigator.clipboard.writeText(texto);
    setCopiado(true);
    setTimeout(() => setCopiado(false), 2000);
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-black/75 backdrop-blur-sm animate-fadeIn">
      <div className="bg-[#181818] border-2 border-[#F3D84F] rounded-2xl w-full max-w-4xl max-h-[92vh] flex flex-col shadow-2xl overflow-hidden text-[#FFFAE6]">
        {/* Cabecera del modal */}
        <div className="bg-neutral-900 px-6 py-4 border-b border-neutral-700 flex items-center justify-between">
          <div className="flex items-center gap-3">
            <div className="p-2 rounded-lg bg-[#F3D84F] text-[#181818]">
              <Database className="w-5 h-5" />
            </div>
            <div>
              <h3 className="font-carter text-xl text-[#F3D84F] flex items-center gap-2">
                <span>Estructura de Base de Datos y Consultas SQL</span>
                <span className="text-xs px-2 py-0.5 rounded-full bg-[#24AB58] text-[#FFFAE6] font-mono font-normal">
                  .sql
                </span>
              </h3>
              <p className="text-xs text-neutral-400">
                Modelo relacional y operaciones para "A Todo Ritmo" (ISEC - Tomillo Sofía)
              </p>
            </div>
          </div>
          <button
            onClick={onCerrar}
            className="p-2 rounded-lg text-neutral-400 hover:text-white hover:bg-neutral-800 transition-colors"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Pestañas de navegación */}
        <div className="flex border-b border-neutral-800 bg-neutral-950 px-6 text-sm font-semibold">
          <button
            onClick={() => setPestaña('esquema')}
            className={`py-3 px-4 border-b-2 transition-colors flex items-center gap-2 ${
              pestaña === 'esquema'
                ? 'border-[#F3D84F] text-[#F3D84F]'
                : 'border-transparent text-neutral-400 hover:text-neutral-200'
            }`}
          >
            <Layers className="w-4 h-4" />
            <span>Tablas y Esquema</span>
          </button>
          <button
            onClick={() => setPestaña('consultas')}
            className={`py-3 px-4 border-b-2 transition-colors flex items-center gap-2 ${
              pestaña === 'consultas'
                ? 'border-[#F3D84F] text-[#F3D84F]'
                : 'border-transparent text-neutral-400 hover:text-neutral-200'
            }`}
          >
            <Play className="w-4 h-4" />
            <span>Consultas SQL (SELECT, INSERT, JOIN)</span>
          </button>
          <button
            onClick={() => setPestaña('archivo_sql')}
            className={`py-3 px-4 border-b-2 transition-colors flex items-center gap-2 ${
              pestaña === 'archivo_sql'
                ? 'border-[#F3D84F] text-[#F3D84F]'
                : 'border-transparent text-neutral-400 hover:text-neutral-200'
            }`}
          >
            <Code2 className="w-4 h-4" />
            <span>Script Completo (database.sql)</span>
          </button>
        </div>

        {/* Notificación de última consulta ejecutada en vivo si existe */}
        {consultaEjecutadaReciente && (
          <div className="bg-[#24AB58]/15 border-b border-[#24AB58]/40 px-6 py-2.5 text-xs flex items-center justify-between">
            <div className="flex items-center gap-2 text-[#24AB58] font-mono">
              <Terminal className="w-4 h-4 shrink-0" />
              <span className="font-bold">Última operación SQL ejecutada en vivo por el usuario:</span>
            </div>
            <span className="text-[11px] text-neutral-400">Ver pestaña "Consultas"</span>
          </div>
        )}

        {/* Contenido según pestaña */}
        <div className="p-6 overflow-y-auto flex-1 font-sans text-sm space-y-4">
          {pestaña === 'esquema' && (
            <div className="space-y-4">
              <div className="flex flex-wrap gap-2 pb-2 border-b border-neutral-800">
                {Object.keys(tablasInfo).map((tabla) => (
                  <button
                    key={tabla}
                    onClick={() => setTablaSeleccionada(tabla)}
                    className={`px-3 py-1.5 rounded-lg text-xs font-mono font-bold transition-colors ${
                      tablaSeleccionada === tabla
                        ? 'bg-[#F3D84F] text-[#181818]'
                        : 'bg-neutral-800 text-neutral-300 hover:bg-neutral-700'
                    }`}
                  >
                    <span className="flex items-center gap-1.5">
                      <Table className="w-3.5 h-3.5" />
                      {tabla}
                    </span>
                  </button>
                ))}
              </div>

              {tablasInfo[tablaSeleccionada] && (
                <div className="space-y-3">
                  <div className="p-3 bg-neutral-900 rounded-lg border border-neutral-800 flex justify-between items-center">
                    <div>
                      <p className="text-xs text-[#F3D84F] font-mono uppercase tracking-wide">
                        Tabla: <span className="font-bold">{tablaSeleccionada}</span>
                      </p>
                      <p className="text-xs text-neutral-300">
                        {tablasInfo[tablaSeleccionada].descripcion}
                      </p>
                    </div>
                    <span className="text-[11px] bg-neutral-800 px-2 py-1 rounded text-neutral-400 font-mono">
                      {tablasInfo[tablaSeleccionada].columnas.length} columnas
                    </span>
                  </div>

                  <div className="overflow-x-auto rounded-lg border border-neutral-800">
                    <table className="w-full text-left text-xs">
                      <thead className="bg-neutral-900 text-neutral-400 uppercase font-mono">
                        <tr>
                          <th className="py-2.5 px-3">Columna</th>
                          <th className="py-2.5 px-3">Tipo de Dato</th>
                          <th className="py-2.5 px-3">Clave / Restricción</th>
                          <th className="py-2.5 px-3">Descripción Funcional</th>
                        </tr>
                      </thead>
                      <tbody className="divide-y divide-neutral-800 font-mono">
                        {tablasInfo[tablaSeleccionada].columnas.map((col, idx) => (
                          <tr key={idx} className="hover:bg-neutral-900/60">
                            <td className="py-2 px-3 font-bold text-[#F3D84F]">{col.nombre}</td>
                            <td className="py-2 px-3 text-emerald-400">{col.tipo}</td>
                            <td className="py-2 px-3">
                              {col.clave ? (
                                <span className={`px-1.5 py-0.5 rounded text-[10px] ${
                                  col.clave.includes('PK')
                                    ? 'bg-amber-400/20 text-amber-300 font-bold'
                                    : 'bg-blue-400/20 text-blue-300'
                                }`}>
                                  {col.clave}
                                </span>
                              ) : (
                                <span className="text-neutral-500">-</span>
                              )}
                            </td>
                            <td className="py-2 px-3 font-sans text-neutral-300">{col.desc}</td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>
                </div>
              )}
            </div>
          )}

          {pestaña === 'consultas' && (
            <div className="space-y-6">
              {consultaEjecutadaReciente && (
                <div className="p-4 rounded-xl bg-emerald-950/40 border border-emerald-500/50 space-y-2">
                  <div className="flex items-center justify-between">
                    <span className="text-xs font-bold text-emerald-400 flex items-center gap-1.5">
                      <Terminal className="w-4 h-4" /> Transacción SQL Generada en Vivo:
                    </span>
                    <button
                      onClick={() => copiarAlPortapapeles(consultaEjecutadaReciente)}
                      className="text-xs text-neutral-400 hover:text-emerald-300 flex items-center gap-1"
                    >
                      <Copy className="w-3.5 h-3.5" /> Copiar
                    </button>
                  </div>
                  <pre className="p-3 bg-black/60 rounded-lg text-emerald-300 font-mono text-xs overflow-x-auto">
                    {consultaEjecutadaReciente}
                  </pre>
                </div>
              )}

              <div className="grid grid-cols-1 gap-4">
                {consultasSqlEjemplo.map((item, idx) => (
                  <div key={idx} className="p-4 rounded-xl bg-neutral-900 border border-neutral-800 space-y-2">
                    <div className="flex items-center justify-between">
                      <h4 className="font-carter text-sm text-[#F3D84F]">{item.titulo}</h4>
                      <button
                        onClick={() => copiarAlPortapapeles(item.query)}
                        className="text-xs text-neutral-400 hover:text-[#F3D84F] flex items-center gap-1"
                      >
                        <Copy className="w-3 h-3" /> Copiar SQL
                      </button>
                    </div>
                    <p className="text-xs text-neutral-400">{item.proposito}</p>
                    <pre className="p-3 bg-black/80 rounded-lg text-neutral-200 font-mono text-xs overflow-x-auto border border-neutral-800">
                      {item.query}
                    </pre>
                  </div>
                ))}
              </div>
            </div>
          )}

          {pestaña === 'archivo_sql' && (
            <div className="space-y-3">
              <div className="flex items-center justify-between">
                <span className="text-xs text-neutral-400 font-mono">
                  Ubicación física: /database.sql & /src/data/database.sql
                </span>
                <button
                  onClick={() => copiarAlPortapapeles(archivoSqlCompleto)}
                  className="flex items-center gap-1 px-3 py-1 rounded bg-[#24AB58] hover:bg-[#1f934b] text-white text-xs font-bold transition-colors"
                >
                  {copiado ? <Check className="w-3.5 h-3.5" /> : <Copy className="w-3.5 h-3.5" />}
                  <span>{copiado ? '¡Copiado al portapapeles!' : 'Copiar todo el archivo .sql'}</span>
                </button>
              </div>
              <pre className="p-4 bg-black/90 rounded-xl text-neutral-300 font-mono text-xs overflow-x-auto border border-neutral-800 leading-relaxed max-h-[55vh]">
                {archivoSqlCompleto}
              </pre>
            </div>
          )}
        </div>

        {/* Pie del modal */}
        <div className="bg-neutral-950 px-6 py-3 border-t border-neutral-800 flex justify-between items-center text-xs text-neutral-400">
          <span>Diseño con paleta #F3D84F, #24AB58, #181818, #FFFAE6</span>
          <button
            onClick={onCerrar}
            className="px-4 py-1.5 rounded-lg bg-neutral-800 hover:bg-neutral-700 text-[#FFFAE6] font-semibold transition-colors"
          >
            Cerrar
          </button>
        </div>
      </div>
    </div>
  );
};
