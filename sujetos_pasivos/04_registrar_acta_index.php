<?php
session_start();
include "../conexion.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 999;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------	

//--------- PARA GUARDAR LA SOLICITUD
if ($_POST['CMDGUARDAR'] == "Guardar") {
	$consulta = "SELECT id_articulo FROM alm_solicitudes_detalle_tmp WHERE usuario = " . $_SESSION['CEDULA_USUARIO'] . " GROUP BY id_articulo;";
	$tablax = mysql_query($consulta);
	if ($registrox = mysql_fetch_array($tablax)) {
		//--- DIVISION DEL FUNCIONARIO
		$division = $_SESSION['DIVISION_USUARIO'];

		// PARA BUSCAR LA ULTIMA SOLICITUD
		$consultax = 'SELECT max(solicitud) as numero FROM alm_solicitudes WHERE year(fecha)=year(date(now())) and status<>99;';
		$tablax = mysql_query($consultax);
		if ($registrox = mysql_fetch_object($tablax)) {
			$numero = $registrox->numero + 1;
		} else {
			$numero = 1;
		}

		//PARA GUARDAR LA SOLICITUD
		$consulta = "INSERT INTO alm_solicitudes ( solicitud, fecha, division, funcionario, status, usuario ) SELECT '0" . $numero . "' as num, date(now()) AS Expr1, '" . $division . "' AS Expr2, '" . $_SESSION['CEDULA_USUARIO'] . "' AS Expr3, '0' AS Expr4, '" . $_SESSION['CEDULA_USUARIO'] . "' AS Expr5;";
		$tabla = mysql_query($consulta);
		echo $consulta;

		// PARA BUSCAR LA ULTIMA SOLICITUD
		$consultax = "SELECT id_solicitud FROM alm_solicitudes WHERE usuario = " . $_SESSION['CEDULA_USUARIO'] . " ORDER BY id_solicitud DESC;";
		$tablax = mysql_query($consultax);
		$registrox = mysql_fetch_object($tablax);
		$solicitud = $registrox->id_solicitud;

		// PARA GUARDAR LOS ARTICULOS
		$consultay = "SELECT * FROM alm_solicitudes_detalle_tmp WHERE usuario= '" . $_SESSION['CEDULA_USUARIO'] . "' GROUP BY id_articulo;";
		$tablay = mysql_query($consultay);
		while ($registroy = mysql_fetch_object($tablay)) {
			// PARA AGREGAR
			$consultai = "INSERT INTO alm_solicitudes_detalle (id_solicitud, id_articulo, cant_solicitada, usuario) VALUES ('" . $solicitud . "', '" . $registroy->id_articulo . "', '" . $registroy->cantidad . "', '" . $_SESSION['CEDULA_USUARIO'] . "');";
			$tablai = mysql_query($consultai);
		}

		// PARA ELIMINAR EL TEMPORAL
		$consultad = "DELETE FROM alm_solicitudes_detalle_tmp WHERE usuario = " . $_SESSION['CEDULA_USUARIO'] . ";";
		$tablad = mysql_query($consultad);

		//--------------------
		//$_SESSION['VARIABLE'] = $solicitud;
		$_SESSION['BOTON'] = '<a href="../almacen/formatos/x_solicitud.php?solicitud=' . $solicitud . '" target="_blank">Ver la Solicitud</a>';
		// MENSAJE DE GUARDADO
		$_SESSION['MOSTRAR'] = 'SI';
		$_SESSION['MENSAJE'] = 'La Solicitud fue Registrada Exitosamente!';
		//--------------------
		// REDIRECCION
		header("Location: menuprincipal.php");
		exit();
	} else {
?>
		<script language="JavaScript">
			alert("No hay Articulos Incluidos!");
		</script>
<?php
	}
} else {
	// PARA ELIMINAR EL TEMPORAL
	$consultad = "DELETE FROM alm_solicitudes_detalle_tmp WHERE usuario = " . $_SESSION['CEDULA_USUARIO'] . ";";
	$tablad = mysql_query($consultad);
}
?>
<html>

<head>
	<title>Solicitud</title>
	<?php //include "../funciones/head.php"; 
	?>
</head>
<?php include "../funciones/mensajes.php"; ?>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>
	</p>
	<p align="center">
		<?php
		include "menu.php";
		?>
	</p>
	<form name="form1" id="form1" method="post">
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
		<table class="formateada" align="center" width="330">
			<tr>
				<td height="41" colspan="4" align="center" bgcolor="#FF0000"><span class="Estilo7"><u>Informaci&oacute;n para el Acta</u></span></td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC"><strong>R.I.F.:</strong></td>
				<td><label><span>
							<input type="text" name="ORIF" id="ORIF" size="14" maxlength="10" style="text-align:center">
							<input type="button" name="Submit" value="Buscar" onClick="mostrar()">
						</span></label></td>
			</tr>
		</table>
		<br>
		<div id="div3">
		</div>
		<br>
		<div id="div2">
		</div>
		<br>
		<div id="div1">
		</div>
		<p>&nbsp;</p>
		<p>
		<div align="center"><input type="submit" class="boton" value="Guardar" name="CMDGUARDAR" onClick="pregunta_guardar()"></div>
		</p>
	</form>
	<p>
		<?php include "../pie.php"; ?>
	</p>
	<p>&nbsp;</p>
</body>

</html>
<script language="JavaScript">
	// PARA MOSTRAR LA TABLA
	function mostrar() {
		alertify.success('Rif Validado Correctamente!');
		$('#div1').load('4_registrar_acta_tabla.php');
		$('#div2').load('4_registrar_acta_combo.php');
		return false;
	}
	// PARA AGREGAR
	function agregar() {
		//Obtenemos datos formulario.
		var parametros = $("#form1").serialize();
		//AJAX.
		$.ajax({
			type: 'POST',
			url: '2solicitud_agregar_articulo.php',
			dataType: "json",
			data: parametros,
			success: function(data) {
				if (data.tipo == "alerta") {
					alertify.alert(data.msj);
				} else {
					alertify.success(data.msj);
				}
				$('#div1').load('2solicitud_tabla.php');
				$('#div2').load('2solicitud_combo.php');
			}
		});
		return false;
	}
	// PARA GUARDAR
	function guardar() {
		alertify.confirm("Desea Generar la Solicitud?",
			function() { //Obtenemos datos formulario.
				var parametros = $("#form1").serialize();
				//AJAX.
				$.ajax({
					type: 'POST',
					url: '2solicitud_guardar.php',
					dataType: "json",
					data: parametros,
					success: function(data) {
						if (data.tipo == "alerta") {
							alertify.alert(data.msj);
						} else {
							alertify.success(data.msj);
						}
						$('#div1').load('2solicitud_tabla.php');
					}
				});
			});
		return false;
	}
	// PARA ELIMINAR
	function eliminar(id) {
		alertify.confirm("Estas seguro de eliminar el Registro?",
			function() {
				var parametros = "id=" + id;
				$.ajax({
					url: "2solicitud_eliminar.php",
					type: "POST",
					data: parametros,
					success: function(r) {
						$('#div1').load('2solicitud_tabla.php');
						alertify.success('Registro Eliminado Correctamente');
					}
				});
			});
		return false;
	}
</script>