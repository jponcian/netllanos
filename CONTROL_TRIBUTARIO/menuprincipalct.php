<?php
session_start();
include "../conexion.php";
//include "../auxiliar.php";


error_reporting(0);

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 53;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

//session_register('VAR_GLOBAL');
//session_register('CARGO_USUARIO');
//session_register('NOM_USUARIO');
//session_register('CARGO_USUARIO');
//session_register('VALOR_ID');


$_SESSION[VAR_GLOBAL] = 0;
$_SESSION[VALOR_ID] = 0;
?>
<!DOCTYPE html>
<html lang="es">

<head>

	<!--[if lt IE 9]>
        <script src="js/html5shiv.js"></script>
    <![endif]-->

	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<meta http-equiv="X-UA-Compatible" content="IE=7" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/styles.css">
	<link rel="stylesheet" href="css/estilos_form.css">
	<link rel="stylesheet" href="css/estilotabla.css">

	<link rel="stylesheet" href="jquery_ui/css/jquery-ui-1.10.4.custom.min.css">
	<link rel="stylesheet" href="themes/jquery.alerts.css">
	<title>Acta Destruccion de Facturas</title>

</head>

<body style="background: transparent !important;">
	<header>
		<img src="images/logocr.gif" alt="">
		<div id="navbar">
			<h2 align="center"><strong>División de Fiscalización - Area de Control Tributario</strong></h2>
			<div id='cssmenu'>
				<ul>
					<?php
					if ($_SESSION['ADMINISTRADOR'] > 0) {
						$ocultar = 0;
					?>
						<li class='menu active'><a href='#' id="destruccion">Destruccion de Facturas</a></li>
						<li class='menu'><a href='#' id="salida">Salida de Expedientes</a></li>
						<li class='menu'><a href='Modificaciones/menu.php' id="modificar">Modificaciones</a></li>
						<!--<li class='menu'><a href='siger_fiscalizacion/inicio.php' id="siger">Siger Fiscalización</a></li>-->
						<?php
					} else {

						//VALIDAMOS SI EL USUARIO TIENE ACCESO A TODOS LOS MODULO 
						$sql_salida = "SELECT acceso FROM z_empleados_accesos WHERE cedula = " . $_SESSION['CEDULA_USUARIO'] . " AND acceso BETWEEN 54 AND 55";
						$tabla_s = mysql_query($sql_salida);
						$cantidad_s = mysql_num_rows($tabla_s);
						if ($cantidad_s > 1) {
							$ocultar = 3;
						?>
							<li class='menu active'><a href='#' id="destruccion">Destruccion de Facturas</a></li>
							<li class='menu'><a href='#' id="salida">Salida de Expedientes</a></li>
							<li class='menu'><a href='Modificaciones/menu.php' id="modificar">Modificaciones</a></li>
							<?php
						} else {

							//VALIDAMOS SI EL USUARIO TIENE ACCESO AL MODULO DE DESTRUCCION DE FACTURAS
							$sql_salida = "SELECT acceso FROM z_empleados_accesos WHERE cedula = " . $_SESSION['CEDULA_USUARIO'] . " AND acceso = 55";
							$tabla_s = mysql_query($sql_salida);
							$cantidad_s = mysql_num_rows($tabla_s);
							if ($cantidad_s > 0) {
								$ocultar = 1;
							?>
								<li class='menu active'><a href='#' id="destruccion">Destruccion de Facturas</a></li>
							<?php
							}

							//VALIDAMOS SI EL USUARIO TIENE ACCESO AL MODULO DE SALIDA DE EXPEDIENTES
							$sql_dest = "SELECT acceso FROM z_empleados_accesos WHERE cedula = " . $_SESSION['CEDULA_USUARIO'] . " AND acceso = 54";
							$tabla_d = mysql_query($sql_dest);
							$cantidad_d = mysql_num_rows($tabla_d);
							if ($cantidad_d > 0) {
								$ocultar = 2;
							?>
								<li class='menu'><a href='#' id="salida">Salida de Expedientes</a></li>
					<?php
							}
						}
					}
					?>
					<!--<li class='menu'><a href='#' id="gestion">Control de Gestion</a></li>			      
			      <li class='menu'><a href='#' id="recepcion">Recepción de Expedientes</a></li>-->
					<li class='menu'><a href='../fiscalizacion/menuprincipal.php' id="Salir_CT">Salir</a></li>
				</ul>
			</div>
		</div>
	</header>
	<section>
		<div id="contenidogral">
			<div id="barmenu" class="menui">
				<?php
				include "menuexp.php";
				include "menuacta.php";
				//include "menusiger.php"; 
				//include "menurecepcion.php";
				?>
				<input type="hidden" name="cedfunc" id="cedfunc" value="<?php echo $_SESSION['CEDULA_USUARIO']; ?>">
				<input type="hidden" name="nomfunc" id="nomfunc" value="<?php echo $_SESSION['NOM_USUARIO']; ?>">
				<input type="hidden" name="cargofunc" id="cargofunc" value="<?php echo $_SESSION['CARGO_USUARIO']; ?>">
				<input type="hidden" name="administrador" id="administrador" value="<?php echo $_SESSION['ADMINISTRADOR']; ?>">
				<input type="hidden" name="actagenearada" id="actagenearada">
				<input type="hidden" name="txtid" id="txtid">
				<input type="hidden" name="acta_impresa" id="acta_impresa" value="0">
				<input type="hidden" name="memo_impreso" id="memo_impreso" value="0">
				<input type="hidden" name="bloquedor" id="bloquedor" value="0">
				<input type="hidden" name="ocultarmenu" id="ocultarmenu" value="<?php echo $ocultar; ?>">
			</div>
			<div id="contenedor" class="contenido">
				<?php
				include "destfact.php";
				include "reimpacta.php";
				include "consulta.php";
				include "expedientes/salida.php";
				include "expedientes/reimpmemo.php";
				include "expedientes/modificarmemo.php";
				include "expedientes/consulta.php";
				?>

			</div>
		</div>
	</section>
	<footer>
		<div id="pie">
			Diseño y programación: Lcdo. Gustavo Garcia - 2015 - Para la División de Fiscalización - Area de Control Tributario - GRTI Región Los Llanos
		</div>
	</footer>
	<script type="text/javascript" src="jquery/jquery.js"></script>
	<script type="text/javascript" src="jquery_ui/js/jquery-ui-1.10.4.custom.js"></script>
	<script src="jquery/jquery.blockUI.js" type="text/javascript"></script>
	<script type="text/javascript" src="themes/jquery.alerts.js"></script>
	<script type="text/javascript" src="js/validacion.js"></script>
	<script type="text/javascript" src="funciones/funciones_destfactura.js"></script>
	<script type="text/javascript" src="funciones/funciones_salidaexpedientes.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {

		});

		function Confirmar($id) {
			jConfirm('¿Seguro que desea eliminar el registro?', 'Confirmar Eliminar', function(r) {
				if (r) {
					var id = $id;
					var acta = $('#acta').val();
					var sector = $('#sector').val();
					var datos = 'accion=1' + '&id=' + id + '&sector=' + sector + '&numacta=' + acta;
					//alert(datos);
					$("#registrodoc").load('agregardoc.php?' + datos, function() {
						if ($('#bloquedor').val() == 1) {
							$('#persona option:not(:selected)').attr('disabled', true);
							$('#tiposol option:not(:selected)').attr('disabled', true);
							$('#rif').prop('readonly', true);
							$('#cedula').prop('readonly', true);
							$('#nombrerp').prop('readonly', true);
							$('#numsolicitud').prop('readonly', true);
							$('#fechasol').prop('readonly', true);
							$('#fechasol').datepicker("destroy");
						} else {
							$('#persona option:not(:selected)').attr('disabled', false);
							$('#tiposol option:not(:selected)').attr('disabled', false);
							$('#rif').removeAttr('readonly');
							$('#cedula').removeAttr('readonly');
							$('#nombrerp').removeAttr('readonly');
							$('#numsolicitud').removeAttr('readonly');
							$('#fechasol').datepicker();
						}
						$('#barmenu').css("height", $('#contenedor').css("height"));
						$("#registrodoc").fadeIn("slow");
					});
				}
			});
		}
	</script>

</body>

</html>