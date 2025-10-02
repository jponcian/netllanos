<?php
ob_end_clean();
session_start();

if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: ../index.php?errorusuario=val"); 
    exit(); 
	}

include "../../conexion.php";

	$Titulos = array('','Auxiliar Contable tributos Internos');
	
	list($anno1,$mes1,$dia1)=explode('/',$_SESSION['INICIO']);
	list($anno2,$mes2,$dia2)=explode('/',$_SESSION['FIN']);
	
	$Titulo = '	DESDE : ' . $dia1.'/'. $mes1 .'/'. $anno1 . ' HASTA: ' . $dia2.'/'. $mes2 .'/'. $anno2 ;
	
	////////// REGION DE EMISION
	$consulta_x = "SELECT Nombre FROM Regiones;";
	$tabla_x = mysql_query ($consulta_x);
	$registro_x = mysql_fetch_object($tabla_x);
	$Region=$registro_x->Nombre;
	// ---------------------

	$Gerencia ='GERENCIA REGIONAL DE TRIBUTOS INTERNOS - '.$Region;
	
	$SEDES=array('Gerencia','Calabozo','San Juan de los Morros','San Fernando de Apure','Altagracia de Orituco','Valle de la Pascua');

	$Sede ='UNIDAD: ' . strtoupper($SEDES[$_SESSION['OSEDE']]);
	
?>
<style type="text/css">
<!--
.Estilo7 {font-size: 12px; font-weight: bold; }
.Estilo9 {font-size: 18px; font-weight: bold; }
.Estilo10 {font-size: 16px; font-weight: bold; }
.Estilo11 {font-size: 16px}
-->
</style>

<table width="100%" border="1">
		<tr>
	    <td><div align="center"><strong><?php echo $Gerencia; ?></strong></div></td>
	</tr>
</table>
	
	<table width="100%" border="1">
   <tr>
    <td bgcolor="#999999" ><div align="center" class="Estilo10">Num</div></td> 
	<td bgcolor="#999999" ><div align="center" class="Estilo10">Contribuyente</div></td> 
	<td bgcolor="#999999" ><div align="center" class="Estilo10">Rif</div></td>
    <td bgcolor="#999999" ><div align="center" class="Estilo10">Domicilio</div></td>
    <td bgcolor="#999999" ><div align="center" class="Estilo10">Actividad Económica</div></td>
    <td bgcolor="#999999" ><div align="center" class="Estilo10">Tipo de Sujeto </div></td>
    <td bgcolor="#999999" ><div align="center" class="Estilo10">Representante</div></td>
	<td bgcolor="#999999" ><div align="center" class="Estilo10">Rif Representante</div></td>
	<td bgcolor="#999999" ><div align="center" class="Estilo10">Tel&eacute;fono</div></td>
  </tr>

<?php

// ----- CONSULTA 
$tabla_x = mysql_query ($_SESSION[''VARIABLE1''] );

$i=1;

	while ($registro_x = mysql_fetch_object($tabla_x))
		{
		// DIRECCION Y NOMBRE DEL CONTRIBUYENTE
		$consulta = "SELECT * FROM Contribuyentes_Direccion WHERE Rif = '".$registro_x->Rif."';";
		$tabla_xx = mysql_query ($consulta );
		$registro_acta = mysql_fetch_object($tabla_xx);
		// ACTIVIDAD ECONÓMICA
		$consulta = "SELECT * FROM [Actividades Economicas] WHERE Codigo = 0".$registro_x->Ciiu1.";";
		$tabla_xxx = mysql_query ($consulta );
		$registro_xxx = mysql_fetch_object($tabla_xxx);
		// REPRESENTANTE
		$consulta = "SELECT * FROM Tipo_Socio_o_Asesor WHERE Rif = '".$registro_x->Rif."' AND Tipo='R';";
		$tabla_xxxx = mysql_query ($consulta );
		if ($registro_xxxx = mysql_fetch_object($tabla_xxxx))
			{}	else 
			{
			$consulta = "SELECT * FROM Tipo_Socio_o_Asesor WHERE Rif = '".$registro_x->Rif." ORDER BY Tipo DESC';";
			$tabla_xxxx = mysql_query ($consulta );
			$registro_xxxx = mysql_fetch_object($tabla_xxxx);
			}
		// -------- SI ES CONTRIBUYENTE ESPECIAL
		if ($registro_x->Especial == 1) { $especial = 'ESPECIAL';} else { $especial = ''; }
		// ---- IMPRESION DE LOS DATOS
		printf ('<tr>
			<td ><div align="center" class="Estilo10">%s</div></td> 
			<td ><div align="center" class="Estilo10">%s</div></td> 
			<td  ><div align="center" class="Estilo10">%s</div></td>
			<td  ><div align="center" class="Estilo10">%s</div></td>
			<td  ><div align="center" class="Estilo10">%s</div></td>
			<td  ><div align="center" class="Estilo10">%s</div></td>
			<td  ><div align="center" class="Estilo10">%s</div></td>
			<td  ><div align="center" class="Estilo10">%s</div></td>
			<td  ><div align="center" class="Estilo10">%s</div></td>
		  </tr>	',$i,$registro_x->NombreRazon,$registro_x->Rif,$registro_acta->Direccion,$registro_xxx->Nombre,$especial,$registro_xxxx->ApellidosyNombres,$registro_xxxx->Cedula_Rif,$registro_x->Telefonos);	
		$i++;
		}
		
// FIN

?>
</table>
    <p>&nbsp;</p>
