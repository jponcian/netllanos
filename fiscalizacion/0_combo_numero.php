<option value="0">Seleccione</option>
<?php
session_start();
include "../conexion.php";
//--------------
$consulta_x = 'SELECT numero FROM expedientes_fiscalizacion WHERE anno=' . $_GET['anno'] . ' AND sector=0' . $_GET['sede'] . '  ORDER BY numero DESC;';
$tabla_x = mysql_query($consulta_x);
while ($registro_x = mysql_fetch_array($tabla_x)) {
	echo '<option value=' . $registro_x['numero'] . '>' . $registro_x['numero'] . '</option>';
}
?>