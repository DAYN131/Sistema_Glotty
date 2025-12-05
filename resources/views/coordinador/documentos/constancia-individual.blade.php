<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Constancia Oficial</title>
    <style>
        @page {
            margin: 1.2cm;
            size: A4;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            font-size: 11pt;
        }
        
        /* Encabezado muy compacto */
        .header {
            text-align: center;
            margin-bottom: 0.8cm;
        }
        
        .titulo-tec {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .subtitulo {
            font-size: 11pt;
            font-weight: bold;
        }
        
        /* Información de oficio en una línea por campo */
        .oficio-info {
            margin: 0.6cm 0;
            font-size: 9.5pt;
        }
        
        .oficio-line {
            margin: 2px 0;
        }
        
        /* Contenido ajustado */
        .contenido {
            text-align: justify;
            line-height: 1.5;
            font-size: 10.5pt;
        }
        
        .destacado {
            text-align: center;
            font-weight: bold;
            margin: 0.4cm 0;
        }
        
        .datos-estudiante {
            text-align: center;
            margin: 0.4cm 0;
        }
        
        .nombre-estudiante {
            font-weight: bold;
            text-transform: uppercase;
            margin: 3px 0;
        }
        
        /* Reducir márgenes de párrafos */
        p {
            margin: 0.3cm 0;
        }
        
        /* Firma ajustada */
        .firma-area {
            margin-top: 1.5cm;
            text-align: center;
        }
        
        .firma-line {
            width: 45%;
            border-top: 1px solid #000;
            margin: 5px auto;
        }
        
        /* Pie de página mínimo */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="titulo-tec">TECNOLÓGICO NACIONAL DE MÉXICO</div>
        <div class="subtitulo">INSTITUTO TECNOLÓGICO DE SAN JUAN DEL RÍO</div>
        <div class="subtitulo">DEPARTAMENTO DE GESTIÓN TECNOLÓGICA Y VINCULACIÓN</div>
    </div>
    
    <div class="oficio-info">
        <div class="oficio-line"><strong>DEPARTAMENTO:</strong> GESTIÓN TECNOLÓGICA Y VINCULACIÓN</div>
        <div class="oficio-line"><strong>OFICIO:</strong> GTYV-CLE-{{ strtoupper(Str::random(4)) }}/{{ date('Y') }}</div>
        <div class="oficio-line"><strong>EXPEDIENTE:</strong> {{ $usuario->numero_control ?? 'S/N' }}</div>
        <div class="oficio-line"><strong>CLAVE:</strong> 22DIT0003K</div>
        <div class="oficio-line"><strong>ASUNTO:</strong> CONSTANCIA DE ACREDITACIÓN DE LENGUA EXTRANJERA</div>
    </div>
    
    <div class="contenido">
        <div class="destacado">A QUIEN CORRESPONDA:</div>
        
        <p>Por este conducto, la que suscribe <strong>Edith Rosalío Uribe</strong>, Jefa de Departamento de Gestión Tecnológica y Vinculación, hace <strong>CONSTAR</strong> que, según documentos que obran en los expedientes de la Coordinación de Lenguas Extranjeras el (la) estudiante:</p>
        
        <div class="datos-estudiante">
            <div class="nombre-estudiante">{{ $usuario->nombre_completo ?? $usuario->name }}</div>
            <div>Número de control: {{ $usuario->numero_control ?? 'EXTERNO' }}</div>
            <div>Carrera: {{ $estudiante->carrera ?? 'INGENIERÍA EN SISTEMAS COMPUTACIONALES' }}</div>
        </div>
        
        <p>Acreditó el Programa de Lengua Extranjera en la <strong>COORDINACIÓN DE LENGUAS EXTRANJERAS</strong> del <strong>INSTITUTO TECNOLÓGICO DE SAN JUAN DEL RÍO</strong>, obteniendo competencias correspondientes al Nivel {{ $grupo->nivel_ingles ?? 'B1' }} del Marco Común Europeo de Referencia (MCER) para el idioma inglés.</p>
        
        <p>Lo anterior cumple con lo establecido en el Manual de Lineamientos Académico-Administrativo del Tecnológico Nacional de México, apartado 14.4.1.2 referente a la acreditación de programas de lengua extranjera.</p>
        
        <p style="text-align: center; margin-top: 0.6cm;">
            San Juan del Río, Querétaro, {{ date('d') }} de 
            {{ $meses[date('n')] ?? date('F') }} de {{ date('Y') }}
        </p>
    </div>
    
    <div class="firma-area">
        <div class="firma-line"></div>
        <div style="font-weight: bold;">EDITH ROSALÍO URIBE</div>
        <div>JEFA DEL DEPARTAMENTO DE GESTIÓN TECNOLÓGICA Y VINCULACIÓN</div>
    </div>
    
    <div class="footer">
        Calle Tecnológico #100, Col. Valle del Sol, San Juan del Río, Querétaro. C.P. 76800 • Tel: (427) 274-04-00
    </div>
</body>
</html>