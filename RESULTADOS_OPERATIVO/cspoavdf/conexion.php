<?php
session_start();
if (phpversion()=="4.1.10")
{
	////session_register("conexion");
}
$_SESSION['secuser']=1;
$dns=cargaDNS($_SESSION['secuser']);
//$dns="LLANOS";
$usuario="Administrador";
$pass="losllanos";
$_SESSION["conexion"] = odbc_connect ($dns,$usuario,$pass);

function cargaDNS($sector)
{
	switch ($sector)
	{
		case 1:
			$zonav="LLANOS";
			break;
		case 2:
			$zonav="SJM";
			break;
		case 3:
			$zonav="SFA";
			break;
		case 4:
			$zonav="LLANOS";
			break;
		case 5:
			$zonav="VLP";
			break;
	}
	return $zonav;
}

?>