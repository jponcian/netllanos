<?php
session_start();
include "../conexion.php";
//--------------
$sede = $_GET['sede'];
$division = $_GET['division'];
//--------------
if ($_POST['tipo'] == 1) {
	// Siempre mostrar opción Todas
	echo '<option value="0" selected>Todas</option>';
	if ($_SESSION['ADMINISTRADOR'] > 0 or $division == 9) {
		$consulta_x = 'SELECT z_jefes_detalle.division, z_jefes_detalle.descripcion FROM bn_areas INNER JOIN z_jefes_detalle ON z_jefes_detalle.division = bn_areas.division INNER JOIN z_sectores ON z_jefes_detalle.id_sector = z_sectores.id_sector WHERE z_jefes_detalle.id_sector = 0' . $sede . ' group by division ORDER BY bn_areas.division ASC, bn_areas.descripcion ASC';
	} else {
		$consulta_x = 'SELECT z_jefes_detalle.division, z_jefes_detalle.descripcion FROM bn_areas INNER JOIN z_jefes_detalle ON z_jefes_detalle.division = bn_areas.division INNER JOIN z_sectores ON z_jefes_detalle.id_sector = z_sectores.id_sector WHERE z_jefes_detalle.id_sector = 0' . $sede . ' and z_jefes_detalle.division = ' . $_SESSION['DIVISION_USUARIO'] . ' group by division ORDER BY bn_areas.division ASC, bn_areas.descripcion ASC';
	}
	$tabla_x = mysql_query($consulta_x);
	while ($registro_x = mysql_fetch_array($tabla_x)) {
		echo '<option value=' . $registro_x['division'] . '>' . ($registro_x['descripcion']) . '</option>';
	}
}
//--------------
if ($_POST['tipo'] == 2) {
	if ($_SESSION['ADMINISTRADOR'] > 0 or $division == 9) {
		$consulta_x = 'SELECT z_jefes_detalle.division, z_jefes_detalle.descripcion FROM bn_areas INNER JOIN z_jefes_detalle ON z_jefes_detalle.division = bn_areas.division INNER JOIN z_sectores ON z_jefes_detalle.id_sector = z_sectores.id_sector WHERE z_jefes_detalle.id_sector = 0' . $sede . ' and z_jefes_detalle.division<>' . $division . ' group by division ORDER BY bn_areas.division ASC, bn_areas.descripcion ASC';
	} else {
		$consulta_x = 'SELECT z_jefes_detalle.division, z_jefes_detalle.descripcion FROM bn_areas INNER JOIN z_jefes_detalle ON z_jefes_detalle.division = bn_areas.division INNER JOIN z_sectores ON z_jefes_detalle.id_sector = z_sectores.id_sector WHERE z_jefes_detalle.id_sector = 0' . $sede . ' and z_jefes_detalle.division = ' . $_SESSION['DIVISION_USUARIO'] . ' and z_jefes_detalle.division<>' . $division . ' group by division ORDER BY bn_areas.division ASC, bn_areas.descripcion ASC';
	}
	$tabla_x = mysql_query($consulta_x);
	while ($registro_x = mysql_fetch_array($tabla_x)) {
		echo '<option value=' . $registro_x['division'] . '>' . ($registro_x['descripcion']) . '</option>';
	}
}
//--------------
if ($_POST['tipo'] == 3) {
	echo '<option value="0" selected>Todas</option>';

	if ($_SESSION['ADMINISTRADOR'] > 0 or $division == 9) {
		$consulta_x = 'SELECT * FROM bn_areas WHERE division=' . $division . ' ORDER BY bn_areas.division ASC, bn_areas.descripcion ASC;';
	} else {
		$consulta_x = 'SELECT * FROM bn_areas WHERE division=' . $division . ' ORDER BY bn_areas.division ASC, bn_areas.descripcion ASC;';
	}
	$tabla_x = mysql_query($consulta_x);
	while ($registro_x = mysql_fetch_array($tabla_x)) {
		echo '<option value=' . $registro_x['id_area'] . '>' . ($registro_x['descripcion']) . '</option>';
	}

}
//-------------- 
?>