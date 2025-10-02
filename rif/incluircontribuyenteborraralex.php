<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";


if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 67;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

if ($_POST['CMDINCLUIR'] == 'Incluir') {
	if (trim($_POST['ORIF']) == "") {
		header("Location: incluircontribuyente.php?errorusuario=cv");
		exit();
	}

	$consulta = "SELECT * FROM contribuyentes WHERE Rif = '" . $_POST['ORIF'] . "'";

	echo "<script type=\"text/javascript\">alert('" . $consulta . "');</script>";

	$tabla = mysql_query($consulta);
	if ($registro = mysql_fetch_object($tabla)) {
		$_SESSION['VARIABLE1'] = "MODIFICAR";
	} else {
		$_SESSION['VARIABLE1'] = "INCLUIR";
	}

	$DIGITO = substr($_POST['ORIF'], (strlen($_POST['ORIF']) - 1), 1);
	$DIGITO_F = VALIDAR_RIF($_POST['ORIF']);
	if ($DIGITO == $DIGITO_F) {
		$_SESSION['RIF'] = strtoupper($_POST['ORIF']);
		header("Location: incluircont.php");
		exit();
	} else {
		echo "<script type=\"text/javascript\">alert('Rif Incorrecto!!!');</script>";
	}
}

?>

<?php
function VALIDAR_RIF($RIF)
{

	$LETRA = strtoupper(substr($RIF, 0, 1));

	switch ($LETRA) {
		case "V":
			$LETRA = 1;
			break;
		case "E":
			$LETRA = 2;
			break;
		case "J":
			$LETRA = 3;
			break;
		case "P":
			$LETRA = 4;
			break;
		case "G":
			$LETRA = 5;
			break;
	}

	$I = 1;
	while ($I < (strlen($RIF) - 1)) {
		$CUERPO = $CUERPO . substr($RIF, $I, 1);
		$I++;
	}

	if (strlen($CUERPO) < 8) {
		$CUERPO = "0" . $CUERPO;
	}

	$RIF = $LETRA . $CUERPO;
	$ACUMULADOR = 0;
	$CONTADOR = 2;

	for ($I = 8; $I >= 0; $I--) {
		$DIGITO = substr($RIF, $I, 1);
		$ACUMULADOR = $ACUMULADOR + ($CONTADOR * $DIGITO);
		$CONTADOR++;
		if ($CONTADOR == 8) {
			$CONTADOR = 2;
		}
	}

	$VAR = fmod($ACUMULADOR, 11);
	$VAR = (11 - $VAR);
	if ($VAR > 9) {
		$DIGITO = 0;
	} else {
		$DIGITO = $VAR;
	}
	return ($DIGITO);
}
?>

<html>

<head>
	<title>Rif del Contribuyente</title>
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
	</script>
</head>
<style type="text/css">
	<!--
	.Estilomenun {
		font-family: Verdana, Arial, Helvetica, sans-serif;
		font-size: 12px;
		font-weight: bold;
	}

	body {
		background-image: url();
	}

	.Estilo7 {
		font-size: 18px;
		font-weight: bold;
		color: #FFFFFF;
	}
	-->
</style>

<body style="background: transparent !important;">

	<p>
		<?php include "../titulo.php"; ?>

	</p>

	<div align="center">
		<p align="center">
			<?php
			include "menu.php";
			?>
	</div>
	</p>

	<form name="form1" method="post">
		<p>&nbsp;</p>
		<table width="35%" border="1" align="center">
			<tr>
				<td height="54" colspan="3" align="center" bgcolor="#FF0000">
					<p class="Estilo7"><u>CONTRIBUYENTE</u></p>
			</tr>
			<tr>
				<td width="7%" bgcolor="#CCCCCC"><strong>Rif:</strong></td>
				<td width="15%"><label>
						<input type="text" name="ORIF" id="ORIF" onKeyPress="return SoloRif(event)" size="12" maxlength="10">
					</label></td>
				<td width="19%"><label>
						<input type="submit" class="boton" name="CMDINCLUIR" value="Incluir">
					</label></td>
			</tr>
			<tr>
				<td colspan="7" align="center">
					<p>
						<?php include "../msg_validacion.php"; ?></p>
				</td>
			</tr>
		</table>

		<p>&nbsp;</p>
	</form>
	<p>&nbsp;</p>
	<p>
		<?php include "../pie.php"; ?>
	</p>


	<p>&nbsp;</p>
</body>

</html>