<?php

	//CONECTAR A LA BD
	include "conexion.php";
	include "../funciones/auxiliar_php.php";

	//VARIABLES A UTILIZAR
	$sector = $_POST['sector'];
	$numacta = $_POST['numacta'];
	$emision = date("Y-m-d");
	$cedfunc = $_POST['cedfunc'];
	$nomfunc = strtoupper($_POST['nomfunc']);
	$persona = $_POST['persona'];
	$nomrep = strtoupper($_POST['nomrep']); 
	$cedrep = $_POST['cedrep']; 
	$rif = strtoupper($_POST['rif']);  
	$nombre = $_POST['nombre'];
	$tiposol = $_POST['tiposol'];
	$numsol = $_POST['num_solicitud']; 
	$fechasol = voltea_fecha($_POST['fechasol']);
	$horasistema = $_POST['hora'];
	$horasistema = strtotime($horasistema);
	$horasistema = date("h:i a", $horasistema);	
	$documento = $_POST['documento']; 
	$inicio = $_POST['inicio'];  
	$fin = $_POST['fin'];
	$info=array();
	$mensaje="Error al general el Acta, por favor intentelo de nuevo";
	$permitido=false;
	$ultid = 0;

	//VERIFICAMOS SI HAY DOCUMENTOS A DESTRUIR
	$sqldocumentos = "SELECT * FROM ct_tmp_doc_destfacturas WHERE numero_acta=".$numacta." AND sector=".$sector;
	$resultado = $conexionsql->query($sqldocumentos);
	$numdocumentos = $resultado->num_rows;

	if ($numdocumentos > 0)
	{
		//AGREGAMOS EL ACTA
		//echo $numdocumentos;
		
		$query = "INSERT INTO ct_destruccion_facturas (numero_acta, sector, fecha_emision, ced_funcionario, nom_funcionario, persona_responsable, rep_contribuyente, ced_rep_contribuyente, rif, nombre, num_solicitud, fecha_solicitud, tipo_solicitud, hora)
					VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

		//Ejecutar la consulta
		$sentencia = $conexionsql->prepare($query);

		$sentencia->bind_param('iisisisissssss', $numacta,$sector,$emision,$cedfunc,$nomfunc,$persona,$nomrep,$cedrep,$rif,$nombre,$numsol,$fechasol,$tiposol,$horasistema);

		if ($sentencia->execute())
		{
			//OBTENEMOS EL ID GENERADO
			$ultid = $sentencia->insert_id;
			//AGREGAMOS LOS DOCUMENTOS A DESTRUIR Y VACIAMOS EL TEMPORAL
			$sqltemp = "SELECT * FROM ct_tmp_doc_destfacturas WHERE numero_acta=".$numacta." AND sector=".$sector;
			$resultmp = $conexionsql->query($sqltemp);
			$reg = 0;

			while ($valor = $resultmp->fetch_object())
			{
				$queryTMP = "INSERT INTO ct_doc_destfacturas (numero_acta, sector, fecha_emision, rif, tipo_documento, control_inicial, control_final)
							VALUES (?,?,?,?,?,?,?)";

				//Ejecutar la consulta
				$sentencia = $conexionsql->prepare($queryTMP);

				$sentencia->bind_param('iisssii', $valor->numero_acta,$valor->sector,$valor->fecha_emision,$valor->rif,$valor->tipo_documento,$valor->control_inicial,$valor->control_final);

				$sentencia->execute();

				$reg = $reg + 1;
			}

			if ($reg > 0)
			{
				//BORRADO DEL TEMPORAL
				$queryDELETE = "DELETE FROM ct_tmp_doc_destfacturas WHERE numero_acta=".$numacta." AND sector=".$sector;

				$sentencia = $conexionsql->prepare($queryDELETE);

				if ($sentencia->execute())
				{
					$mensaje="Acta Generada Satisfactoriamente";
					$permitido=true;	
				}

				/*
				//GUARDAMO EL NUMERO DE ACTA
				$año = date("Y");
				$ultnumero = $numacta;
				$queryNUM = "UPDATE control SET numero=?, anno=? WHERE id=1";
				$sentencia = $conexionsql->prepare($queryNUM);
				$sentencia->bind_param('ii', $ultnumero,$año);
				$sentencia->execute();
				*/								
			}
		}
		else
		{
			$mensaje="Error al general el Acta, por favor intentelo de nuevo";
			$permitido=false;
		}
	}
	else
	{
		$mensaje="El Acta no se puede generar, no existen documentos a destruir/inutilizar, por favor verifique";
		$permitido=false;
	}
	
	$info = array("permitido"=>$permitido,
					"mensaje"=>$mensaje,
					"ID"=>$ultid);

	echo json_encode($info);

?>