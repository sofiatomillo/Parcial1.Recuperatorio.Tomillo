<?php
require_once __DIR__ . '/conexion.php';

try {
    $sqlDocentes = "SELECT * FROM docentes ORDER BY id ASC";
    $stmt = $pdo->prepare($sqlDocentes);
    $stmt->execute();
    $docentes_db = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $docentes = [];
    
    foreach ($docentes_db as $doc) {
        $bio_real = isset($doc['biografia']) ? $doc['biografia'] : (isset($doc['descripcion']) ? $doc['descripcion'] : 'Docente de la institución.');
        $cv_real = isset($doc['trayectoria_cv']) ? $doc['trayectoria_cv'] : (isset($doc['cv']) ? $doc['cv'] : 'Egresado/a de conservatorios nacionales.');
        
        // 🟢 CORRECCIÓN DIRECTA: Forzamos a leer la ruta de la base de datos limpia
        // Si por alguna razón está vacía en la DB, le dejamos un descarte genérico local
        $ruta_imagen = !empty($doc['imagen']) ? $doc['imagen'] : 'img/default-docente.jpg';
        
        $instrumento_real = !empty($doc['instrumento_principal']) ? $doc['instrumento_principal'] : 'Música';

        $talleres_docente = [];
        if (!empty($doc['talleres_texto'])) {
            $lista = explode(',', $doc['talleres_texto']);
            foreach($lista as $t) {
                $talleres_docente[] = ['nombre' => trim($t), 'link' => 'inscripcion.php?taller=' . $doc['id']];
            }
        } else {
            $talleres_docente[] = ['nombre' => 'Taller de ' . $instrumento_real, 'link' => 'inscripcion.php?taller=' . $doc['id']];
        }

        $docentes[] = [
            'id'                    => $doc['id'],
            'nombre'                => isset($doc['nombre']) ? $doc['nombre'] : 'Docente',
            'apellido'              => isset($doc['apellido']) ? $doc['apellido'] : '', 
            'especialidad'          => isset($doc['especialidad']) ? $doc['especialidad'] : 'Música Popular',
            'instrumento_principal' => $instrumento_real, 
            'biografia'             => $bio_real,
            'email'                 => 'info@atodoritmo.edu.ar', 
            
            // 🟢 PASAMOS LA RUTA LIMPIA CORREGIDA
            'imagen'                => $ruta_imagen,
            
            'talleres'              => $talleres_docente,
            'trayectoria_cv'        => $cv_real 
        ];
    }
} catch (PDOException $e) {
    $docentes = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuerpo Docente | Escuela de Música "A Todo Ritmo"</title>
    <link rel="stylesheet" href="index.css">

    <style>
        body { 
        font-family: 'Mukta', Arial, sans-serif !important; 
        }

        h2, h3, .malla-docentes-propia h3 {
        font-family: 'Carter One', cursive !important;
        font-weight: 400 !important;
        }

        .malla-docentes-propia { 
            display: grid !important; 
            grid-template-columns: repeat(3, 1fr) !important; 
            gap: 30px !important; 
            max-width: 1200px; 
            margin: 40px auto 60px auto; 
            padding: 0 20px; 
            box-sizing: border-box; 
        }
        .bloque-tarjeta-docente { background: #ffffff !important; border: 2px solid #181818 !important; border-radius: 24px !important; overflow: hidden !important; box-shadow: 4px 4px 0px #181818 !important; display: flex !important; flex-direction: column !important; width: 100% !important; max-width: 360px !important; margin: 0 auto !important; padding: 0 !important; box-sizing: border-box; }
        .caja-foto-docente { height: 250px !important; position: relative !important; border-bottom: 2px solid #181818 !important; background: #e3e1da !important; overflow: hidden !important; width: 100% !important; }
        .caja-foto-docente img { width: 100% !important; height: 100% !important; object-fit: cover !important; display: block !important; }
        .etiqueta-doc-instrumento { position: absolute !important; top: 15px !important; left: 15px !important; background-color: #ffcc00 !important; color: #181818 !important; border: 2px solid #181818 !important; font-weight: 700 !important; padding: 6px 15px !important; border-radius: 50px !important; font-size: 13px !important; z-index: 5; }
        .cuerpo-tarjeta-docente { padding: 25px !important; display: flex !important; flex-direction: column !important; background: #ffffff !important; flex-grow: 1 !important; }
        .cuerpo-tarjeta-docente h3 { font-size: 22px !important; font-weight: 800 !important; color: #181818 !important; margin: 0 0 5px 0 !important; text-align: left !important; }
        .cuerpo-tarjeta-docente .subtitulo-esp { color: #24AB58 !important; font-weight: 600 !important; font-size: 14px !important; margin: 0 0 15px 0 !important; text-align: left !important; }
        .cuerpo-tarjeta-docente .resumen-bio { font-size: 13.5px !important; line-height: 1.6 !important; color: #6c757d !important; margin: 0 0 20px 0 !important; text-align: left !important; }
        .tit-talleres { text-transform: uppercase; font-weight: 700; color: #6c757d; font-size: 11px; letter-spacing: 0.5px; margin: 0 0 8px 0; text-align: left !important; }
        .fila-taller-item { display: flex !important; justify-content: space-between !important; align-items: center !important; padding: 10px 12px !important; background-color: #f8f9fa !important; border: 1px solid #dee2e6 !important; border-radius: 8px !important; margin-bottom: 8px !important; }
        .fila-taller-item span { font-size: 13px !important; font-weight: 500 !important; color: #212529 !important; }
        .btn-taller-comprar { background-color: #24AB58 !important; color: #ffffff !important; text-decoration: none !important; font-weight: 600 !important; font-size: 12px !important; padding: 6px 16px !important; border-radius: 50px !important; border: 1px solid #181818 !important; display: inline-block !important; }
        .desplegable-cv-nat { border-top: 1px solid #dee2e6 !important; margin-top: 15px !important; padding-top: 10px !important; }
        .desplegable-cv-nat summary { font-size: 14px !important; font-weight: 700 !important; color: #181818 !important; cursor: pointer !important; list-style: none !important; display: flex !important; justify-content: space-between !important; align-items: center; padding: 8px 4px; }
        .desplegable-cv-nat summary::-webkit-details-marker { display: none !important; }
        .desplegable-cv-nat summary .tit-btn-cv { display: flex; align-items: center; }
        .desplegable-cv-nat summary .tit-btn-cv::before { content: "🔰" !important; margin-right: 8px; }
        .desplegable-cv-nat summary::after { content: "▼" !important; font-size: 11px; color: #181818; }
        .desplegable-cv-nat[open] summary::after { content: "▲" !important; }
        .desplegable-cv-nat .caja-interior-cv { background-color: #fbf9f1 !important; border: 1px solid #f3eedb !important; border-radius: 16px !important; padding: 20px !important; margin-top: 10px !important; box-sizing: border-box !important; }
        .desplegable-cv-nat .caja-interior-cv strong { font-size: 14px !important; color: #181818 !important; display: flex !important; align-items: center !important; margin-bottom: 8px !important; }
        .desplegable-cv-nat .caja-interior-cv strong::before { content: "✓" !important; color: #24AB58 !important; font-weight: 900 !important; margin-right: 6px !important; }
        .desplegable-cv-nat .caja-interior-cv p { font-size: 13.5px !important; line-height: 1.6 !important; color: #555555 !important; margin: 0 0 15px 0 !important; text-align: left !important; }
        .desplegable-cv-nat .contacto-docente-bloque { border-top: 1px solid #ebdcb9 !important; padding-top: 12px !important; margin-top: 10px !important; }
        .desplegable-cv-nat .contacto-item { font-size: 12.5px !important; color: #6c757d !important; margin-bottom: 6px !important; display: flex !important; align-items: center !important; }
        .desplegable-cv-nat .contacto-item:last-child { margin-bottom: 0 !important; }
        .seccion-final-cta { background-color: #181818 !important; border-top: 4px solid #ffcc00 !important; border-bottom: 4px solid #ffcc00 !important; text-align: center !important; padding: 50px 20px !important; color: #ffcc00 !important; margin-top: 40px; }
        .seccion-final-cta h3 { font-size: 28px !important; font-weight: 800 !important; margin: 0 0 10px 0 !important; }
        .seccion-final-cta p { color: #ffffff !important; font-size: 14px !important; margin: 0 0 25px 0 !important; line-height: 1.5 !important; }
        .btn-final-enlace { background-color: #24AB58 !important; color: #ffffff !important; font-weight: 700 !important; text-decoration: none !important; padding: 12px 40px !important; border-radius: 50px !important; border: 1px solid #ffcc00 !important; display: inline-block !important; font-size: 15px !important; }
    </style>
        <!-- 🟢 Importación de las tipografías oficiales de "A Todo Ritmo" -->
    <link rel="preconnect" href="https://googleapis.com">
    <link rel="preconnect" href="https://gstatic.com" crossorigin>
    <link href="https://googleapis.com/css2?family=Carter+One&family=Mukta:wght@300;400;600;700;800&display=swap" rel="stylesheet">

</head>

<body>
    <!-- HEADER -->
    <?php include 'header.php'; ?>

        
        <div style="text-align: center; max-width: 800px; margin: 60px auto 40px auto; padding: 0 20px; box-sizing: border-box;">
            <span style="color: #24AB58; text-transform: uppercase; font-weight: 700; font-size: 14px; letter-spacing: 1px; display: block; margin-bottom: 8px;">
                Equipo Pedagógico & Artístico
            </span>
            <h2 style="font-size: 38px; font-weight: 800; margin: 0 0 15px 0; color: #181818; letter-spacing: -0.5px;">
                Nuestro Cuerpo Docente
            </h2>
            <p style="font-size: 16px; color: #6c757d; line-height: 1.6; margin: 0;">
                Músicos profesionales y pedagogos graduados de conservatorios nacionales (Manuel de Falla, UNA, Castro) con amplia trayectoria escénica y vocación social.
            </p>
        </div>


        <div class="malla-docentes-propia">
            <?php if (!empty($docentes)): ?>
                <?php foreach ($docentes as $prof): ?>
                    
                    <!-- 🟢 CORRECCIÓN: Contenedor principal de la tarjeta -->
                    <div class="bloque-tarjeta-docente" id="docente-<?= $prof['id'] ?>">
                        
                        <!-- Foto y etiqueta de cabecera dinámica -->
                        <div class="caja-foto-docente">
                            <img src="<?= htmlspecialchars($prof['imagen']) ?>" alt="<?= htmlspecialchars($prof['nombre'] . ' ' . $prof['apellido']) ?>">
                            
                            <!-- Muestra el instrumento real mapeado arriba -->
                            <span class="etiqueta-doc-instrumento"><?= htmlspecialchars($prof['instrumento_principal']) ?></span>
                        </div>



                        <!-- Datos internos de cada profesor -->
                        <div class="cuerpo-tarjeta-docente">
                            <h3><?= htmlspecialchars($prof['nombre'] . ' ' . $prof['apellido']) ?></h3>
                            <p class="subtitulo-esp"><?= htmlspecialchars($prof['especialidad']) ?></p>
                            
                            <p class="resumen-bio"><?= htmlspecialchars($prof['biografia']) ?></p>

                            <p class="tit-talleres">Talleres que dicta:</p>
                            <?php foreach ($prof['talleres'] as $taller): ?>
                                <div class="fila-taller-item">
                                    <span><?= htmlspecialchars($taller['nombre']) ?></span>
                                    <a href="<?= htmlspecialchars($taller['link']) ?>" class="btn-taller-comprar">Inscribirme</a>
                                </div>
                            <?php endforeach; ?>

                            <!-- Acordeón Desplegable Premium Renovado -->
                            <details class="desplegable-cv-nat">
                                <summary>
                                    <span class="tit-btn-cv">Ver CV & Trayectoria Académica</span>
                                </summary>
                                
                                <!-- Caja contenedora interna crema -->
                                <div class="caja-interior-cv">
                                    <strong>Trayectoria y Formación:</strong>
                                    <p><?= htmlspecialchars($prof['trayectoria_cv']) ?></p>
                                    
                                    <!-- Bloque de datos de contacto (Email y Teléfono) -->
                                    <div class="contacto-docente-bloque">
                                        <div class="contacto-item">
                                            <span style="margin-right: 8px;">✉</span> 
                                            <?= htmlspecialchars($prof['email']) ?>
                                        </div>
                                        <div class="contacto-item">
                                            <span style="margin-right: 8px;">📞</span> 
                                            (011) 15-5829-1022
                                        </div>
                                    </div>
                                </div>
                            </details>


                        </div>
                    </div> <!-- 🟢 Cierre correcto de cada tarjeta individual -->

                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: span 3; text-align: center; background: #fff3cd; border: 1px solid #ffe69c; padding: 20px; border-radius: 10px;">
                    No se encontraron docentes cargados en este momento.
                </div>
            <?php endif; ?>
        </div>

        <div class="seccion-final-cta">
            <h3>¿Querés formarte con nuestro equipo docente?</h3>
            <p>Todas las comisiones tienen vacantes limitadas para asegurar un seguimiento cercano.<br>¡Inscribite hoy y asegurá tu lugar para el ciclo 2026!</p>
            <a href="inscripcion.php" class="btn-final-enlace">Comenzar Inscripción Online</a>
        </div>

        <?php include 'footer.php'; ?>
</body>
</html>
