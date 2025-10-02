<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 140;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------
?>
<html>

<head>
	<title>Crear Recurso</title>
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>
		<?php
		// --------- BUSQUEDAS ----------
		if ($_POST['ORIF'] <> "") {
			list($Contribuyente, $Direccion) = funcion_contribuyente($_POST['ORIF']);
		}

		?>

	</p>
	<div align="center">
		<p align="center">
			<?php
			include "menu.php";
			?>
	</div>

	<form name="form1" method="post" action="">
		<table width="35%" border=1 align=center>
			<tbody>
				<tr>
					<td bgcolor="#FF0000" height="43" colspan="6" align="center">
						<p class="Estilo7"><u>Datos del Contribuyente</u></p>
					</td>
				</tr>
				<tr>
					<td height=27 colspan="1" bgcolor="#CCCCCC">
						<div align="center"><strong>Rif</strong></div>
					</td>
					<td bgcolor="#CCCCCC">
						<div align="center"><strong>Contribuyente</strong></div>
					</td>
				</tr>
				<tr>
					<td bgcolor="#FFFFFF" colspan="1" height=27>
						<div align="center">
							<input style="text-align:center" type="text" name="ORIF" size="12" maxlength="10" value="<?php echo mayuscula($_POST['ORIF']); ?>">
							<input type="submit" class="boton" name="CMDBUSCAR" value="Buscar">
						</div>
					</td>
					<td bgcolor="#FFFFFF" colspan="4">
						<div align="center">
							<?php echo $Contribuyente;		?></div>
					</td>
				</tr>
				<!-- <tr>
			<td width="23%" height=27  colspan="1" bgcolor="#CCCCCC"><div align="center"><strong>Domicilio</strong></div></td>
        <td bgcolor="#FFFFFF" colspan="4" ><div align="center">
		<?php		//echo $Direccion;		
		?></div></td>
			  </tr>-->
			</tbody>
		</table>
		<br>
	</form>
	<form name="form2" method="post" action="../fiscalizacion/providencias/certificacion_aa.php?rif=<?php echo $_POST['ORIF']; ?>" target="_blank">

		<br>
		<p align="center">
			<?php if ($Contribuyente <> '') { ?> <input type="submit" class="boton" name="CMDGUARDAR" value="Ver Certificado">
				<br><?php } ?>
		</p>

		<p>&nbsp;</p>

	</form>
	<?php include "../pie.php"; ?>


	<p>&nbsp;</p>
</body>

</html>