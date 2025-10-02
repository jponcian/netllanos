<?php	
//------- VALIDAR LOS CAMPOS
if ($_POST['OTRIBUTO']>0 and $_POST['OSANCION']>0 and trim($_POST['OINICIO'])<>'' and trim($_POST['OFIN'])<>'')
	{
	//------ BUSQUEDA DE LA SANCION APLICADA
	$consulta3 = 'SELECT id_sancion, ind_planilla, art_regla, aplicacion, art_ley_rgto, ut_minimo, ut_maximo FROM a_sancion WHERE id_sancion='.$_POST['OSANCION'].';';
	$tabla3 = mysql_query ($consulta3);
	$registro3 = mysql_fetch_object($tabla3);
	//----------------
	$sancion =  $registro3->id_sancion;
	$ley =  $registro3->art_ley_rgto;
	$prov =  $registro3->art_regla;
	$aplicacion =  $registro3->aplicacion;
	$ut_min =  $registro3->ut_minimo;
	$ut_max =  $registro3->ut_maximo;
	$planilla =  $registro3->ind_planilla; 
	// PARA VER SI SE PIDE PLANILLA DE PRESENTACIÓN
	//---------- VALIDAR POR APLICACION
	//---------- XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
	if ($aplicacion==50 or $aplicacion==52 or $aplicacion==150 or $aplicacion==152 or $aplicacion==200)
		{
		include "0_calculo_multas2_parte1.php";
		}		
	//---------- XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
	if ($aplicacion==10 or $aplicacion==12 or $aplicacion==53 or $aplicacion==110 or $aplicacion==112 or $aplicacion==153)
		{
		if (trim($_POST['OPLANILLA'])<>'' and $_POST['OMONTO']>0 and trim($_POST['OPRESENTACION'])<>'' and trim($_POST['OVENCIMIENTO'])<>'' and $_POST['OPORCENTAJE']>0)
			{
			include "0_calculo_multas2_parte1.php";
			}
		else
			{
			echo "<script type=\"text/javascript\">alert('ï¿½ï¿½ï¿½Existen Campos Vacï¿½os!!!');</script>";		
			}
		}		
	//---------- XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
	if ($aplicacion==2 or $aplicacion==9 or $aplicacion==13 or $aplicacion==15)
		{
		if ($planilla>0)
			{
			if (trim($_POST['OPLANILLA'])<>'' and trim($_POST['OPRESENTACION'])<>'' and trim($_POST['OVENCIMIENTO'])<>'')
				{
				include "0_calculo_multas2_parte1.php";
				}
			else
				{
				echo "<script type=\"text/javascript\">alert('ï¿½ï¿½ï¿½Existen Campos Vacï¿½os!!!');</script>";		
				}
			}
		else
			{
			if (trim($_POST['OVENCIMIENTO'])<>'')
				{
				include "0_calculo_multas2_parte1.php";
				}
			else
				{
				echo "<script type=\"text/javascript\">alert('ï¿½ï¿½ï¿½Existen Campos Vacï¿½os!!!');</script>";		
				}
			}
		}		
	//---------- XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
	if ($aplicacion==3 or $aplicacion==11 or $aplicacion==18 or $aplicacion==51 or $aplicacion==54 or $aplicacion==118 or $aplicacion==151 or $aplicacion==154 or $aplicacion==155)
		{
		if (trim($_POST['OPLANILLA'])<>'' and $_POST['OMONTO']>0 and trim($_POST['OPAGO'])<>'' and trim($_POST['OVENCIMIENTO'])<>'')
			{
			include "0_calculo_multas2_parte1.php";
			}
		else
			{
			echo "<script type=\"text/javascript\">alert('ï¿½ï¿½ï¿½Existen Campos Vacï¿½os!!!');</script>";		
			}
		}	

		
	//---------- XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
	if ($aplicacion==14 or $aplicacion==114 )
		{
		if (($sancion<836 or $sancion>=1550) and $sancion<10000)
			{
			if ($_POST['OFACTURAS']>0)
				{
				include "0_calculo_multas2_parte1.php";
				}
			else
				{
				echo "<script type=\"text/javascript\">alert('ï¿½ï¿½ï¿½Existen Campos Vacï¿½os!!!');</script>";		
				}
			}
		else
			{
			if (trim($_POST['OPLANILLA'])<>'' and trim($_POST['OPRESENTACION'])<>'' and trim($_POST['OVENCIMIENTO'])<>'')
				{
				include "0_calculo_multas2_parte1.php";
				}
			else
				{
				echo "<script type=\"text/javascript\">alert('ï¿½ï¿½ï¿½Existen Campos Vacï¿½os!!!');</script>";		
				}
			}
		}		
	}
else
	{
	echo "<script type=\"text/javascript\">alert('ï¿½ï¿½ï¿½Existen Campos Vacï¿½os!!!');</script>";		
	}
?>