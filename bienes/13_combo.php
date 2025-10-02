<option value="0" >--> Seleccione <--</option>
<?php
session_start();
include "../conexion.php";
//--------------
$sede = $_GET['sede'];
//$division = $_GET['division'];
//--------------
if ($_POST['tipo']==1)
	{
	if ($_SESSION['ADMINISTRADOR'] > 0 or $division==9) 
		{ 
		$consulta_x = 'SELECT division_actual, id_division_actual  FROM vista_bienes_reasignaciones_solicitadas WHERE id_division_actual=id_division_destino AND por_reasignar = 2 AND id_sector_actual = 0'.$sede.' GROUP BY id_division_actual';
		}
	else
		{
		$consulta_x = 'SELECT division_actual, id_division_actual  FROM vista_bienes_reasignaciones_solicitadas WHERE id_division_actual=id_division_destino AND por_reasignar = 2 AND id_sector_actual = 0'.$sede.' GROUP BY id_division_actual';
		}
	$tabla_x = mysql_query($consulta_x);
	while ($registro_x = mysql_fetch_array($tabla_x))
		{ 
		echo '<option value='.$registro_x['id_division_actual'].'>'.$registro_x['division_actual'].'</option>';
		}
	}
////--------------
//if ($_POST['tipo']==2)
//	{
//	if ($_SESSION['ADMINISTRADOR'] > 0 or $division==9) 
//		{ 
//		$consulta_x = 'SELECT division_destino, id_division_destino  FROM vista_bienes_reasignaciones_solicitadas WHERE id_division_actual=id_division_destino AND por_reasignar = 2 AND id_division_actual = '.$division.' AND id_sector_actual = 0'.$sede.' GROUP BY id_division_destino'; 
//		}
//	else
//		{
//		$consulta_x = 'SELECT division_destino, id_division_destino  FROM vista_bienes_reasignaciones_solicitadas WHERE id_division_actual=id_division_destino AND por_reasignar = 2 AND id_division_actual = '.$division.' AND id_sector_actual = 0'.$sede.' GROUP BY id_division_destino'; 
//		}
//	$tabla_x = mysql_query($consulta_x);
//	while ($registro_x = mysql_fetch_array($tabla_x))
//		{ 
//		echo '<option value='.$registro_x['id_division_destino'].'>'.$registro_x['division_destino'].'</option>';
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
////-------------- 
?>
