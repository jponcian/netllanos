<!DOCTYPE html>
<html lang="es">

<head>
    <title></title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE-edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
</head>
<link rel="stylesheet" href="jquery_ui/css/jquery-ui-1.10.4.custom.min.css">
<link rel="stylesheet" href="themes/jquery.alerts.css">

<body style="background: transparent !important;">
    <style type="text/css">
        @media (min-width: 768px) {
            .navbar-collapse {
                height: auto;
                border-top: 0;
                box-shadow: none;
                max-height: none;
                padding-left: 0;
                padding-right: 0;
            }

            .navbar-collapse.collapse {
                display: block !important;
                width: auto !important;
                padding-bottom: 0;
                overflow: visible !important;
            }

            .navbar-collapse.in {
                overflow-x: visible;
            }

            .navbar {
                max-width: 300px;
                margin-right: 0;
                margin-left: 0;
            }

            .navbar-nav,
            .navbar-nav>li,
            .navbar-left,
            .navbar-right,
            .navbar-header {
                float: none !important;
            }

            .navbar-right .dropdown-menu {
                left: 0;
                right: auto;
            }

            .navbar-collapse .navbar-nav.navbar-right:last-child {
                margin-right: 0;
            }
        }

        /* Put your css in here */
        html {
            min-height: 100%;
        }

        body {
            font-family: 'Open Sans', sans-serif;
        }

        /*Columna izquierda*/
        .contenedor-logo {
            background-color: #ffffff;
            padding: 0;
            margin: 0;
            text-align: center;
        }

        .contenedor-logo img {
            padding: 0;
            margin: 0;
        }

        .franja-color-sobre-logo {
            height: 6px;
            background-color: #ffffff;
            background-image: url("http://snit.addax.cc/img/franja_color_sobre_logo.png");
            background-size: auto 6px;
            background-repeat: repeat-x;
        }

        .franja-color-bajo-logo {
            height: 7px;
            background-color: #ffffff;
            background-image: url("http://snit.addax.cc/img/franja_color_bajo_logo.png");
            background-size: auto 7px;
            background-repeat: repeat-x;
        }

        .imagen-logo-snit {
            padding-top: 7px;
            padding-bottom: 7px;
        }

        .contenedor-menu-noticias {
            background-color: #ffffff;
            min-height: 100%;
            padding-left: 0;
            padding-right: 0;
        }

        .contenedor-noticias {
            background-color: #f4f4f4;
        }

        .titulo-noticias {
            text-align: center;
            font-weight: bold;
            color: #878787;
            font-size: 15px;
            margin-left: 10px;
            margin-right: 10px;
        }

        .franja-color-sobre-noticias {
            height: 7px;
            background-color: #ffffff;
            background-image: url("http://snit.addax.cc/img/franja_color_sobre_noticias.png");
            background-size: auto 7px;
            background-repeat: repeat-x;
        }

        .lista-noticias {
            /*background-color: #f4f4f4 !important;*/
        }

        .noticia {
            position: relative;
            margin-bottom: 10px;
            margin-left: 15px;
            margin-right: 15px;
            margin-top: 10px;
            min-height: 130px;
        }

        .encabezado-noticia {
            position: absolute;
            width: 100%;
        }

        .titulo-noticia {
            float: left;
            background-color: #3d80b7;
            color: #ffffff;
            padding: 6px 10px;
            font-weight: bold;
            height: 44px;
            width: 80%;
            line-height: 1.2em;
        }

        .fecha-noticia {
            float: right;
            width: 20%;
        }

        .dia-noticia {
            background-color: #ffffff;
            color: #1c3a5c;
            top: 0;
            font-size: 21px;
            font-weight: 800;
            text-align: center;
        }

        .mes-noticia {
            background-color: #5c2012;
            color: #ffffff;
            bottom: 0;
            font-size: 10px;
            font-weight: 700;
            text-align: center;
        }

        .cuerpo-noticia {
            position: absolute;
            margin-top: 45px;
            font-size: 13px;
            margin-left: 10px;
            margin-right: 10px;
        }

        .leer-mas-noticia {
            color: #3d80b7;
            font-weight: bold;
        }

        .item-menu {
            background-color: red;
            color: white;
            min-height: 55px;
            position: relative;
            margin-bottom: 3px;
        }

        .item-menu a {
            min-height: 55px;
        }

        .item-menu:hover {
            background-color: orange;
        }

        .item-menu.current {
            background-color: #afafaf;
        }

        .icono-menu {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
        }

        .texto-menu {
            font-stretch: condensed;
            color: white;
            text-transform: uppercase;
            margin-left: 45px;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
        }

        .item-menu.current .texto-menu {
            color: #ffffff;
        }

        .item-menu.current .icono-menu img {
            filter: brightness(5000%);
            -webkit-filter: brightness(5000%);
            -moz-filter: brightness(5000%);
            -o-filter: brightness(5000%);
            -ms-filter: brightness(5000%);
        }

        /*Columna central*/
        .contenedor-buscar {
            background-color: #1c3a5c;
            color: white;
            height: 77px;
        }

        .input-buscar {
            margin-top: 15px;
            margin-bottom: 15px;
            height: 47px;
            background-color: #2d5a87;
            cursor: text;
            color: #89b5da;
        }

        .contenido-input-buscar {
            font-size: large;
            margin-left: 15px;
            line-height: 47px;
        }

        .imagen-buscar {
            margin-left: 10px;
        }

        .contenedor-central {
            background-color: #e8e7e7;
            min-height: 100%;
        }

        /*Tematicas y Capas*/
        .titulo-tematicas {
            padding-top: 10px;
        }

        .titulo-tematicas,
        .titulo-capas {
            margin-bottom: 5px;
            font-size: 20px;
            color: #878787;
            font-weight: 700;
        }

        .fila-imgs-tematica,
        .fila-imgs-capa {}

        .contenedor-img-tematica,
        .contenedor-img-capa {
            position: relative;
            margin-bottom: 20px;
        }

        /*
.contenedor-img-tematica img{
  sepia(100%) hue-rotate(150deg);
  sepia(100%) brightness(0.7) hue-rotate(250deg); MORADO
  -webkit-filter: sepia(100%) brightness(0.7) hue-rotate(165deg);
  filter: sepia(100%) brightness(0.7) hue-rotate(165deg);
}
.contenedor-img-mi-propia-tematica img{
  -webkit-filter: sepia(100%) brightness(0.7) hue-rotate(80deg);
  filter: sepia(100%) brightness(0.7) hue-rotate(80deg);
}
.contenedor-img-capa img{
  -webkit-filter: sepia(100%) brightness(0.7) hue-rotate(325deg);
  filter: sepia(100%) brightness(0.7) hue-rotate(325deg);
}
.contenedor-img-tematica img:hover, .contenedor-img-mi-propia-tematica img:hover, .contenedor-img-capa img:hover{
  -webkit-filter: none;
  filter: none;
}
*/

        .nombre-tematica,
        .nombre-capa {
            position: absolute;
            left: 25px;
            top: 15px;
            text-transform: uppercase;
            font-weight: bold;
            color: white;
            font-size: 20px;
            margin-right: 25px;
        }

        .label-wfs,
        .label-wms,
        .label-visor {
            position: absolute;
            bottom: 5px;
            text-transform: uppercase;
            color: white;
            font-size: 13px;
            font-weight: lighter;
        }

        .label-wfs {
            left: 20px;
            background-color: #1ac41e;
        }

        .label-wms {
            /*left: 85px;*/
            left: 38%;
            background-color: #f2c900;
        }

        .label-visor {
            right: 20px;
            background-color: #b600e2;
        }

        .label-institucion,
        .label-fecha {
            position: absolute;
            bottom: 25px;
            text-transform: uppercase;
            color: white;
        }

        .label-institucion {
            left: 20px;
            font-weight: bold;
        }

        .label-fecha {
            right: 20px;
            font-size: 11px;
        }

        .nueva-capa {
            position: absolute;
            height: 0px;
            width: 0px;
            top: 0px;
            right: 0px;
            border-top: 28px solid #b9e500;
            border-left: 28px solid transparent;
            border-right: 28px solid #b9e500;
            border-bottom: 28px solid transparent;
        }

        .nueva-capa-texto {
            position: absolute;
            -webkit-transform: rotate(45deg);
            -moz-transform: rotate(45deg);
            -o-transform: rotate(45deg);
            top: 0px;
            right: 0px;
            z-index: 999;
            margin-top: 14px;
            margin-right: -2px;
            font-weight: 800;
            font-size: 12px;
        }

        /*Colores sobre imagenes*/
        .tint {
            position: relative;
        }

        .tint:before {
            content: "";
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 255, 255, 0.5);
            -moz-transition: all .3s linear;
            -webkit-transition: all .3s linear;
            -ms-transition: all .3s linear;
            -o-transition: all .3s linear;
            transition: all .3s linear;
        }

        .tint:hover:before {
            background: none;
        }

        .orange:before {
            background: rgba(255, 102, 0, 0.6);
        }

        .purple:before {
            background: rgba(255, 0, 240, 0.5);
        }

        .green:before {
            background: rgba(63, 255, 0, 0.27);
            /*background: rgba(0,255,0, 0.5);*/
        }

        .red:before {
            background: rgba(255, 0, 0, 0.5);
        }

        .blue:before {
            background: rgba(0, 110, 255, 0.48);
            /*background: rgba(0,0,255, 0.5);*/
        }

        .boton-mas-tematicas,
        .boton-mas-capas {
            background-color: #d1d1d1;
            color: #878787;
            text-transform: uppercase;
            margin-bottom: 15px;
            padding: 2px;
            text-align: center;
            cursor: pointer;
        }

        /* Columna derecha */
        .contenedor-usuario {
            background-color: #b7b7b7;
            height: 77px;
            padding-left: 0;
            padding-right: 0;
            position: relative;
        }

        .nombre-usuario {
            background-color: #8e8e8e;
            color: #e8e8e8;
            text-align: center;
            font-size: large;
            font-weight: bold;
        }

        .informacion-usuario {
            color: #8e8e8e;
            text-align: right;
            font-size: 10px;
            margin-right: 10px;
        }

        .informacion-usuario p {
            margin: 0;
        }

        .imagen-icono-usuario {
            position: absolute;
            top: 5px;
            left: 5px;
        }

        .contenedor-varios {
            background-color: #b7b7b7;
            min-height: 100%;
            padding-left: 0;
            padding-right: 0;
        }

        .contenedor-menu-usuario {
            background-color: #b7b7b7;
        }

        .item-menu-usuario {
            background-color: #cccccc;
            min-height: 55px;
            position: relative;
            margin-bottom: 3px;
        }

        .item-menu-usuario a {
            min-height: 55px;
        }

        .item-menu-usuario:hover {
            background-color: #e8e8e8;
        }

        .item-menu-usuario.current {
            background-color: #afafaf;
        }

        .icono-menu-usuario {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
        }

        .texto-menu-usuario {
            font-stretch: condensed;
            color: #878787;
            text-transform: uppercase;
            margin-left: 40px;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
        }

        .item-menu-usuario.current .texto-menu-usuario {
            color: #ffffff;
        }

        .item-menu-usuario.current .icono-menu-usuario img {
            filter: brightness(5000%);
            -webkit-filter: brightness(5000%);
            -moz-filter: brightness(5000%);
            -o-filter: brightness(5000%);
            -ms-filter: brightness(5000%);
        }

        .contenedor-consumo-datos {
            background-color: #d1d1d1;
            color: #595a5a;
        }

        .titulo-consumo-datos {
            text-align: center;
            font-weight: bold;
            font-size: 15px;
            margin-left: 10px;
            margin-right: 10px;
            padding-top: 5px;
        }

        .ip-consumo {
            text-align: center;
            margin-top: 5px;
            margin-bottom: 10px;
            font-size: 15px;
        }

        .consumo-datos {
            position: relative;
            width: 100%;
            height: 110px;
        }

        .consumo-datos-diario {
            position: absolute;
            width: 50%;
            left: 0;
            text-align: center;
        }

        .consumo-datos-mensual {
            position: absolute;
            width: 50%;
            right: 0;
            text-align: center;
        }

        .porcentaje-grafico-diario,
        .porcentaje-grafico-mensual {
            position: absolute;
            z-index: 999;
            top: 40%;
            left: 50%;
            transform: translateY(-50%) translateX(-50%);
            font-size: 23px;
        }

        #grafico-consumo-diario,
        #grafico-consumo-mensual {
            margin: auto;
            width: 70px;
            height: 70px;
        }

        .leyenda-grafico-diario,
        .leyenda-grafico-mensual {
            font-size: 10px;
        }

        .consumo-datos small {
            font-size: 50%;
        }

        .contenedor-reproyeccion-coordenadas {
            background-color: #e0e0e0;
        }

        .titulo-reproyeccion-coordenadas {
            text-align: center;
            font-weight: bold;
            color: #878787;
            font-size: 15px;
            margin-left: 10px;
            margin-right: 10px;
            padding-top: 5px;
        }

        .contenedor-capas-populares {
            background-color: #d1d1d1;
            padding-bottom: 10px;
        }

        .titulo-capas-populares {
            text-align: center;
            font-weight: bold;
            color: #595a5a;
            font-size: 15px;
            margin-left: 10px;
            margin-right: 10px;
            padding-top: 5px;
            margin-bottom: 10px;
        }

        .grafico-capas-populares {
            background-color: #d1d1d1;
        }

        .capa-popular {
            padding: 10px;
            background-color: #bbbbbc;
            margin-bottom: 10px;
        }

        .capa-popular:hover {
            /*background-color: #545454;
    opacity: 0.2;*/
        }

        .capa-popular .progress {
            background-color: #a8a8a8;
            margin-bottom: 0px;
            border-radius: 0px;
        }

        .capa-popular .progress-bar {
            text-align: right;
            padding-right: 5px;
            font-size: 16px;
        }

        .nombre-capa-popular {
            color: #8a8b8b;
            font-size: 12px;
        }

        .capa-popular-1 {
            background-color: #77499c;
            color: #ffffff;
        }

        .capa-popular-2 {
            background-color: #9b4b86;
            color: #ffffff;
        }

        .capa-popular-3 {
            background-color: #ec9c3d;
            color: #ffffff;
        }

        .capa-popular-4 {
            background-color: #5e9f72;
            color: #ffffff;
        }

        .capa-popular-5 {
            background-color: #4c839a;
            color: #ffffff;
        }

        /*Animacion*/
        .animated {
            -webkit-animation-duration: 1s;
            animation-duration: 1s;
            -webkit-animation-fill-mode: both;
            animation-fill-mode: both;
        }

        @-webkit-keyframes fadeIn {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        .fadeIn {
            -webkit-animation-name: fadeIn;
            animation-name: fadeIn;
        }

        /*Alerta*/
        .alerta {
            position: relative;
            width: 100%;
            margin-top: 10px;
            margin-bottom: 10px;
            height: 63px;
        }

        .contenedor-titulo-alerta {
            position: absolute;
            left: 0;
            width: 40%;
            background-color: #9f1d21;
            color: #ffffff;
            height: 100%;
        }

        .imagen-alerta {
            margin-left: 15px;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
        }

        .titulo-alerta {
            font-size: 40px;
            font-weight: bold;
            text-transform: uppercase;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 95px;
        }

        .contenedor-mensaje-alerta {
            right: 0;
            position: absolute;
            width: 60%;
            background-color: #cc2027;
            color: #ffffff;
            height: 100%;
            font-size: 18px;
            font-weight: 300;
            line-height: 1.1em;
            padding-left: 15px;
        }

        .mensaje-alerta {
            position: absolute;
            margin-right: 140px;
            top: 50%;
            transform: translateY(-50%);
        }

        /*Formulario reproyeccion coordenadas*/
        .formulario-reproyeccion-coordenadas {
            padding-bottom: 20px;
            padding-top: 10px;
            position: relative;
            width: 100%;
            height: 120px
        }

        .formulario-reproyeccion-coordenadas-izq {
            position: absolute;
            left: 0;
            width: 50%;
            padding-left: 12px;
        }

        .formulario-reproyeccion-coordenadas-der {
            position: absolute;
            right: 0;
            width: 50%;
            padding-left: 5px;
        }

        .label-coordenadaX {
            margin-right: 5px;
        }

        .label-coordenadaY {
            margin-right: 6px
        }

        #coordenadaX,
        #coordenadaY {
            width: 78px;
            margin-bottom: 10px;
        }

        #selectOrigenDestino1,
        #selectOrigenDestino2 {
            width: 95px;
            margin-bottom: 10px;
        }

        #botonReproyectar,
        #botonAvanzado {
            background-color: #f48030;
            color: #ffffff;
            border: 0;
            width: 95px;
            margin-bottom: 10px;
        }
    </style>
    <div class="container">
        <div class="row justify-content-center border">
            <p class="text-center">
            <h4>MENU PARA MODIFICACIONES</h4>
            </p>
            <div class="row justify-content-center border">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <div class="navbar navbar-default navbar-fixed-top">
                        <div class="navbar-collapse collapse">
                            <ul class="nav navbar-nav">
                                <li role="presentation" class="item-menu"><a href="Providencias.php"><span class="texto-menu">1. Notificación Providencia</span></a></li>
                                <li role="presentation" class="item-menu"><a href="periodo.php"><span class="texto-menu">2. Periodo Sanción</span></a></li>
                                <li role="presentation" class="item-menu"><a href="resolucion.php"><span class="texto-menu">3. Resolución</span></a></li>
                                <li role="presentation" class="item-menu"><a href="acta.php"><span class="texto-menu">4. Acta de Reparo</span></a></li>
                                <li role="presentation" class="item-menu"><a href="reversar.php"><span class="texto-menu">5. Reversar</span></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4"></div>
            </div>
        </div>

</body>

<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
<!-------------------------------------------------------------->
<link rel="stylesheet" type="text/css" href="estilos/estilos.css" />

<script type="text/javascript" src="jquery/jquery.js"></script>
<script type="text/javascript" src="jquery_ui/js/jquery-ui-1.10.4.custom.js"></script>

<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="funciones/funciones.js"></script>

</html>