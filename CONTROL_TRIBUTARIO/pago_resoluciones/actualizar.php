<?php
	session_start();

	//CONEXION A MYSQL con MySQLi
	set_time_limit(0);
	$id = $_POST['id'];
	$fecha_pago = $_POST['fecha_pago'] ;
	$agencia_pago = $_POST['agencia_pago'];
	$usuario = $_SESSION['CEDULA_USUARIO'];
	$bdd = $_SESSION[BDD];
	$permitido = false;
	$mensaje = "Procesado satisfactoriamente";
	$info = array();

	//Establecemos la conexion a la base de datos
	$con = new mysqli('localhost', 'root','', $bdd);
	$con->query("SET NAMES 'utf8'");

	$csql = "UPDATE liquidacion SET 
	liquidacion.fecha_pag = '".$fecha_pago."', 
	liquidacion.agencia_pag = ".$agencia_pago.",
	liquidacion.status = 100, 
	liquidacion.usuario = ".$usuario." 
	WHERE liquidacion.fecha_pag IS NULL AND liquidacion.id_liquidacion = ".$id;

	$result = $con->query($csql);

	if ($con->affected_rows > 0)
	{
		$permitido = true;
	} else {
		$mensaje = 'Error no se pudo actualizar el registro';
	}

	$info = array(
		'permitido' => $permitido,
		'mensaje' => $mensaje
	);

	echo json_encode($info);