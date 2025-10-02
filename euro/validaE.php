<?php
//mantenimiento();
//-----------
if (isset($_POST['OUSUARIO'])) {
  $_SESSION['CEDULA_USUARIO'] = (get_magic_quotes_gpc()) ? $_POST['OUSUARIO'] : addslashes($_POST['OUSUARIO']);
	}
if (isset($_POST['OCLAVE'])) {
  $_SESSION['VAR_CLAVE'] = (get_magic_quotes_gpc()) ? $_POST['OCLAVE'] : addslashes($_POST['OCLAVE']);
	}

if ((trim($_SESSION['CEDULA_USUARIO'])=='') or (trim($_SESSION['VAR_CLAVE'])==''))
	{
	header("Location: index.php?errorusuario=vacio");
	exit();
	}

//----------- VALIDAR LA CEDULA
$consulta_x = "SELECT cedula FROM z_empleados WHERE cedula = ".$_SESSION['CEDULA_USUARIO'].";";
$tabla_x = mysql_query($consulta_x);
$registro_x = mysql_fetch_array($tabla_x);

if ($registro_x['cedula']<>$_SESSION['CEDULA_USUARIO'])
	{
	header ("Location: index.php?errorusuario=sist");
	exit();
	}

//------------ VALIDAR LA CLAVE
$consulta_x = "SELECT * FROM z_empleados WHERE cedula = ".$_SESSION['CEDULA_USUARIO']." AND clave='".$_SESSION['VAR_CLAVE']."'"; 
$tabla_x = mysql_query ($consulta_x);
$registro_x = mysql_fetch_array($tabla_x);

//---------
if ($registro_x['cedula']==$_SESSION['CEDULA_USUARIO'])
	{
	//-------- CLAVE VERIFICADA
	$_SESSION['VERIFICADO'] = 'SI';
	$_SESSION['SEDE_USUARIO'] = $registro_x['sector'];
	$_SESSION['DIVISION_USUARIO'] = $registro_x['division'];
	$_SESSION['ADMINISTRADOR'] = $registro_x['administrador'];
	$_SESSION['TWITTER'] = $registro_x['twitter'];
	$_SESSION['BDD'] = 'losllanos';
	$_SESSION['NOM_USUARIO']=$registro_x['Nombres'].' '.$registro_x['Apellidos'];
	$_SESSION['CARGO_USUARIO']=$registro_x['Cargo'];
	
	//----------- UT ACTUAL
	$consulta1 = "SELECT ValorUT FROM a_valorut ORDER BY FechaAplicacion DESC;"; 
	$tabla1 = mysql_query ($consulta1);
	$registro1 = mysql_fetch_array($tabla1);
	$_SESSION['VALOR_UT_ACTUAL'] = $registro1['ValorUT'];	

	//----------------- POR SI EL USUARIO ES IGUAL A LA CLAVE
	if ($_SESSION['CEDULA_USUARIO'] == $_SESSION['VAR_CLAVE'])
		{
		header ("Location: ../CLAVES/menuprincipal.php?errorusuario=cc");
		exit();
		}
	//-----------------
	if ($_SESSION['ADMINISTRADOR']>0)
		{	
		//------------------
		if ($_POST['OBDD']=='losllanos' or $_POST['OBDD']=='losllanos_prueba')
			{
			//$_SESSION['VAR_CLAVE']='-1';
			$_SESSION['BDD'] = $_POST['OBDD'];
			//-------------
			header ("Location: ../cobro/30_actualizar_moneda1.php");
			exit();
			}
		}
	else
		{
		$_SESSION['ENCUESTA'] = 'SI';
		//---------------
		header ("Location: ../cobro/30_actualizar_moneda1.php");
		exit();
		}
	}
else 	
	{ 
	header("Location: index.php?errorusuario=sist");
	exit();
	} 
?>
<form name="form1" method="post">
<div align="center">
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<table border="1" align="center">
<tr>
  <td height="35" align="center" colspan="6" bgcolor="#FF0000"><span class="Estilo1 Estilo7"><strong><u>SELECCIONE LA BASE DE DATOS A TRABAJAR </u></strong></span></td>
  </tr><tr>	
		<td  bgcolor="#CCCCCC"><div align="center"><strong>BASE DE DATOS=&gt;</strong></div></td>
		  <td bgcolor="#FFFFFF"><div align="center"><span class="Estilo1">
			<select name="OBDD" size="1" onChange="this.form.submit()">
			  <option value="-1">Seleccione</option>
<?php
	if ($registro_x['cedula']<>'123456789')
	{
?>
	 <option value="losllanos">ORIGINAL</option>
<?php
	}
?>
			 <option value="losllanos_prueba">COPIA</option>
			</select>
		  </span></div></td>
		</tr>
</table>
<p>&nbsp;</p>
</div>
</form>