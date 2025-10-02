<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
//-------------
$_SESSION['VARIABLE'] = 'NO';

//--------- BUSCAMOS LOS DATOS DEL EXPEDIENTE
$consulta_00 = "SELECT * FROM vista_exp_especiales WHERE anno=" . $_SESSION['ANNO_PRO'] . " AND numero=" . $_SESSION['NUM_PRO'] . " AND sector =" . $_SESSION['SEDE'] . ";";
$tabla_00 = mysql_query($consulta_00);
$registro_00 = mysql_fetch_object($tabla_00);
//-----------
$rif = $registro_00->rif;
$_SESSION['RIF'] = $registro_00->rif;

//-----------------------------
if ($_POST['ORESOLUCION'] == '' and $registro_00->resolucion_prim <> '') {
	$_POST['ORESOLUCION'] = $registro_00->resolucion_prim;
}

if ($_POST['OFECHA'] == '' and $registro_00->resolucion_prim_fecha <> '') {
	$_POST['OFECHA'] = $registro_00->resolucion_prim_fecha;
}

//-----------------------------
if ($_POST['CMDAGREGAR'] == "Agregar") {
	if ($registro_00->rif <> "" and $_POST['ORESOLUCION'] <> '' and $_POST['OFECHA'] <> '') {
		// GUARDADO DE LOS DATOS
		$consulta = "UPDATE expedientes_especiales SET resolucion_prim='" . $_POST['ORESOLUCION'] . "', resolucion_prim_fecha='" . voltea_fecha($_POST['OFECHA']) . "', Usuario=" . $_SESSION['CEDULA_USUARIO'] . " WHERE Numero=" . $_SESSION['NUM_PRO'] . " and Anno=" . $_SESSION['ANNO_PRO'] . " AND Sector=" . $_SESSION['SEDE'] . ";";
		$tabla = mysql_query($consulta);
		echo $consulta;
		// FIN
	} else {
		echo "<script type=\"text/javascript\">alert('���Existen Campos Vac�os!!!');</script>";
	}
}

?>
<html>

<head>
	<title>Incluir Interes Complementario</title>
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

	.Estilo15 {
		font-size: 14px;
	}

	.Estilo20 {
		font-size: 20px
	}

	.Estilo16 {
		color: #FF0000
	}
	-->
</style>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>
	</p>
	<p>
	<div align="center">
		<p align="center">
			<?php
			include "menu.php";
			?>
	</div>
	</p>

	<form name="form1" method="post" action="#vista">

		<table width="45%" border="1" align="center">
			<tr>
				<td align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7"><u>Datos del Expediente </u></span></td>
			</tr>
			<tr>
				<td width="10%" bgcolor="#CCCCCC"><strong>A&ntilde;o:</strong></td>
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
						<div align="center"><span class="Estilo15"><?php echo voltea_fecha($registro_00->FechaRegistro); ?></span></div>
					</label></td>
				<td width="10%" bgcolor="#CCCCCC"><strong>Sector:</strong></td>
				<td><label>
						<div align="center"><span class="Estilo15"><?php echo strtoupper($registro_00->nombre); ?></span></div>
					</label></td>
			</tr>
		</table>
		<table width="45%" border="1" align="center">
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
						<div align="center"><span class="Estilo15"><?php echo $registro_00->coordinador; ?></span></div>
					</label></td>
				<td bgcolor="#CCCCCC"><strong>Supervisor:</strong></td>
				<td><label><span class="Estilo15"><?php echo $registro_00->nombrecoordinador; ?></span></label></td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC"><strong>Cedula:</strong></td>
				<td><label>
						<div align="center"><span class="Estilo15"><?php echo $registro_00->funcionario; ?></span></div>
					</label></td>
				<td bgcolor="#CCCCCC"><strong>Fiscal:</strong></td>
				<td><label><span class="Estilo15"><?php echo $registro_00->nombrefuncionario; ?></span></label></td>
			</tr>
		</table>
		<p></p>
		<table width="45%" border="1" align="center">
			<tr>
				<td align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7"><u>Datos de la Resoluci&oacute;n</u></span></td>
			</tr>
			<tr>
				<td width="22%" bgcolor="#CCCCCC">
					<div align="center"><strong>Resolucion:</strong></div>
				</td>
				<td width="49%"><label>
						<input type="text" name="ORESOLUCION" size="50" value="<?php echo $_POST['ORESOLUCION']; ?>">
					</label></td>
				<td width="14%" bgcolor="#CCCCCC">
					<div align="center"><strong>Fecha:</strong></div>
				</td>
				<td width="15%">
					<div align="center"><input onclick='javascript:scwShow(this,event);' type="text" name="OFECHA" size="8" readonly value="<?php echo $_POST['OFECHA']; ?>"></label>
					</div>
				</td>
			<tr>
		</table>
		<p></p>
		<p> <?php include "../funciones/0_calculo_interes.php"; ?>
		</p><a name="vista"></a>
	</form>
	<p>&nbsp; </p>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>
<?php
//----------
include "../desconexion.php";
//----------
?>