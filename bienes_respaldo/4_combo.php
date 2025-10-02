<option value="0">Seleccione</option>
<?php
session_start();
include "../conexion.php";
//--------------
$sede = $_GET['sede'];
$division = $_GET['division'];
//--------------
if ($_POST['tipo'] == 1) {
	// Si es administrador o pertenece a la división 9 (Administración), ver todas las divisiones del sector seleccionado
	if ($_SESSION['ADMINISTRADOR'] > 0 || (isset($_SESSION['DIVISION_USUARIO']) && $_SESSION['DIVISION_USUARIO'] == 9)) {
		$consulta_x = 'SELECT z_jefes_detalle.division, z_jefes_detalle.descripcion FROM bn_areas INNER JOIN z_jefes_detalle ON z_jefes_detalle.division = bn_areas.division INNER JOIN z_sectores ON z_jefes_detalle.id_sector = z_sectores.id_sector WHERE z_jefes_detalle.id_sector = 0' . $sede . ' group by division ORDER BY bn_areas.division ASC, bn_areas.descripcion ASC';
	} else {
		// Usuario normal: sólo ver su propia división dentro de su sector
		$sede_user = isset($_SESSION['SEDE_USUARIO']) ? intval($_SESSION['SEDE_USUARIO']) : 0;
		$div_user = isset($_SESSION['DIVISION_USUARIO']) ? intval($_SESSION['DIVISION_USUARIO']) : 0;
		$consulta_x = 'SELECT z_jefes_detalle.division, z_jefes_detalle.descripcion FROM bn_areas INNER JOIN z_jefes_detalle ON z_jefes_detalle.division = bn_areas.division INNER JOIN z_sectores ON z_jefes_detalle.id_sector = z_sectores.id_sector WHERE z_jefes_detalle.id_sector = ' . $sede_user . ' AND z_jefes_detalle.division = ' . $div_user . ' group by division ORDER BY bn_areas.division ASC, bn_areas.descripcion ASC';
	}
	$tabla_x = mysql_query($consulta_x);
	while ($registro_x = mysql_fetch_array($tabla_x)) {
		echo '<option value=' . $registro_x['division'] . '>' . ($registro_x['descripcion']) . '</option>';
	}
}
//--------------
if ($_POST['tipo'] == 2) {
	if ($_SESSION['ADMINISTRADOR'] > 0 || (isset($_SESSION['DIVISION_USUARIO']) && $_SESSION['DIVISION_USUARIO'] == 9)) {
		$consulta_x = 'SELECT z_jefes_detalle.division, z_jefes_detalle.descripcion FROM bn_areas INNER JOIN z_jefes_detalle ON z_jefes_detalle.division = bn_areas.division INNER JOIN z_sectores ON z_jefes_detalle.id_sector = z_sectores.id_sector WHERE z_jefes_detalle.id_sector = 0' . $sede . ' and z_jefes_detalle.division<>' . $division . ' group by division ORDER BY bn_areas.division ASC, bn_areas.descripcion ASC';
	} else {
		$sede_user = isset($_SESSION['SEDE_USUARIO']) ? intval($_SESSION['SEDE_USUARIO']) : 0;
		$div_user = isset($_SESSION['DIVISION_USUARIO']) ? intval($_SESSION['DIVISION_USUARIO']) : 0;
		// Usuario normal: sólo su división (no se listan otras divisiones)
		$consulta_x = 'SELECT z_jefes_detalle.division, z_jefes_detalle.descripcion FROM bn_areas INNER JOIN z_jefes_detalle ON z_jefes_detalle.division = bn_areas.division INNER JOIN z_sectores ON z_jefes_detalle.id_sector = z_sectores.id_sector WHERE z_jefes_detalle.id_sector = ' . $sede_user . ' and z_jefes_detalle.division = ' . $div_user . ' group by division ORDER BY bn_areas.division ASC, bn_areas.descripcion ASC';
	}
	$tabla_x = mysql_query($consulta_x);
	while ($registro_x = mysql_fetch_array($tabla_x)) {
		echo '<option value=' . $registro_x['division'] . '>' . ($registro_x['descripcion']) . '</option>';
	}
}
//--------------
if ($_POST['tipo'] == 3) {
	if ($_SESSION['ADMINISTRADOR'] > 0 || (isset($_SESSION['DIVISION_USUARIO']) && $_SESSION['DIVISION_USUARIO'] == 9)) {
		$consulta_x = 'SELECT * FROM bn_areas WHERE division=' . $division . ' ORDER BY bn_areas.division ASC, bn_areas.descripcion ASC;';
	} else {
		$div_user = isset($_SESSION['DIVISION_USUARIO']) ? intval($_SESSION['DIVISION_USUARIO']) : 0;
		// Usuario normal: sólo áreas de su división
		$consulta_x = 'SELECT bn_areas.descripcion, bn_areas.id_area FROM bn_areas WHERE bn_areas.division = ' . $div_user . ' ORDER BY bn_areas.division ASC, bn_areas.descripcion ASC;';
	}
	$tabla_x = mysql_query($consulta_x);
	while ($registro_x = mysql_fetch_array($tabla_x)) {
		echo '<option value=' . $registro_x['id_area'] . '>' . ($registro_x['descripcion']) . '</option>';
	}
}
//-------------- 
?>