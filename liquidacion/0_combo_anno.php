<option value="0">Seleccione</option>
<?php
session_start();
include "../conexion.php";
//--------------
$consulta_x = 'SELECT anno_expediente FROM vista_sanciones_aplicadas WHERE status>=' . $_GET['status1'] . ' AND status<=' . $_GET['status2'] . ' AND origen_liquidacion=' . $_POST['id'] . ' AND sector=0' . $_GET['sede'] . ' GROUP BY anno_expediente ORDER BY anno_expediente DESC';
$tabla_x = mysql_query($consulta_x);
while ($registro_x = mysql_fetch_array($tabla_x)) {
	echo '<option value=' . $registro_x['anno_expediente'] . '>' . $registro_x['anno_expediente'] . '</option>';
}
?>