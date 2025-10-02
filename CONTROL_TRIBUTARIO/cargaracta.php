<?php

	include "conexion.php";

	//VARIABLES A UTILIZAR
	$acta = $_POST['acta'];
	$anno = $_POST['anno'];
	$sector = $_POST['sector'];
	$fecha1 = $anno."/01/01";
	$fecha2 = $anno."/12/31";
	$info = array();
	$permitido = false;
	$mensaje = "";

	//BUSCAMOS EL ACTA
	$consulta = "SELECT id,ced_funcionario FROM ct_destruccion_facturas WHERE numero_acta=".$acta." AND sector=".$sector." AND fecha_emision BETWEEN '".$fecha1."' AND '".$fecha2."'";
	$resultado = $conexionsql->query($consulta);
	$existe = $resultado->num_rows;
	$valor = $resultado->fetch_object();
	
	if ($existe>0)
	{
		$id = $valor->id;
		$cedula = $valor->ced_funcionario;
		$permitido = true;
		$mensaje = "El Acta Nro. ".$acta." del año ".$anno." ha sido cargada satisfactoriamente";

		//BUSCAMOS EL CARGO DEL EMPLEADO
		$consulta = "SELECT Cargo FROM z_empleados WHERE cedula=".$cedula;
		$tabla = $conexionsql->query($consulta);
		$registro = $tabla->fetch_object();
		$cargo=utf8_encode($registro->Cargo);
	}
	else
	{
		$id = 0;
		$permitido = false;
		$mensaje = "El Numero de Acta no se encuentra registrada";
		$cargo = "";	
	}

	$info = array("permitido"=>$permitido,
					"mensaje"=>$mensaje,
					"ID"=>$id,
					"cargo"=>$cargo);

	echo json_encode($info);

?>