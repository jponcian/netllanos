<form name="form1" method="post" >
		  <p>&nbsp;</p>
<table width="47%" border="1" align="center">
            <tr>
              <td height="35" align="center" bgcolor="#FF0000"colspan="8"><span class="Estilo7"><u>Datos de la Providencia</u></span></td>
            </tr>
            <tr><td  bgcolor="#CCCCCC"><div align="center"><strong>Dependencia:</strong></div></td>
			  <td bgcolor="#FFFFFF"><div align="center">
			    <select id="OSEDE" name="OSEDE" onChange="cargar_combo2(this.value);">
                  <option value="-1">Seleccione</option>
                  <?php
if ($_SESSION['ADMINISTRADOR'] == 1 or $_SESSION['SEDE_USUARIO']==1)
	{ 
	$consulta_x = 'SELECT sector as id_sector, nombre FROM vista_providencias GROUP BY sector';
	}
else
	{
	// --- VALIDACION DE LA SEDE DEL USUARIO
	if ($_SESSION['SEDE_USUARIO']<>0) 
		{$sede='sector='.$_SESSION['SEDE_USUARIO'];} else {$sede='';}
	// -------------------------------------
	$consulta_x = 'SELECT sector as id_sector, nombre FROM vista_providencias WHERE '.$sede.' GROUP BY sector;';
	}
//---------------------
$tabla_x = mysql_query ($consulta_x); 
while ($registro_x = mysql_fetch_array($tabla_x))
	{
	echo '<option '; if ($_POST['OSEDE']==$registro_x['id_sector']) {echo 'selected="selected" ';}
	echo ' value='.$registro_x['id_sector'].'>'.$registro_x['nombre'].'</option>';
	}
?>
  </select><?php //echo $consulta_x; ?>
			  </div></td>
			  
			  <td  bgcolor="#CCCCCC"><div align="center"><strong>A&ntilde;o:</strong></div></td>
			  <td bgcolor="#FFFFFF"><div align="center"><span class="">
			    <select id="OANNO" name="OANNO" onChange="cargar_combo3(this.value);">
                  <option value="-1">Seleccione</option>
				  <?php
if ($_POST['OSEDE']>0)
	{
	$consulta_x = 'SELECT anno FROM expedientes_fiscalizacion WHERE sector=0'.$_POST['OSEDE'].' GROUP BY anno ORDER BY anno DESC;'; 
	$tabla_x = mysql_query ($consulta_x);
	while ($registro_x = mysql_fetch_array($tabla_x))
		{
		echo '<option '; if ($_POST['OANNO']==$registro_x['anno']) {echo 'selected="selected" ';}
		echo ' value='.$registro_x['anno'].'>'.$registro_x['anno'].'</option>';
		}
	}
?>
                </select>
			  </span></div></td>
              <td width="15%" bgcolor="#CCCCCC"><div align="center"><strong>Numero:</strong></div></td>
              <td width="36%">
                <div align="center">
                  <select id="ONUMERO" name="ONUMERO" size="1" onchange="this.form.submit();">
                    <option value="-1">Seleccione</option>
					  <?php
if ($_POST['OANNO']>0)
{
$consulta_x = 'SELECT numero FROM expedientes_fiscalizacion WHERE anno='.$_POST['OANNO'].' AND sector=0'.$_POST['OSEDE'].'  ORDER BY numero DESC;'; 
$tabla_x = mysql_query ($consulta_x);
while ($registro_x = mysql_fetch_array($tabla_x))
{
echo '<option '; if ($_POST['ONUMERO']==$registro_x['numero']) {echo 'selected="selected" ';}
echo ' value='.$registro_x['numero'].'>'.$registro_x['numero'].'</option>';
}
}
?>
                  </select>
                  </div></td>
            </tr>
			<tr> <td colspan="8" align="center"><p>&nbsp;</p>
		     </td></tr>
</table>
<p>&nbsp;</p>
</form>
<script language="JavaScript" >
$('#div1').hide();
$('#div2').hide();
$('#div3').hide();
//--------------------------------------------
function cargar_combo2(val)
{
    $.ajax({
        type: "POST",
        url: '0_combo_anno.php?sede='+document.form1.OSEDE.value,
        data: 'id='+val,
        success: function(resp){
            $('#OANNO').html(resp);
        }
    });
alertify.message("Por favor espere la carga de datos...");	
}
//--------------------------------------------
function cargar_combo3(val)
{
    $.ajax({
        type: "POST",
        url: '0_combo_numero.php?sede='+document.form1.OSEDE.value+'&anno='+document.form1.OANNO.value,
        data: 'id='+val,
        success: function(resp){
            $('#ONUMERO').html(resp);
        }
    });
alertify.message("Por favor espere la carga de datos...");	
}
//--------------------------------------------
</script>