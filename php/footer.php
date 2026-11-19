<!-- footer.php -->
<style>
    #footer-escuela {
        background-color: #181818 !important;
        color: #FFFAE6 !important;
        border-top: 4px solid #24AB58 !important;
        margin-top: 80px !important;
        width: 100% !important;
        padding: 45px 0 !important;
        min-height: 240px !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
    }

    #footer-escuela * {
        box-sizing: border-box !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    #footer-escuela .footer-grid {
        max-width: 1200px !important;
        width: 100% !important;
        margin: 0 auto !important;
        padding: 0 20px !important;
        display: grid !important;
        grid-template-columns: 280px 180px 220px 240px !important;
        justify-content: space-between !important;
        align-items: start !important;
        gap: 24px !important;
    }

    #footer-escuela .footer-col {
        display: block !important;
    }

    #footer-escuela .footer-col h4,
    #footer-escuela .footer-grid .footer-col h4 {
        font-family: 'Carter One', cursive !important;
        color: #F3D84F !important;
        font-size: 17px !important;
        font-weight: normal !important;
        line-height: 24px !important;
        margin-bottom: 15px !important;
    }

    #footer-escuela .footer-col p,
    #footer-escuela .footer-grid .footer-col p,
    #footer-escuela .footer-col strong {
        font-family: 'Mukta', Arial, Helvetica, sans-serif !important;
        font-size: 14px !important;
        line-height: 1.6 !important;
        color: #cccccc !important;
        margin-bottom: 10px !important;
    }

    #footer-escuela .footer-col ul {
        list-style: none !important;
    }

    #footer-escuela .footer-col ul li {
        margin-bottom: 6px !important;
    }

    #footer-escuela .footer-col ul li a,
    #footer-escuela .footer-grid .footer-col ul li a {
        font-family: 'Mukta', Arial, Helvetica, sans-serif !important;
        font-size: 13.5px !important;
        line-height: 20px !important;
        color: #ffffff !important;
        text-decoration: none !important;
    }

    #footer-escuela .footer-col ul li a:hover {
        color: #F3D84F !important;
    }

    @media (max-width: 992px) {
        #footer-escuela {
            height: auto !important;
            padding: 30px 20px !important;
        }
        #footer-escuela .footer-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 30px !important;
            padding: 0 !important;
        }
    }

    @media (max-width: 650px) {
        #footer-escuela .footer-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<footer id="footer-escuela">
    <div class="footer-grid">
        <div class="footer-col">
            <h4>A Todo Ritmo</h4>
            <p>Escuela de música comunitaria que brinda talleres integrales, ensambles en vivo y préstamo gratuito de instrumentos para todas las edades.</p>
        </div>
        <div class="footer-col">
            <h4>Navegación</h4>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="nosotros.php">Nosotros</a></li>
                <li><a href="talleres.php">Talleres</a></li>
                <li><a href="eventos.php">Eventos</a></li>
                <li><a href="docentes.php">Docentes</a></li>
                <li><a href="inscripcion.php">Inscripción</a></li>
                <li><a href="consultas.php">Consultas</a></li>
                <li><a href="login.php" style="color:#F3D84F !important;">Acceso Administrador</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Sedes</h4>
            <p><strong>San Telmo:</strong> Defensa 842<br>
               <strong>Almagro:</strong> Av. Corrientes 4120</p>
        </div>
        <div class="footer-col">
            <h4>Contacto</h4>
            <p>WhatsApp: (011) 15-5829-1022<br>
               Email: info@atodoritmo.edu.ar</p>
        </div>
    </div>
</footer>
