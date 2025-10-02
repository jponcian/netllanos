<?php 
session_start();
include "../conexion.php";
//--------------
$status  = $_GET['status1'];
$status2  = $_GET['status2'];
?>
<table border="0" align="center">
    <tr>
      <td>
	<form name="form2" method="post" action="modelos/demostrativa.php" target="_blank" ><input type="submit" class="btn btn-danger" name="CMDDEMOSTRATIVA" value="Demostrativa"></form>
	  </td>
	<?php 	
	$consulta = "SELECT * FROM vista_liquidacion_planillas WHERE status>=$status AND status<=$status2 AND origen_liquidacion=0".$_SESSION['ORIGEN']." AND anno_expediente=".$_SESSION['ANNO_PRO']." AND num_expediente=".$_SESSION['NUM_PRO']." AND sector=".$_SESSION['SEDE']." and serie=38;";
	//echo $consulta;
	$tabla = mysql_query($consulta);
	//------------------
if ($registro = mysql_fetch_object($tabla))
	{
?>  <td >
	<form name="form4" method="post" action="modelos/interes.php" target="_blank" ><input type="submit" class="btn btn-danger" name="CMDINTERES" value="Intereses"></form>
	  </td>
	  <?php
	}
?>
	   <td>
	<form name="form3" method="post" action="modelos/planilla.php" target="_blank" ><input type="submit" class="btn btn-danger" name="CMDPLANILLA" value="Planilla">
	 
	 </form>
	<form name="form1a" method="post" action="" >
</td>
<td ><input type="submit"  name="CMDAJUSTAR" value="Ajustar Valor UT"></form></td>
</tr>		
</table>