<?php
session_start();
//--------------
$_SESSION['conexionsql'] = mysql_connect("localhost", "root", "");
mysql_select_db($_GET['bdd'], $_SESSION['conexionsql']); 

include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") { 
    header ("Location: index.php?errorusuario=val"); 
    exit(); 
	}
	
//$acceso=6;
//------- VALIDACION ACCESO USUARIO
//include "../validacion_usuario.php";
//-----------------------------------	
?>
<style type="text/css">
<!--
.Estilo1 {
	font-size: 18px;
	color: #FFFFFF;
	font-weight: bold;
}
-->
</style>

  <form name="form5" method="post" action="">
    <td bgcolor="#CCCCCC" height=17><div align="center" class="Estilo8"><strong>::Rif Consultado::</strong></div></td>
		  <table width="92%" border=1 align=center>	
            <tbody>

              <tr>
                <td bgcolor="#FF0000" height="27" colspan="11" align="center"><p class="Estilo7 Estilo1"><u>Sanciones actuales aplicadas al Contribuyente</u></p>                </td>
              </tr>
			  <tr>
<td bgcolor="#CCCCCC" height=27><div align="center" class="Estilo8"><strong>Num</strong></div></td>			  
   <td bgcolor="#CCCCCC" height=27><div align="center" class="Estilo8"><strong>Sancion</strong></div></td>
    <td bgcolor="#CCCCCC" width="31%" ><div align="center" class="Estilo8"><strong>Descripci&oacute;n</strong></div></td>
	<td bgcolor="#CCCCCC" width="9%" ><div align="center" class="Estilo8"><strong>Periodo Inicial </strong></div></td>
   <td bgcolor="#CCCCCC" width="9%" ><div align="center" class="Estilo8"><strong>Periodo Final </strong></div></td>
	<td bgcolor="#CCCCCC" width="4%" ><div align="center" class="Estilo8"><strong>UT</strong></div></td>
	<td bgcolor="#CCCCCC" width="8%" ><div align="center" class="Estilo8"><strong>Monto</strong></div></td>
    <td bgcolor="#CCCCCC" width="13%" ><div align="center" class="Estilo8"><strong>Concurrencia</strong></div></td>
			  </tr>

 	<?php
//----------------------- MONTAJE DE LOS DATOS
	$consulta = "SELECT * FROM vista_sanciones_aplicadas_est_cta WHERE rif='".$_GET[rif]."' AND anno_expediente=".$_GET[anno]." AND num_expediente=".$_GET[num]." AND sector=".$_GET[sector]." ORDER BY id_sancion";
//echo $consulta;
$tabla = mysql_query($consulta);

$i=0;

while ($registro = mysql_fetch_object($tabla))
	{
	$i++;
	    echo '<div align="center" class="Estilo8">';
	    echo $registro->rif;	
		echo '<tr> <td bgcolor="#FFFFFF" height=27><div align="center" class="Estilo8">'; 
		echo $i;
		echo '</div></td><td ><div align="center">';
		echo $registro->id_sancion;
		echo '</div></td><td bgcolor="#FFFFFF" ><div align="left" class="Estilo8">';
		echo $registro->sancion;
		echo '</div></td><td ><div align="center">';
		echo date("d-m-Y",strtotime($registro->periodoinicio));
		echo '</div></td><td><div align="center">';	
		echo date("d-m-Y",strtotime($registro->periodofinal));
		echo '</div></td><td><div align="center">';
		echo (formato_moneda($registro->ut));
		echo '</div></td><td><div align="center" class="Estilo5">';
		echo (formato_moneda($registro->monto));
		echo '</div></td><td><div align="center" class="Estilo5"> <input name="checkbox" type="checkbox" value="checkbox"';
			if ($registro->concurrencia>1)	
			{
			echo ' checked';
			}
		echo' disabled="disabled" > ';
		echo '</div></td></tr>';
	}	

	?>		
            </tbody>
    </table>
	
		         </p>
         
  </form>
 
