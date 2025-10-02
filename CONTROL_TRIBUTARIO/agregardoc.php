<?php

//CONECTAR A LA BD
include "conexion.php";

//VARIABLES A UTILIZAR
//$numacta = 1;
$accion=$_GET['accion'];
$sector = $_GET['sector'];

//$documento=$_GET['documento'];

/*if ($_GET['documento']==1) {
	$documento="FACTURAS";
}
if ($_GET['documento']==2) {
	$documento="NOTAS DE DEBITOS";
}
if ($_GET['documento']==3) {
	$documento="NOTAS DE CREDITOS";
}
*/
if ($accion == 0)
{
	$ahora = date('Y-m-d');
	$numacta=$_GET['numacta'];
	$rif=strtoupper($_GET['rif']);
	switch ($_GET['documento']) {
	    case 1:
	        $documento= "FACTURAS";
	        break;
	    case 2:
	        $documento= "FORMAS LIBRES";
	        break;
	    case 3:
	        $documento= "NOTAS DE DEBITOS";
	        break;
	    case 4:
	        $documento= "NOTAS DE CREDITOS";
	        break;
	    case 5:
	        $documento= "RECIBOS DE INGRESOS";
	        break;
	    case 6:
	        $documento= "RECIBOS DE EGRESOS";
	        break;
	    case 7:
	        $documento= "ONDEN DE ENTREGA";
	        break;
	}

	$encontrado=0;
	$inicio=$_GET['inicio'];
	$fin=$_GET['fin'];
	//PARA GUARDAR LA HORA DEL SISTEMA
	/*$horasistema = $_GET['hora'];
	$horasistema = strtotime($horasistema);
	$horasistema = date("h:i a", $horasistema);*/	
	///////////////////////////////////////////

	//VERIFICAR SI EXISTE EL NUMERO DE CONTROL EN EL TEMPORAL
	$registros = "SELECT rif,tipo_documento,control_inicial,control_final FROM ct_tmp_doc_destfacturas WHERE rif='".$rif."' AND control_inicial<=".$inicio." AND control_final>=".$inicio."";
	$resultregistros = $conexionsql->query($registros);
	$numreg = $resultregistros->num_rows;

	//VERIFICAMOS SI EXISTE EL NUMERO DE CONTROL EN LA BASE DE DATOS
	if ($numreg == 0)
	{
		$registroBD = "SELECT rif,tipo_documento,control_inicial,control_final FROM ct_doc_destfacturas WHERE rif='".$rif."' AND control_inicial<=".$inicio." AND control_final>=".$inicio."";
		$resultregistroBD = $conexionsql->query($registroBD);
		$numregBD = $resultregistroBD->num_rows;
		if ($numregBD < 1)
		{
			$encontrado = 0;
		} else {
			$encontrado = 1;
		}

	} else {
		$encontrado = 1;
	}

	if ($encontrado == 0)
	{
		//INGRSESAR EL REGISTRO NUEVO
		$query = "INSERT INTO ct_tmp_doc_destfacturas (numero_acta, sector, fecha_emision, rif, tipo_documento, control_inicial, control_final)
					VALUES (?,?,?,?,?,?,?)"; //echo $query;

		//Ejecutar la consulta
		$sentencia = $conexionsql->prepare($query);

		$sentencia->bind_param('iisssii', $numacta,$sector,$ahora,$rif,$documento,$inicio,$fin);

		if ($sentencia->execute())
		{
			$validar="";
		}
		else
		{
			echo "Error: Fallo ejecucion";
		}
	} 
	else
	{
		$validar = "EL NUMERO DE CONTROL YA POSEE ACTA DE DESTRUCCION/INUTILIZACION";
	}
}
else
{
	$id=$_GET['id'];
	$numacta=$_GET['numacta'];
	$query = "DELETE FROM ct_tmp_doc_destfacturas WHERE id = ?";

	$sentencia = $conexionsql->prepare($query);

	$sentencia->bind_param('i', $id);

	if ($sentencia->execute())
	{
		$validar="";
	}
	else
	{
		echo "Error: Ha fallado la eliminacion del registro";
	}
}

$consulta = "SELECT * FROM ct_tmp_doc_destfacturas WHERE numero_acta=".$numacta." AND year(fecha_emision) = year(date(now())) AND sector=".$sector;
$resultado = $conexionsql->query($consulta);
$numregistro = $resultado->num_rows;

if ($numregistro > 0)
{
	if ($validar<>"")
	{?>
	<table width="90%" border="0" align="center" bgcolor="#999999" style="margin-top: 5px;margin-bottom: 5px;color;color:#FFFF00">
	  <tbody>
	    <tr>
	      <td align="center"><strong><?php echo $validar ?></strong></td>
	    </tr>
	  </tbody>
	</table>
	<?php
	}
	?>
	<table width="90%" border="0" align="center" bgcolor="#999999">
	  <tr bgcolor="#333333">
	    <td align="center" style="color: #FFF">Tipo de Documento</td>
	    <td align="center" style="color: #FFF">Numero de Control Inicial</td>
	    <td align="center" style="color: #FFF">Numero de Control Final</td>
	    <td align="center" style="color: #FFF">Accion</td>
	  </tr>
	 <?php

	while ($valor = $resultado->fetch_object()) 
	{
	?>
		<tr bgcolor="#333333">
			<td align="center"><?php echo $valor->tipo_documento ?></td>
			<td align="center"><?php echo $valor->control_inicial ?></td>
			<td align="center"><?php echo $valor->control_final ?></td>
			<td align="center"><img src="images/delete2015.png" style="cursor:pointer" width="18" height="18" onclick="Confirmar(<?php echo $valor->id ?>)" /></td>
		</tr>
	<?php
	}
} else
{
	if ($validar<>"")
	{
	?>
	<table width="90%" border="0" align="center" bgcolor="#999999" style="margin-top: 5px;margin-bottom: 5px; color:#FFFF00">
	  <tbody>
	    <tr>
	      <td align="center"><strong><?php echo $validar ?></strong></td>
	    </tr>
	  </tbody>
	</table>
	<?php
	}
}

//PARA SABER SI EXISTEN REGISTROS
$sql_bloquear= "SELECT * FROM ct_tmp_doc_destfacturas WHERE numero_acta = ".$numacta." AND year(fecha_emision) = year(date(now())) AND sector = ".$sector;
$result_bloqueador = $conexionsql->query($sql_bloquear);
$bloquear = $result_bloqueador->num_rows;
if ($bloquear > 0)
{
	?><script type="text/javascript">
		$('#bloquedor').val(1);
	</script><?php
} else {
	?><script type="text/javascript">
		$('#bloquedor').val(0);
	</script><?php
}
?>
</table>
