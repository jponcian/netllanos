<option value="0">Seleccione</option>
<?php
session_start();
include "../conexion.php";
//--------------
$consulta_x = 'SELECT num_expediente FROM vista_sanciones_aplicadas WHERE status>=' . $_GET['status1'] . ' AND status<=' . $_GET['status2'] . ' AND origen_liquidacion=' . $_GET['origen'] . ' AND sector=0' . $_GET['sede'] . ' AND anno_expediente =0' . $_POST['id'] . ' GROUP BY num_expediente ORDER BY num_expediente DESC';
$tabla_x = mysql_query($consulta_x);
while ($registro_x = mysql_fetch_array($tabla_x)) {
	echo '<option value=' . $registro_x['num_expediente'] . '>' . $registro_x['num_expediente'] . '</option>';
}
?>