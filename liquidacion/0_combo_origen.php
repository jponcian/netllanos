<option value="0">Seleccione</option>
<?php
session_start();
include "../conexion.php";
//---------- ORIGEN DEL FUNCIONARIO 
include "../funciones/origen_funcionario.php";
//--------------
//$consulta_x = 'SELECT origen_liquidacion, area FROM vista_sanciones_aplicadas WHERE origen_liquidacion>0 GROUP BY area';
$consulta_x = 'SELECT origen_liquidacion, area FROM vista_sanciones_aplicadas WHERE origen_liquidacion>0 AND status>=' . $_GET['status1'] . ' AND status<=' . $_GET['status2'] . ' AND sector=0' . $_POST['id'] . ' AND origen_liquidacion IN ' . $origenes . ' GROUP BY area';
//--------------
$tabla_x = mysql_query($consulta_x);
while ($registro_x = mysql_fetch_array($tabla_x)) {
	echo '<option value=' . $registro_x['origen_liquidacion'] . '>' . $registro_x['area'] . '</option>';
}
?>