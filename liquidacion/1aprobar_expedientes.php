<?php
session_start();
include "../conexion.php";
//include "../auxiliar.php";
//-------------
if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
} //mantenimiento();
$acceso = 42;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

$status = 10;
$status2 = 10;

//---------- ORIGEN DEL FUNCIONARIO 
include "../funciones/origen_funcionario.php";

///////// VALIDACION DE LOS MONTOS DE LA UT
$i = 0;
$consulta = "SELECT monto_bs FROM liquidacion WHERE id_tributo<>99 AND id_tributo2<>99 AND id_resolucion<=0 AND status=" . $status . " AND origen_liquidacion=0" . $_SESSION['ORIGEN'] . " AND anno_expediente=0" . $_SESSION['ANNO_PRO'] . " AND num_expediente=0" . $_SESSION['NUM_PRO'] . " AND sector=0" . $_SESSION['SEDE'] . ";";
$tabla = mysql_query($consulta);
while ($registro = mysql_fetch_object($tabla)) {
	if ($registro->monto_bs < ($registro->monto_ut * $_SESSION['VALOR_UT_ACTUAL'])) {
		$i++;
	}
}
if ($i > 0) {
	echo "<script type=\"text/javascript\">alert('Existen planillas por ajustar el valor de la Unidad Tributaria!!!');</script>";
}

//-------------
if ($_POST['ONUMERO'] > 0) {
	$_SESSION['ANNO_PRO'] = $_POST['OANNO'];
	$_SESSION['NUM_PRO'] = $_POST['ONUMERO'];
	$_SESSION['SEDE'] = $_POST['OSEDE'];
	$_SESSION['ORIGEN'] = $_POST['OORIGEN'];
}

// PARA APROBAR TODAS
if ($_POST['CMDACEPTAR'] == "Aprobar") {
	///////// APROBAR LIQUIDACIONES
	$consulta = "UPDATE liquidacion SET status = 11, fecha_aprobacion_liq = date(now()), aprobador_liquidacion = " . $_SESSION['CEDULA_USUARIO'] . "  WHERE id_resolucion<=0 AND status=" . $status . " AND origen_liquidacion=0" . $_SESSION['ORIGEN'] . " AND anno_expediente=0" . $_SESSION['ANNO_PRO'] . " AND num_expediente=0" . $_SESSION['NUM_PRO'] . " AND sector=0" . $_SESSION['SEDE'] . ";";
	$tabla = mysql_query($consulta);
	// ------ GENERAR SECUENCIALES
	include "0_generar_secuencial.php";
	// MENSAJE
	echo "<script type=\"text/javascript\">alert('Expediente Aprobado Exitosamente!!!');</script>";
	//-- CAMBIO DE LA DIRECCION
	echo '<meta http-equiv="refresh" content="0";/>';
}

// PARA AJUSTAR VALOR UT TODAS
if ($_POST['CMDAJUSTAR'] == "Ajustar Valor UT") {
	///////// ACTUALIZAR LA UT DE UNA VEZ AL APROBAR
	$consulta = "UPDATE liquidacion SET monto_bs=(monto_ut*" . $_SESSION['VALOR_UT_ACTUAL'] . ") WHERE id_tributo<>99 AND id_tributo2<>99 AND id_resolucion<=0 AND status=" . $status . " AND origen_liquidacion=0" . $_SESSION['ORIGEN'] . " AND anno_expediente=0" . $_SESSION['ANNO_PRO'] . " AND num_expediente=0" . $_SESSION['NUM_PRO'] . " AND sector=0" . $_SESSION['SEDE'] . ";";
	$tabla = mysql_query($consulta);
	// MENSAJE
	echo "<script type=\"text/javascript\">alert('Planillas Ajustadas Exitosamente!!!');</script>";
}

// PARA RECHAZAR TODAS
if ($_POST['CMDRECHAZAR'] == "Devolver") {
	include "0devolver_expediente.php";
	// MENSAJE
	echo "<script type=\"text/javascript\">alert('Expediente Devuelto Exitosamente!!!');</script>";
	//-- CAMBIO DE LA DIRECCION
	echo '<meta http-equiv="refresh" content="0";/>';
}

?>
<html>

<head>
	<title>Aprobar Expediente</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>
	</p>
	<p align="center">
		<?php
		include "menu.php";
		?>
	</p>
	<form name="form1" method="post" action="">
		<div align="center">
			<?php
			include "0_seleccion_expediente.php";
			?>
			<br>
			<div id="div1">
				<?php
				include "0_expediente_liquidacion1.php";
				?>
			</div>
			<br>
			<div id="div2">
				<?php
				include "0_sanciones_aplicadas_liquidacion1.php";
				?>
			</div>
			<div id="div3">
				<br>
				<table width="20%" border="0" align="center">
					<tr>
						<td bgcolor="#FFFFFF">
							<p align="center">
								<input type="submit" class="btn btn-danger" name="CMDACEPTAR" value="Aprobar">
								<?php if ($_POST['OORIGEN'] == 2 or $_POST['OORIGEN'] == 3 or $_POST['OORIGEN'] == 4 or $_POST['OORIGEN'] == 7 or $_POST['OORIGEN'] == 12 or $_POST['OORIGEN'] == 13 or $_POST['OORIGEN'] == 16) { ?> <input type="submit" class="btn btn-danger" name="CMDRECHAZAR" value="Devolver"> <?php }; ?>
								<input type="submit" class="boton" name="CMDAJUSTAR" value="Ajustar Valor UT">
							</p>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</form>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>
<script language="JavaScript">
	$('#div1').hide();
	$('#div2').hide();
	$('#div3').hide();
	//--------------------------------------------
	function cargar_combo1(val) {
		$.ajax({
			type: "POST",
			url: '0_combo_origen.php?status1=10&status2=10',
			data: 'id=' + val,
			success: function(resp) {
				$('#OORIGEN').html(resp);
			}
		});
		alertify.message("Por favor espere la carga de datos...");
	}
	//--------------------------------------------
	function cargar_combo2(val) {
		$.ajax({
			type: "POST",
			url: '0_combo_anno.php?status1=10&status2=10&sede=' + document.form1.OSEDE.value,
			data: 'id=' + val,
			success: function(resp) {
				$('#OANNO').html(resp);
			}
		});
		alertify.message("Por favor espere la carga de datos...");
	}
	//--------------------------------------------
	function cargar_combo3(val) {
		$.ajax({
			type: "POST",
			url: '0_combo_numero.php?status1=10&status2=10&sede=' + document.form1.OSEDE.value + '&origen=' + document.form1.OORIGEN.value,
			data: 'id=' + val,
			success: function(resp) {
				$('#ONUMERO').html(resp);
			}
		});
		alertify.message("Por favor espere la carga de datos...");
	}
	//--------------------------------------------
	function cargar(numero) {
		var sede = document.getElementById("OSEDE").value;
		var origen = document.getElementById("OORIGEN").value;
		var anno = document.getElementById("OANNO").value;
		//-------------
		$('#div1').show();
		$('#div2').show();
		$('#div3').show();
		$('#div1').load('0_expediente_liquidacion1.php?sede=' + sede + '&origen=' + origen + '&anno=' + anno + '&numero=' + numero);
		$('#div2').load('0_sanciones_aplicadas_liquidacion1.php?sede=' + sede + '&origen=' + origen + '&anno=' + anno + '&numero=' + numero + '&status1=10&status2=10');
		alertify.success("Carga Completada...");
	}
</script>