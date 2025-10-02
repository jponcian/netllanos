<?php

include "../conexion.php";
//include "../auxiliar.php";


$numeromemo = $_GET['num'];	
$año = $_GET['anno'];
$hoy = date("Y/m/d");
$admin = $_GET['admin'];
$sector = $_GET['sector'];

$registros = "SELECT * FROM ct_tmp_mod_salida_expediente WHERE Anno_memo=$año AND NroMemo=$numeromemo AND sector=$sector AND borrado=0"; echo $registros;
$resultregistros = $conexionsql->query($registros);
$valor = $resultregistros->fetch_object();

buscar_registro($valor->Status,0,0,$valor->Anno_memo,$valor->NroMemo,$sector);

function buscar_registro($estatus,$añoprovidencia,$numprovidencia,$Anno_memo,$nummemo,$sector)
{
	global $conexionsql;
	global $admin;
	//echo "Valores Recibidos: (".$estatus.",".$añoprovidencia.",".$numprovidencia.",".$Anno_memo.",".$nummemo.")";
	//BUSCAR REGISTROS EN EL TEMPORAL
	
///*************************P O R  A Q U ííííííííííííííí******************************************************************************************
	
	//SI NO EXISTE EN EL TEMPORAL BUSCAR EL TABLA
	if ($estatus!="" and $Anno_memo>0 and $nummemo>0)
	{
		$año= $añoprovidencia;
		$numero= $numprovidencia;
		$Ccsql1 = "SELECT count(*) as Total FROM ct_tmp_mod_salida_expediente WHERE Anno_Memo=".$Anno_memo." and NroMemo=".$nummemo." and sector=".$sector." and Status=".$estatus." AND borrado=0";
		//echo $Ccsql1;
		$rs_access = $conexionsql->query($Ccsql1);
		$valor = $rs_access->num_rows;
		$num = $valor; 
		//echo $num;
		
		if ($num!=0)
		{ 
			$año= $añoprovidencia;
			$numero= $numprovidencia;
			//echo $año.",".$numero.",".$añoprovidencia.",".$numprovidencia;
			//$conexion = odbc_connect ("LLANOS","Administrador","losllanos");
					
			$Ccsql = "SELECT * FROM ct_tmp_mod_salida_expediente WHERE Anno_Memo=".$Anno_memo." and NroMemo=".$nummemo." and sector=".$sector." and Status=".$estatus." AND borrado=0";
			//echo $Ccsql;
			$rs_access = $conexionsql->query($Ccsql);
			$i=1;
			$valor = $rs_access->fetch_array();
			//echo "...Este es el Estatus=: ".$valor['Status'];

			if (substr($valor['Status'], -1)==1)
			{
				$resultado="Conformes";
			}
			if (substr($valor['Status'], -1)==2)
			{
				$resultado="Sancionados";
			}
			if (substr($valor['Status'], -1)==3)
			{
				$resultado="Sancionados Allanados";
			}
			if (substr($valor['Status'], -1)==5)
			{
				$resultado="Sancionados No Allanados";
			}
			if (substr($valor['Status'], -1)==4)
			{
				$resultado="Allanados Parcialmente";
			}
			//echo $resultado;
			$Ccsql = "SELECT * FROM ct_tmp_mod_salida_expediente WHERE Anno_Memo=".$Anno_memo." and NroMemo=".$nummemo." and sector=".$sector." and Status=".$estatus." AND borrado=0";
			$rs_access = $conexionsql->query($Ccsql);
			$i=1;

			if ($resultado=="Conformes")
			{
				$año= $añoprovidencia;
				$numero= $numprovidencia;
				//echo $año.",".$numero.",".$añoprovidencia.",".$numprovidencia;
				//$conexion = odbc_connect ("LLANOS","Administrador","losllanos");
				echo '<br />';
				echo '<table width="95%" border="0" cellpadding="3" cellspacing="0" align="center">';
				echo '<tr><td bgcolor="#999999"><b>N°';
				echo '</td><td bgcolor="#999999"><b>Año</td>';			
				echo '</td><td bgcolor="#999999"><b>Numero</td>';			
				echo '</td><td bgcolor="#999999"><b>Fecha Notificacion</td>';			
				echo '</td><td bgcolor="#999999"><b>Rif</td>';
				echo '</td><td bgcolor="#999999"><b>Nombre</td>';
				echo '</td><td bgcolor="#999999">&nbsp;</td>';			
				echo '</b>';
				while ($valor = $rs_access->fetch_array())
					{
						//echo "...Este es el Estatus=: ".$valor['Status']; 
						if ($color=="#EFEFEF") {
							$color="#D0D6DF";
						} else {
							$color="#EFEFEF";
						}
						echo '<tr bgcolor="'.$color.'"><td>';
						echo $i;
						echo '</td><td>';
						echo $valor['Anno_Providencia'];
						echo '</td><td>';
						echo $valor['NroAutorizacion'];
						echo '</td><td>';
						echo date("d-m-Y",strtotime($valor['FechaNotificacion']));
						echo '</td><td>';
						echo $valor['Rif'];
						echo '</td><td>';
						echo $valor['Nombre'];
						echo '</td><td>';
						if ($admin==1)
						{?>
							<img src="images/delete.png" width="16" height="16" style="cursor:pointer" title="Eliminar" onClick="EliminarExpediente(1,'<?php echo $valor['id']; ?>')"><?php
						} else {
							echo '&nbsp;';
						}
						echo '</td></tr>';
						$_SESSION['scantidad']=$i;
						$_SESSION[VAR_GLOBAL]=$i;
						$i++;
					}
				echo '</table>';
				$_SESSION['statusmemo']=$estatus;
			}

			if ($resultado=="Sancionados")
			{

				echo '<p></p>';
				echo '<table width="95%" border="0" cellpadding="3" cellspacing="0" align="center">';
				echo '<tr><td bgcolor="#999999"><b>N°';
				echo '</td><td bgcolor="#999999"><b>Año</td>';			
				echo '</td><td bgcolor="#999999"><b>Numero</td>';			
				echo '</td><td bgcolor="#999999"><b>Fecha Notificacion</td>';			
				echo '</td><td bgcolor="#999999"><b>Rif</td>';
				echo '</td><td bgcolor="#999999"><b>Nombre</td>';			
				echo '</td><td bgcolor="#999999"><b>N° Resol.</td>';			
				echo '</td><td bgcolor="#999999"><b>Fecha Resol.</td>';			
				echo '</td><td bgcolor="#999999"><b>Multas</td>';			
				echo '</td><td bgcolor="#999999">&nbsp;</td>';			
				echo '</b>';
				while ($valor = $rs_access->fetch_array())
					{
						if ($color=="#EFEFEF") {
							$color="#D0D6DF";
						} else {
							$color="#EFEFEF";
						}
						echo '<tr bgcolor="'.$color.'"><td>';
						echo $i;
						echo '</td><td>';
						echo $valor['Anno_Providencia'];
						echo '</td><td>';
						echo $valor['NroAutorizacion'];
						echo '</td><td>';
						echo date("d-m-Y",strtotime($valor['FechaNotificacion']));
						echo '</td><td>';
						echo $valor['Rif'];
						echo '</td><td>';
						echo $valor['Nombre'];
						echo '</td><td>';
						echo $valor['Anno_Resolucion']."-".$valor['NroResolucion'];
						echo '</td><td>';
						echo date("d-m-Y",strtotime($valor['FechaResolucion']));
						echo '</td><td align="right">';
						echo number_format($valor['Multa_DF'],2);
						echo '</td><td>';						if ($admin==1)
						{?>
							<img src="images/delete.png" width="16" height="16" style="cursor:pointer" title="Eliminar" onClick="EliminarExpediente(1,'<?php echo $valor['id']; ?>')"><?php
						} else {
							echo '&nbsp;';
						}
						echo '</td></tr>';
						$_SESSION['scantidad']=$i;
						$_SESSION[VAR_GLOBAL]=$i;
						$i++;
					}
			}
			if ($resultado=="Sancionados Allanados")
			{
				echo '<p></p>';
				echo '<table width="95%" border="0" cellpadding="3" cellspacing="0" align="center">';
				echo '<tr><td bgcolor="#999999"><b>N°';
				echo '</td><td bgcolor="#999999"><b>Año</td>';			
				echo '</td><td bgcolor="#999999"><b>Numero</td>';			
				echo '</td><td bgcolor="#999999"><b>Fecha Notificacion</td>';			
				echo '</td><td bgcolor="#999999"><b>Rif</td>';
				echo '</td><td bgcolor="#999999"><b>Nombre</td>';			
				echo '</td><td bgcolor="#999999"><b>N° Resol.</td>';			
				echo '</td><td bgcolor="#999999"><b>Fecha Resol.</td>';			
				echo '</td><td bgcolor="#999999"><b>Reparo</td>';			
				echo '</td><td bgcolor="#999999"><b>Impto Omitido</td>';			
				echo '</td><td bgcolor="#999999"><b>Multas Reparo</td>';			
				echo '</td><td bgcolor="#999999"><b>Intereses</td>';			
				echo '</td><td bgcolor="#999999"><b>Monto Pagado</td>';			
				echo '</td><td bgcolor="#999999">&nbsp;</td>';			
				echo '</b>';
				while ($valor = $rs_access->fetch_array())
					{
						if ($color=="#EFEFEF") {
							$color="#D0D6DF";
						} else {
							$color="#EFEFEF";
						}
						echo '<tr bgcolor="'.$color.'"><td>';
						echo $i;
						echo '</td><td>';
						echo $valor['Anno_Providencia'];
						echo '</td><td>';
						echo $valor['NroAutorizacion'];
						echo '</td><td>';
						echo date("d-m-Y",strtotime($valor['FechaNotificacion']));
						echo '</td><td>';
						echo $valor['Rif'];
						echo '</td><td>';
						echo $valor['Nombre'];
						echo '</td><td>';
						echo $valor['NroResolucion'];
						echo '</td><td>';
						echo date("d-m-Y",strtotime($valor['FechaResolucion']));
						echo '</td><td align="right">';
						echo number_format($valor['Monto_Reparo'],2);
						echo '</td><td align="right">';
						echo number_format($valor['Impto_Omitido'],2);
						echo '</td><td align="right">';
						echo number_format($valor['Multa_Reparo'],2);
						echo '</td><td align="right">';
						echo number_format($valor['Intereses'],2);
						echo '</td><td align="right">';
						echo number_format($valor['Monto_Pagado'],2);
						echo '</td><td>';						if ($admin==1)
						{?>
							<img src="images/delete.png" width="16" height="16" style="cursor:pointer" title="Eliminar" onClick="EliminarExpediente(1,'<?php echo $valor['id']; ?>')"><?php
						} else {
							echo '&nbsp;';
						}
						echo '</td></tr>';
						$_SESSION['scantidad']=$i;
						$_SESSION[VAR_GLOBAL]=$i;
						$i++;
					}
			}

			if ($resultado=="Allanados Parcialmente")
			{
				echo '<p></p>';
				echo '<table width="95%" border="0" cellpadding="3" cellspacing="0" align="center">';
				echo '<tr><td bgcolor="#999999"><b>N°';
				echo '</td><td bgcolor="#999999"><b>Año</td>';			
				echo '</td><td bgcolor="#999999"><b>Numero</td>';			
				echo '</td><td bgcolor="#999999"><b>Fecha Notificacion</td>';			
				echo '</td><td bgcolor="#999999"><b>Rif</td>';
				echo '</td><td bgcolor="#999999"><b>Nombre</td>';			
				echo '</td><td bgcolor="#999999"><b>N° Resol.</td>';			
				echo '</td><td bgcolor="#999999"><b>Fecha Resol.</td>';			
				echo '</td><td bgcolor="#999999"><b>Reparo</td>';			
				echo '</td><td bgcolor="#999999"><b>Impto Omitido</td>';			
				echo '</td><td bgcolor="#999999"><b>Multas Reparo</td>';			
				echo '</td><td bgcolor="#999999"><b>Intereses</td>';			
				echo '</td><td bgcolor="#999999"><b>Monto Pagado</td>';			
				echo '</td><td bgcolor="#999999">&nbsp;</td>';			
				echo '</b>';
				while ($valor = $rs_access->fetch_array())
					{
						if ($color=="#EFEFEF") {
							$color="#D0D6DF";
						} else {
							$color="#EFEFEF";
						}
						echo '<tr bgcolor="'.$color.'"><td>';
						echo $i;
						echo '</td><td>';
						echo $valor['Anno_Providencia'];
						echo '</td><td>';
						echo $valor['NroAutorizacion'];
						echo '</td><td>';
						echo date("d-m-Y",strtotime($valor['FechaNotificacion']));
						echo '</td><td>';
						echo $valor['Rif'];
						echo '</td><td>';
						echo $valor['Nombre'];
						echo '</td><td>';
						echo $valor['NroResolucion'];
						echo '</td><td>';
						echo date("d-m-Y",strtotime($valor['FechaResolucion']));
						echo '</td><td align="right">';
						echo number_format($valor['Monto_Reparo'],2);
						echo '</td><td align="right">';
						echo number_format($valor['Impto_Omitido'],2);
						echo '</td><td align="right">';
						echo number_format($valor['Multa_Reparo'],2);
						echo '</td><td align="right">';
						echo number_format($valor['Intereses'],2);
						echo '</td><td align="right">';
						echo number_format($valor['Monto_Pagado'],2);
						echo '</td><td>';						if ($admin==1)
						{?>
							<img src="images/delete.png" width="16" height="16" style="cursor:pointer" title="Eliminar" onClick="EliminarExpediente(1,'<?php echo $valor['id']; ?>')"><?php
						} else {
							echo '&nbsp;';
						}
						echo '</td></tr>';
						$_SESSION['scantidad']=$i;
						$_SESSION[VAR_GLOBAL]=$i;
						$i++;
					}
			}
			
			if ($resultado=="Sancionados No Allanados")
			{
				echo '<p></p>';
				echo '<table width="95%" border="0" cellpadding="3" cellspacing="0" align="center">';
				echo '<tr><td bgcolor="#999999"><b>N°';
				echo '</td><td bgcolor="#999999"><b>Año</td>';			
				echo '</td><td bgcolor="#999999"><b>Numero</td>';			
				echo '</td><td bgcolor="#999999"><b>Fecha Notificacion</td>';			
				echo '</td><td bgcolor="#999999"><b>Rif</td>';
				echo '</td><td bgcolor="#999999"><b>Nombre</td>';			
				echo '</td><td bgcolor="#999999"><b>N° Acta.</td>';			
				echo '</td><td bgcolor="#999999"><b>Fecha Acta.</td>';			
				echo '</td><td bgcolor="#999999"><b>Reparo</td>';			
				echo '</td><td bgcolor="#999999"><b>Impto Omitido</td>';			
				echo '</td><td bgcolor="#999999"><b>Fecha Notif. Acta</td>';			
				echo '</td><td bgcolor="#999999">&nbsp;</td>';			
				echo '</b>';
				while ($valor = $rs_access->fetch_array())
					{
						if ($color=="#EFEFEF") {
							$color="#D0D6DF";
						} else {
							$color="#EFEFEF";
						}
						echo '<tr bgcolor="'.$color.'"><td>';
						echo $i;
						echo '</td><td>';
						echo $valor['Anno_Providencia'];
						echo '</td><td>';
						echo $valor['NroAutorizacion'];
						echo '</td><td>';
						echo date("d-m-Y",strtotime($valor['FechaNotificacion']));
						echo '</td><td>';
						echo $valor['Rif'];
						echo '</td><td>';
						echo $valor['Nombre'];
						echo '</td><td>';
						echo $valor['NumActa'];
						echo '</td><td>';
						echo date("d-m-Y",strtotime($valor['FechaActa']));
						echo '</td><td align="right">';
						echo number_format($valor['Monto_Reparo'],2);
						echo '</td><td align="right">';
						echo number_format($valor['Impto_Omitido'],2);
						echo '</td><td>';
						echo date("d-m-Y",strtotime($valor['FechaNotificacionActa']));
						echo '</td><td>';						if ($admin==1)
						{?>
							<img src="images/delete.png" width="16" height="16" style="cursor:pointer" title="Eliminar" onClick="EliminarExpediente(1,'<?php echo $valor['id']; ?>')"><?php
						} else {
							echo '&nbsp;';
						}
						echo '</td></tr>';
						$_SESSION['scantidad']=$i;
						$_SESSION[VAR_GLOBAL]=$i;
						$i++;
					}
			}

			echo '</table>';
		}
	}
	$_SESSION['statusmemo']=$estatus;	
}

//PARA SABER SI EXISTEN REGISTROS
$sql_bloquear= "SELECT * FROM ct_tmp_mod_salida_expediente WHERE Anno_Memo=".$año." and NroMemo=".$numeromemo." and sector=".$sector." AND borrado=0";
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