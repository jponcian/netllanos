<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

//if ($_SESSION['VERIFICADO'] != "SI") { 
//    header ("Location: index.php?errorusuario=val"); 
//    exit(); 
//	}
//----------
?>
<html>
<head>
  <title>Carpetas Compartidas</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" /></head>
<p>
  <?php include "../titulo.php";?>
</p>
<div align="center"><p align="center">
<?php
	include "menu.php";
?>
</div>
<form id="form1" name="form1" method="post" action="">
<p align="center">&nbsp;</p>
<table width="350" height="249" border="1" align="center" class="formateada">
    <tr>
      <th ><div align="left" class="TituloTabla">
        <div align="center">Carpetas Compartidas</div>
      </div></th>
    </tr>
	 <tr id="linea1">
      <td><div align="left" class="Estilo2"><a href="\redes\Gerencia\index.php">Regi&oacute;n Los Llanos</a></div></td>
    </tr>
	 <tr id="linea02">
      <td ><div align="left" class="Estilo2"><a href="\redes\administracion">Administraci&oacute;n</a></div></td>
    </tr>
		 <tr id="linea02">
      <td ><div align="left" class="Estilo2"><a href="\Documentos en redes\especiales">Contribuyentes Especiales</a></div></td>
    </tr>
    <tr id="linea01">
      <td><div align="left" class="Estilo2"><a href="\redes\recaudacion">Recaudaci&oacute;n</a></div></td>
    </tr>
    <tr id="linea01">
      <td><div align="left" class="Estilo2"><a href="\redes\tramitaciones">Tramitaciones</a></div></td>
    </tr>
	  <tr id="linea22">
      <td><div align="left" class="Estilo2"><a href="\redes\sanjuan">Sector San Juan de los Morros</a></div></td>
    </tr>
	  <tr id="linea33">
      <td><div align="left" class="Estilo2"><a href="\redes\apure">Sector San Fernando de Apure</a></div></td>
    </tr>
	  <tr id="linea44">
      <td><div align="left" class="Estilo2"><a href="\redes\altagracia">Unidad Altagracia de Orituco</a></div></td>
    </tr>
	 <tr id="linea55">
      <td><div align="left" class="Estilo2"><a href="\redes\lapascua">Sector Valle de la Pascua</a></div></td>
    </tr>
</table>
<p align="center">&nbsp;</p>
<p align="center">&nbsp;</p>
<p align="center">&nbsp;</p>
<p align="center">&nbsp;</p>
<p align="center">&nbsp;</p>
<p align="center">&nbsp;</p>
<p align="center">&nbsp;</p>
</form>
<p>
  <?php include "../pie.php";?>
</p>
<p>&nbsp;</p>