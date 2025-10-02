<?php
session_start();
include "../conexion.php";
include "../funciones/auxiliar_php.php";


error_reporting(0);

if ($_SESSION['VERIFICADO'] != "SI") { 
	header ("Location: index.php?errorusuario=val"); 
	exit(); 
	}

$acceso=4;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

/*	
////session_register('VAR_GLOBAL');
////session_register('SEDE_USUARIO');
$_SESSION[VAR_GLOBAL]=0;

$sede=cargasector($_SESSION[SEDE_USUARIO]);
function cargasector($zona)
{
	switch ($zona)
	{
		case 1:
			$zonav="SEDE REGION LOS LLANOS";
			break;
		case 2:
			$zonav="SECTOR S.J.M.";
			break;
		case 3:
			$zonav="SECTOR S.F.A.";
			break;
		case 4:
			$zonav="UNIDAD A.O.";
			break;
		case 5:
			$zonav="SECTOR V.L.P.";
			break;
	}
	return $zonav;
}*/

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/plantilla_CT.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Area de Control Tributario</title>
<script src="jquery/jquery.js" type="text/javascript"></script>
<style type="text/css"> 
<!-- 
body  {
	font: 100% Verdana, Arial, Helvetica, sans-serif;
	background: #666666;
	margin: 0; /* es recomendable ajustar a cero el margen y el relleno del elemento body para lograr la compatibilidad con la configuración predeterminada de los diversos navegadores */
	padding: 0;
	text-align: center; /* esto centra el contenedor en los navegadores IE 5*. El texto se ajusta posteriormente con el valor predeterminado de alineación a la izquierda en el selector #container */
	color: #000000;
}
.twoColLiqLtHdr #container {
	width: 100%;  /* esto creará un contenedor con el 80% del ancho del navegador */
	background: #FFFFFF; /* los márgenes automáticos (conjuntamente con un ancho) centran la página */
	border: 1px solid #000000;
	text-align: left; /* esto anula text-align: center en el elemento body. */
	margin-top: 0;
	margin-right: auto;
	margin-bottom: 0;
	margin-left: auto;
} 
.twoColLiqLtHdr #header {
	padding: 0 10px;  /* este relleno coincide con la alineación izquierda de los elementos de los divs que aparecen bajo él. Si se utiliza una imagen en el #header en lugar de texto, es posible que le interese quitar el relleno. */
} 
.twoColLiqLtHdr #header h1 {
	margin: 0; /* el ajuste en cero del margen del último elemento del div de #header evita la contracción del margen (un espacio inexplicable entre divs). Si el div tiene un borde alrededor, esto no es necesario, ya que también evita la contracción del margen */
	padding: 10px 0; /* el uso de relleno en lugar de margen le permitirá mantener el elemento alejado de los bordes del div */
}

/* Sugerencias para sidebar1:
1. dado que está trabajando en porcentajes, es conveniente no utilizar relleno en la barra lateral. Se añadirá al ancho en el caso de navegadores que cumplen los estándares, creando un ancho real desconocido. 
2. El espacio entre el lado del div y los elementos que contiene puede crearse colocando un margen izquierdo y derecho en dichos elementos, como se observa en la regla ".twoColLiqLtHdr #sidebar1 p".
3. Dado que Explorer calcula los anchos después de mostrarse el elemento padre, puede que ocasionalmente encuentre errores inexplicables con columnas basadas en porcentajes. Si necesita resultados más predecibles, puede optar por cambiar a columnas con tamaño en píxeles.
*/
.twoColLiqLtHdr #sidebar1 {
	float: left;
	width: 235px; /* el relleno superior e inferior crea un espacio visual dentro de este div  */
	padding-top: 15px;
	padding-right: 0;
	padding-bottom: 15px;
	padding-left: 0;
	background-color: #CCC;
}
.twoColLiqLtHdr #sidebar1 h3, .twoColLiqLtHdr #sidebar1 p {
	margin-left: 10px; /* deben asignarse los márgenes izquierdo y derecho de cada elemento que vaya a colocarse en las columnas laterales */
	margin-right: 10px;
}

/* Sugerencias para mainContent:
1. el espacio entre el mainContent y sidebar1 se crea con el margen izquierdo del div mainContent.  Con independencia de la cantidad de contenido que incluya el div sidebar1, permanecerá el espacio de la columna. Puede quitar el margen izquierdo si desea que el texto del div #mainContent llene el espacio de #sidebar1 cuando termine el contenido de #sidebar1.
2. para evitar la caída de un elemento flotante con una resolución mínima admitida de 800 x 600, los elementos situados dentro del div mainContent deben tener un tamaño de 430px o inferior (incluidas las imágenes).
3. en el siguiente comentario condicional de Internet Explorer, la propiedad zoom se utiliza para asignar a mainContent "hasLayout." Esto evita diversos problemas específicos de IE.
*/
.twoColLiqLtHdr #mainContent {
	margin-top: 0;
	margin-right: 20px;
	margin-bottom: 0;
	margin-left: 260px;
} 
.twoColLiqLtHdr #footer { 
	padding: 0 10px; /* este relleno coincide con la alineación izquierda de los elementos de los divs que aparecen por encima de él. */
	background:#DDDDDD;
} 
.twoColLiqLtHdr #footer p {
	margin: 0; /* el ajuste en cero de los márgenes del primer elemento del pie evitará que puedan contraerse los márgenes (un espacio entre divs) */
	padding: 10px 0; /* el relleno de este elemento creará espacio, de la misma forma que lo haría el margen, sin el problema de la contracción de márgenes */
	font-size: 60%;
	color: #333;
}

/* Varias clases diversas para su reutilización */
.fltrt { /* esta clase puede utilizarse para que un elemento flote en la parte derecha de la página. El elemento flotante debe preceder al elemento junto al que debe aparecer en la página. */
	float: right;
	margin-left: 8px;
}
.fltlft { /* esta clase puede utilizarse para que un elemento flote en la parte izquierda de la página. */
	float: left;
	margin-right: 8px;
}
.clearfloat { /* esta clase debe colocarse en un elemento div o break y debe ser el último elemento antes del cierre de un contenedor que incluya completamente a un elemento flotante */
	clear:both;
    height:0;
    font-size: 1px;
    line-height: 0px;
}
--> 
</style><!--[if IE]>
<style type="text/css"> 
/* coloque las reparaciones de css para todas las versiones de IE en este comentario condicional */
.twoColLiqLtHdr #sidebar1 { padding-top: 30px; }
.twoColLiqLtHdr #mainContent { zoom: 1; padding-top: 15px; }
/* la propiedad zoom propia que se indica más arriba proporciona a IE el hasLayout que necesita para evitar diversos errores */
</style>
<![endif]--></head>

<body class="twoColLiqLtHdr">

<div id="container">
  <div id="header">
    <table width="100%" border="0">
      <tr>
        <td colspan="2"><img src="images/header1.jpg" width="100%" height="52" /></td>
      </tr>
      <tr>
        <td width="22%"><img src="images/logocr.gif" width="207" height="73" /></td>
        <td width="78%"><table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#FF0000">
          <tr>
            <td align="center" valign="middle" bgcolor="#FF0000" style="font-weight: bold; color: #FFF;" bordercolor="#FF0000">GERENCIA REGIONAL DE TRIBUTOS INTERNOS - REGION LOS LLANOS</td>
          </tr>
          <tr>
            <td align="center" valign="middle" bgcolor="#FF0000" style="font-weight: bold; color: #FFF;" bordercolor="#FF0000">DIVISION DE FISCALIZACION - AREA DE CONTROL TRIBUTARIO</td>
          </tr>
        </table></td>
      </tr>
    </table>
    <!-- end #header -->
  </div>
<div id="navbar"><table width="100%" border="0" height="20" style="background-image:url(images/ct_barra.png); background-repeat:repeat">
  <tr>
    <td></td>
  </tr>
</table>
  <!-- end #navbar -->
  </div>
<!-- InstanceBeginEditable name="Columna1" -->
<div id="sidebar1">
  <?php echo $sede; include_once("act_menu.php"); ?>
  <!-- end #sidebar1 -->
</div>
<!-- InstanceEndEditable --><!-- InstanceBeginEditable name="Columna2" -->
<div id="mainContent">
		<?php
		$web = $_GET["idpagina"];
		switch($web)
		{
			case 1:
			include_once("rsincluir.php");
			break;
			case 2:
			include_once("rsmodificar.php");
			break;
			case 3:
			include_once("rsconsulta.php");
			break;
			case 4:
			include_once("expedientes/salida.php");
			break;
			case 5:
			include_once("expedientes/memocierre.php");
			break;
			case 6:
			include_once("expedientes/reimpmemo.php");
			break;
			case 7:
			include_once("expedientes/memoconsulta.php");
			break;
			case 8:
			include_once("providencias.php");
			break;
			case 9:
			include_once("providencias.php");
			break;
			case 10:
			include_once("providencias.php");
			break;
			case 11:
			include_once("providencias.php");
			break;
			case 12:
			include_once("providencias.php");
			break;
			/*default:
			include("");*/
			}
		?>

  <!-- end #mainContent -->
</div>
<!-- InstanceEndEditable -->
<!-- Este elemento de eliminación siempre debe ir inmediatamente después del div #mainContent para forzar al div #container a que contenga todos los elementos flotantes hijos --><br class="clearfloat" />
<div id="footer">
  <p>© Copyright, SENIAT, Servicio Nacional Integrado de Administración Aduanera y Tributaria, todos los derechos reservados</p>
  <!-- end #footer -->
</div>
<!-- end #container --></div>
</body>
<!-- InstanceEnd --></html>
