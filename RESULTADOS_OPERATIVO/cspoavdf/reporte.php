<?php
header("Content-Type: text/html;charset=utf-8");

$sector = $_GET['sector'];
$inicio = $_GET['inicio'];
$fin = $_GET['fin'];
$año = $_GET['anno'];
$dns=$sector;
$usuario="Administrador";
$pass="losllanos";
$conn = odbc_connect ($dns,$usuario,$pass);
$cssql = "SELECT * FROM CS_Resoluciones_POA WHERE AnnoProv=".$año." AND Fecha_Resolucion>=#".$inicio."# AND Fecha_Resolucion<=#".$fin."# ORDER BY Fecha_Resolucion ASC";
$result = odbc_exec ($conn, $cssql);
echo '<br/>';
if (odbc_fetch_row($result)>0)
{
	echo '<table width="95%" border="0" cellpadding="3" cellspacing="0" align="center">';
	echo '<tr><td bgcolor="#999999">&nbsp;</td>';			
	echo '<td bgcolor="#999999"><b>N° Providencia</td>';
	echo '<td bgcolor="#999999"><b>Año Providencia</td>';			
	echo '<td bgcolor="#999999"><b>N° Resolucion</td>';			
	echo '<td bgcolor="#999999"><b>Año Resolucion</td>';			
	echo '<td bgcolor="#999999"><b>Fecha Resolucion</td>';
	echo '<td bgcolor="#999999"><b>Multas Bs.</td>';
	echo '</b>';
	$result = odbc_exec ($conn, $cssql);
	$i=1;
	while ($valor = odbc_fetch_array($result))
	{

		$sqlmulta = "SELECT * FROM CS_MultasResol_POA WHERE AnnoProvidencia=".$valor['AnnoProv']." AND Autorizacion=".$valor['NroProvidencia'];
		$resultado = odbc_exec ($conn, $sqlmulta);
		$fila = odbc_fetch_array($resultado);
		$monto = $fila['Multas'];
		if ($color=="#EFEFEF") {
			$color="#D0D6DF";
		} else {
			$color="#EFEFEF";
		}
		echo '<tr bgcolor="'.$color.'"><td>';
		echo $i;
		echo '</td><td>';
		echo $valor['NroProvidencia'];
		echo '</td><td>';
		echo $valor['AnnoProv'];
		echo '</td><td>';
		echo $valor['NroResolucion'];
		echo '</td><td>';
		echo $valor['AnnoResol'];
		echo '</td><td>';
		echo date("d-m-Y",strtotime($valor['Fecha_Resolucion']));
		echo '</td><td align="right">';
		echo number_format($monto,2,",",".");
		echo '</td></tr>';
		$i++;
	}
	echo '</table>';
} else {
	echo "NO HAY RESOLUCIONES EMITIDAS DEL AÑO ".$año." PARA EL PERIODO DEL ".date("d-m-Y",strtotime($inicio))." AL ".date("d-m-Y",strtotime($fin)); 
}

?>