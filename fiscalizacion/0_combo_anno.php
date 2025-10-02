<option value="0">Seleccione</option>
<?php
session_start();
include "../conexion.php";
//--------------
$consulta_x = 'SELECT anno FROM expedientes_fiscalizacion WHERE sector=0' . $_GET['sede'] . ' GROUP BY anno ORDER BY anno DESC;';
$tabla_x = mysql_query($consulta_x);
while ($registro_x = mysql_fetch_array($tabla_x)) {
	echo '<option value=' . $registro_x['anno'] . '>' . $registro_x['anno'] . '</option>';
}
?>