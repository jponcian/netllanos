<?php 
session_start(); 
include "conexion.php";

if (isset($_POST['OUSUARIO'])) {
  $_SESSION['VAR_USUARIO'] = (get_magic_quotes_gpc()) ? $_POST['OUSUARIO'] : addslashes($_POST['OUSUARIO']);
	}
if (isset($_POST['OCLAVE'])) {
  $_SESSION['VAR_CLAVE'] = (get_magic_quotes_gpc()) ? $_POST['OCLAVE'] : addslashes($_POST['OCLAVE']);
	}

if ((trim($_SESSION['VAR_USUARIO'])=='') or (trim($_SESSION['VAR_CLAVE'])==''))
	{
	header("Location: index.php?errorusuario=vacio");
	exit();
	}

$consulta = "SELECT * FROM Claves WHERE Cedula = ".$_SESSION['VAR_USUARIO']." AND Clave='".$_SESSION['VAR_CLAVE']."'"; 
$tabla = $conexionsql->query($consulta);
$registro = $tabla->fetch_object();

if ($registro->Cedula==$_SESSION['VAR_USUARIO'])
	{

	$sqlcargo = "SELECT Cedula,Apellidos,Nombres,Telefonos FROM Empleados WHERE Cedula = ".$_SESSION['VAR_USUARIO']; 
	$cargo = $conexionsql->query($sqlcargo);
	$valor = $cargo->fetch_object();

	$_SESSION['NOM_USUARIO'] = $valor->Nombres." ".$valor->Apellidos;
	$_SESSION['CARGO_USUARIO'] = $valor->Telefonos." TRIBUTARIO";

	if ($registro->Administrador==1)
		{
		$_SESSION[VERIFICADO] = 'SI';
		$_SESSION['ADMINISTRADOR'] = 1;
		$_SESSION['SEDE_USUARIO'] = $registro->sector;
		header ("Location: prueba.php");
		exit();
		}
	// => Segunda Validación del Usuario <=	
	if ($registro->Fis_Expedientes==1)
		{
		$_SESSION[VERIFICADO] = 'SI';
		$_SESSION[PROVIDENCIA]=1;
		$_SESSION['ADMINISTRADOR'] = 0;
		$_SESSION['SEDE_USUARIO'] = $registro->sector;
		?><script>alert("<?php echo $_SESSION['SEDE_USUARIO'];?>");</script><?php
		header ("Location: prueba.php");
		exit();
		}
	}
else 	
	{ 
	$_SESSION[VERIFICADO] = 'NO';
	header("Location: index.php?errorusuario=sist");
	exit();
	} 
?>
