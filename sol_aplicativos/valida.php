<?php
session_start();
//------------
include "../conexion.php";
include "../auxiliar.php";

//----------- VALIDAR LA CEDULA SOLO
if (isset($_POST['OUSUARIO'])) {
    $_SESSION['CEDULA_USUARIO'] = (get_magic_quotes_gpc()) ? $_POST['OUSUARIO'] : addslashes($_POST['OUSUARIO']);
}

if (trim($_SESSION['CEDULA_USUARIO']) == '') {
    header("Location: index.php?errorusuario=vacio");
    exit();
}

//----------- VALIDAR LA CEDULA
$consulta_x = "SELECT * FROM z_empleados WHERE cedula = " . $_SESSION['CEDULA_USUARIO'] . ";";
$tabla_x = mysqli_query($_SESSION['conexionsqli'], $consulta_x);
$registro_x = mysqli_fetch_array($tabla_x);

if ($registro_x['cedula'] <> $_SESSION['CEDULA_USUARIO']) {
    header("Location: index.php?errorusuario=sist");
    exit();
}

//-------- USUARIO VERIFICADO (SIN CLAVE)
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
$pos = strpos($script, '/netllanos/');
if ($pos !== false) {
    $base = substr($script, 0, $pos + strlen('/netllanos'));
} else {
    $base = '';
}
$_SESSION['BASE_URL'] = $base;

//------------ REDIRECCIONAR AL MENU PRINCIPAL
header("Location: menuprincipal.php");
exit();
