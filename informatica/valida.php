<?php
session_start();
//------------
include "../conexion.php";
include "../auxiliar.php";

//------------
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
$consulta_x = "SELECT * FROM z_empleados WHERE cedula = " . $_SESSION['CEDULA_USUARIO'] . ";";
$tabla_x = mysql_query($consulta_x);
$registro_x = mysql_fetch_array($tabla_x);

if ($registro_x['cedula'] <> $_SESSION['CEDULA_USUARIO']) {
	header("Location: index.php?errorusuario=ced");
	exit();
}

// SECTOR DEL USUARIO
$_SESSION['SEDE_USUARIO'] = $registro_x['sector'];

//------------ VALIDAR LA CLAVE
$consulta_x = "SELECT cedula, administrador FROM z_empleados WHERE cedula = " . $_SESSION['CEDULA_USUARIO'] . " AND clave='" . $_SESSION['VAR_CLAVE'] . "'";
$tabla_x = mysql_query($consulta_x);
$registro_x = mysql_fetch_array($tabla_x);

//---------
if ($registro_x['cedula'] == $_SESSION['CEDULA_USUARIO']) {
	//-------- CLAVE VERIFICADA
	$_SESSION['VERIFICADO'] = 'SI';
	$_SESSION['BDD'] = 'losllanos';
	//----------- UT ACTUAL
	$consulta1 = "SELECT ValorUT FROM a_valorut ORDER BY FechaAplicacion DESC;";
	$tabla1 = mysql_query($consulta1);
	$registro1 = mysql_fetch_array($tabla1);
	$_SESSION['VALOR_UT_ACTUAL'] = $registro1['ValorUT'];
	//---------------------------------------
	//-------- ADMINISTRADOR
	$_SESSION['ADMINISTRADOR'] = $registro_x['administrador'];
	//-----------------	
	if ($_SESSION['ADMINISTRADOR'] > 0) {
		if ($_POST['OBDD'] == 'losllanos' or $_POST['OBDD'] == 'losllanos_prueba') {
			$_SESSION['BDD'] = $_POST['OBDD'];
			//-------------
			header("Location: menuprincipal.php");
			exit();
		}
	} else {
		header("Location: index.php?errorusuario=sis");
		exit();
	}
} else {
	header("Location: index.php?errorusuario=sist");
	exit();
}
?>
<link href="../lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<script src="../lib/bootstrap/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="../lib/fontawesome/css/all.min.css">
<script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>
<style type="text/css">
	<!--
	.Estilo1 {
		color: #FFFFFF
	}
	-->
</style>


<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
	<div class="card shadow p-4" style="max-width: 400px; width: 100%;">
		<form name="form1" method="post">
			<h5 class="card-title text-center mb-4 bg-danger text-white p-2 rounded">SELECCIONE LA BASE DE DATOS A TRABAJAR</h5>
			<div class="mb-3">
				<label for="OBDD" class="form-label fw-bold">Base de Datos:</label>
				<select name="OBDD" id="OBDD" class="form-select" onchange="this.form.submit()" required>
					<option value="-1">Seleccione</option>
					<option value="losllanos">ORIGINAL</option>
					<option value="losllanos_prueba">COPIA</option>
				</select>
			</div>
		</form>
	</div>
</div>