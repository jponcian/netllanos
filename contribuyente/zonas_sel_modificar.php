<?php

session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 33;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

?>
<?php

if ($_POST['CMDGUARDAR'] == "Guardar Cambios") {
	if ($_POST['OZONA'] <> "") {

		//PARA GUARDAR LA ZONA
		$consulta = mysql_query("UPDATE dir_zonas SET descripcion='" . $_POST['OZONA'] . "' WHERE id_zona=" . $_POST['CBO_ZONAS'] . "");

		// MENSAJE DE GUARDADO
		header("Location: menuprincipal.php?errorusuario=si");
		exit();
	} else {
		// MENSAJE DE CAMPOS VACIOS
		echo '<table width="75%" border="1" align="center"><tr> <td width="11%" bgcolor="#CCCCCC"><div align="center" class="Estilo2"><strong>EXISTEN CAMPOS VACIOS!!!</strong> </div></td> </tr>  </table>';
	}
}

?>

<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<title>Incluir Zonas</title>

	<script language="javascript" type="text/javascript" src="datetimepicker_css.js">
	</script>
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
		-->
	</style>

</head>

<body style="background: transparent !important;">
	<p>

		<?php include "../titulo.php"; ?>
	</p>
	<table width=669 align=center border=0>
		<tbody>
			<tr>
				<td colSpan=5>
					<form name="form2" method="post" action="menuprincipal.php">
						<div align="center"> <?php include "menu.php"; ?>
						</div>
					</form>
				</td>
			</tr>
		</tbody>
	</table>

	<form name="form1" method="post" action="">
		</table>
		<table width="45%" border="1" align="center">

			<tr>
				<td colspan="1" width="40%" bgcolor="#CCCCCC"><strong>Modificar Descripcion de la Zona:</strong></td>
				<td width="60%" colspan="3"><label>

						<!--SELECT DE REGISTROS DE CALLES-->
						<select name="CBO_ZONAS" size="1" onChange="this.form.submit()">
							<option value="-1" selected="selected">Seleccione</option>
							<?php
							$consulta = "SELECT distinct descripcion, id_zona FROM dir_zonas ORDER BY descripcion ASC;";
							$tabla = mysql_query($consulta);
							while ($registro = mysql_fetch_object($tabla)) {
								echo '<option';
								//echo ' selected="selected" ';
								if ($_POST[CBO_ZONAS] == $registro->id_zona) {
									echo ' selected="selected" ';
									$zona = $registro->descripcion;
								}
								//
								echo ' value="';
								echo $registro->id_zona;
								echo '">';
								echo $registro->descripcion;
								echo '</option>';
							}
							?>
						</select>

						<input type="text" name="OZONA" size="57" value="<?php echo $zona; ?>"></label></label></td>
			</tr>
		</table>
		<p>
		<div align="center"> <input type="submit" class="boton" name="CMDGUARDAR" value="Guardar Cambios"></div>
		</p>
	</form>
	<p>
		<?php include "../pie.php";; ?>

	</p>
	<p>&nbsp;</p>
</body>
<?php
include "../desconexion.php";
?>