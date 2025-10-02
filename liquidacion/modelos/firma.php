<?php
// BUSQUEDA DEL JEFE DE LA DIVISION O SECTOR DEPENDE DEL ORIGEN
if ($_SESSION['SEDE']==1)
	{
	list ($funcionario, $cargo1, $cargo2, $division) = funcion_funcionario(0+$_SESSION['CEDULA_USUARIO']);
	
	
	if ($division==7)
		{
		$consulta_x = "SELECT * FROM vista_jefe_esp WHERE id_sector=1;";
		}
	else
		{
		if ($division==6)
			{
			$consulta_x = "SELECT * FROM vista_jefe_fis WHERE id_sector=1;";
			} 
		else
			{	
			if ($division==14)
				{
				$consulta_x = "SELECT * FROM vista_jefe_sum WHERE id_sector=1;";
				}
				else
				{	
				$consulta_x = "SELECT * FROM vista_jefe_rec WHERE id_sector=1;";
				}
			}
		}
	}
else
	{
	$consulta_x = "SELECT * FROM vista_jefe_rec WHERE id_sector=".$_SESSION['SEDE'].";";
	}
$tabla_x = mysql_query ( $consulta_x);
$registro_x = mysql_fetch_object($tabla_x);

//---------------------------------
$jefe = $registro_x->jefe;
$cedula = "C.I. N° V-" .$registro_x->cedula;
$cargo = $registro_x->cargo;
$providencia = $registro_x->providencia;
$fecha_prov = $registro_x->fecha_prov;
$gaceta = $registro_x->gaceta;
$fecha_gac = $registro_x->fecha_gaceta;
$division_sector = $registro_x->descripcion;

// FIN
//----------------
?>