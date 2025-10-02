<option value="0">Seleccione</option>
<?php
session_start();
include "../conexion.php";
//--------------
$sede = $_GET['sede'];
$division = $_GET['division'];
//--------------
if ($_POST['tipo'] == 1) {
	// Determinar si el usuario tiene privilegios para ver todas las divisiones
	$is_admin = ($_SESSION['ADMINISTRADOR'] > 0) || (isset($_SESSION['DIVISION_USUARIO']) && $_SESSION['DIVISION_USUARIO'] == 9);

	if ($is_admin) {
		// Corregir el filtro de sede/id_sector: si se pasó sede, usarla; si no, no filtrar por id_sector
		$sede_int = isset($sede) && $sede !== '' ? intval($sede) : 0;
		if ($sede_int > 0) {
			$where_sector = 'id_sector = ' . $sede_int;
		} else {
			$where_sector = '1=1';
		}
		$consulta_x = 'SELECT * FROM vbienes_pendientes WHERE ' . $where_sector . ' AND interno = 0 AND por_reasignar = 1 GROUP BY division';
		$tabla_x = mysql_query($consulta_x);
		while ($registro_x = mysql_fetch_array($tabla_x)) {
			// Intentamos usar campos esperados 'division' y 'descripcion'
			$val = isset($registro_x['division']) ? $registro_x['division'] : (isset($registro_x['division_actual']) ? $registro_x['division_actual'] : '');
			$txt = isset($registro_x['descripcion']) ? $registro_x['descripcion'] : (isset($registro_x['division_actual']) ? $registro_x['division_actual'] : $val);
			if ($val !== '')
				echo '<option value=' . $val . '>' . ($txt) . '</option>';
		}
	} else {
		// Usuario sin privilegios: mostrar únicamente su división
		$sede_user = isset($_SESSION['SEDE_USUARIO']) ? intval($_SESSION['SEDE_USUARIO']) : 0;
		$div_user = isset($_SESSION['DIVISION_USUARIO']) ? intval($_SESSION['DIVISION_USUARIO']) : 0;

		// Obtener descripción de la división desde z_jefes_detalle para mostrar en el option
		$descripcion_div = '';
		if ($div_user > 0) {
			// usar la conexión mysqli si está disponible en sesión, sino caer a mysql
			if (isset($_SESSION['conexionsqli'])) {
				$q = "SELECT descripcion FROM z_jefes_detalle WHERE division = " . intval($div_user) . " LIMIT 1";
				$r = mysqli_query($_SESSION['conexionsqli'], $q);
				if ($r && $row = mysqli_fetch_assoc($r)) {
					$descripcion_div = $row['descripcion'];
				}
			} else {
				$q = 'SELECT descripcion FROM z_jefes_detalle WHERE division = ' . intval($div_user) . ' LIMIT 1';
				$r = mysql_query($q);
				if ($r && $row = mysql_fetch_assoc($r)) {
					$descripcion_div = $row['descripcion'];
				}
			}
		}

		if (trim($descripcion_div) == '')
			$descripcion_div = 'División ' . $div_user;
		if ($div_user > 0) {
			echo '<option value=' . $div_user . '>' . htmlspecialchars($descripcion_div) . '</option>';
		}
	}
}
////--------------
//if ($_POST['tipo']==2)
//	{
//	if ($_SESSION['ADMINISTRADOR'] > 0 or $division==9) 
//		{ 
//		$consulta_x = 'SELECT z_jefes_detalle.division, z_jefes_detalle.descripcion FROM bn_areas INNER JOIN z_jefes_detalle ON z_jefes_detalle.division = bn_areas.division INNER JOIN z_sectores ON z_jefes_detalle.id_sector = z_sectores.id_sector WHERE z_jefes_detalle.id_sector = 0'.$sede.' and z_jefes_detalle.division<>'.$division.' group by division ORDER BY bn_areas.division ASC, bn_areas.descripcion ASC'; 
//		}
//	else
//		{
//		$consulta_x = 'SELECT z_jefes_detalle.division, z_jefes_detalle.descripcion FROM bn_areas INNER JOIN z_jefes_detalle ON z_jefes_detalle.division = bn_areas.division INNER JOIN z_sectores ON z_jefes_detalle.id_sector = z_sectores.id_sector WHERE z_jefes_detalle.id_sector = 0'.$sede.' and z_jefes_detalle.division = '.$_SESSION['DIVISION_USUARIO'].' and z_jefes_detalle.division<>'.$division.' group by division ORDER BY bn_areas.division ASC, bn_areas.descripcion ASC'; 
//		}
//	$tabla_x = mysql_query($consulta_x);
//	while ($registro_x = mysql_fetch_array($tabla_x))
//		{ 
//		echo '<option value='.$registro_x['division'].'>'.($registro_x['descripcion']).'</option>';
//		}
//	}
////--------------
//if ($_POST['tipo']==3)
//	{
//	if ($_SESSION['ADMINISTRADOR'] > 0 or $division==9) 
//		{ 
//		$consulta_x = 'SELECT * FROM bn_areas WHERE division='.$division.' ORDER BY bn_areas.division ASC, bn_areas.descripcion ASC;'; 
//		}
//	else
//		{
//		$consulta_x = 'SELECT bn_areas.descripcion, bn_areas.id_area FROM bn_areas INNER JOIN z_jefes_detalle ON bn_areas.division = z_jefes_detalle.id_division WHERE z_jefes_detalle.division='.$division.' ORDER BY bn_areas.division ASC, bn_areas.descripcion ASC;'; 
//		}
//	$tabla_x = mysql_query ($consulta_x);
//	while ($registro_x = mysql_fetch_array($tabla_x))
//		{
//		echo '<option value='.$registro_x['id_area'].'>'.($registro_x['descripcion']).'</option>';
//		}
//
//	}
//-------------- 
?>