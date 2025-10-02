<html>

<head>
	<?php

	session_start();
	include "../conexion.php";
	include "../auxiliar.php";

	if ($_SESSION['VERIFICADO'] != "SI") {
		header("Location: index.php?errorusuario=val");
		exit();
	}

	$acceso = 12;
	//------- VALIDACION ACCESO USUARIO
	include "../validacion_usuario.php";
	//-----------------------------------

	// PARA ELIMINAR PLANILLAS ------------------------------------
	if ($_POST['CMDELIMINAR'] == 'Eliminar') {
		$i = 1;
		$proceso = false;
		$cantidad = 0;
		while ($i <= $_SESSION['VARIABLE1']) {
			if ($_POST[$i] > 0) {
				// CONSULTA PARA ELIMINAR
				$consulta = "DELETE FROM ce_pagos WHERE indice=" . $_POST[$i] . ";";
				$tabla = mysql_query($consulta);
				$proceso = true;
				$cantidad += 1;
			}
			$i++;
		}
		if (proceso == true) {
			echo "<script type=\"text/javascript\">alert('!!!..Eliminado(s) " . $cantidad . " Registro(s) Satisfactoriamente...!!!');</script>";
		}
	}
	// FIN ------------------------------------

	?>

	<title>Eliminar Planillas</title>
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

	.Estilo1 {
		color: #FFFFFF;
		font-weight: bold;
		font-size: 18px;
	}

	.Estilo7 {
		font-weight: bold
	}

	.Estilo9 {
		color: #000000;
		font-weight: bold;
	}

	.Estilo10 {
		color: #FF0000
	}

	.Estilo11 {
		color: #0000FF
	}

	.Estilo09 {
		color: #000000;
		font-family: Arial, Helvetica, sans-serif;
		font-size: 12px;
	}

	.Estilo15 {
		color: #FFFFFF;
		font-weight: bold;
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
	<form name="form1" method="post" action="">
		<table width="60%" border=1 align=center>
			<tr>
				<td width="60%" height="50" align="center" bgcolor="#FF0000">
					<p class="Estilo7 Estilo1"><u>ELIMINAR PLANILLAS</u></p>
				</td>
			</tr>
		</table>
		<p></p>
		<table align="center" width="60%" border="1">
			<tr height="40">
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo9">Dependencia</div>
				</td>
				<td>
					<div align="center" class="Estilo9">
						<label>
							<select name="OSEDE" size="1" onChange="this.form.submit()">
								<option value="-1">Seleccione</option>
								<?php
								if ($_SESSION['ADMINISTRADOR'] > 0) {
									$consulta_x = 'SELECT * FROM z_sectores WHERE id_sector<=5;';
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['OSEDE'] == $registro_x['id_sector']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
									}
								} else {
									$consulta_x = 'SELECT * FROM z_sectores WHERE id_sector=' . $_SESSION['SEDE_USUARIO'] . ';';
									$tabla_x = mysql_query($consulta_x);
									while ($registro_x = mysql_fetch_array($tabla_x)) {
										echo '<option ';
										if ($_POST['OSEDE'] == $registro_x['id_sector']) {
											echo 'selected="selected" ';
										}
										echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
									}
								}
								?>
							</select>
						</label>
					</div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo9">N&deg; Rif</div>
				</td>
				<td>
					<div align="center" class="Estilo9">
						<label>
							<input type="text" name="ORIF" size="10" value="<?php echo $_POST['ORIF']; ?>">
						</label>
					</div>
				</td>
				<td>
					<div align="center" class="Estilo9">
						<input type="submit" class="boton" name="CMDBUSCARRIF" value="Buscar" />
					</div>
				</td>

				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo9">N&deg; <h3></h3>Planilla</div>
				</td>
				<td>
					<div align="center" class="Estilo9">
						<input type="text" name="OPLANILLA" size="10" value="<?php echo $_POST[OPLANILLA]; ?>">
					</div>
				</td>
				<td>
					<div align="center" class="Estilo9">
						<input type="submit" class="boton" name="CMDBUSCARPLANILLA" value="Buscar" />
					</div>
				</td>

				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo9">Fecha Pago</div>
				</td>
				<td>
					<div align="center" class="Estilo9">
						<input onclick='javascript:scwShow(this,event);' name="OPAGO" type="text" value="<?php echo $_POST['OPAGO']; ?>" size="15" maxlength="10" readonly>
					</div>
				</td>
				<td>
					<div align="center" class="Estilo9">
						<input type="submit" class="boton" name="CMDBUSCARPAGO" value="Buscar" />
					</div>
				</td>


			</tr>
		</table>
		<p></p>
		<table align="center" width="60%" border="1">
			<tr>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo9">N&deg;</div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo9">Rif</div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo9">Contribuyente</div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo9">Planilla</div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo9">Impuesto</div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo9">Periodo</div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo9">Fecha Vencimiento </div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo9">Fecha Declaracion</div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo9">Fecha Pago</div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo9">Monto</div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo9">Banco</div>
				</td>
				<td bgcolor="#CCCCCC">
					<div align="center" class="Estilo9">Eliminar</div>
				</td>
			</tr>
			<?php

			// CONSULTA DE TODOS LOS PAGOS

			$CargarDatos = 'NO';

			if ($_POST['ORIF'] <> '') {
				$consulta_x = "SELECT ce_pagos.Numero, ce_pagos.indice, vista_contribuyentes_direccion.rif, vista_contribuyentes_direccion.contribuyente AS NombreRazon, ce_cal_tip_obligaciones.Tipo, ce_pagos.Periodo, ce_pagos.Quincena, date_format(ce_pagos.Fecha_Ven, '%d/%m/%Y') AS Fecha_Ven, date_format(ce_pagos.Fecha_Presentacion, '%d/%m/%Y') AS Fecha_Presentacion, date_format(ce_pagos.Fecha_Pago, '%d/%m/%Y') AS Fecha_Pago, ce_pagos.Monto, a_banco.Descripcion FROM ce_pagos INNER JOIN vista_contribuyentes_direccion ON vista_contribuyentes_direccion.rif = ce_pagos.Rif INNER JOIN ce_cal_tip_obligaciones ON ce_cal_tip_obligaciones.Numero = ce_pagos.Tipo_Impuesto INNER JOIN a_agencia ON a_agencia.id_agencia = ce_pagos.Agencia INNER JOIN a_banco ON a_banco.id_banco = a_agencia.id_banco WHERE ce_pagos.Rif='" . $_POST['ORIF'] . "' AND ce_pagos.Sector=" . $_POST['OSEDE'] . " ORDER BY vista_contribuyentes_direccion.contribuyente, ce_pagos.Fecha_Ven DESC, ce_pagos.Fecha_Pago DESC;";
				$tabla_x = mysql_query($consulta_x);
				$CargarDatos = 'SI';
			}

			if ($_POST['OPLANILLA'] <> '') {
				$consulta_x = "SELECT ce_pagos.Numero, ce_pagos.indice, vista_contribuyentes_direccion.rif, vista_contribuyentes_direccion.contribuyente AS NombreRazon, ce_cal_tip_obligaciones.Tipo, ce_pagos.Periodo, ce_pagos.Quincena, date_format(ce_pagos.Fecha_Ven, '%d/%m/%Y') AS Fecha_Ven, date_format(ce_pagos.Fecha_Presentacion, '%d/%m/%Y') AS Fecha_Presentacion, date_format(ce_pagos.Fecha_Pago, '%d/%m/%Y') AS Fecha_Pago, ce_pagos.Monto, a_banco.Descripcion FROM ce_pagos INNER JOIN vista_contribuyentes_direccion ON vista_contribuyentes_direccion.rif = ce_pagos.Rif INNER JOIN ce_cal_tip_obligaciones ON ce_cal_tip_obligaciones.Numero = ce_pagos.Tipo_Impuesto INNER JOIN a_agencia ON a_agencia.id_agencia = ce_pagos.Agencia INNER JOIN a_banco ON a_banco.id_banco = a_agencia.id_banco WHERE ce_pagos.Numero='" . $_POST['OPLANILLA'] . "' AND ce_pagos.Sector=" . $_POST['OSEDE'] . " ORDER BY vista_contribuyentes_direccion.contribuyente ,ce_pagos.Fecha_Ven DESC, ce_pagos.Fecha_Pago DESC;";
				$tabla_x = mysql_query($consulta_x);
				$CargarDatos = 'SI';
			}

			if ($_POST['OPAGO'] <> '') {
				$consulta_x = "SELECT ce_pagos.Numero, ce_pagos.indice, vista_contribuyentes_direccion.rif, vista_contribuyentes_direccion.contribuyente AS NombreRazon, ce_cal_tip_obligaciones.Tipo, ce_pagos.Periodo, ce_pagos.Quincena, date_format(ce_pagos.Fecha_Ven, '%d/%m/%Y') AS Fecha_Ven, date_format(ce_pagos.Fecha_Presentacion, '%d/%m/%Y') AS Fecha_Presentacion, date_format(ce_pagos.Fecha_Pago, '%d/%m/%Y') AS Fecha_Pago, ce_pagos.Monto, a_banco.Descripcion FROM ce_pagos INNER JOIN vista_contribuyentes_direccion ON vista_contribuyentes_direccion.rif = ce_pagos.Rif INNER JOIN ce_cal_tip_obligaciones ON ce_cal_tip_obligaciones.Numero = ce_pagos.Tipo_Impuesto INNER JOIN a_agencia ON a_agencia.id_agencia = ce_pagos.Agencia INNER JOIN a_banco ON a_banco.id_banco = a_agencia.id_banco WHERE ce_pagos.Fecha_Pago='" . voltea_fecha($_POST['OPAGO']) . "' AND ce_pagos.Sector=" . $_POST['OSEDE'] . " ORDER BY vista_contribuyentes_direccion.contribuyente, ce_pagos.Fecha_Pago DESC, ce_pagos.Fecha_Pago DESC;";
				$tabla_x = mysql_query($consulta_x);
				$CargarDatos = 'SI';
			}

			//WHERE (((Contribuyente.sector)=1)) 

			if ($CargarDatos == 'SI') {
				$I = 1;

				while ($registro_x = mysql_fetch_object($tabla_x)) {
					echo '<tr>
		<td bgcolor="#FFFFFF"><div align="center" class="Estilo09">' . $I . '</div></td>
		<td bgcolor="#FFFFFF"><div align="center" class="Estilo09">' . $registro_x->rif . '</div></td>
		<td bgcolor="#FFFFFF"><div align="center" class="Estilo09">' . $registro_x->NombreRazon . '</div></td>
		<td bgcolor="#FFFFFF"><div align="center" class="Estilo09">' . $registro_x->Numero . '</div></td>
		<td bgcolor="#FFFFFF"><div align="center" class="Estilo09">' . $registro_x->Tipo . '</div></td>
		<td bgcolor="#FFFFFF"><div align="center" class="Estilo09">' . $registro_x->Periodo;

					if ($registro_x->Quincena > 0) {
						echo ' - ' . $registro_x->Quincena;
					}

					echo '</div></td>
		<td bgcolor="#FFFFFF"><div align="center" class="Estilo09"> ' . $registro_x->Fecha_Ven . ' </div></td>
		<td bgcolor="#FFFFFF"><div align="center" class="Estilo09"> ' . $registro_x->Fecha_Presentacion . '</div></td>
		<td bgcolor="#FFFFFF"><div align="center" class="Estilo09">' . $registro_x->Fecha_Pago . '</div></td>
		<td bgcolor="#FFFFFF"><div align="center" class="Estilo09">' . number_format(doubleval($registro_x->Monto), 2, ',', '.') . '</div></td>
		<td bgcolor="#FFFFFF"><div align="center" class="Estilo09">' . $registro_x->Descripcion . '</div></td>	
		<td bgcolor="#FFFFFF"><div align="center" class="Estilo09">
		  <label><input type="checkbox" name="' . $I . '" value="' . $registro_x->indice . '"/></label></div></td>			  
	  </tr>';

					$I++;
				}

				$_SESSION['VARIABLE1'] = $I;
			}

			?>
		</table>
		<div align="center">
			<p>
				<input type="submit" class="boton" name="CMDELIMINAR" value="Eliminar" />
			</p>
		</div>
		<span class="Estilo10"></span>
		<span class="Estilo11"></span>

	</form>

	<?php include "../pie.php"; ?>


	<p>&nbsp;</p>
</body>

</html>
<?php
//----------
include "../desconexion.php";
//----------

?>