<option value="0">Seleccione</option>
<?php
session_start();
include "../conexion.php";
//--------------
$sede = $_GET['sede'];
$division = $_GET['division'];
//--------------
if ($_POST['tipo'] == 1) {
	if ($_SESSION['ADMINISTRADOR'] > 0 or $division == 9) {
		$consulta_x = 'SELECT division_actual, id_division_actual  FROM vista_bienes_reasignaciones_solicitadas WHERE id_division_actual<>id_division_destino AND numero>0 AND id_sector_actual = 0' . $sede . ' GROUP BY id_division_actual ORDER BY division_actual';
	} else {
		$consulta_x = 'SELECT division_actual, id_division_actual  FROM vista_bienes_reasignaciones_solicitadas WHERE id_division_actual<>id_division_destino AND numero>0 AND id_sector_actual = 0' . $sede . ' GROUP BY id_division_actual ORDER BY division_actual';
	}
	$tabla_x = mysql_query($consulta_x);
	while ($registro_x = mysql_fetch_array($tabla_x)) {
		echo '<option value=' . $registro_x['id_division_actual'] . '>' . ($registro_x['division_actual']) . '</option>';
	}
}
//--------------
if ($_POST['tipo'] == 2) {
	if ($_SESSION['ADMINISTRADOR'] > 0 or $division == 9) {
		$consulta_x = 'SELECT anno  FROM vista_bienes_reasignaciones_solicitadas WHERE id_division_actual<>id_division_destino AND numero>0 AND id_sector_actual = 0' . $sede . ' GROUP BY anno DESC';
	} else {
		$consulta_x = 'SELECT anno  FROM vista_bienes_reasignaciones_solicitadas WHERE id_division_actual<>id_division_destino AND numero>0 AND id_sector_actual = 0' . $sede . ' GROUP BY anno DESC';
	}
	$tabla_x = mysql_query($consulta_x);
	while ($registro_x = mysql_fetch_array($tabla_x)) {
		echo '<option value=' . $registro_x['anno'] . '>' . ($registro_x['anno']) . '</option>';
	}
}
//-------------- 
?>