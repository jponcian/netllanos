<?php 
$_SESSION[1] = odbc_connect ("LLANOS","Administrador","losllanos");
$_SESSION[2] = odbc_connect ("SJM","Administrador","losllanos");
$_SESSION[3] = odbc_connect ("SFA","Administrador","losllanos");
$_SESSION[4] = odbc_connect ("ALT","Administrador","losllanos");
$_SESSION[5] = odbc_connect ("VLP","Administrador","losllanos");

// BUSQUEDA DEL MAYOR POR LA SERIE	
$consulta = "SELECT Max(Liquidacion.Secuencial) AS Secuencial FROM Liquidacion WHERE (((Liquidacion.Secuencial)<>999999999) AND ((Liquidacion.Serie)=".$Serie.") AND ((Left([Numero_Liquidacion],4))=Year(Date())));";
$tabla = odbc_exec ($_SESSION[$_SESSION['SEDE_USUARIO']], $consulta);
$registro = odbc_fetch_object($tabla);
if ($registro->Secuencial>0)
	{$SECUENCIAL_VIEJO=$registro->Secuencial+1;}
else 
	{$SECUENCIAL_VIEJO=1;}
// FIN
?>      