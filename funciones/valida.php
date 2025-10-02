<?php
// include_once __DIR__ . '/../config.php';
//mantenimiento();
//-----------
if (isset($_POST['OUSUARIO'])) {
	$_SESSION['CEDULA_USUARIO'] = (get_magic_quotes_gpc()) ? $_POST['OUSUARIO'] : addslashes($_POST['OUSUARIO']);
}
if (isset($_POST['OCLAVE'])) {
	$_SESSION['VAR_CLAVE'] = (get_magic_quotes_gpc()) ? $_POST['OCLAVE'] : addslashes($_POST['OCLAVE']);
}

if ((trim($_SESSION['CEDULA_USUARIO']) == '') or (trim($_SESSION['VAR_CLAVE']) == '')) {
	header("Location: index.php?errorusuario=vacio");
	exit();
}

//----------- VALIDAR LA CEDULA
$consulta_x = "SELECT cedula FROM z_empleados WHERE cedula = " . $_SESSION['CEDULA_USUARIO'] . ";";
$tabla_x = mysql_query($consulta_x);
$registro_x = mysql_fetch_array($tabla_x);

if ($registro_x['cedula'] <> $_SESSION['CEDULA_USUARIO']) {
	header("Location: index.php?errorusuario=sist");
	exit();
}

//------------ VALIDAR LA CLAVE
$consulta_x = "SELECT * FROM z_empleados WHERE cedula = " . $_SESSION['CEDULA_USUARIO'] . " AND clave='" . $_SESSION['VAR_CLAVE'] . "'";
$tabla_x = mysql_query($consulta_x);
$registro_x = mysql_fetch_array($tabla_x);

//---------
if ($registro_x['cedula'] == $_SESSION['CEDULA_USUARIO']) {
	//-------- CLAVE VERIFICADA
	$_SESSION['VERIFICADO'] = 'SI';
	$_SESSION['SEDE_USUARIO'] = $registro_x['sector'];
	$_SESSION['DIVISION_USUARIO'] = $registro_x['division'];
	$_SESSION['ADMINISTRADOR'] = $registro_x['administrador'];
	$_SESSION['TWITTER'] = $registro_x['twitter'];
	$_SESSION['BDD'] = 'losllanos';
	$_SESSION['NOM_USUARIO'] = $registro_x['Nombres'] . ' ' . $registro_x['Apellidos'];
	$_SESSION['CARGO_USUARIO'] = $registro_x['Cargo'];

	// Forzar que la ruta base sea siempre /netlosllanos (en local) o raíz del proyecto
	$script = $_SERVER['SCRIPT_NAME'];
	$pos = strpos($script, '/netlosllanos/');
	if ($pos !== false) {
		$base = substr($script, 0, $pos + strlen('/netlosllanos'));
	} else {
		$base = '';
	}
	if ($base === '')
		$base = '/';
	$imgUsuario = $base . '/imagenes/funcionarios/' . $_SESSION['CEDULA_USUARIO'] . '.png';
	echo "<script>window.parent.activarUsuario('" . ucwords(strtolower($_SESSION['NOM_USUARIO'])) . "','" . $imgUsuario . "')</script>";

	//----------- UT ACTUAL
	$consulta1 = "SELECT ValorUT FROM a_valorut ORDER BY FechaAplicacion DESC;";
	$tabla1 = mysql_query($consulta1);
	$registro1 = mysql_fetch_array($tabla1);
	$_SESSION['VALOR_UT_ACTUAL'] = $registro1['ValorUT'];

	//----------------- POR SI EL USUARIO ES IGUAL A LA CLAVE
	if ($_SESSION['CEDULA_USUARIO'] == $_SESSION['VAR_CLAVE']) {
		header("Location: ../CLAVES/menuprincipal.php?errorusuario=cc");
		exit();
	}
	//-----------------
	if ($_SESSION['ADMINISTRADOR'] > 0) {
		//------------------
		if ($_POST['OBDD'] == 'losllanos' or $_POST['OBDD'] == 'losllanos_prueba') {
			//$_SESSION['VAR_CLAVE']='-1';
			$_SESSION['BDD'] = $_POST['OBDD'];
			//-------------
			header("Location: menuprincipal.php");
			exit();
		}
	} else {
		$_SESSION['ENCUESTA'] = 'SI';
		//---------------
		header("Location: menuprincipal.php");
		exit();
	}
} else {
	header("Location: index.php?errorusuario=sist");
	exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<title>Seleccione Base de Datos</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="../lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<script src="../lib/bootstrap/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="../lib/fontawesome/css/all.min.css">
	<script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>
</head>

<body style="background: transparent;">
	<form name="form1" method="post" class="mt-5">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-md-6">
					<div class="card border-danger">
						<div class="card-header bg-danger text-white text-center">
							<strong><u>SELECCIONE LA BASE DE DATOS A TRABAJAR</u></strong>
						</div>
						<div class="card-body">
							<div class="mb-3">
								<label for="OBDD" class="form-label"><strong>BASE DE DATOS =&gt;</strong></label>
								<select name="OBDD" id="OBDD" class="form-select" onChange="this.form.submit()">
									<option value="-1">Seleccione</option>
									<?php
									if ($registro_x['cedula'] <> '123456789') {
										?>
										<option value="losllanos">ORIGINAL</option>
										<?php
									}
									?>
									<option value="losllanos_prueba">COPIA</option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</body>


</html>