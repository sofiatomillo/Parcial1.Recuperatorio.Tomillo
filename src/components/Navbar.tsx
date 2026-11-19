import React, { useState } from 'react';
import { SeccionActiva, ContenidoPagina, AdminUser } from '../types';
import { Music, Menu, X, Database, Calendar, Users, BookOpen, HelpCircle, UserCheck, Shield } from 'lucide-react';

interface NavbarProps {
  seccionActiva: SeccionActiva;
  onCambiarSeccion: (seccion: SeccionActiva) => void;
  onAbrirSqlModal: () => void;
  contenidoPagina: ContenidoPagina;
  adminActual: Omit<AdminUser, 'password'> | null;
}

export const Navbar: React.FC<NavbarProps> = ({
  seccionActiva,
  onCambiarSeccion,
  onAbrirSqlModal,
  contenidoPagina,
  adminActual
}) => {
  const [menuMovilAbierto, setMenuMovilAbierto] = useState(false);

  const enlaces: { id: SeccionActiva; etiqueta: string; icono: React.ReactNode }[] = [
    { id: 'home', etiqueta: 'Home', icono: <Music className="w-4 h-4" /> },
    { id: 'nosotros', etiqueta: 'Nosotros', icono: <Users className="w-4 h-4" /> },
    { id: 'talleres', etiqueta: 'Talleres', icono: <BookOpen className="w-4 h-4" /> },
    { id: 'eventos', etiqueta: 'Eventos', icono: <Calendar className="w-4 h-4" /> },
    { id: 'docentes', etiqueta: 'Docentes', icono: <Users className="w-4 h-4" /> },
    { id: 'inscripcion', etiqueta: 'Inscripción', icono: <UserCheck className="w-4 h-4" /> },
    { id: 'consultas', etiqueta: 'Consultas', icono: <HelpCircle className="w-4 h-4" /> },
  ];

  const handleNavegar = (id: SeccionActiva) => {
    onCambiarSeccion(id);
    setMenuMovilAbierto(false);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  return (
    <header className="sticky top-0 z-40 bg-[#181818] text-[#FFFAE6] border-b-4 border-[#F3D84F] shadow-lg">
      {/* Barra superior de anuncios e información rápida */}
      <div className="bg-[#24AB58] text-[#FFFAE6] text-xs sm:text-sm py-1.5 px-4 font-semibold tracking-wide">
        <div className="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-1">
          <div className="flex items-center gap-2">
            <span className="inline-block w-2 h-2 rounded-full bg-[#F3D84F] animate-pulse"></span>
            <span>{contenidoPagina.bannerSuperiorIzq}</span>
          </div>
          <div className="flex items-center gap-4 text-xs">
            <span>{contenidoPagina.bannerSuperiorDer}</span>
            <span className="hidden md:inline">|</span>
            <span className="hidden md:inline">WhatsApp: {contenidoPagina.contactoWhatsapp.split('(')[1] ? `(${contenidoPagina.contactoWhatsapp.split('(')[1]}` : contenidoPagina.contactoWhatsapp}</span>
          </div>
        </div>
      </div>

      {/* Barra de navegación principal */}
      <nav className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
        <div className="flex items-center justify-between gap-2">
          {/* Logo y Nombre del caso */}
          <button
            onClick={() => handleNavegar('home')}
            className="flex items-center gap-3 group text-left focus:outline-none shrink-0"
            id="btn-logo-home"
          >
            <div className="w-11 h-11 rounded-xl bg-[#F3D84F] text-[#181818] flex items-center justify-center font-bold shadow-md transform group-hover:rotate-6 transition-transform">
              <Music className="w-6 h-6 text-[#181818]" />
            </div>
            <div>
              <span className="block font-carter text-xl sm:text-2xl text-[#F3D84F] tracking-wide leading-none">
                A Todo Ritmo
              </span>
              <span className="text-[11px] sm:text-xs text-[#FFFAE6]/80 font-medium uppercase tracking-wider">
                Escuela de Música Comunitaria
              </span>
            </div>
          </button>

          {/* Menú para pantallas medianas/grandes */}
          <div className="hidden xl:flex items-center space-x-1">
            {enlaces.map((enlace) => {
              const estaActivo = seccionActiva === enlace.id;
              return (
                <button
                  key={enlace.id}
                  id={`nav-link-${enlace.id}`}
                  onClick={() => handleNavegar(enlace.id)}
                  className={`px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-200 flex items-center gap-1.5 ${
                    estaActivo
                      ? 'bg-[#F3D84F] text-[#181818] shadow-sm font-bold'
                      : 'text-[#FFFAE6] hover:bg-[#24AB58]/30 hover:text-[#F3D84F]'
                  }`}
                >
                  {enlace.etiqueta}
                </button>
              );
            })}
          </div>

          {/* Botones de acción derecha */}
          <div className="hidden sm:flex items-center gap-2 shrink-0">
            {/* Botón Ingresar como Administrador */}
            <button
              onClick={() => handleNavegar('admin')}
              id="btn-nav-admin"
              title="Acceso para Administradores"
              className={`flex items-center gap-1.5 px-3 py-2 text-xs font-bold rounded-lg border transition-colors ${
                seccionActiva === 'admin'
                  ? 'bg-[#F3D84F] text-[#181818] border-[#F3D84F]'
                  : adminActual
                    ? 'bg-[#24AB58]/25 border-[#24AB58] text-[#F3D84F] hover:bg-[#24AB58] hover:text-white'
                    : 'border-neutral-600 text-[#FFFAE6] hover:border-[#F3D84F] hover:text-[#F3D84F]'
              }`}
            >
              <Shield className="w-3.5 h-3.5" />
              <span>{adminActual ? 'Panel Admin' : 'Ingresar como Admin'}</span>
            </button>

            {/* Botón Ver Consultas SQL / Lógica de Base de Datos */}
            <button
              onClick={onAbrirSqlModal}
              id="btn-nav-sql-modal"
              title="Ver esquema SQL y consultas relacionales"
              className="flex items-center gap-1.5 px-3 py-2 text-xs font-bold rounded-lg border border-[#F3D84F]/60 text-[#F3D84F] hover:bg-[#F3D84F] hover:text-[#181818] transition-colors"
            >
              <Database className="w-3.5 h-3.5" />
              <span>Lógica SQL</span>
            </button>

            {/* Botón directo a inscripción */}
            <button
              onClick={() => handleNavegar('inscripcion')}
              id="btn-nav-inscribirme-cta"
              className="px-4 py-2 bg-[#24AB58] hover:bg-[#1f934b] text-[#FFFAE6] font-carter text-sm rounded-lg shadow-md hover:shadow-lg transition-transform transform active:scale-95"
            >
              ¡Inscribirme!
            </button>
          </div>

          {/* Botón hamburguesa móvil */}
          <div className="xl:hidden flex items-center gap-2">
            <button
              onClick={() => handleNavegar('admin')}
              id="btn-nav-admin-mobile"
              className="sm:hidden p-2 text-xs font-bold rounded-md bg-[#F3D84F] text-[#181818]"
              title="Ingresar como Admin"
            >
              <Shield className="w-4 h-4" />
            </button>
            <button
              onClick={onAbrirSqlModal}
              id="btn-nav-sql-modal-mobile"
              className="sm:hidden p-2 text-xs font-bold rounded-md bg-[#24AB58]/30 text-[#F3D84F]"
              title="Lógica SQL"
            >
              <Database className="w-4 h-4" />
            </button>
            <button
              onClick={() => setMenuMovilAbierto(!menuMovilAbierto)}
              id="btn-menu-movil-toggle"
              className="p-2 rounded-lg text-[#FFFAE6] hover:bg-neutral-800 focus:outline-none"
              aria-label="Abrir menú de navegación"
            >
              {menuMovilAbierto ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
            </button>
          </div>
        </div>

        {/* Menú desplegable móvil */}
        {menuMovilAbierto && (
          <div className="xl:hidden mt-3 pt-3 border-t border-neutral-700 space-y-1 pb-2 animate-fadeIn">
            {enlaces.map((enlace) => {
              const estaActivo = seccionActiva === enlace.id;
              return (
                <button
                  key={enlace.id}
                  id={`nav-mobile-${enlace.id}`}
                  onClick={() => handleNavegar(enlace.id)}
                  className={`w-full text-left px-4 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-3 ${
                    estaActivo
                      ? 'bg-[#F3D84F] text-[#181818] font-bold'
                      : 'text-[#FFFAE6] hover:bg-neutral-800'
                  }`}
                >
                  {enlace.icono}
                  <span>{enlace.etiqueta}</span>
                </button>
              );
            })}
            <button
              onClick={() => handleNavegar('admin')}
              className={`w-full text-left px-4 py-2.5 rounded-lg text-sm font-bold flex items-center gap-3 ${
                seccionActiva === 'admin'
                  ? 'bg-[#F3D84F] text-[#181818]'
                  : 'text-[#F3D84F] hover:bg-neutral-800'
              }`}
            >
              <Shield className="w-4 h-4" />
              <span>{adminActual ? 'Panel de Administración' : 'Ingresar como Administrador'}</span>
            </button>
            <div className="pt-2">
              <button
                onClick={() => handleNavegar('inscripcion')}
                className="w-full py-2.5 bg-[#24AB58] hover:bg-[#1f934b] text-[#FFFAE6] font-carter text-sm rounded-lg text-center shadow-md"
              >
                Comenzar Inscripción 2026
              </button>
            </div>
          </div>
        )}
      </nav>
    </header>
  );
};
