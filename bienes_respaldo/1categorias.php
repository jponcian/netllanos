<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 78;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

?>

<html>

<head>
	<title>Incluir Categor&iacute;a</title>
	<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
	<script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>

	<script language="javascript" type="text/javascript" src="datetimepicker_css.js">
	</script>

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
	<?php

	if ($_POST['CMDGUARDAR'] == "Guardar") {
		if ($_POST['OCODIGO'] <> '' and $_POST['ODESCRIPCION'] <> "") {
			//PARA BUSCAR EL CODIGO
			$consulta = "SELECT * FROM bn_categorias WHERE codigo='" . mayuscula($_POST['OCODIGO']) . "';";
			$tabla = mysql_query($consulta);
			if ($registro = mysql_fetch_object($tabla)) {
				echo "<script type=\"text/javascript\">alert('���Ya existe una Categor�a con ese C�digo!!!');</script>";
			} else {
				//PARA BUSCAR LA DESCRIPCION
				$consulta = "SELECT * FROM bn_categorias WHERE descripcion='" . mayuscula($_POST['ODESCRIPCION']) . "';";
				$tabla = mysql_query($consulta);
				if ($registro = mysql_fetch_object($tabla)) {
					echo "<script type=\"text/javascript\">alert('���Ya existe una Categor�a con esa Descripci�n!!!');</script>";
				} else {
					//PARA GUARDAR LA CATEGOR�A
					$consulta = "INSERT INTO bn_categorias ( codigo, descripcion, usuario ) SELECT '" . mayuscula($_POST['OCODIGO']) . "' AS Expr1, '" . mayuscula($_POST['ODESCRIPCION']) . "' AS Expr2, '" . $_SESSION['CEDULA_USUARIO'] . "' AS Expr3;";
					$tabla = mysql_query($consulta);
					// MENSAJE DE GUARDADO
					echo "<script type=\"text/javascript\">alert('����REGISTRO GUARDADO EXITOSAMENTE!!!!');</script>";
					//header("Location: menuprincipal.php?errorusuario=si");
					//exit();
				}
			}
		} else {
			// MENSAJE DE CAMPOS VACIOS
			echo "<script type=\"text/javascript\">alert('���EXISTEN CAMPOS VAC�OS!!!');</script>";
		}
	}

	?>
	<form name="form1" method="post" action="">
		<table width="40%" border="1" align="center">
			<tr>
				<td align="center" bgcolor="#FF0000" colspan="8"><span class="Estilo7"><u>Registrar Categor&iacute;a</u></span></td>
			</tr>
			<tr>
				<td colspan="1" width="13%" bgcolor="#CCCCCC"><strong>Codigo:</strong></td>
				<td width="14%"><label>
						<input type="text" name="OCODIGO" size="20" maxlength="10" value="<?php echo $_POST['OCODIGO']
																							?>"></label></td>
				<td colspan="1" width="13%" bgcolor="#CCCCCC"><strong>Descripci&oacute;n:</strong></td>
				<td colspan="3"><label>
						<input type="text" name="ODESCRIPCION" size="40" value="<?php echo $_POST['ODESCRIPCION']; ?>"></label></td>
			</tr>
		</table>
		<p>
		<div align="center"> <input type="submit" class="boton" name="CMDGUARDAR" value="Guardar"></div>
		</p>
	</form>
	<p>
		<?php include "0_categorias.php"; ?>

	</p>
	<p>
		<?php include "../pie.php"; ?>

	</p> }
	<p>&nbsp;</p>
</body>