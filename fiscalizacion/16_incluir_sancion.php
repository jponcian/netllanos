<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";
//-----------
if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 6;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------
?>
<html>

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<title>Incluir Sanci&oacute;n</title>
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>
		<?php
		//--------- BUSCAMOS LOS DATOS DE LA PROVIDENCIA
		$consulta_00 = "SELECT * FROM vista_providencias WHERE anno=" . $_SESSION['ANNO_PRO'] . " AND numero=" . $_SESSION['NUM_PRO'] . " AND sector =" . $_SESSION['SEDE'] . ";";
		$tabla_00 = mysql_query($consulta_00);
		$registro_00 = mysql_fetch_object($tabla_00);
		//-----------
		$tipo = $registro_00->tipo;
		$rif = $registro_00->rif;

		//-----------
		if ($_POST['CMDGUARDAR'] == 'Guardar') {
			//-----------------
			include "../funciones/0_calculo_multas2.php";
			//-----------------
			if ($ut_aplicadas > 0) {
				//--- BUSCAR LA SANCION QUE NO ESTE REPETIDA
				include "../funciones/0_guardar_sancion.php";
				//-----------
			}
		}
		?>
	</p>
	<p align="center">
		<?php
		include "menu.php";
		//echo $_SESSION['VALOR_UT_PRIMITIVA'];
		//echo $Dias;
		echo $monto;
		?>

	</p>
	<form name="form1" method="post" action="#vista">
		<table width="60%" border="1" align="center">
			<tr>
				<td align="center" class="TituloTabla" colspan="9"><span><u>Datos de la Providencia</u></span></td>
			</tr>
			<tr>
				<td width="10%" bgcolor="#CCCCCC"><strong>A�o:</strong></td>
				<td width="10%"><label>
						<div align="center"><span class="Estilo15">
								<?php
								echo $registro_00->anno;
								?>
							</span></div>
					</label></td>
				<td width="10%" bgcolor="#CCCCCC"><strong>N&uacute;mero:</strong></td>
				<td width="10%"><label>
						<div align="center"><span class="Estilo15"><?php echo $registro_00->numero; ?></span></div>
					</label></td>
				<td width="10%" bgcolor="#CCCCCC"><strong>Fecha:</strong></td>
				<td width="10%"><label>
						<div align="center"><span class="Estilo15"><?php echo voltea_fecha($registro_00->fecha_emision); ?></span></div>
					</label></td>
				<td width="10%" bgcolor="#CCCCCC"><strong>Sector:</strong></td>
				<td><label>
						<div align="center"><span class="Estilo15"><?php echo strtoupper($registro_00->nombre); ?></span></div>
					</label></td>
			</tr>
		</table>
		<table width="60%" border="1" align="center">
			<tr>
				<td bgcolor="#CCCCCC"><strong>Rif: </strong></td>
				<td><label>
						<div align="center"><span class="Estilo15"><?php echo $registro_00->rif; ?></span></div>
					</label></td>
				<td bgcolor="#CCCCCC"><strong>Contribuyente:</strong></td>
				<td><label><span class="Estilo15"><?php echo $registro_00->contribuyente; ?></span></label></td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC"><strong>Cedula:</strong></td>
				<td><label>
						<div align="center"><span class="Estilo15"><?php echo $registro_00->ci_supervisor; ?></span></div>
					</label></td>
				<td bgcolor="#CCCCCC"><strong>Supervisor:</strong></td>
				<td><label><span class="Estilo15"><?php echo $registro_00->Nombres . " " . $registro_00->Apellidos; ?></span></label></td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC"><strong>Cedula:</strong></td>
				<td><label>
						<div align="center"><span class="Estilo15"><?php echo $registro_00->ci_fiscal1; ?></span></div>
					</label></td>
				<td bgcolor="#CCCCCC"><strong>Fiscal:</strong></td>
				<td><label><span class="Estilo15"><?php echo $registro_00->Nombres1 . " " . $registro_00->Apellidos1; ?></span></label></td>
			</tr>
		</table>
		<a name="vista"></a>

		<?php include "../funciones/0_cuadro_multas1.php"; ?>

		<p><?php $serie = "serie<>38";
			include "../funciones/0_sanciones_aplicadas.php"; ?>&nbsp;</p>
	</form>
	<p align="center"><a href="../ARCHIVOS/Sanciones.pdf" target="_blank">&lt; Haga click aqu&iacute; para Ver la hoja de Sanciones &gt;</a></p>
	<p>&nbsp; </p>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>