<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 161;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------	

//--------- PARA GUARDAR LA SOLICITUD
if ($_POST['CMDGUARDAR'] == "Guardar") {
	$consulta = "SELECT id_articulo FROM alm_ingresos_detalle_tmp WHERE usuario = " . $_SESSION['CEDULA_USUARIO'] . ";";
	$tablax = mysql_query($consulta);
	if ($registrox = mysql_fetch_array($tablax)) {
		//--- DIVISION DEL FUNCIONARIO
		$division = $_SESSION['DIVISION_USUARIO'];

		// PARA BUSCAR EL ULTIMO INGRESO
		$consultax = 'SELECT max(ingreso) as numero FROM alm_ingresos WHERE year(fecha)=year(date(now())) and status<>99;';
		$tablax = mysql_query($consultax);
		if ($registrox = mysql_fetch_object($tablax)) {
			$numero = $registrox->numero + 1;
		} else {
			$numero = 1;
		}

		//PARA GUARDAR EL INGRESO
		$consulta = "INSERT INTO alm_ingresos ( ingreso, fecha, division, funcionario, status, usuario ) SELECT '0" . $numero . "' as num, date(now()) AS Expr1, '" . $division . "' AS Expr2, '" . $_SESSION['CEDULA_USUARIO'] . "' AS Expr3, '0' AS Expr4, '" . $_SESSION['CEDULA_USUARIO'] . "' AS Expr5;";
		$tabla = mysql_query($consulta);
		//echo $consulta;

		// PARA BUSCAR EL ULTIMO INGRESO
		$consultax = "SELECT id_ingreso FROM alm_ingresos WHERE usuario = " . $_SESSION['CEDULA_USUARIO'] . " ORDER BY id_ingreso DESC;";
		$tablax = mysql_query($consultax);
		$registrox = mysql_fetch_object($tablax);
		$ingreso = $registrox->id_ingreso;

		// PARA GUARDAR LOS ARTICULOS
		$consultay = "SELECT * FROM alm_ingresos_detalle_tmp WHERE usuario= '" . $_SESSION['CEDULA_USUARIO'] . "';";
		$tablay = mysql_query($consultay);
		while ($registroy = mysql_fetch_object($tablay)) {
			// PARA AGREGAR
			$consultai = "INSERT INTO alm_ingresos_detalle (id_ingreso, id_articulo, precio, cantidad, usuario) VALUES ('" . $ingreso . "', '" . $registroy->precio . "','" . $registroy->id_articulo . "', '" . $registroy->cantidad . "', '" . $_SESSION['CEDULA_USUARIO'] . "');";
			$tablai = mysql_query($consultai);
			//--- PARA AUMENTAR EL INVENTARIO
			$consultai = "UPDATE alm_inventario SET precio=" . $registroy->precio . ", cantidad=cantidad+0" . $registroy->cantidad . ", usuario='" . $_SESSION['CEDULA_USUARIO'] . "' WHERE id_articulo=" . $registroy->id_articulo . ";";
			$tablai = mysql_query($consultai);
		}

		// PARA ELIMINAR EL TEMPORAL
		$consultad = "DELETE FROM alm_ingresos_detalle_tmp WHERE usuario = " . $_SESSION['CEDULA_USUARIO'] . ";";
		$tablad = mysql_query($consultad);

		//--------------------
		//$_SESSION['VARIABLE'] = $ingreso;
		//$_SESSION['BOTON'] = '<a href="../almacen/formatos/x_ingreso.php?ingreso='.$ingreso.'" target="_blank">Ver el Ingreso</a>';
		// MENSAJE DE GUARDADO
		$_SESSION['MOSTRAR'] = 'SI';
		$_SESSION['MENSAJE'] = 'El Ingreso fue Registrado Exitosamente!';
		//--------------------
		// REDIRECCION
		header("Location: menuprincipal.php");
		exit();
	} else {
		// MENSAJE
		$_SESSION['MOSTRAR'] = 'SI';
		$_SESSION['MENSAJE'] = 'No hay Articulos Registrados!';
		//--------------------
	}
}

//--------- PARA GUARDAR LOS ARTICULOS
if ($_POST['CMDAGREGAR'] == "Agregar") {
	if (trim($_POST['OARTICULO']) <> '' and trim($_POST['OCANTIDAD']) > 0) {
		//PARA GUARDAR EL DETALLE
		$consulta = "INSERT INTO alm_ingresos_detalle_tmp ( precio, id_articulo, cantidad, usuario ) SELECT '" . (trim($_POST['OPRECIO'])) . "' AS Expr2, '" . mayuscula(trim($_POST['OARTICULO'])) . "' AS Expr1, '" . ($_POST['OCANTIDAD']) . "' AS Expr2, '" . $_SESSION['CEDULA_USUARIO'] . "' AS Expr3;";
		$tabla = mysql_query($consulta);
		// MENSAJE DE GUARDADO
		$_SESSION['MOSTRAR'] = 'SI';
		$_SESSION['MENSAJE'] = 'El Articulo fue Agregado Exitosamente!';
		//--------------------
	} else {
		// MENSAJE CAMPOS VACIOS
		$_SESSION['MOSTRAR'] = 'SI';
		$_SESSION['MENSAJE'] = 'Por favor rellene todos los Campos!';
		//--------------------
	}
}

//--------- PARA ELIMINAR LOS ARTICULOS
$consulta = "SELECT id_detalle FROM alm_ingresos_detalle_tmp WHERE usuario = " . $_SESSION['CEDULA_USUARIO'] . ";";
$tablax = mysql_query($consulta);
// ------------
while ($registrox = mysql_fetch_object($tablax)) {
	if ($_POST['E' . $registrox->id_detalle . ''] == 'Eliminar') {
		$consulta = "DELETE FROM alm_ingresos_detalle_tmp WHERE id_detalle =" . $registrox->id_detalle . ";";
		$tablaxx = mysql_query($consulta);
		// MENSAJE
		$_SESSION['MOSTRAR'] = 'SI';
		$_SESSION['MENSAJE'] = 'Item Eliminado Exitosamente!';
		//--------------------
	}
}
//
?>
<html>

<head>
	<title>Registrar Ingreso al Almacen</title>
	<?php include "../funciones/head.php";
	?>
</head>
<?php include "../funciones/mensajes.php"; ?>

<body style="background: transparent !important;">
	<p>
		<?php //include "../titulo.php";
		?>
	</p>
	<p align="center">
		<?php
		include "menu.php";
		?>
	</p>
	<form name="form1" method="post" action="">
		<?php
		if ($_SESSION['VERIFICADO'] == 'SI') {
			$consulta = 'SELECT z_empleados.cedula, Apellidos as apellidos, Nombres as nombres, descripcion as division FROM z_empleados , z_jefes_detalle WHERE z_jefes_detalle.division = z_empleados.division AND z_empleados.cedula=0' . $_SESSION['CEDULA_USUARIO'] . ';';
			$tabla = mysql_query($consulta);
			if ($registro = mysql_fetch_array($tabla)) {
				$usuario = strtoupper($registro['nombres'] . ' ' . $registro['apellidos']);
				$dependencia = strtoupper($registro['division']);
			}
		}
		?>
		<table class="formateada" align="center" width="480">
			<tr>
				<td height="41" colspan="4" align="center" bgcolor="#FF0000"><span class="Estilo7"><u>Datos del Ingreso </u></span></td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC"><strong>Nombres:</strong></td>
				<td><label><span>
							<?php echo $usuario; ?>
						</span></label></td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC"><strong>Dependencia:</strong></td>
				<td><label><span><?php echo $dependencia; ?></span></label></td>
			</tr>
		</table>
		<br>
		<table class="formateada" border=1 width="40%" align=center>
			<tbody>
				<tr>
					<td bgcolor="#FF0000" height="40" colspan="4" align="center">
						<p class="Estilo7"><u>Detalle del Ingreso</u></p>
					</td>
				</tr>
				<tr>
					<th>
						<div align="center" class="Estilo8"><strong>Descripci&oacute;n</strong></div>
					</th>
					<th>
						<div align="center" class="Estilo8"><strong>Precio</strong></div>
					</th>
					<th>
						<div align="center" class="Estilo8"><strong>Cantidad</strong></div>
					</th>
					<th bgcolor="#CCCCCC">
						<div align="center"><strong>Opcion</strong></div>
					</th>
				</tr>
				<tr id="fila1">
					<td>
						<div align="left" class="Estilo15">
							<select class="Select2" name="OARTICULO" size="1">
								<option value="">Seleccione</option>
								<?php
								//--------------------
								$consulta_x = "SELECT id_articulo, descripcion FROM alm_inventario WHERE (numero_bien='' or numero_bien is null) AND id_articulo NOT IN (SELECT id_articulo FROM alm_ingresos_detalle_tmp WHERE usuario= " . $_SESSION['CEDULA_USUARIO'] . ") ORDER BY descripcion;";
								$tabla_x = mysql_query($consulta_x);
								while ($registro_x = mysql_fetch_object($tabla_x))
								//-------------
								{
									echo '<option';
									if ($_POST['OARTICULO'] == $registro_x->id_articulo) {
										echo ' selected="selected" ';
									}
									echo ' value="';
									echo $registro_x->id_articulo;
									echo '">';
									echo $registro_x->descripcion;
									echo '</option>';
								}
								?>
							</select>
						</div>
					</td>
					<td>
						<div align="center" class="Estilo15">
							<input type="text" name="OPRECIO" size="5" maxlength="10" style="text-align:right">
						</div>
					</td>
					<td>
						<div align="center" class="Estilo15">
							<input type="text" name="OCANTIDAD" size="5" maxlength="4" style="text-align:center">
						</div>
					</td>
					<td>
						<div align="center"><span class="Estilo15"><input type="submit" class="boton" name="CMDAGREGAR" value="Agregar" /></span></div>
					</td>
				</tr>
			</tbody>
		</table>
		<br>
		<table class="formateada" align=center width="40%">
			<tbody>
				<tr>
					<td bgcolor="#FF0000" height="40" colspan="5" align="center">
						<p class="Estilo7"><u>Articulos Registrados</u></p>
					</td>
				</tr>
				<tr>
					<th width="35">
						<div align="center" class="Estilo8"><strong>Item</strong></div>
					</th>
					<th>
						<div align="center" class="Estilo8"><strong>Descripci&oacute;n</strong></div>
					</th>
					<th>
						<div align="center" class="Estilo8"><strong>Precio</strong></div>
					</th>
					<th>
						<div align="center" class="Estilo8"><strong>Cantidad</strong></div>
					</th>
					<th bgcolor="#CCCCCC">
						<div align="center"><strong>Opcion</strong></div>
					</th>
				</tr>
				<?php
				//--------
				$consulta = "SELECT alm_ingresos_detalle_tmp.id_detalle, alm_ingresos_detalle_tmp.cantidad,alm_ingresos_detalle_tmp.precio, descripcion FROM alm_ingresos_detalle_tmp, alm_inventario WHERE alm_ingresos_detalle_tmp.id_articulo = alm_inventario.id_articulo AND alm_ingresos_detalle_tmp.usuario = " . $_SESSION['CEDULA_USUARIO'] . ";";
				//echo $consulta ;
				//----------------------- MONTAJE DE LOS DATOS
				$i = 0;

				$tabla = mysql_query($consulta);

				while ($registro = mysql_fetch_object($tabla)) {
					$i++;
				?>
					<tr id="fila<?php echo $i; ?>">
						<td>
							<div align="center" class="Estilo15"><?php echo $i; ?></div>
						</td>
						<td>
							<div align="left" class="Estilo15"><?php echo mayuscula($registro->descripcion); ?></div>
						</td>
						<td>
							<div align="center" class="Estilo15"><?php echo ($registro->precio); ?></div>
						</td>
						<td>
							<div align="center" class="Estilo15"><?php echo ($registro->cantidad); ?></div>
						</td>
						<td>
							<div align="center"><span class="Estilo15"><input type="submit" class="boton" name="E<?php echo $registro->id_detalle; ?>" value="Eliminar" /></span></div>
						</td>
					</tr>
				<?php
				}
				?>
			</tbody>
		</table>
		<p>&nbsp;</p>
		<p>
		<div align="center"><input type="submit" class="boton" name="CMDGUARDAR" value="Guardar" onClick="return pregunta_guardar()"></div>
		</p>
	</form>
	<p>
		<?php //include "../pie.php";
		?>
	</p>
	<p>&nbsp;</p>
</body>
<script language="javascript">
	//---- ICONO DE ESPERA
	$(document).ready(function() {
		$('.select2').select2({});
	});
	//-----------
</script>