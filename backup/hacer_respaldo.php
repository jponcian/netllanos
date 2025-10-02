<?php
//error_reporting(0);
//-----------------
$servername = 'localhost';// 
$servername2 = 'localhost';// 
$port =3307;
$porta =3306;
$dbusername = 'root';// 
$dbpassword = '';// 
$dbusername2 = 'root';// 
$dbpassword2 = 'root';// 
$dbname = 'losllanos';// 
$dbname2 = 'tributos';// 
$dbname3 = 'illanos';// 
$dbname4 = 'db_siger_fiscalizacion';//
//--------------
$fecha = "_".date("Y-m-d");     
//$fichero = "../backup/".$dbname.$fecha.".sql";
$fichero = "d:/bdappllanos/".$dbname.$fecha.".sql";
$fichero_pd = "f:/bdappllanos/".$dbname.$fecha.".sql"; 
$fichero2 = "d:/bdappllanos/".$dbname2.$fecha.".sql";
$fichero_pd2 = "f:/bdappllanos/".$dbname2.$fecha.".sql"; 
$fichero3 = "d:/bdappllanos/".$dbname3.$fecha.".sql";
$fichero_pd3 = "f:/bdappllanos/".$dbname3.$fecha.".sql"; 
$fichero4 = "d:/bdappllanos/".$dbname4.$fecha.".sql";
$fichero_pd4 = "f:/bdappllanos/".$dbname4.$fecha.".sql"; 
//$fichero = "\\appllanos\c$\prueba\'".$dbname.$fecha.".sql";
//--------------
//---- PARA VALIDAR SI YA SE HAN HECHO RESPALDOS
$consulta_x = "SELECT * FROM zzz_respaldos WHERE fecha = date(now())"; 
$tabla_x = mysql_query ($consulta_x);
$numero_filas = mysql_num_rows($tabla_x);
//----------------
if ($numero_filas>0 or strtoupper(substr('losllanos',(strlen('losllanos')-6),6))=='PRUEBA')
	//if ($numero_filas>0 or strtoupper(substr($_SESSION['BDD'],(strlen($_SESSION['BDD'])-6),6))=='PRUEBA')
	{	
	// POR SI ESTÁ MAL HECHO EL RESPALDO	
//	if (filesize($fichero)<2000)
//		{
//		$sistema="show variables where variable_name= 'basedir'"; 
//		$restore=mysql_query($sistema); 
//		$DirBase=mysql_result($restore,0,"value"); 
//		$primero=substr($DirBase,0,1); 
//		if ($primero=="/") { $DirBase="mysqldump"; 	}  
//		else  
//		{ 	$DirBase=$DirBase."bin\mysqldump";	} 
//		//--------------------------
//		$executa="$DirBase --host=$servername --user=$dbusername --password=$dbpassword -R -c  --add-drop-table $dbname > $fichero"; 	
//		system($executa);
//		}
	}
else
	{
	$sistema="show variables where variable_name= 'basedir'"; 
	$restore=mysql_query($sistema); 
	$DirBase=mysql_result($restore,0,"value"); 
	$primero=substr($DirBase,0,1); 
	if ($primero=="/") { $DirBase="mysqldump"; 	}  
	else  
	{ 	$DirBase=$DirBase."\bin\mysqldump";	} 
	//--------------------------
	$executa="$DirBase --host=$servername  --user=$dbusername --password=$dbpassword -R -c  --add-drop-table $dbname > $fichero";
	system($executa);
	//--------------------------
	$executa="$DirBase --host=$servername  --user=$dbusername --password=$dbpassword -R -c  --add-drop-table $dbname > $fichero_pd";
	system($executa);
	//-----------------
	//--------------------------
	$executa="$DirBase --host=$servername2  --port=$port --user=$dbusername2 --password=$dbpassword2 -R -c  --add-drop-table $dbname2 > $fichero2";
	system($executa);
	//--------------------------
	$executa="$DirBase --host=$servername2 --port=$port --user=$dbusername2 --password=$dbpassword2 -R -c  --add-drop-table $dbname2 > $fichero_pd2";
	system($executa);
	//-----------------
	//--------------------------
	$executa="$DirBase --host=$servername2 --port=$port --user=$dbusername2 --password=$dbpassword2 -R -c  --add-drop-table $dbname3 > $fichero3";
	system($executa);
	//--------------------------
	$executa="$DirBase --host=$servername2 --port=$port --user=$dbusername2 --password=$dbpassword2 -R -c  --add-drop-table $dbname3 > $fichero_pd3";
	system($executa);
		//--------------------------
	$executa="$DirBase --host=$servername --user=$dbusername --password=$dbpassword -R -c  --add-drop-table $dbname4 > $fichero4";
	system($executa);
	//--------------------------
	$executa="$DirBase --host=$servername --user=$dbusername --password=$dbpassword -R -c  --add-drop-table $dbname4 > $fichero_pd4";
	system($executa);
	//-----------------

	//-----------------
	$consulta = "INSERT INTO zzz_respaldos (fecha, usuario) VALUES (date(now()), ".$_SESSION['CEDULA_USUARIO'].");";
	$tabla = mysql_query ($consulta);
	//---- ELIMINO LOS RESPALDOS CON 10 DIAS DE ANTIGUEDAD
	$fecha = "_".date ( 'Y-m-d' , strtotime("-10 day") );
	$fichero = "../backup/".$dbname.$fecha.".sql"; 
	unlink($fichero);
	$consulta = "DELETE FROM zzz_respaldos WHERE fecha='".date ( 'Y-m-d' , strtotime("-10 day") )."';";
	$tabla = mysql_query ($consulta);
	}
//---------
?>