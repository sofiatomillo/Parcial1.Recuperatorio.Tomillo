import React, { useState } from 'react';
import { Evento, EntradaEvento } from '../types';
import { DataService } from '../services/dataService';
import { Calendar, Clock, MapPin, Music, Ticket, CheckCircle2, AlertCircle, X, Download, Share2, Sparkles } from 'lucide-react';

interface EventosSectionProps {
  eventos: Evento[];
  onActualizarEventos: () => void;
  onNotificarSqlEjecutado: (sql: string) => void;
  eventoPreseleccionadoParaEntrada?: Evento | null;
  onLimpiarEventoPreseleccionado?: () => void;
}

export const EventosSection: React.FC<EventosSectionProps> = ({
  eventos,
  onActualizarEventos,
  onNotificarSqlEjecutado,
  eventoPreseleccionadoParaEntrada,
  onLimpiarEventoPreseleccionado
}) => {
  const [filtroTipo, setFiltroTipo] = useState<'proximos' | 'previos'>('proximos');
  const [eventoParaReserva, setEventoParaReserva] = useState<Evento | null>(
    eventoPreseleccionadoParaEntrada || null
  );

  // Formulario de Reserva de Entradas
  const [nombre, setNombre] = useState('');
  const [email, setEmail] = useState('');
  const [telefono, setTelefono] = useState('');
  const [cantidad, setCantidad] = useState(1);
  const [metodoPago, setMetodoPago] = useState<'Efectivo en Puerta' | 'Transferencia' | 'Beca Comunitaria'>('Transferencia');
  const [reservaCompletada, setReservaCompletada] = useState<EntradaEvento | null>(null);
  const [errorForm, setErrorForm] = useState('');

  // Actualizar si viene una preselección desde otra vista
  React.useEffect(() => {
    if (eventoPreseleccionadoParaEntrada) {
      setEventoParaReserva(eventoPreseleccionadoParaEntrada);
      setReservaCompletada(null);
    }
  }, [eventoPreseleccionadoParaEntrada]);

  const eventosFiltrados = eventos.filter((e) => {
    if (filtroTipo === 'proximos') return e.estado === 'proximo';
    return e.estado === 'previo';
  });

  const handleAbrirReserva = (evento: Evento) => {
    setEventoParaReserva(evento);
    setReservaCompletada(null);
    setErrorForm('');
    setCantidad(1);
  };

  const handleCerrarModal = () => {
    setEventoParaReserva(null);
    setReservaCompletada(null);
    setErrorForm('');
    if (onLimpiarEventoPreseleccionado) {
      onLimpiarEventoPreseleccionado();
    }
  };

  const handleConfirmarReserva = (e: React.FormEvent) => {
    e.preventDefault();
    if (!eventoParaReserva) return;

    if (!nombre.trim() || !email.trim()) {
      setErrorForm('Por favor completa tu nombre y correo electrónico.');
      return;
    }

    if (cantidad < 1 || cantidad > 6) {
      setErrorForm('Puedes reservar entre 1 y 6 entradas por persona.');
      return;
    }

    const totalAbonado = eventoParaReserva.precioEntrada * cantidad;

    const { entrada, sqlQuery } = DataService.reservarEntradas({
      eventoId: eventoParaReserva.id,
      nombreComprador: nombre.trim(),
      emailComprador: email.trim(),
      telefonoComprador: telefono.trim(),
      cantidad,
      totalAbonado,
      metodoPago
    });

    setReservaCompletada(entrada);
    onActualizarEventos();
    onNotificarSqlEjecutado(sqlQuery);
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">
      {/* CABECERA */}
      <div className="text-center max-w-3xl mx-auto space-y-3">
        <span className="text-xs font-bold text-[#24AB58] uppercase tracking-wider">
          Muestras & Conciertos
        </span>
        <h1 className="font-carter text-3xl sm:text-5xl text-[#181818]">
          Agenda Cultural y de Alumnos
        </h1>
        <p className="text-sm sm:text-base text-neutral-700 leading-relaxed font-sans">
          Cada mes la escuela abre sus puertas para compartir en vivo el trabajo de los talleres, ensambles y artistas invitados.
        </p>
      </div>

      {/* PESTAÑAS PRÓXIMOS / PREVIOS */}
      <div className="flex justify-center">
        <div className="inline-flex p-1.5 rounded-2xl bg-[#FFFDF5] border-2 border-[#181818] shadow-sm">
          <button
            onClick={() => setFiltroTipo('proximos')}
            className={`px-5 py-2 rounded-xl text-xs sm:text-sm font-carter transition-all ${
              filtroTipo === 'proximos'
                ? 'bg-[#24AB58] text-[#FFFAE6] shadow'
                : 'text-neutral-700 hover:text-black'
            }`}
          >
            Próximos Eventos Mensuales
          </button>
          <button
            onClick={() => setFiltroTipo('previos')}
            className={`px-5 py-2 rounded-xl text-xs sm:text-sm font-carter transition-all ${
              filtroTipo === 'previos'
                ? 'bg-[#181818] text-[#F3D84F] shadow'
                : 'text-neutral-700 hover:text-black'
            }`}
          >
            Eventos Previos / Archivo
          </button>
        </div>
      </div>

      {/* LISTA DE EVENTOS */}
      <div className="space-y-6">
        {eventosFiltrados.map((evento) => (
          <div
            key={evento.id}
            className="bg-[#FFFDF5] border-2 border-[#181818] rounded-3xl overflow-hidden shadow-md hover:shadow-xl transition-all grid grid-cols-1 lg:grid-cols-12 gap-6"
          >
            {/* Imagen del evento */}
            <div className="lg:col-span-4 relative h-56 lg:h-auto min-h-[220px] bg-neutral-900 overflow-hidden">
              <img
                src={evento.imagenUrl}
                alt={evento.titulo}
                className="w-full h-full object-cover"
                referrerPolicy="no-referrer"
              />
              <div className="absolute top-4 left-4 px-3 py-1 rounded-full bg-[#F3D84F] text-[#181818] text-xs font-carter shadow">
                {evento.tipo}
              </div>
            </div>

            {/* Información del evento */}
            <div className="lg:col-span-8 p-6 lg:p-8 flex flex-col justify-between space-y-4">
              <div className="space-y-3">
                <div className="flex flex-wrap items-center gap-3 text-xs font-bold text-neutral-600">
                  <span className="flex items-center gap-1.5 bg-[#FFFAE6] border border-neutral-300 px-3 py-1 rounded-lg text-[#181818]">
                    <Calendar className="w-3.5 h-3.5 text-[#24AB58]" />
                    {evento.fecha}
                  </span>
                  <span className="flex items-center gap-1.5 bg-[#FFFAE6] border border-neutral-300 px-3 py-1 rounded-lg text-[#181818]">
                    <Clock className="w-3.5 h-3.5 text-[#24AB58]" />
                    {evento.hora}
                  </span>
                  <span className="flex items-center gap-1.5 text-neutral-700">
                    <MapPin className="w-3.5 h-3.5 text-[#181818]" />
                    {evento.lugar}
                  </span>
                </div>

                <h3 className="font-carter text-2xl sm:text-3xl text-[#181818]">
                  {evento.titulo}
                </h3>

                <p className="text-xs sm:text-sm text-neutral-700 leading-relaxed">
                  {evento.descripcion}
                </p>

                {/* Repertorio */}
                <div className="p-3.5 rounded-xl bg-[#FFFAE6] border border-neutral-200 text-xs">
                  <span className="font-bold text-[#181818] flex items-center gap-1.5 mb-1">
                    <Music className="w-3.5 h-3.5 text-[#24AB58]" />
                    Repertorio Destacado:
                  </span>
                  <p className="text-neutral-700 italic">
                    {evento.repertorio}
                  </p>
                </div>
              </div>

              {/* Botón y entradas */}
              <div className="pt-4 border-t border-neutral-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                  <span className="text-[11px] text-neutral-500 uppercase font-bold">Valor de Bono / Entrada</span>
                  <p className="font-carter text-xl text-[#181818]">
                    {evento.precioEntrada === 0 ? 'Entrada Libre y Gratuita' : `$${evento.precioEntrada.toLocaleString('es-AR')}`}
                  </p>
                </div>

                {evento.estado === 'proximo' ? (
                  <div className="flex items-center gap-3 w-full sm:w-auto">
                    <span className="text-xs text-[#24AB58] font-bold">
                      {evento.entradasDisponibles > 0 ? `${evento.entradasDisponibles} butacas disponibles` : 'Entradas Agotadas'}
                    </span>
                    <button
                      onClick={() => handleAbrirReserva(evento)}
                      disabled={evento.entradasDisponibles <= 0}
                      className={`px-6 py-2.5 rounded-xl font-carter text-xs sm:text-sm flex items-center justify-center gap-2 shadow transition-all ${
                        evento.entradasDisponibles > 0
                          ? 'bg-[#F3D84F] hover:bg-[#e6c83b] text-[#181818] active:scale-95'
                          : 'bg-neutral-300 text-neutral-500 cursor-not-allowed'
                      }`}
                    >
                      <Ticket className="w-4 h-4" />
                      <span>{evento.entradasDisponibles > 0 ? 'Sacar Entradas' : 'Agotado'}</span>
                    </button>
                  </div>
                ) : (
                  <span className="px-3 py-1 rounded-lg bg-neutral-200 text-neutral-600 text-xs font-bold">
                    Evento Finalizado
                  </span>
                )}
              </div>
            </div>
          </div>
        ))}
      </div>

      {/* MODAL DE RESERVA / COMPRA DE ENTRADAS */}
      {eventoParaReserva && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm animate-fadeIn">
          <div className="bg-[#FFFAE6] border-4 border-[#181818] rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl relative text-[#181818] max-h-[90vh] overflow-y-auto">
            <button
              onClick={handleCerrarModal}
              className="absolute top-4 right-4 p-2 text-neutral-600 hover:text-black rounded-lg"
            >
              <X className="w-5 h-5" />
            </button>

            {!reservaCompletada ? (
              <form onSubmit={handleConfirmarReserva} className="space-y-5">
                <div>
                  <div className="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-[#24AB58] text-[#FFFAE6] text-xs font-carter mb-2">
                    <Ticket className="w-3.5 h-3.5" />
                    <span>Reserva Oficial de Entradas</span>
                  </div>
                  <h3 className="font-carter text-2xl text-[#181818]">
                    {eventoParaReserva.titulo}
                  </h3>
                  <p className="text-xs text-neutral-600 mt-1">
                    📅 {eventoParaReserva.fecha} a las {eventoParaReserva.hora} • {eventoParaReserva.lugar}
                  </p>
                </div>

                {errorForm && (
                  <div className="p-3 bg-red-100 border border-red-400 text-red-700 text-xs rounded-xl flex items-center gap-2">
                    <AlertCircle className="w-4 h-4 shrink-0" />
                    <span>{errorForm}</span>
                  </div>
                )}

                <div className="space-y-3 text-xs">
                  <div>
                    <label className="block font-bold text-neutral-700 mb-1">Nombre Completo *</label>
                    <input
                      type="text"
                      required
                      placeholder="Ej: Sofía Tomillo"
                      value={nombre}
                      onChange={(e) => setNombre(e.target.value)}
                      className="w-full p-2.5 bg-[#FFFDF5] border border-neutral-300 rounded-xl focus:border-[#24AB58] focus:outline-none"
                    />
                  </div>

                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                      <label className="block font-bold text-neutral-700 mb-1">Correo Electrónico *</label>
                      <input
                        type="email"
                        required
                        placeholder="tu@email.com"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        className="w-full p-2.5 bg-[#FFFDF5] border border-neutral-300 rounded-xl focus:border-[#24AB58] focus:outline-none"
                      />
                    </div>
                    <div>
                      <label className="block font-bold text-neutral-700 mb-1">Teléfono / WhatsApp</label>
                      <input
                        type="tel"
                        placeholder="11 5544-3322"
                        value={telefono}
                        onChange={(e) => setTelefono(e.target.value)}
                        className="w-full p-2.5 bg-[#FFFDF5] border border-neutral-300 rounded-xl focus:border-[#24AB58] focus:outline-none"
                      />
                    </div>
                  </div>

                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                    <div>
                      <label className="block font-bold text-neutral-700 mb-1">Cantidad de Butacas</label>
                      <select
                        value={cantidad}
                        onChange={(e) => setCantidad(Number(e.target.value))}
                        className="w-full p-2.5 bg-[#FFFDF5] border border-neutral-300 rounded-xl focus:border-[#24AB58] focus:outline-none"
                      >
                        {[1, 2, 3, 4, 5, 6].map((num) => (
                          <option key={num} value={num}>
                            {num} entrada{num > 1 ? 's' : ''}
                          </option>
                        ))}
                      </select>
                    </div>

                    <div>
                      <label className="block font-bold text-neutral-700 mb-1">Forma de Pago / Retiro</label>
                      <select
                        value={metodoPago}
                        onChange={(e) => setMetodoPago(e.target.value as any)}
                        className="w-full p-2.5 bg-[#FFFDF5] border border-neutral-300 rounded-xl focus:border-[#24AB58] focus:outline-none"
                      >
                        <option value="Transferencia">Transferencia Bancaria (Alias)</option>
                        <option value="Efectivo en Puerta">Abonar en Puerta al ingresar</option>
                        <option value="Beca Comunitaria">Beca Comunitaria (Sin costo)</option>
                      </select>
                    </div>
                  </div>
                </div>

                {/* Resumen de cobro */}
                <div className="p-4 bg-[#FFFDF5] border-2 border-dashed border-[#181818] rounded-2xl flex items-center justify-between">
                  <div>
                    <span className="text-xs text-neutral-600">Total a Pagar ({cantidad} entrada{cantidad > 1 ? 's' : ''}):</span>
                    <p className="font-carter text-2xl text-[#24AB58]">
                      {metodoPago === 'Beca Comunitaria' || eventoParaReserva.precioEntrada === 0
                        ? 'GRATIS / LIBRE'
                        : `$${(eventoParaReserva.precioEntrada * cantidad).toLocaleString('es-AR')}`}
                    </p>
                  </div>
                  <span className="text-[11px] bg-[#F3D84F] text-[#181818] px-2.5 py-1 rounded-lg font-bold">
                    Reserva Inmediata
                  </span>
                </div>

                <div className="flex gap-3">
                  <button
                    type="button"
                    onClick={handleCerrarModal}
                    className="w-1/2 py-2.5 border border-neutral-300 text-neutral-700 font-bold text-xs rounded-xl"
                  >
                    Cancelar
                  </button>
                  <button
                    type="submit"
                    className="w-1/2 py-2.5 bg-[#24AB58] hover:bg-[#1e8f49] text-[#FFFAE6] font-carter text-sm rounded-xl shadow transition-colors"
                  >
                    Confirmar Reserva
                  </button>
                </div>
              </form>
            ) : (
              /* Comprobante de Entrada Exitosa */
              <div className="text-center space-y-4">
                <div className="w-14 h-14 rounded-full bg-[#24AB58] text-[#FFFAE6] flex items-center justify-center mx-auto shadow-md">
                  <CheckCircle2 className="w-8 h-8" />
                </div>

                <h3 className="font-carter text-2xl text-[#181818]">
                  ¡Entrada Reservada con Éxito!
                </h3>

                <p className="text-xs text-neutral-700">
                  Guardá tu código de comprobante. También te enviamos una copia a <strong>{reservaCompletada.emailComprador}</strong>.
                </p>

                {/* VOUCHER / TICKET VISUAL */}
                <div className="bg-[#FFFDF5] border-2 border-[#181818] rounded-2xl p-5 text-left space-y-3 relative overflow-hidden shadow-inner">
                  <div className="flex justify-between items-center pb-2 border-b border-neutral-200">
                    <span className="font-carter text-sm text-[#F3D84F] bg-[#181818] px-2.5 py-0.5 rounded">
                      A Todo Ritmo • Ticket Oficial
                    </span>
                    <span className="font-mono text-xs font-bold text-[#24AB58]">
                      {reservaCompletada.codigoTicket}
                    </span>
                  </div>

                  <div className="text-xs space-y-1 text-neutral-800">
                    <p><strong>Evento:</strong> {eventoParaReserva.titulo}</p>
                    <p><strong>Titular:</strong> {reservaCompletada.nombreComprador}</p>
                    <p><strong>Butacas:</strong> {reservaCompletada.cantidad} persona(s)</p>
                    <p><strong>Total:</strong> ${reservaCompletada.totalAbonado.toLocaleString('es-AR')} ({reservaCompletada.metodoPago})</p>
                    <p><strong>Fecha y Lugar:</strong> {eventoParaReserva.fecha} - {eventoParaReserva.hora} ({eventoParaReserva.lugar})</p>
                  </div>

                  {/* Código de barras simulado */}
                  <div className="pt-2 border-t border-dashed border-neutral-300 flex flex-col items-center">
                    <div className="font-mono text-xs tracking-widest text-neutral-500 font-bold">
                      ||| | |||| | |||||| || | |||| |||
                    </div>
                    <span className="text-[10px] text-neutral-400 font-mono mt-0.5">
                      {reservaCompletada.codigoTicket}
                    </span>
                  </div>
                </div>

                <button
                  onClick={handleCerrarModal}
                  className="w-full py-3 bg-[#181818] text-[#F3D84F] hover:bg-neutral-800 font-carter text-sm rounded-xl transition-colors shadow"
                >
                  Cerrar y Volver a la Agenda
                </button>
              </div>
            )}
          </div>
        </div>
      )}
    </div>
  );
};
