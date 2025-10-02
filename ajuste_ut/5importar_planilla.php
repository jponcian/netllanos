<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}
$acceso = 82;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------
//------ ORIGEN DEL FUNCIONARIO
include "../funciones/origen_funcionario.php";
//--------------------

// --------- BUSQUEDAS ----------
if ($_POST['ORIF'] <> "") {
	$consultax = "SELECT contribuyente, direccion FROM vista_contribuyentes_direccion WHERE Rif='" . $_POST['ORIF'] . "';";
	$tablax = mysql_query($consultax);
	if ($registrox = mysql_fetch_object($tablax)) {
		$Contribuyente = $registrox->contribuyente;
		$Direccion = $registrox->direccion;
	} else {
		echo "<script type=\"text/javascript\">alert('No Existe Contribuyente Registrado con ese Rif');</script>";
	}
}

// --------- AJUSTAR PLANILLAS
if ($_POST['ORIF'] <> "") {
	$bdd = $_SESSION['BDD'];
	$_SESSION['BDD'] = 'losllanos_viejo';
	mysql_select_db($_SESSION['BDD'], $_SESSION['conexionsql']);
	// BUSQUEDA DE PLANILLAS PARA AJUSTAR
	$consulta = "SELECT * FROM liquidaciones_viejas WHERE rif='" . $_POST['ORIF'] . "';";
	$tabla = mysql_query($consulta);
	while ($registro = mysql_fetch_object($tabla)) {
		if ($_POST[$registro->numero_liquidacion] == 'Importar') {
			$_SESSION['BDD'] = $bdd;
			mysql_select_db($_SESSION['BDD'], $_SESSION['conexionsql']);
			// VALIDAR SI YA SE AGREGO LA PLANILLA NUEVA
			$consulta_busqueda = "SELECT rif FROM liquidacion WHERE liquidacion='" . $registro->numero_liquidacion . "';";
			//------------------------
			$tabla_busqueda =  mysql_query($consulta_busqueda);
			if ($registro_busqueda = mysql_fetch_object($tabla_busqueda)) {
			} else {
				if ($registro->utcifras <= 0) {
					$valorut = unidad_infraccion(extrae_fecha($registro->fechaliquidacion));
					$cantut = ($registro->montocifras - $registro->montodividida) / $valorut;
				} else {
					$cantut = $registro->utcifras - $registro->utdividida;
				}
				// ------ PARA BUSCAR EL TRIBUTO
				$consulta_tributo = "SELECT tributo FROM a_sancion WHERE id_sancion='" . $registro->sancion . "';";
				$tabla_tributo =  mysql_query($consulta_tributo);
				$registro_tributo = mysql_fetch_object($tabla_tributo);
				// ------------------------------------
				// ------ INSERTAR LA PLANILLA NUEVA
				$consultaxx = "INSERT INTO liquidacion (fecha_impresion, secuencial, status, liquidacion, usuario, sector, origen_liquidacion, anno_expediente, num_expediente, rif, periodoinicio, periodofinal, id_sancion, id_tributo, monto_ut, monto_bs ) VALUES (
				'" . extrae_fecha($registro->fechaliquidacion) . "',
				'" . $registro->secuencial . "',
				'99', 
				'" . $registro->numero_liquidacion . "', 
				'" . $_SESSION['CEDULA_USUARIO'] . "', 
				'" . $_SESSION['SEDE_USUARIO'] . "',
				'0',
				'0',
				'0',
				'" . $_POST['ORIF'] . "', 
				'" . extrae_fecha($registro->fechainiciodeclaracion) . "', 
				'" . extrae_fecha($registro->fechafindeclaracion) . "', 
				'" . $registro->sancion . "', 
				'" . $registro_tributo->tributo . "', 
				'" . formato_moneda2($cantut) . "', 
				'" . formato_moneda2($registro->montocifras - $registro->montodividida) . "');";
				//-----------------
				$tablaxx =  mysql_query($consultaxx);
			}
		}
	}
	//------------------
	$_SESSION['BDD'] = $bdd;
	mysql_select_db($_SESSION['BDD'], $_SESSION['conexionsql']);
}
?>

<html>

<head>
	<title>Importar Planillas</title>
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
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<script type='text/JavaScript' src='scw.js'></script>
</head>

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
	<form name="form1" method="post" action="">
		<table width="55%" border=1 align=center>
			<tbody>
				<tr>
					<td bgcolor="#FF0000" height="24" colspan="6" align="center">
						<p class="Estilo7"><u>Datos del Contribuyente</u></p>
					</td>
				</tr>
				<tr>
					<td width="23%" height=27 colspan="1" bgcolor="#CCCCCC">
						<div align="center"><strong>Rif</strong></div>
					</td>
					<td colspan="4" bgcolor="#CCCCCC">
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
				<tr>
					<td width="23%" height=27 colspan="1" bgcolor="#CCCCCC">
						<div align="center"><strong>Domicilio</strong></div>
					</td>
					<td bgcolor="#FFFFFF" colspan="4">
						<div align="center">
							<?php echo $Direccion;		?></div>
					</td>
				</tr>
			</tbody>
		</table>
		<br>
		<table align="center" width="55%" border="1">
			<tr>
				<td bgcolor="#FF0000" height="27" colspan="8" align="center">
					<p class="Estilo7"><u>Planillas Disponibles</u> (Sistema anterior) </p>
				</td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>N�</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Planilla</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Monto</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Cant. U.T.</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Valor U.T.</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Fecha Liquidacion</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Opciones</strong></div>
				</td>
			</tr>
			<?php
			$bdd = $_SESSION['BDD'];
			$_SESSION['BDD'] = 'losllanos_viejo';
			mysql_select_db($_SESSION['BDD'], $_SESSION['conexionsql']);
			//------------------
			$consulta = "SELECT * FROM liquidaciones_viejas WHERE rif='" . $_POST['ORIF'] . "' ORDER BY secuencial;";
			$tabla = mysql_query($consulta);
			$i = 0;
			while ($registro = mysql_fetch_object($tabla)) {
				$_SESSION['BDD'] = $bdd;
				mysql_select_db($_SESSION['BDD'], $_SESSION['conexionsql']);
				// VALIDAR SI YA SE AGREGO LA PLANILLA NUEVA
				$consulta_busqueda = "SELECT rif FROM liquidacion WHERE liquidacion='" . $registro->numero_liquidacion . "';";
				//------------------------
				$tabla_busqueda =  mysql_query($consulta_busqueda);
				if ($registro_busqueda = mysql_fetch_object($tabla_busqueda)) {
				} else {
					$i++;
					echo '<tr>
		  <td bgcolor="#FFFFFF"><div align="center">' . $i . '</div></td>
		<td bgcolor="#FFFFFF"><div align="center">' . $registro->numero_liquidacion . '</div></td>
		  <td bgcolor="#FFFFFF"><div align="center">' . number_format(doubleval(($registro->montocifras - $registro->montodividida)), 2, ',', '.') . '</div></td>
		  <td bgcolor="#FFFFFF"><div align="center">' . number_format(doubleval(($registro->utcifras - $registro->utdividida)), 2, ',', '.') . '</div></td>
		  <td bgcolor="#FFFFFF"><div align="center">' . number_format(doubleval(($registro->montocifras - $registro->montodividida) / ($registro->utcifras - $registro->utdividida)), 2, ',', '.') . '</div></td>
		  <td bgcolor="#FFFFFF"><div align="center">' . voltea_fecha($registro->fechaliquidacion) . '</div></td>';
					echo ' <td width="94" bgcolor="#FFFFFF"><div align="center"><input name="' . $registro->numero_liquidacion . '" type="submit" value="Importar" ';
					echo ' ></div></td>	</tr>';
				}
			}
			//}
			//--------------------
			$_SESSION['BDD'] = $bdd;
			mysql_select_db($_SESSION['BDD'], $_SESSION['conexionsql']);
			?>
		</table>
		<br>
		<table align="center" width="55%" border="1">
			<tr>
				<td bgcolor="#FF0000" height="27" colspan="8" align="center">
					<p class="Estilo7"><u>Planillas en el Sistema </u></p>
				</td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>N�</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Planilla</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Monto</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Cant. U.T.</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Valor U.T.</strong></div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center"><strong>Fecha Liquidacion</strong></div>
				</td>
			</tr>
			<?php

			//$consulta = "SELECT liquidacion, id_liquidacion, monto_ut / concurrencia * especial as cant_ut, monto_bs / concurrencia * especial as monto, fecha_impresion, fecha_pag FROM liquidacion WHERE id_sancion<>2500 AND id_sancion<>2501 AND id_sancion<>2009 AND id_sancion<100000 AND rif='".$_POST['ORIF']."';";
			$consulta = "SELECT liquidacion, id_liquidacion, monto_ut / concurrencia * especial as cant_ut, monto_bs / concurrencia * especial as monto, fecha_impresion, fecha_pag FROM liquidacion WHERE status=99 AND rif='" . $_POST['ORIF'] . "';";
			$tabla = mysql_query($consulta);

			$i = 0;

			while ($registro = mysql_fetch_object($tabla)) {
				$i++;
			?>
				<tr>
					<td>
						<div align="center">
							<?php echo $i;		?>
						</div>
					</td>
					<td>
						<div align="center">
							<?php echo $registro->liquidacion;		?>
						</div>
					</td>
					<td>
						<div align="center">
							<?php echo formato_moneda($registro->monto);		?>
						</div>
					</td>
					<td>
						<div align="center">
							<?php echo formato_moneda($registro->cant_ut);		?>
						</div>
					</td>
					<td>
						<div align="center">
							<?php echo formato_moneda($registro->monto / $registro->cant_ut);		?>
						</div>
					</td>
					<td>
						<div align="center">
							<?php echo voltea_fecha($registro->fecha_impresion);		?>
						</div>
					</td>
				</tr>
			<?php
			}
			?>
		</table>
		<p>&nbsp;</p>
		<p><br>
		</p>
	</form>

	<?php include "../pie.php"; ?>

	<p>&nbsp;</p>
</body>

</html>