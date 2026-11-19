import React, { useState } from 'react';
import { PreguntaFrecuente, ContenidoPagina, Sede } from '../types';
import { DataService } from '../services/dataService';
import { Send, CheckCircle2, ChevronDown, ChevronUp, Phone, Mail, MapPin } from 'lucide-react';

interface ConsultasSectionProps {
  preguntasFrecuentes: PreguntaFrecuente[];
  contenidoPagina: ContenidoPagina;
  sedes: Sede[];
  onNotificarSqlEjecutado: (sql: string) => void;
}

export const ConsultasSection: React.FC<ConsultasSectionProps> = ({
  preguntasFrecuentes,
  contenidoPagina,
  sedes,
  onNotificarSqlEjecutado
}) => {
  // Estado del formulario de consulta
  const [nombre, setNombre] = useState('');
  const [email, setEmail] = useState('');
  const [telefono, setTelefono] = useState('');
  const [motivo, setMotivo] = useState('Consulta sobre Talleres');
  const [mensaje, setMensaje] = useState('');
  const [enviadoExitoso, setEnviadoExitoso] = useState(false);

  // Acordeón de FAQs
  const [faqAbiertaId, setFaqAbiertaId] = useState<number | null>(1);
  const [categoriaFaq, setCategoriaFaq] = useState<string>('todas');

  const categorias = ['todas', 'Instrumentos y Préstamos', 'Inscripciones', 'Cuotas y Becas', 'Niveles y Edades'];

  const faqsFiltradas = preguntasFrecuentes.filter((faq) => {
    if (categoriaFaq === 'todas') return true;
    return faq.categoria === categoriaFaq;
  });

  const toggleFaq = (id: number) => {
    setFaqAbiertaId(faqAbiertaId === id ? null : id);
  };

  const handleEnviarConsulta = (e: React.FormEvent) => {
    e.preventDefault();
    if (!nombre.trim() || !email.trim() || !mensaje.trim()) return;

    const { sqlQuery } = DataService.registrarConsulta({
      nombre: nombre.trim(),
      email: email.trim(),
      telefono: telefono.trim(),
      motivo,
      mensaje: mensaje.trim()
    });

    onNotificarSqlEjecutado(sqlQuery);
    setEnviadoExitoso(true);
    setNombre('');
    setEmail('');
    setTelefono('');
    setMensaje('');
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-16">
      {/* CABECERA */}
      <div className="text-center max-w-3xl mx-auto space-y-3">
        <span className="text-xs font-bold text-[#24AB58] uppercase tracking-wider">
          Atención a la Comunidad
        </span>
        <h1 className="font-carter text-3xl sm:text-5xl text-[#181818]">
          Espacio de Consultas & Preguntas Frecuentes
        </h1>
        <p className="text-sm sm:text-base text-neutral-700 leading-relaxed font-sans">
          ¿Tenés dudas sobre las clases, el banco de instrumentos, las cuotas o las sedes? Estamos para ayudarte.
        </p>
      </div>

      {/* SECCIÓN PREGUNTAS FRECUENTES (FAQ) */}
      <section className="space-y-6">
        <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
          <div>
            <h2 className="font-carter text-2xl text-[#181818]">
              Preguntas Frecuentes
            </h2>
            <p className="text-xs text-neutral-600">
              Respuestas rápidas a las consultas más habituales de ingresantes y familias.
            </p>
          </div>

          {/* Filtro por categoría de FAQ */}
          <div className="flex flex-wrap gap-1.5">
            {categorias.map((cat) => (
              <button
                key={cat}
                onClick={() => setCategoriaFaq(cat)}
                className={`px-3 py-1 rounded-full text-xs font-semibold transition-all ${
                  categoriaFaq === cat
                    ? 'bg-[#181818] text-[#F3D84F]'
                    : 'bg-[#FFFDF5] text-neutral-700 border border-neutral-300 hover:bg-neutral-200'
                }`}
              >
                {cat === 'todas' ? 'Todas las Categorías' : cat}
              </button>
            ))}
          </div>
        </div>

        {/* Acordeón de FAQs */}
        <div className="space-y-3">
          {faqsFiltradas.map((faq) => {
            const estaAbierta = faqAbiertaId === faq.id;

            return (
              <div
                key={faq.id}
                className={`bg-[#FFFDF5] border-2 rounded-2xl overflow-hidden transition-all shadow-sm ${
                  estaAbierta ? 'border-[#24AB58]' : 'border-[#181818]'
                }`}
              >
                <button
                  onClick={() => toggleFaq(faq.id)}
                  className="w-full p-4 sm:p-5 text-left flex items-center justify-between gap-4 focus:outline-none"
                >
                  <div className="space-y-1">
                    <span className="text-[10px] font-bold text-[#24AB58] uppercase tracking-wider block">
                      {faq.categoria}
                    </span>
                    <h3 className="font-carter text-base sm:text-lg text-[#181818]">
                      {faq.pregunta}
                    </h3>
                  </div>
                  <div className={`p-1.5 rounded-full ${estaAbierta ? 'bg-[#24AB58] text-[#FFFAE6]' : 'bg-neutral-200 text-neutral-700'}`}>
                    {estaAbierta ? <ChevronUp className="w-4 h-4" /> : <ChevronDown className="w-4 h-4" />}
                  </div>
                </button>

                {estaAbierta && (
                  <div className="px-5 pb-5 pt-1 border-t border-neutral-200 text-xs sm:text-sm text-neutral-700 leading-relaxed font-sans animate-fadeIn">
                    <p>{faq.respuesta}</p>
                  </div>
                )}
              </div>
            );
          })}
        </div>
      </section>

      {/* SECCIÓN FORMULARIO DE CONSULTA & DATOS DE CONTACTO */}
      <section className="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
        {/* Canales de Contacto Directo */}
        <div className="lg:col-span-5 bg-[#181818] text-[#FFFAE6] border-4 border-[#F3D84F] rounded-3xl p-6 sm:p-8 space-y-6 shadow-xl">
          <div className="space-y-2">
            <span className="text-xs font-bold text-[#F3D84F] uppercase tracking-wider">
              Secretaría & Atención
            </span>
            <h3 className="font-carter text-2xl text-[#FFFAE6]">
              Contactate Directamente
            </h3>
            <p className="text-xs text-neutral-300 leading-relaxed">
              Podés acercarte personalmente a nuestras sedes o comunicarte por nuestros canales virtuales.
            </p>
          </div>

          <div className="space-y-4 text-xs text-neutral-300">
            <div className="flex items-start gap-3 p-3 bg-neutral-900 rounded-xl border border-neutral-800">
              <Phone className="w-4 h-4 text-[#F3D84F] shrink-0 mt-0.5" />
              <div>
                <strong className="text-white block">WhatsApp de Admisiones:</strong>
                {contenidoPagina.contactoWhatsapp}
              </div>
            </div>

            <div className="flex items-start gap-3 p-3 bg-neutral-900 rounded-xl border border-neutral-800">
              <Mail className="w-4 h-4 text-[#24AB58] shrink-0 mt-0.5" />
              <div>
                <strong className="text-white block">Correo Electrónico:</strong>
                {contenidoPagina.contactoEmail}
              </div>
            </div>

            {sedes.map((sede, idx) => (
              <div key={sede.id} className="flex items-start gap-3 p-3 bg-neutral-900 rounded-xl border border-neutral-800">
                <MapPin className={`w-4 h-4 shrink-0 mt-0.5 ${idx % 2 === 0 ? 'text-[#F3D84F]' : 'text-[#24AB58]'}`} />
                <div>
                  <strong className="text-white block">{sede.nombre}:</strong>
                  {sede.direccion}, {sede.barrio} ({sede.horariosAtencion})
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* Formulario de Mensaje */}
        <div className="lg:col-span-7 bg-[#FFFDF5] border-3 border-[#181818] rounded-3xl p-6 sm:p-10 shadow-xl space-y-6">
          <div>
            <h3 className="font-carter text-2xl text-[#181818]">
              Dejanos tu Mensaje
            </h3>
            <p className="text-xs text-neutral-600 mt-1">
              Respondemos habitualmente en menos de 24 horas hábiles.
            </p>
          </div>

          {enviadoExitoso ? (
            <div className="p-6 bg-emerald-50 border-2 border-[#24AB58] rounded-2xl text-center space-y-3 animate-fadeIn">
              <div className="w-12 h-12 rounded-full bg-[#24AB58] text-[#FFFAE6] flex items-center justify-center mx-auto shadow">
                <CheckCircle2 className="w-7 h-7" />
              </div>
              <h4 className="font-carter text-xl text-[#181818]">¡Consulta Enviada con Éxito!</h4>
              <p className="text-xs text-neutral-700">
                Muchas gracias por contactarte. El equipo de secretaría te responderá a la brevedad.
              </p>
              <button
                onClick={() => setEnviadoExitoso(false)}
                className="px-4 py-2 bg-[#24AB58] text-[#FFFAE6] text-xs font-bold rounded-lg"
              >
                Enviar otra consulta
              </button>
            </div>
          ) : (
            <form onSubmit={handleEnviarConsulta} className="space-y-4 text-xs">
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="block font-bold text-neutral-800 mb-1">Nombre Completo *</label>
                  <input
                    type="text"
                    required
                    placeholder="Tu nombre y apellido"
                    value={nombre}
                    onChange={(e) => setNombre(e.target.value)}
                    className="w-full p-2.5 bg-[#FFFAE6] border border-neutral-300 rounded-xl focus:border-[#24AB58] focus:outline-none"
                  />
                </div>

                <div>
                  <label className="block font-bold text-neutral-800 mb-1">Correo Electrónico *</label>
                  <input
                    type="email"
                    required
                    placeholder="tu@email.com"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    className="w-full p-2.5 bg-[#FFFAE6] border border-neutral-300 rounded-xl focus:border-[#24AB58] focus:outline-none"
                  />
                </div>
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="block font-bold text-neutral-800 mb-1">Teléfono (Opcional)</label>
                  <input
                    type="tel"
                    placeholder="11 5544-3322"
                    value={telefono}
                    onChange={(e) => setTelefono(e.target.value)}
                    className="w-full p-2.5 bg-[#FFFAE6] border border-neutral-300 rounded-xl focus:border-[#24AB58] focus:outline-none"
                  />
                </div>

                <div>
                  <label className="block font-bold text-neutral-800 mb-1">Motivo de Consulta *</label>
                  <select
                    value={motivo}
                    onChange={(e) => setMotivo(e.target.value)}
                    className="w-full p-2.5 bg-[#FFFAE6] border border-neutral-300 rounded-xl focus:border-[#24AB58] focus:outline-none"
                  >
                    <option value="Consulta sobre Talleres">Consulta sobre Talleres y Horarios</option>
                    <option value="Banco de Instrumentos">Préstamo de Instrumentos</option>
                    <option value="Aranceles y Becas">Aranceles y Becas Comunitarias</option>
                    <option value="Eventos y Entradas">Eventos y Muestras</option>
                    <option value="Otro Asunto">Otro Asunto</option>
                  </select>
                </div>
              </div>

              <div>
                <label className="block font-bold text-neutral-800 mb-1">Tu Mensaje *</label>
                <textarea
                  required
                  rows={4}
                  placeholder="Escribinos tus dudas o comentarios..."
                  value={mensaje}
                  onChange={(e) => setMensaje(e.target.value)}
                  className="w-full p-3 bg-[#FFFAE6] border border-neutral-300 rounded-xl focus:border-[#24AB58] focus:outline-none"
                />
              </div>

              <button
                type="submit"
                className="w-full py-3 bg-[#24AB58] hover:bg-[#1e8f49] text-[#FFFAE6] font-carter text-sm rounded-xl shadow transition-colors flex items-center justify-center gap-2"
              >
                <Send className="w-4 h-4" />
                <span>Enviar Consulta</span>
              </button>
            </form>
          )}
        </div>
      </section>
    </div>
  );
};
