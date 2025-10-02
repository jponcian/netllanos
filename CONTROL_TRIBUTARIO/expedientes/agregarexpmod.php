<?php
	session_start();

	//$conexionsql = mysqli_connect('localhost','root','','losllanos');
	include "../conexion.php";
//include "../auxiliar.php";

	
	$hoy =date("Y-m-d");
	$nummemo=$_GET['nummemo'];
	$sector=$_GET['sector'];
	$fechamemo=date("Y-m-d",strtotime($_GET['fechamemo']));
	$tipo=$_GET['tipo'];
	$resultado=$_GET['resultado'];
	$destino=$_GET['destino'];
	$clausura=$_GET['clausura'];
	$añoprovidencia=$_GET['annoprovidencia'];
	$numprovidencia=$_GET['numprovidencia'];
	$Folio=$_GET['folio'];
	$fp=$_GET['fp'];
	$estatus =$_GET['estatus'];
	global $Anno_memo;
	$Anno_memo=date("Y",strtotime($_GET['fechamemo']));
	$admin = $_GET['admin'];
	//echo "Aquiiii el destino: ".$destino;

//************GUARDAR NUMERO DEL MEMORANDO****************//
/*$ultimomemo= "SELECT num_memo FROM correlativo_memos WHERE anno_memo=".$Anno_memo."";
$rs_memo = $conexionsql->query($ultimomemo);
$valormemo = $rs_memo->fetch_array(); 
$ultimoMemorando = $valormemo['num_memo'];*/

/*
function guardar_memo($proceder)
{
	global $conexionsql;
	if ($proceder == 1 )
	{
		global $ultimoMemorando;
		global $nummemo;
		global $nummemo;
		if ($ultimoMemorando<>$nummemo)
		{
			$agegarnumero= "UPDATE correlativo_memos SET num_memo=".$nummemo.", anno_memo=".$Anno_memo."";
			$tablanummemo = $conexionsql->query($agegarnumero);
		}
	}
}
*/
//echo $añoprovidencia.",".$numprovidencia.",".$nummemo.",".$fechamemo.",".$tipo.",".$resultado.",".$destino;

if ($añoprovidencia>0 and $numprovidencia>0 and $nummemo!="" and $fechamemo!="" and $tipo!="" and $resultado!="" and $destino!="") 
{
	if ($clausura=="No")
	{ 
		$cierre=0;
	}
	else
	{ 
		$cierre=1;
	}
	$consulta= "SELECT * FROM ct_tmp_mod_salida_expediente WHERE Anno_Providencia=".$añoprovidencia." and NroAutorizacion=".$numprovidencia." and sector=".$sector;
	//echo "AquiConsulta 1ra...: " . $consulta;
	$rs_access = $conexionsql->query($consulta);
	$encontrado = $rs_access->num_rows;
	//echo "Hallado en temporal: ".$encontrado;
	$borrado = 0;
	if ($encontrado > 0)
	{
		while ($registro_borrado = $rs_access->fetch_object())
		{
			//echo $registro_borrado->NroAutorizacion.'<br/>';
			$borrado = $registro_borrado->borrado;
		}
	}

	if ($borrado == 0)
	{
		$consulta= "SELECT * FROM ct_salida_expediente WHERE Anno_Providencia=".$añoprovidencia." and NroAutorizacion=".$numprovidencia." and sector=".$sector;
		$rs_access = $conexionsql->query($consulta);
		//echo "AquiConsulta 2da...: ".$consulta;
	}
	
	if ($fila = $rs_access->fetch_object())
	{
		//buscar_registro($varstatus);
		?>
		<table width="95%" border="0" cellspacing="0" cellpadding="0" align="center">
		  <tr>
			<td align="center" style="color:#FCFF00"><strong>!...Providencia YA Registrada, por favor verifique...!</strong></td>
		  </tr>
		</table><?php
		//echo '<br/>';
		buscar_registro($estatus,$añoprovidencia,$numprovidencia,$Anno_memo,$nummemo,$sector);
		//echo "Buscar Registro (".$estatus.",".$añoprovidencia.",".$numprovidencia.",".$Anno_memo.",".$nummemo.")";
	}
	else
	{
		/////////////CONTRIBUYENTES VDF///////////////////
	
		if ($tipo=="VDF")
		{
			if ($estatus==11)
			{
				conformes($destino,$estatus,$añoprovidencia,$numprovidencia,$fp,$sector);
			}
	
			if ($estatus==12 or $estatus==42)
			{
				vdfsancionados($destino,$estatus,$añoprovidencia,$numprovidencia,$fp,$sector,$especial);
			}
		}
		/////////////FIN CONTRIBUYENTES VDF///////////////
		
		/////////////CONTRIBUYENTES SUCESIONES///////////////////
		if ($tipo=="Sucesiones")
		{
			if ($estatus==21)
			{
				SUCconformes($destino,$estatus,$añoprovidencia,$numprovidencia,$fp,$sector);
			}
	
			if ($estatus==23)
			{		
				Allanados($destino,$estatus,$añoprovidencia,$numprovidencia,$fp,$sector);
			}
			
			if ($estatus==25)
			{
				NoAllanados($destino,$estatus,$añoprovidencia,$numprovidencia,$fp,$sector);
			}
	
			if ($estatus==24)
			{
				AllanadosParcial($destino,$estatus,$añoprovidencia,$numprovidencia,$fp,$sector);
			}
		}
		/////////////FIN CONTRIBUYENTES SUCESIONES///////////////
		
		/////////////CONTRIBUYENTES INVESTIGACIONES///////////////////
		if ($tipo=="Investigaciones")
		{
			if ($estatus==31)
			{
				conformes($destino,$estatus,$añoprovidencia,$numprovidencia,$fp,$sector);
			}
	
			if ($estatus==32 or $estatus==42)
			{
				invsancionados($destino,$estatus,$añoprovidencia,$numprovidencia,$fp,$sector,$especial);
			}
			
			if ($estatus==33 or $estatus==43)
			{
				Allanados($destino,$estatus,$añoprovidencia,$numprovidencia,$fp,$sector,$especial);
			}
			
			if ($estatus==35)
			{
				NoAllanados($destino,$estatus,$añoprovidencia,$numprovidencia,$fp,$sector);
			}
	
			if ($estatus==34 or $estatus==44)
			{
				AllanadosParcial($destino,$estatus,$añoprovidencia,$numprovidencia,$fp,$sector,$especial);
			}
		}
		/////////////FIN CONTRIBUYENTES SUCESIONES///////////////
		
	}
} else {
	?>
	<table width="95%" border="0" cellspacing="0" cellpadding="0" align="center">
	  <tr>
		<td align="center" style="color:#F00"><strong>!...Existen Campos vacios, por favor verifique...!</strong></td>
	  </tr>
	</table><?php
	buscar_registro($estatus,$añoprovidencia,$numprovidencia,$Anno_memo,$nummemo,$sector);
	echo '<br/>';	
}

///////////FUNCIONES////////////////////////////////////////

	function conformes($destino,$estatus,$añoprovidencia,$numprovidencia,$fp,$sector)
	{
		global $Folio;
		global $conexionsql;
		global $Anno_memo;
		global $cierre;
		global $fp;
		global $admin;

		$nummemo=$_GET['nummemo'];
		$Anno_memo=date("Y",strtotime($_GET['fechamemo']));

		if ($Folio <> "" and $Folio <> "0")
		{
			//echo $nummemo;
			if ($destino=="Tramitacion")
			{
				//echo "Folio Nro:....".$Folio."-".$Anno_memo;
				$encontrado = 0;
				/*
				//BUSQUEDA EN ACCESS *******************************************************************************************************************************
				$_SESSION[1] = odbc_connect ("LLANOS","Administrador","losllanos");
				$_SESSION[2] = odbc_connect ("SJM","Administrador","losllanos");
				$_SESSION[3] = odbc_connect ("SFA","Administrador","losllanos");
				$_SESSION[4] = odbc_connect ("ALT","Administrador","losllanos");
				$_SESSION[5] = odbc_connect ("VLP","Administrador","losllanos");
				
				$agregar= "SELECT count(Anno) as num FROM CS_Salida_Archivo WHERE Anno=".$añoprovidencia." and Numero=".$numprovidencia;
				$rs = odbc_exec ($_SESSION[$_SESSION['SEDE_USUARIO']], $agregar);
				$num=odbc_result($rs,"num");
				$encontrado = 0;
				if ($num > 0)
				{
					$encontrado = 1;
					$agregar= "SELECT * FROM CS_Salida_Archivo WHERE Anno=".$añoprovidencia." and Numero=".$numprovidencia;
					$rs = odbc_exec ($_SESSION[$_SESSION['SEDE_USUARIO']], $agregar);
					$valor = odbc_fetch_array($rs);
					$nummemo=$_GET['nummemo'];
					$FechaEmision=$_GET['fechamemo'];
					$Anno_memo=date("Y");
					$Rif=$valor['Rif'];
					$Nombre=$valor['NombreRazon'];
					$Tipo = $valor['Tipo'];
					$FechaNotificacion=$valor['FechaNotificacion'];
					$FechaRecepcion=date("Y-m-d");
					$Division='TRAMITACION';
					$Contenido = 'PROV-INFORME FISCAL';
					$status=$estatus;
					$fp = $_GET['fp'];
					$_SESSION[VALOR_ID]++;
					$id = $_SESSION[VALOR_ID];
				}
				odbc_close_all();				
				//**************************************************************************************************************************************************
				*/
				//BUSQUEDA EN MYSQL ********************************************************************************************************************************
				if ($encontrado == 0)
				{
					$agregar= "SELECT * FROM vista_ct_salida_archivo WHERE Anno=".$añoprovidencia." and Numero=".$numprovidencia." and sector=".$sector;
					///**************POR AQUIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIII**********************************************************************///////////////
					$result = $conexionsql->query($agregar);
					$cantidad = $result->num_rows;
					if ($cantidad > 0)
					{
						$encontrado = 1;
						$valor = $result->fetch_array();
						$nummemo=$_GET['nummemo'];
						$FechaEmision=$_GET['fechamemo'];
						$Anno_memo=date("Y",strtotime($_GET['fechamemo']));
						$Rif=$valor['Rif'];
						$Nombre=$valor['NombreRazon'];
						$Tipo = $valor['Tipo'];
						$FechaNotificacion=$valor['FechaNotificacion'];
						$FechaRecepcion=$Anno_memo;
						$Division='TRAMITACION';
						$Contenido = 'PROV-INFORME FISCAL';
						$status=$estatus;
						$fp = $_GET['fp'];
						$_SESSION[VALOR_ID]++;
						$id = $_SESSION[VALOR_ID];
					}
				}
				//**************************************************************************************************************************************************
	
				if ($encontrado > 0)
				{
					$agegarmemo= "INSERT INTO ct_tmp_mod_salida_expediente (id,Anno_memo,NroMemo,sector,FechaEmision,Anno_Providencia,NroAutorizacion,Rif,Nombre,FechaNotificacion,FechaRecepcion,Division,FP,Tipo,Contenido,Folio,Status,modificado) VALUES  (".$id.",'".$Anno_memo."',".$sector.",'".$nummemo."','".date("Y-m-d",strtotime($FechaEmision))."','".$añoprovidencia."','".$numprovidencia."','".$Rif."','".$Nombre."','".date("Y-m-d",strtotime($FechaNotificacion))."','".date("Y-m-d",strtotime($FechaRecepcion))."','".$Division."',".$fp.",'".$Tipo."','".$Contenido."','".$Folio."','".$status."',1)";
							echo $agegarmemo;
					$tabla = $conexionsql->query($agegarmemo);
					if ($añoprovidencia!="" and $numprovidencia!="" and $Anno_memo!="" and $nummemo!="")
					{
						$Anno_memo=date("Y",strtotime($_GET['fechamemo']));
						$año= $añoprovidencia;
						$numero= $numprovidencia;
								
						$Ccsql1 = "SELECT count(*) as Total FROM ct_tmp_mod_salida_expediente WHERE Anno_Memo=".$Anno_memo." and NroMemo=".$nummemo." and sector=".$sector." and Status=".$estatus." and borrado=0";
						$rs_access = $conexionsql->query($Ccsql1);
						$valor = $rs_access->num_rows;
						$num = $valor; 
						
						if ($num!=0)
						{ 
							$Anno_memo=date("Y",strtotime($_GET['fechamemo']));
							$año= $añoprovidencia;
							$numero= $numprovidencia;
									
							$Ccsql = "SELECT * FROM ct_tmp_mod_salida_expediente WHERE Anno_Memo=".$Anno_memo." and NroMemo=".$nummemo." and sector=".$sector." and Status=".$estatus." and borrado=0";
							//echo "Consulta: ".$Ccsql;
							$rs_access = $conexionsql->query($Ccsql);
							$i=1;
							echo '<p></p>';
							echo '<table width="95%" border="0" cellpadding="3" cellspacing="0" align="center">';
							echo '<tr><td bgcolor="#999999"><b>N°';
							echo '</td><td bgcolor="#999999"><b>Año</td>';			
							echo '</td><td bgcolor="#999999"><b>Numero</td>';			
							echo '</td><td bgcolor="#999999"><b>Fecha Notificacion</td>';			
							echo '</td><td bgcolor="#999999"><b>Rif</td>';
							echo '</td><td bgcolor="#999999"><b>Nombre</td>';
							echo '</td><td bgcolor="#999999">&nbsp;</td>';			
							echo '</b>';
							while ($valor = $rs_access->fetch_array())
								{
									if ($color=="#EFEFEF") {
										$color="#D0D6DF";
									} else {
										$color="#EFEFEF";
									}
									echo '<tr bgcolor="'.$color.'"><td>';
									echo $i;
									echo '</td><td>';
									echo $valor['Anno_Providencia'];
									echo '</td><td>';
									echo $valor['NroAutorizacion'];
									echo '</td><td>';
									echo date("d-m-Y",strtotime($valor['FechaNotificacion']));
									echo '</td><td>';
									echo $valor['Rif'];
									echo '</td><td>';
									echo $valor['Nombre'];
									echo '</td><td>';
									if ($admin==1)
									{?>
										<img src="images/delete.png" width="16" height="16" style="cursor:pointer" title="Eliminar" id="btneliminar" onClick="EliminarExpediente(1,'<?php echo $valor['id']; ?>')"><?php
									} else {
										echo '&nbsp;';
									}
									echo '</td></tr>';
									$_SESSION['scantidad']=$i;
									$_SESSION[VAR_GLOBAL]=$i;
									$i++;
								}
							echo '</table>';
							$_SESSION['statusmemo']=$estatus;
							//guardar_memo(true);
						}
					}
				}
				else
				{
					echo "<script type=\"text/javascript\">
					jAlert('La Providencia NO ha sido notificada, por favor verifique');
					</script>"; 
					buscar_registro($estatus,$añoprovidencia,$numprovidencia,$Anno_memo,$nummemo,$sector);
					//echo "buscar_registro(".$estatus.",".$añoprovidencia.",".$numprovidencia.",".$Anno_memo.",".$nummemo.") - FALTA PARA CARGAR LO QUE ESTA EN EL TEMPORAR";
				}
			}
			else
			{
				echo "<script type=\"text/javascript\">
				jAlert('Los Expedientes Conformes deben remitirse a la División de Tramitación, por favor verifique');
				</script>"; 
				buscar_registro($estatus,$añoprovidencia,$numprovidencia,$Anno_memo,$nummemo,$sector);
				//echo "buscar_registro(".$estatus.",".$añoprovidencia.",".$numprovidencia.",".$Anno_memo.",".$nummemo.") - FALTA PARA CARGAR LO QUE ESTA EN EL TEMPORAR";
			}
		}
		else
		{
				echo "<script type=\"text/javascript\">
				jAlert('Los Expedientes Conformes requiere que indique el N° de FOLIO, por favor verifique');
				</script>"; 
				buscar_registro($estatus,$añoprovidencia,$numprovidencia,$Anno_memo,$nummemo,$sector);
				//echo "buscar_registro(".$estatus.",".$añoprovidencia.",".$numprovidencia.",".$Anno_memo.",".$nummemo.") - FALTA PARA CARGAR LO QUE ESTA EN EL TEMPORAR";
		}
	}

	function SUCconformes($destino,$estatus,$añoprovidencia,$numprovidencia,$fp,$sector)
	{
		//echo "SUCconformes(".$destino.",".$estatus.",".$añoprovidencia.",".$numprovidencia.",".$fp.")";
		global $Folio;
		global $conexionsql;
		global $Anno_memo;
		global $cierre;
		global $fp;
		global $admin;
		
		$nummemo=$_GET['nummemo'];
		$Anno_memo=date("Y",strtotime($_GET['fechamemo']));
		//echo "Folio: ".$Folio;
		if ($Folio <> "" and $Folio <> "0")
		{
			if ($destino=="Recaudacion - Sucesiones")
			{
				$encontrado = 0;
				/*
				//BUSQUEDA EN ACCESS *******************************************************************************************************************************
				$_SESSION[1] = odbc_connect ("LLANOS","Administrador","losllanos");
				$_SESSION[2] = odbc_connect ("SJM","Administrador","losllanos");
				$_SESSION[3] = odbc_connect ("SFA","Administrador","losllanos");
				$_SESSION[4] = odbc_connect ("ALT","Administrador","losllanos");
				$_SESSION[5] = odbc_connect ("VLP","Administrador","losllanos");
				
				$agregar= "SELECT count(Anno) as num FROM CS_Salida_Archivo WHERE Anno=".$añoprovidencia." and Numero=".$numprovidencia;
				$rs = odbc_exec ($_SESSION[$_SESSION['SEDE_USUARIO']], $agregar);
				$num=odbc_result($rs,"num");
				$encontrado = 0;
				if ($num > 0)
				{
					$encontrado = 1;
					$agregar= "SELECT * FROM CS_Salida_Archivo WHERE Anno=".$añoprovidencia." and Numero=".$numprovidencia;
					$rs = odbc_exec ($_SESSION[$_SESSION['SEDE_USUARIO']], $agregar);
					$valor = odbc_fetch_array($rs);
					$nummemo=$_GET['nummemo'];
					$FechaEmision=$_GET['fechamemo'];
					$Anno_memo=date("Y",strtotime($_GET['fechamemo']));
					$Rif=$valor['Rif'];
					$Nombre=$valor['NombreRazon'];
					$Tipo = $valor['Tipo'];
					$FechaNotificacion=$valor['FechaNotificacion'];
					$FechaRecepcion=$Anno_memo;
					$Division='RECAUDACION';
					$Contenido = 'PROV-INFORME FISCAL';
					$status=$estatus;
					$_SESSION[VALOR_ID]++;
					$id = $_SESSION[VALOR_ID];
				}				
				odbc_close_all();				
				//**************************************************************************************************************************************************
				*/
				//BUSQUEDA EN MYSQL ********************************************************************************************************************************
				if ($encontrado == 0)
				{
					$agregar= "SELECT * FROM vista_ct_salida_archivo WHERE Anno=".$añoprovidencia." and Numero=".$numprovidencia." and sector=".$sector;
					$result = $conexionsql->query($agregar);
					$cantidad = $result->num_rows;
					if ($cantidad > 0)
					{
						$encontrado = 1;
						$valor = $result->fetch_array();
						$nummemo=$_GET['nummemo'];
						$FechaEmision=$_GET['fechamemo'];
						$Anno_memo=date("Y",strtotime($_GET['fechamemo']));
						$Rif=$valor['Rif'];
						$Nombre=$valor['NombreRazon'];
						$Tipo = $valor['Tipo'];
						$FechaNotificacion=$valor['FechaNotificacion'];
						$FechaRecepcion=$Anno_memo;
						$Division='RECAUDACION';
						$Contenido = 'PROV-INFORME FISCAL';
						$status=$estatus;
						$_SESSION[VALOR_ID]++;
						$id = $_SESSION[VALOR_ID];
					}
				}
				//**************************************************************************************************************************************************

				if ($encontrado > 0)
				{
					$agegarmemo= "INSERT INTO ct_tmp_mod_salida_expediente (id,Anno_memo,NroMemo,sector,FechaEmision,Anno_Providencia,NroAutorizacion,Rif,Nombre,FechaNotificacion,FechaRecepcion,Division,Tipo,Contenido,Folio,Status,modificado) VALUES  (".$id.",'".$Anno_memo."','".$nummemo."',".$sector.",'".date("Y-m-d",strtotime($FechaEmision))."','".$añoprovidencia."','".$numprovidencia."','".$Rif."','".$Nombre."','".date("Y-m-d",strtotime($FechaNotificacion))."','".date("Y-m-d",strtotime($FechaRecepcion))."','".$Division."','".$Tipo."','".$Contenido."','".$Folio."','".$status."',1)";
					$tabla = $conexionsql->query($agegarmemo);
					if ($añoprovidencia!="" and $numprovidencia!="" and $Anno_memo!="" and $nummemo!="")
					{
						$Anno_memo=date("Y",strtotime($_GET['fechamemo']));
						$nummemo=$_GET['nummemo'];
						$año= $añoprovidencia;
						$numero= $numprovidencia;
								
						$Ccsql1 = "SELECT count(*) as Total FROM ct_tmp_mod_salida_expediente WHERE Anno_Memo=".$Anno_memo." and NroMemo=".$nummemo." and sector=".$sector." and Status=".$estatus." and borrado=0";
						$rs_access = $conexionsql->query($Ccsql1);
						$valor = $rs_access->num_rows;
						$num = $valor; 
						
						if ($num!=0)
						{ 
							$año= $añoprovidencia;
							$numero= $numprovidencia;
									
							$Ccsql = "SELECT * FROM ct_tmp_mod_salida_expediente WHERE Anno_Memo=".$Anno_memo." and NroMemo=".$nummemo." and sector=".$sector." and Status=".$estatus." and borrado=0";
							$rs_access = $conexionsql->query($Ccsql);
							$i=1;
							echo '<p></p>';
							echo '<table width="95%" border="0" cellpadding="3" cellspacing="0" align="center">';
							echo '<tr><td bgcolor="#999999"><b>N°';
							echo '</td><td bgcolor="#999999"><b>Año</td>';			
							echo '</td><td bgcolor="#999999"><b>Numero</td>';			
							echo '</td><td bgcolor="#999999"><b>Fecha Notificacion</td>';			
							echo '</td><td bgcolor="#999999"><b>Rif</td>';
							echo '</td><td bgcolor="#999999"><b>Nombre</td>';
							echo '</td><td bgcolor="#999999">&nbsp;</td>';			
							echo '</b>';
							while ($valor = $rs_access->fetch_array())
								{
									if ($color=="#EFEFEF") {
										$color="#D0D6DF";
									} else {
										$color="#EFEFEF";
									}
									echo '<tr bgcolor="'.$color.'"><td>';
									echo $i;
									echo '</td><td>';
									echo $valor['Anno_Providencia'];
									echo '</td><td>';
									echo $valor['NroAutorizacion'];
									echo '</td><td>';
									echo date("d-m-Y",strtotime($valor['FechaNotificacion']));
									echo '</td><td>';
									echo $valor['Rif'];
									echo '</td><td>';
									echo $valor['Nombre'];
									echo '</td><td>';
									if ($admin==1)
									{?>
										<img src="images/delete.png" width="16" height="16" style="cursor:pointer" title="Eliminar" onClick="EliminarExpediente(1,'<?php echo $valor['id']; ?>')"><?php
									} else {
										echo '&nbsp;';
									}
									echo '</td></tr>';
									$_SESSION['scantidad']=$i;
									$_SESSION[VAR_GLOBAL]=$i;
									$i++;
								}
							echo '</table>';
							$_SESSION['statusmemo']=$estatus;
							//guardar_memo(true);

						}
					}
				}
				else
				{
					echo "<script type=\"text/javascript\">
					jAlert('La Providencia NO ha sido notificada, por favor verifique');
					</script>";
					buscar_registro($estatus,$añoprovidencia,$numprovidencia,$Anno_memo,$nummemo,$sector);
				}
			}
			else
			{
				echo "<script type=\"text/javascript\">
				jAlert('Los Expedientes SUCESIONES Conformes deben remitirse a la División de Recaudación Area de Sucesiones, por favor verifique');
				</script>";
				buscar_registro($estatus,$añoprovidencia,$numprovidencia,$Anno_memo,$nummemo,$sector);
			}
		}
		else
		{
				echo "<script type=\"text/javascript\">
				jAlert('Los Expedientes de Sucesiones Conformes requiere que indique el N° de FOLIO, por favor verifique');
				</script>";
				buscar_registro($estatus,$añoprovidencia,$numprovidencia,$Anno_memo,$nummemo,$sector);
		}
	}

	function vdfsancionados($destino,$estatus,$añoprovidencia,$numprovidencia,$fp,$sector)
	{
		if ($destino=="Recaudacion - Liquidacion" or $destino=="Sujetos Pasivos Especiales")
		{
			global $Folio;
			global $conexionsql;
			global $Anno_memo;
			global $cierre;
			global $fp;
			global $admin;
			
			$nummemo=$_GET['nummemo'];
			$Anno_memo=date("Y",strtotime($_GET['fechamemo']));
			$encontrado = 0;
			/*

			//BUSQUEDA EN ACCESS *******************************************************************************************************************************
			$_SESSION[1] = odbc_connect ("LLANOS","Administrador","losllanos");
			$_SESSION[2] = odbc_connect ("SJM","Administrador","losllanos");
			$_SESSION[3] = odbc_connect ("SFA","Administrador","losllanos");
			$_SESSION[4] = odbc_connect ("ALT","Administrador","losllanos");
			$_SESSION[5] = odbc_connect ("VLP","Administrador","losllanos");
			
			$agregar= "SELECT count(Anno) as num FROM CS_Salida_DF WHERE Anno=".$añoprovidencia." and Numero=".$numprovidencia;
			$rs = odbc_exec ($_SESSION[$_SESSION['SEDE_USUARIO']], $agregar);
			$num=odbc_result($rs,"num");
			$encontrado = 0;
			if ($num > 0)
			{
				$encontrado = 1;
				$agregar= "SELECT * FROM CS_Salida_DF WHERE Anno=".$añoprovidencia." and Numero=".$numprovidencia;
				$rs = odbc_exec ($_SESSION[$_SESSION['SEDE_USUARIO']], $agregar);
				$valor = odbc_fetch_array($rs);
				$nummemo=$_GET['nummemo'];
				$FechaEmision=$_GET['fechamemo'];
				$Anno_memo=date("Y",strtotime($_GET['fechamemo']));
				$Rif=$valor['Rif'];
				$Nombre=$valor['NombreRazon'];
				$FechaNotificacion=$valor['FechaNotificacion'];
				$FechaRecepcion=$Anno_memo;
				if ($estatus==42)
				{
					$Division='ESPECIALES';
				} else {
					$Division='TRAMITACION';
				}
				$Multas=$valor['Multas'];
				$Añoresolucion=$valor['AnnoResolucion'];
				$Numresolucion=$valor['NroResolucion'];
				$Fecharesolucion=$valor['Fecha_Resolucion'];
				$status=$estatus;
				$_SESSION[VALOR_ID]++;
				$id = $_SESSION[VALOR_ID];
			}				
				odbc_close_all();				
			//**************************************************************************************************************************************************
			*/
			//BUSQUEDA EN MYSQL ********************************************************************************************************************************
			if ($encontrado == 0)
			{
				$agregar= "SELECT * FROM vista_ct_salida_df WHERE Anno=".$añoprovidencia." and Numero=".$numprovidencia." and sector=".$sector;
				$result = $conexionsql->query($agregar);
				$cantidad = $result->num_rows;
	
				if ($cantidad > 0)
				{
					$encontrado = 1;
					$valor = $result->fetch_array();
					$nummemo=$_GET['nummemo'];
					$FechaEmision=$_GET['fechamemo'];
					$Anno_memo=date("Y",strtotime($_GET['fechamemo']));
					$Rif=$valor['Rif'];
					$Nombre=$valor['NombreRazon'];
					$FechaNotificacion=$valor['FechaNotificacion'];
					$FechaRecepcion=$Anno_memo;
					if ($estatus==42)
					{
						$Division='ESPECIALES';
					} else {
						$Division='TRAMITACION';
					}
					$Multas=$valor['Multas'];
					$Añoresolucion=$valor['AnnoResolucion'];
					$Numresolucion=$valor['NroResolucion'];
					$Fecharesolucion=$valor['Fecha_Resolucion'];
					$status=$estatus;
					$_SESSION[VALOR_ID]++;
					$id = $_SESSION[VALOR_ID];
				}
			}
			//**************************************************************************************************************************************************

			if ($encontrado > 0)
			{
				$agegarmemo= "INSERT INTO ct_tmp_mod_salida_expediente (id,Anno_memo,NroMemo,sector,FechaEmision,Anno_Providencia,NroAutorizacion,Rif,Nombre,FechaNotificacion,FechaRecepcion,Division,Status,Clausurado,Multa_DF,Anno_Resolucion,NroResolucion,FechaResolucion,modificado) VALUES  (".$id.",".$Anno_memo.",".$nummemo.",".$sector.",'".date("Y-m-d",strtotime($FechaEmision))."',".$añoprovidencia.",".$numprovidencia.",'".$Rif."','".$Nombre."','".date("Y-m-d",strtotime($FechaNotificacion))."','".date("Y-m-d",strtotime($FechaRecepcion))."','".$Division."','".$status."',".$cierre.",".$Multas.",".$Añoresolucion.",".$Numresolucion.",'".date("Y-m-d",strtotime($Fecharesolucion))."',1)";
				//echo $agegarmemo;
				$tabla = $conexionsql->query($agegarmemo);

				if ($añoprovidencia!="" and $numprovidencia!="" and $Anno_memo!="" and $nummemo!="")
				{
					$Anno_memo=date("Y",strtotime($_GET['fechamemo']));
					$año= $añoprovidencia;
					$numero= $numprovidencia;
							
					$Ccsql1 = "SELECT count(*) as Total FROM ct_tmp_mod_salida_expediente WHERE Anno_Memo=".$Anno_memo." and NroMemo=".$nummemo." and sector=".$sector." and Status=".$estatus." and borrado=0";
					$rs_access = $conexionsql->query($Ccsql1);
					$valor = $rs_access->num_rows;
					$num = $valor; 
					
					if ($num!=0)
					{ 
						$año= $añoprovidencia;
						$numero= $numprovidencia;
						$Ccsql = "SELECT * FROM ct_tmp_mod_salida_expediente WHERE Anno_Memo=".$Anno_memo." and NroMemo=".$nummemo." and sector=".$sector." and Status=".$estatus." and borrado=0";
						$rs_access = $conexionsql->query($Ccsql);
						$i=1;
						echo '<p></p>';
						echo '<table width="95%" border="0" cellpadding="3" cellspacing="0" align="center">';
						echo '<tr><td bgcolor="#999999"><b>N°';
						echo '</td><td bgcolor="#999999"><b>Año</td>';			
						echo '</td><td bgcolor="#999999"><b>Numero</td>';			
						echo '</td><td bgcolor="#999999"><b>Fecha Notificacion</td>';			
						echo '</td><td bgcolor="#999999"><b>Rif</td>';
						echo '</td><td bgcolor="#999999"><b>Nombre</td>';			
						echo '</td><td bgcolor="#999999"><b>N° Resol.</td>';			
						echo '</td><td bgcolor="#999999"><b>Fecha Resol.</td>';			
						echo '</td><td bgcolor="#999999"><b>Multas</td>';			
						echo '</td><td bgcolor="#999999">&nbsp;</td>';			
						echo '</b>';
						while ($valor = $rs_access->fetch_array())
							{
								if ($color=="#EFEFEF") {
									$color="#D0D6DF";
								} else {
									$color="#EFEFEF";
								}
								echo '<tr bgcolor="'.$color.'"><td>';
								echo $i;
								echo '</td><td>';
								echo $valor['Anno_Providencia'];
								echo '</td><td>';
								echo $valor['NroAutorizacion'];
								echo '</td><td>';
								echo date("d-m-Y",strtotime($valor['FechaNotificacion']));
								echo '</td><td>';
								echo $valor['Rif'];
								echo '</td><td>';
								echo $valor['Nombre'];
								echo '</td><td>';
								echo $valor['Anno_Resolucion']."-".$valor['NroResolucion'];
								echo '</td><td>';
								echo date("d-m-Y",strtotime($valor['FechaResolucion']));
								echo '</td><td align="right">';
								echo number_format($valor['Multa_DF'],2);
								echo '</td><td>';
								if ($admin==1)
								{?>
									<img src="images/delete.png" width="16" height="16" style="cursor:pointer" title="Eliminar" onClick="EliminarExpediente(1,'<?php echo $valor['id']; ?>')"><?php
								} else {
									echo '&nbsp;';
								}
								echo '</td></tr>';
								$_SESSION['scantidad']=$i;
								$_SESSION[VAR_GLOBAL]=$i;
								$i++;
							}
						echo '</table>';
						$_SESSION['statusmemo']=$estatus;
						//guardar_memo(true);
					}
				}
			}
			else
			{
				echo "<script type=\"text/javascript\">
				jAlert('La Providencia NO ha sido notificada ó NO posee sanciones, por favor verifique');
				</script>";
				buscar_registro($estatus,$añoprovidencia,$numprovidencia,$Anno_memo,$nummemo,$sector);
			}
		}	
		else
		{
			//
			echo "<script type=\"text/javascript\">
			jAlert('Los Expedientes VDF Sancionados deben remitirse a la División de Recaudación Area de Liquidación, por favor verifique');
			</script>"; 
			buscar_registro($estatus,$añoprovidencia,$numprovidencia,$Anno_memo,$nummemo,$sector);
		}
	}

	function invsancionados($destino,$estatus,$añoprovidencia,$numprovidencia,$fp,$sector)
	{
		global $Folio;
		global $conexionsql;
		global $Anno_memo;
		global $cierre;
		global $fp;
		global $admin;
		
		$nummemo=$_GET['nummemo'];
		$Anno_memo=date("Y",strtotime($_GET['fechamemo']));

		if ($destino=="Recaudacion - Liquidacion" or $destino=="Sujetos Pasivos Especiales")
		{
			$encontrado = 0;
			/*

			//BUSQUEDA EN ACCESS *******************************************************************************************************************************
			$_SESSION[1] = odbc_connect ("LLANOS","Administrador","losllanos");
			$_SESSION[2] = odbc_connect ("SJM","Administrador","losllanos");
			$_SESSION[3] = odbc_connect ("SFA","Administrador","losllanos");
			$_SESSION[4] = odbc_connect ("ALT","Administrador","losllanos");
			$_SESSION[5] = odbc_connect ("VLP","Administrador","losllanos");
			
			$agregar= "SELECT count(Anno) as num FROM CS_Salida_DF WHERE Anno=".$añoprovidencia." and Numero=".$numprovidencia;
			$rs = odbc_exec ($_SESSION[$_SESSION['SEDE_USUARIO']], $agregar);
			$num=odbc_result($rs,"num");
			$encontrado = 0;
			if ($num > 0)
			{
				$encontrado = 1;
				$agregar= "SELECT * FROM CS_Salida_DF WHERE Anno=".$añoprovidencia." and Numero=".$numprovidencia;
				$rs = odbc_exec ($_SESSION[$_SESSION['SEDE_USUARIO']], $agregar);
				$valor = odbc_fetch_array($rs);
				$nummemo=$_GET['nummemo'];
				$FechaEmision=$_GET['fechamemo'];
				$Anno_memo=date("Y",strtotime($_GET['fechamemo']));
				$Rif=$valor['Rif'];
				$Nombre=$valor['NombreRazon'];
				$FechaNotificacion=$valor['FechaNotificacion'];
				$FechaRecepcion=$Anno_memo;
				if ($estatus==42)
				{
					$Division='ESPECIALES';
				} else {
					$Division='TRAMITACION';
				}
				$Multas=$valor['Multas'];
				$Añoresolucion=$valor['AnnoResolucion'];
				$Numresolucion=$valor['NroResolucion'];
				$Fecharesolucion=$valor['Fecha_Resolucion'];
				$status=$estatus;
				$_SESSION[VALOR_ID]++;
				$id = $_SESSION[VALOR_ID];
			}				
				odbc_close_all();				
			//**************************************************************************************************************************************************
			*/
			//BUSQUEDA EN MYSQL ********************************************************************************************************************************
			if ($encontrado == 0)
			{
				$agregar= "SELECT * FROM vista_ct_salida_df WHERE Anno=".$añoprovidencia." and Numero=".$numprovidencia." and sector=".$sector;
				$result = $conexionsql->query($agregar);
				$cantidad = $result->num_rows;
	
				if ($cantidad > 0)
				{
					$encontrado = 1;
					$valor = $result->fetch_array();
					$nummemo=$_GET['nummemo'];
					$FechaEmision=$_GET['fechamemo'];
					$Anno_memo=date("Y",strtotime($_GET['fechamemo']));
					$Rif=$valor['Rif'];
					$Nombre=$valor['NombreRazon'];
					$FechaNotificacion=$valor['FechaNotificacion'];
					$FechaRecepcion=$Anno_memo;
					if ($estatus==42)
					{
						$Division='ESPECIALES';
					} else {
						$Division='TRAMITACION';
					}
					$Multas=$valor['Multas'];
					$Añoresolucion=$valor['AnnoResolucion'];
					$Numresolucion=$valor['NroResolucion'];
					$Fecharesolucion=$valor['Fecha_Resolucion'];
					$status=$estatus;
					$_SESSION[VALOR_ID]++;
					$id = $_SESSION[VALOR_ID];
				}
			}
			//**************************************************************************************************************************************************

			if ($encontrado > 0)
			{
				$agegarmemo= "INSERT INTO ct_tmp_mod_salida_expediente (id,Anno_memo,NroMemo,sector,FechaEmision,Anno_Providencia,NroAutorizacion,Rif,Nombre,FechaNotificacion,FechaRecepcion,Division,Status,Clausurado,Multa_DF,Anno_Resolucion,NroResolucion,FP,FechaResolucion,modificado) VALUES  (".$id.",".$Anno_memo.",".$nummemo.",".$sector.",'".date("Y-m-d",strtotime($FechaEmision))."',".$añoprovidencia.",".$numprovidencia.",'".$Rif."','".$Nombre."','".date("Y-m-d",strtotime($FechaNotificacion))."','".date("Y-m-d",strtotime($FechaRecepcion))."','".$Division."','".$status."',".$cierre.",".$Multas.",".$Añoresolucion.",".$Numresolucion.",".$fp.",'".date("Y-m-d",strtotime($Fecharesolucion))."',1)";
				$tabla = $conexionsql->query($agegarmemo);

				if ($añoprovidencia!="" and $numprovidencia!="" and $Anno_memo!="" and $nummemo!="")
				{
					$Anno_memo=date("Y",strtotime($_GET['fechamemo']));
					$año= $añoprovidencia;
					$numero= $numprovidencia;
							
					$Ccsql1 = "SELECT count(*) as Total FROM ct_tmp_mod_salida_expediente WHERE Anno_Memo=".$Anno_memo." and NroMemo=".$nummemo." and sector=".$sector." and Status=".$estatus." and borrado=0";
					$rs_access = $conexionsql->query($Ccsql1);
					$valor = $rs_access->num_rows;
					$num = $valor; 
					
					if ($num!=0)
					{ 
						$año= $añoprovidencia;
						$numero= $numprovidencia;
						$Ccsql = "SELECT * FROM ct_tmp_mod_salida_expediente WHERE Anno_Memo=".$Anno_memo." and NroMemo=".$nummemo." and sector=".$sector." and Status=".$estatus." and borrado=0";
						$rs_access = $conexionsql->query($Ccsql);
						$i=1;
						echo '<p></p>';
						echo '<table width="95%" border="0" cellpadding="3" cellspacing="0" align="center">';
						echo '<tr><td bgcolor="#999999"><b>N°';
						echo '</td><td bgcolor="#999999"><b>Año</td>';			
						echo '</td><td bgcolor="#999999"><b>Numero</td>';			
						echo '</td><td bgcolor="#999999"><b>Fecha Notificacion</td>';			
						echo '</td><td bgcolor="#999999"><b>Rif</td>';
						echo '</td><td bgcolor="#999999"><b>Nombre</td>';			
						echo '</td><td bgcolor="#999999"><b>N° Resol.</td>';			
						echo '</td><td bgcolor="#999999"><b>Fecha Resol.</td>';			
						echo '</td><td bgcolor="#999999"><b>Multas</td>';			
						echo '</td><td bgcolor="#999999">&nbsp;</td>';			
						echo '</b>';
						while ($valor = $rs_access->fetch_array())
							{
								if ($color=="#EFEFEF") {
									$color="#D0D6DF";
								} else {
									$color="#EFEFEF";
								}
								echo '<tr bgcolor="'.$color.'"><td>';
								echo $i;
								echo '</td><td>';
								echo $valor['Anno_Providencia'];
								echo '</td><td>';
								echo $valor['NroAutorizacion'];
								echo '</td><td>';
								echo date("d-m-Y",strtotime($valor['FechaNotificacion']));
								echo '</td><td>';
								echo $valor['Rif'];
								echo '</td><td>';
								echo $valor['Nombre'];
								echo '</td><td>';
								echo $valor['Anno_Resolucion']."-".$valor['NroResolucion'];
								echo '</td><td>';
								echo date("d-m-Y",strtotime($valor['FechaResolucion']));
								echo '</td><td align="right">';
								echo number_format($valor['Multa_DF'],2);
								echo '</td><td>';
								if ($admin==1)
								{?>
									<img src="images/delete.png" width="16" height="16" style="cursor:pointer" title="Eliminar" onClick="EliminarExpediente(1,'<?php echo $valor['id']; ?>')"><?php
								} else {
									echo '&nbsp;';
								}
								echo '</td></tr>';
								$_SESSION['scantidad']=$i;
								$_SESSION[VAR_GLOBAL]=$i;
								$i++;
							}
						echo '</table>';
						$_SESSION['statusmemo']=$estatus;
						//guardar_memo(true);
					}
				}
			}
			else
			{
				echo "<script type=\"text/javascript\">
				jAlert('La Providencia NO ha sido notificada ó NO posee sanciones, por favor verifique');
				</script>";
				buscar_registro($estatus,$añoprovidencia,$numprovidencia,$Anno_memo,$nummemo,$sector);
			}
		}	
		else
		{
			//
			echo "<script type=\"text/javascript\">
			jAlert('Los Expedientes de Investigacion Sancionados deben remitirse a la División de Recaudación Area de Liquidación, por favor verifique');
			</script>";
			buscar_registro($estatus,$añoprovidencia,$numprovidencia,$Anno_memo,$nummemo,$sector); 
		}
	}

	function Allanados($destino,$estatus,$añoprovidencia,$numprovidencia,$fp,$sector)
	{
		global $Folio;
		global $conexionsql;
		global $Anno_memo;
		global $cierre;
		global $fp;
		global $admin;
		
		$nummemo=$_GET['nummemo'];
		$Anno_memo=date("Y",strtotime($_GET['fechamemo']));

		if ($destino=="Recaudacion - Liquidacion" or $destino=="Sujetos Pasivos Especiales")
		{
			$encontrado = 0;
			/*
			//BUSQUEDA EN ACCESS *******************************************************************************************************************************
			$_SESSION[1] = odbc_connect ("LLANOS","Administrador","losllanos");
			$_SESSION[2] = odbc_connect ("SJM","Administrador","losllanos");
			$_SESSION[3] = odbc_connect ("SFA","Administrador","losllanos");
			$_SESSION[4] = odbc_connect ("ALT","Administrador","losllanos");
			$_SESSION[5] = odbc_connect ("VLP","Administrador","losllanos");
			
			$agregar= "SELECT count(Anno) as num FROM CS_Salida_Allanados WHERE Anno=".$añoprovidencia." and Numero=".$numprovidencia;
			$rs = odbc_exec ($_SESSION[$_SESSION['SEDE_USUARIO']], $agregar);
			$num=odbc_result($rs,"num");
			$encontrado = 0;
			if ($num > 0)
			{
				$encontrado = 1;
				$agregar= "SELECT * FROM CS_Salida_Allanados WHERE Anno=".$añoprovidencia." and Numero=".$numprovidencia;
				$rs = odbc_exec ($_SESSION[$_SESSION['SEDE_USUARIO']], $agregar);
				$valor = odbc_fetch_array($rs);
				$nummemo=$_GET['nummemo'];
				$FechaEmision=$_GET['fechamemo'];
				$Anno_memo=date("Y",strtotime($_GET['fechamemo']));
				$Rif=$valor['Rif'];
				$Nombre=$valor['NombreRazon'];
				$FechaNotificacion=$valor['FechaNotificacion'];
				$FechaRecepcion=$Anno_memo;
				if ($estatus==42)
				{
					$Division='ESPECIALES';
				} else {
					$Division='TRAMITACION';
				}
				$Reparo=$valor['Reparo'];
				$ImptoOmitido=$valor['ImpuestoOmitido'];
				$Multa_Reparo=$valor['MultaReparo'];
				$Intereses=$valor['Intereses'];
				$MontoPagado=$valor['MontoPagado'];
				$Numresolucion=$valor['NroResolucion'];
				$Fecharesolucion=$valor['Fecha_Resolucion'];
				$AñoResolucion=date("Y",strtotime($valor['Fecha_Resolucion']));
				$status=$estatus;
				$_SESSION[VALOR_ID]++;
				$id = $_SESSION[VALOR_ID];
			}				
				odbc_close_all();				
			//**************************************************************************************************************************************************
			*/
			//BUSQUEDA EN MYSQL ********************************************************************************************************************************
			if ($encontrado == 0)
			{
				$agregar= "SELECT * FROM vista_ct_salida_allanados WHERE Anno=".$añoprovidencia." and Numero=".$numprovidencia." and sector=".$sector;
				$result = $conexionsql->query($agregar);
				$cantidad = $result->num_rows;

				if ($cantidad > 0)
				{
					$encontrado = 1;
					$valor = $result->fetch_array();
					$nummemo=$_GET['nummemo'];
					$FechaEmision=$_GET['fechamemo'];
					$Anno_memo=date("Y",strtotime($_GET['fechamemo']));
					$Rif=$valor['Rif'];
					$Nombre=$valor['NombreRazon'];
					$FechaNotificacion=$valor['FechaNotificacion'];
					$FechaRecepcion=$Anno_memo;
					if ($estatus==42)
					{
						$Division='ESPECIALES';
					} else {
						$Division='TRAMITACION';
					}
					$Reparo=$valor['Reparo'];
					$ImptoOmitido=$valor['ImpuestoOmitido'];
					$Multa_Reparo=$valor['MultaReparo'];
					$Intereses=$valor['Intereses'];
					$MontoPagado=$valor['MontoPagado'];
					$Numresolucion=$valor['NroResolucion'];
					$Fecharesolucion=$valor['Fecha_Resolucion'];
					$AñoResolucion=date("Y",strtotime($valor['Fecha_Resolucion']));
					$status=$estatus;
					$_SESSION[VALOR_ID]++;
					$id = $_SESSION[VALOR_ID];
				}
			}
			//**************************************************************************************************************************************************

			if ($encontrado > 0)
			{
				$agegarmemo= "INSERT INTO ct_tmp_mod_salida_expediente (id,Anno_memo,NroMemo,sector,FechaEmision,Anno_Providencia,NroAutorizacion,Rif,Nombre,FechaNotificacion,FechaRecepcion,Division,Anno_Resolucion,NroResolucion,FechaResolucion,Monto_Reparo,Impto_Omitido,Multa_Reparo,Intereses,Monto_Pagado,FP,Status,modificado) VALUES  (".$id.",'".$Anno_memo."','".$nummemo."',".$sector.",'".date("Y-m-d",strtotime($FechaEmision))."','".$añoprovidencia."','".$numprovidencia."','".$Rif."','".$Nombre."','".date("Y-m-d",strtotime($FechaNotificacion))."','".date("Y-m-d",strtotime($FechaRecepcion))."','".$Division."','".$AñoResolucion."','".$Numresolucion."','".date("Y-m-d",strtotime($Fecharesolucion))."',".$Reparo.",".$ImptoOmitido.",".$valor['MultaReparo'].",".$Intereses.",".$MontoPagado.",".$fp.",'".$status."',1)";
	
				$tabla = $conexionsql->query($agegarmemo);

				if ($añoprovidencia!="" and $numprovidencia!="" and $Anno_memo!="" and $nummemo!="")
				{
					$año= $añoprovidencia;
					$numero= $numprovidencia;
					$Anno_memo=date("Y",strtotime($_GET['fechamemo']));
					
					$Ccsql1 = "SELECT count(*) as Total FROM ct_tmp_mod_salida_expediente WHERE Anno_Memo=".$Anno_memo." and NroMemo=".$nummemo." and sector=".$sector." and Status=".$estatus." and borrado=0";
					$rs_access = $conexionsql->query($Ccsql1);
					$valor = $rs_access->num_rows;
					$num = $valor; 
					//echo $num;
					
					if ($num!=0)
					{ 
						$año= $añoprovidencia;
						$numero= $numprovidencia;
								
						$Ccsql = "SELECT * FROM ct_tmp_mod_salida_expediente WHERE Anno_Memo=".$Anno_memo." and NroMemo=".$nummemo." and sector=".$sector." and Status=".$estatus." and borrado=0";
						$rs_access = $conexionsql->query($Ccsql);
						$i=1;
						echo '<p></p>';
						echo '<table width="95%" border="0" cellpadding="3" cellspacing="0" align="center">';
						echo '<tr><td bgcolor="#999999"><b>N°';
						echo '</td><td bgcolor="#999999"><b>Año</td>';			
						echo '</td><td bgcolor="#999999"><b>Numero</td>';			
						echo '</td><td bgcolor="#999999"><b>Fecha Notificacion</td>';			
						echo '</td><td bgcolor="#999999"><b>Rif</td>';
						echo '</td><td bgcolor="#999999"><b>Nombre</td>';			
						echo '</td><td bgcolor="#999999"><b>N° Resol.</td>';			
						echo '</td><td bgcolor="#999999"><b>Fecha Resol.</td>';			
						echo '</td><td bgcolor="#999999"><b>Reparo</td>';			
						echo '</td><td bgcolor="#999999"><b>Impto Omitido</td>';			
						echo '</td><td bgcolor="#999999"><b>Multas Reparo</td>';			
						echo '</td><td bgcolor="#999999"><b>Intereses</td>';			
						echo '</td><td bgcolor="#999999"><b>Monto Pagado</td>';			
						echo '</td><td bgcolor="#999999">&nbsp;</td>';			
						echo '</b>';
						while ($valor = $rs_access->fetch_array())
							{
								if ($color=="#EFEFEF") {
									$color="#D0D6DF";
								} else {
									$color="#EFEFEF";
								}
								echo '<tr bgcolor="'.$color.'"><td>';
								echo $i;
								echo '</td><td>';
								echo $valor['Anno_Providencia'];
								echo '</td><td>';
								echo $valor['NroAutorizacion'];
								echo '</td><td>';
								echo date("d-m-Y",strtotime($valor['FechaNotificacion']));
								echo '</td><td>';
								echo $valor['Rif'];
								echo '</td><td>';
								echo $valor['Nombre'];
								echo '</td><td>';
								echo $valor['NroResolucion'];
								echo '</td><td>';
								echo date("d-m-Y",strtotime($valor['FechaResolucion']));
								echo '</td><td align="right">';
								echo number_format($valor['Monto_Reparo'],2);
								echo '</td><td align="right">';
								echo number_format($valor['Impto_Omitido'],2);
								echo '</td><td align="right">';
								echo number_format($valor['Multa_Reparo'],2);
								echo '</td><td align="right">';
								echo number_format($valor['Intereses'],2);
								echo '</td><td align="right">';
								echo number_format($valor['Monto_Pagado'],2);
								echo '</td><td>';
								if ($admin==1)
								{?>
									<img src="images/delete.png" width="16" height="16" style="cursor:pointer" title="Eliminar" onClick="EliminarExpediente(1,'<?php echo $valor['id']; ?>')"><?php
								} else {
									echo '&nbsp;';
								}
								echo '</td></tr>';
								$_SESSION['scantidad']=$i;
								$_SESSION[VAR_GLOBAL]=$i;
								$i++;
							}
						echo '</table>';
						$_SESSION['statusmemo']=$estatus;
						//guardar_memo(true);
					}
				}
			}
			else
			{
				echo "<script type=\"text/javascript\">
				jAlert('La Providencia NO ha sido notificada ó NO posee resolución de allanamiento, por favor verifique');
				</script>";
				buscar_registro($estatus,$añoprovidencia,$numprovidencia,$Anno_memo,$nummemo,$sector);
			}
		}
		else
		{
			//
			echo "<script type=\"text/javascript\">
			jAlert('Los Expedientes SUCESIONES e INVESTIGACIONES Allanados deben remitirse a la División de Recaudación Area de Liquidación, por favor verifique');
			</script>"; 
			buscar_registro($estatus,$añoprovidencia,$numprovidencia,$Anno_memo,$nummemo,$sector);
		}
	}

	function AllanadosParcial($destino,$estatus,$añoprovidencia,$numprovidencia,$fp,$sector)
	{
		global $Folio;
		global $conexionsql;
		global $Anno_memo;
		global $cierre;
		global $fp;
		global $admin;
		
		$nummemo=$_GET['nummemo'];
		$Anno_memo=date("Y",strtotime($_GET['fechamemo']));

		if ($destino=="Sumario/Recaudacion - Liquidacion" or $destino=="Sumario/Sujetos Pasivos Especiales")
		{
			$encontrado = 0;
			/*
			//BUSQUEDA EN ACCESS *******************************************************************************************************************************
			$_SESSION[1] = odbc_connect ("LLANOS","Administrador","losllanos");
			$_SESSION[2] = odbc_connect ("SJM","Administrador","losllanos");
			$_SESSION[3] = odbc_connect ("SFA","Administrador","losllanos");
			$_SESSION[4] = odbc_connect ("ALT","Administrador","losllanos");
			$_SESSION[5] = odbc_connect ("VLP","Administrador","losllanos");
			
			$agregar= "SELECT count(Anno) as num FROM CS_Salida_Allanados WHERE Anno=".$añoprovidencia." and Numero=".$numprovidencia;
			$rs = odbc_exec ($_SESSION[$_SESSION['SEDE_USUARIO']], $agregar);
			$num=odbc_result($rs,"num");
			$encontrado = 0;
			if ($num > 0)
			{
				$encontrado = 1;
				$agregar= "SELECT * FROM CS_Salida_Allanados WHERE Anno=".$añoprovidencia." and Numero=".$numprovidencia;
				$rs = odbc_exec ($_SESSION[$_SESSION['SEDE_USUARIO']], $agregar);
				$valor = odbc_fetch_array($rs);
				$nummemo=$_GET['nummemo'];
				$FechaEmision=$_GET['fechamemo'];
				$Anno_memo=date("Y",strtotime($_GET['fechamemo']));
				$Rif=$valor['Rif'];
				$Nombre=$valor['NombreRazon'];
				$FechaNotificacion=$valor['FechaNotificacion'];
				$FechaRecepcion=$Anno_memo;
				$Division='SUMARIO';
				$Reparo=$valor['Reparo'];
				$ImptoOmitido=$valor['ImpuestoOmitido'];
				$Multa_Reparo=$valor['MultaReparo'];
				$Intereses=$valor['Intereses'];
				$MontoPagado=$valor['MontoPagado'];
				$Numresolucion=$valor['NroResolucion'];
				$Fecharesolucion=$valor['Fecha_Resolucion'];
				$AñoResolucion=date("Y",strtotime($valor['Fecha_Resolucion']));
				$status=$estatus;
				$_SESSION[VALOR_ID]++;
				$id = $_SESSION[VALOR_ID];
			}				
				odbc_close_all();				
			//**************************************************************************************************************************************************
			*/
			//BUSQUEDA EN MYSQL ********************************************************************************************************************************
			if ($encontrado == 0)
			{
				$agregar= "SELECT * FROM vista_ct_salida_allanados WHERE Anno=".$añoprovidencia." and Numero=".$numprovidencia." and sector=".$sector;
				$result = $conexionsql->query($agregar);
				$cantidad = $result->num_rows;

				if ($cantidad > 0)
				{
					$encontrado = 1;
					$valor = $result->fetch_array();
					$nummemo=$_GET['nummemo'];
					$FechaEmision=$_GET['fechamemo'];
					$Anno_memo=date("Y",strtotime($_GET['fechamemo']));
					$Rif=$valor['Rif'];
					$Nombre=$valor['NombreRazon'];
					$FechaNotificacion=$valor['FechaNotificacion'];
					$FechaRecepcion=$Anno_memo;
					$Division='SUMARIO';
					$Reparo=$valor['Reparo'];
					$ImptoOmitido=$valor['ImpuestoOmitido'];
					$Multa_Reparo=$valor['MultaReparo'];
					$Intereses=$valor['Intereses'];
					$MontoPagado=$valor['MontoPagado'];
					$Numresolucion=$valor['NroResolucion'];
					$Fecharesolucion=$valor['Fecha_Resolucion'];
					$AñoResolucion=date("Y",strtotime($valor['Fecha_Resolucion']));
					$status=$estatus;
					$_SESSION[VALOR_ID]++;
					$id = $_SESSION[VALOR_ID];
				}
			}
			//**************************************************************************************************************************************************

			if ($encontrado > 0)
			{
				$agegarmemo= "INSERT INTO ct_tmp_mod_salida_expediente (id,Anno_memo,NroMemo,sector,FechaEmision,Anno_Providencia,NroAutorizacion,Rif,Nombre,FechaNotificacion,FechaRecepcion,Division,Anno_Resolucion,NroResolucion,FechaResolucion,Monto_Reparo,Impto_Omitido,Multa_Reparo,Intereses,Monto_Pagado,FP,Status,modificado) VALUES  (".$id.",'".$Anno_memo."','".$nummemo."',".$sector.",'".date("Y-m-d",strtotime($FechaEmision))."','".$añoprovidencia."','".$numprovidencia."','".$Rif."','".$Nombre."','".date("Y-m-d",strtotime($FechaNotificacion))."','".date("Y-m-d",strtotime($FechaRecepcion))."','".$Division."','".$AñoResolucion."','".$Numresolucion."','".date("Y-m-d",strtotime($Fecharesolucion))."',".$Reparo.",".$ImptoOmitido.",".$valor['MultaReparo'].",".$Intereses.",".$MontoPagado.",".$fp.",'".$status."',1)";
				$tabla = $conexionsql->query($agegarmemo);

				if ($añoprovidencia!="" and $numprovidencia!="" and $Anno_memo!="" and $nummemo!="")
				{
					$año= $añoprovidencia;
					$numero= $numprovidencia;
					$Ccsql1 = "SELECT count(*) as Total FROM ct_tmp_mod_salida_expediente WHERE Anno_Memo=".$Anno_memo." and NroMemo=".$nummemo." and sector=".$sector." and Status=".$estatus." and borrado=0";
					$rs_access = $conexionsql->query($Ccsql1);
					$valor = $rs_access->num_rows;
					$num = $valor; 
					//echo $num;
					
					if ($num!=0)
					{ 
						$año= $añoprovidencia;
						$numero= $numprovidencia;
						$Anno_memo=date("Y",strtotime($_GET['fechamemo']));
								
						$Ccsql = "SELECT * FROM ct_tmp_mod_salida_expediente WHERE Anno_Memo=".$Anno_memo." and NroMemo=".$nummemo." and sector=".$sector." and Status=".$estatus." and borrado=0";
						$rs_access = $conexionsql->query($Ccsql);
						$i=1;
						echo '<p></p>';
						echo '<table width="95%" border="0" cellpadding="3" cellspacing="0" align="center">';
						echo '<tr><td bgcolor="#999999"><b>N°';
						echo '</td><td bgcolor="#999999"><b>Año</td>';			
						echo '</td><td bgcolor="#999999"><b>Numero</td>';			
						echo '</td><td bgcolor="#999999"><b>Fecha Notificacion</td>';			
						echo '</td><td bgcolor="#999999"><b>Rif</td>';
						echo '</td><td bgcolor="#999999"><b>Nombre</td>';			
						echo '</td><td bgcolor="#999999"><b>N° Resol.</td>';			
						echo '</td><td bgcolor="#999999"><b>Fecha Resol.</td>';			
						echo '</td><td bgcolor="#999999"><b>Reparo</td>';			
						echo '</td><td bgcolor="#999999"><b>Impto Omitido</td>';			
						echo '</td><td bgcolor="#999999"><b>Multas Reparo</td>';			
						echo '</td><td bgcolor="#999999"><b>Intereses</td>';			
						echo '</td><td bgcolor="#999999"><b>Monto Pagado</td>';			
						echo '</td><td bgcolor="#999999">&nbsp;</td>';			
						echo '</b>';
						while ($valor = $rs_access->fetch_array())
							{
								if ($color=="#EFEFEF") {
									$color="#D0D6DF";
								} else {
									$color="#EFEFEF";
								}
								echo '<tr bgcolor="'.$color.'"><td>';
								echo $i;
								echo '</td><td>';
								echo $valor['Anno_Providencia'];
								echo '</td><td>';
								echo $valor['NroAutorizacion'];
								echo '</td><td>';
								echo date("d-m-Y",strtotime($valor['FechaNotificacion']));
								echo '</td><td>';
								echo $valor['Rif'];
								echo '</td><td>';
								echo $valor['Nombre'];
								echo '</td><td>';
								echo $valor['NroResolucion'];
								echo '</td><td>';
								echo date("d-m-Y",strtotime($valor['FechaResolucion']));
								echo '</td><td align="right">';
								echo number_format($valor['Monto_Reparo'],2);
								echo '</td><td align="right">';
								echo number_format($valor['Impto_Omitido'],2);
								echo '</td><td align="right">';
								echo number_format($valor['Multa_Reparo'],2);
								echo '</td><td align="right">';
								echo number_format($valor['Intereses'],2);
								echo '</td><td align="right">';
								echo number_format($valor['Monto_Pagado'],2);
								echo '</td><td>';
								if ($admin==1)
								{?>
									<img src="images/delete.png" width="16" height="16" style="cursor:pointer" title="Eliminar" onClick="EliminarExpediente(1,'<?php echo $valor['id']; ?>')"><?php
								} else {
									echo '&nbsp;';
								}
								echo '</td></tr>';
								$_SESSION['scantidad']=$i;
								$_SESSION[VAR_GLOBAL]=$i;
								$i++;
							}
						echo '</table>';
						$_SESSION['statusmemo']=$estatus;
						//guardar_memo(true);
					}
				}
			}
			else
			{
				echo "<script type=\"text/javascript\">
				jAlert('La Providencia NO ha sido notificada ó NO posee resolución de allanamiento, por favor verifique');
				</script>";
				buscar_registro($estatus,$añoprovidencia,$numprovidencia,$Anno_memo,$nummemo,$sector);
			}
		}
		else
		{
			//
			echo "<script type=\"text/javascript\">
			jAlert('Los Expedientes SUCESIONES e INVESTIGACIONES Allanados Parcialmente deben remitirse a la División Sumario y Recaudación Area de Liquidación, por favor verifique');
			</script>";
			buscar_registro($estatus,$añoprovidencia,$numprovidencia,$Anno_memo,$nummemo,$sector); 
		}
	}
	
	function NoAllanados($destino,$estatus,$añoprovidencia,$numprovidencia,$fp,$sector)
	{
		global $Folio;
		global $conexionsql;
		global $Anno_memo;
		global $cierre;
		global $fp;
		global $admin;
		
		$nummemo=$_GET['nummemo'];
		$Anno_memo=date("Y",strtotime($_GET['fechamemo']));

		if ($destino=="Sumario")
		{
			$encontrado = 0;
			/*
			//BUSQUEDA EN ACCESS *******************************************************************************************************************************
			$_SESSION[1] = odbc_connect ("LLANOS","Administrador","losllanos");
			$_SESSION[2] = odbc_connect ("SJM","Administrador","losllanos");
			$_SESSION[3] = odbc_connect ("SFA","Administrador","losllanos");
			$_SESSION[4] = odbc_connect ("ALT","Administrador","losllanos");
			$_SESSION[5] = odbc_connect ("VLP","Administrador","losllanos");
			
			$agregar= "SELECT count(Anno) as num FROM CS_Salida_Sumario WHERE Anno=".$añoprovidencia." and Numero=".$numprovidencia;
			$rs = odbc_exec ($_SESSION[$_SESSION['SEDE_USUARIO']], $agregar);
			$num=odbc_result($rs,"num");
			$encontrado = 0;
			if ($num > 0)
			{
				$encontrado = 1;
				$agregar= "SELECT * FROM CS_Salida_Sumario WHERE Anno=".$añoprovidencia." and Numero=".$numprovidencia;
				$rs = odbc_exec ($_SESSION[$_SESSION['SEDE_USUARIO']], $agregar);
				$valor = odbc_fetch_array($rs);
				$nummemo=$_GET['nummemo'];
				$FechaEmision=$_GET['fechamemo'];
				$Anno_memo=date("Y",strtotime($_GET['fechamemo']));
				$Rif=$valor['Rif'];
				$Nombre=$valor['NombreRazon'];
				$FechaNotificacion=$valor['PNotificacion'];
				$FechaRecepcion=$Anno_memo;
				$Division='SUMARIO';
				$Reparo=$valor['Reparo'];
				$ImptoOmitido=$valor['ImpuestoOmitido'];
				$NumActa=$valor['NumActa'];
				$FechaActa=$valor['FechaActa'];
				$ARNotificacion=$valor['ARNotificacion'];
				$status=$estatus;
				$_SESSION[VALOR_ID]++;
				$id = $_SESSION[VALOR_ID];
			}				
				odbc_close_all();				
			//**************************************************************************************************************************************************
			*/
			//BUSQUEDA EN MYSQL ********************************************************************************************************************************
			if ($encontrado == 0)
			{
				$agregar= "SELECT * FROM vista_ct_salida_sumario WHERE Anno=".$añoprovidencia." and Numero=".$numprovidencia." and sector=".$sector;
				$result = $conexionsql->query($agregar);
				$cantidad = $result->num_rows;
	
				if ($cantidad > 0)
				{
					$encontrado = 1;
					$valor = $result->fetch_array();
					$nummemo=$_GET['nummemo'];
					$FechaEmision=$_GET['fechamemo'];
					$Anno_memo=date("Y",strtotime($_GET['fechamemo']));
					$Rif=$valor['Rif'];
					$Nombre=$valor['NombreRazon'];
					$FechaNotificacion=$valor['PNotificacion'];
					$FechaRecepcion=$Anno_memo;
					$Division='SUMARIO';
					$Reparo=$valor['Reparo'];
					$ImptoOmitido=$valor['ImpuestoOmitido'];
					$NumActa=$valor['NumActa'];
					$FechaActa=$valor['FechaActa'];
					$ARNotificacion=$valor['ARNotificacion'];
					$status=$estatus;
					$_SESSION[VALOR_ID]++;
					$id = $_SESSION[VALOR_ID];
				}
			}
			//**************************************************************************************************************************************************
			
			if ($encontrado > 0)
			{
				$agegarmemo= "INSERT INTO ct_tmp_mod_salida_expediente (id,Anno_memo,NroMemo,sector,FechaEmision,Anno_Providencia,NroAutorizacion,Rif,Nombre,FechaNotificacion,FechaRecepcion,Division,Monto_Reparo,Impto_Omitido,NumActa,FechaActa,FechaNotificacionActa,FP,Status,modificado) VALUES  (".$id.",".$Anno_memo.",".$nummemo.",".$sector.",'".date("Y-m-d",strtotime($FechaEmision))."',".$añoprovidencia.",".$numprovidencia.",'".$Rif."','".$Nombre."','".date("Y-m-d",strtotime($FechaNotificacion))."','".date("Y-m-d",strtotime($FechaRecepcion))."','".$Division."',".$Reparo.",".$ImptoOmitido.",".$NumActa.",'".date("Y-m-d",strtotime($FechaActa))."','".date("Y-m-d",strtotime($ARNotificacion))."',".$fp.",'".$status."',1)";
				$tabla = $conexionsql->query($agegarmemo);

				if ($añoprovidencia!="" and $numprovidencia!="" and $Anno_memo!="" and $nummemo!="")
				{
					$año= $añoprovidencia;
					$numero= $numprovidencia;
					$Anno_memo=date("Y",strtotime($_GET['fechamemo']));
					
					$Ccsql1 = "SELECT count(*) as Total FROM ct_tmp_mod_salida_expediente WHERE Anno_Memo=".$Anno_memo." and NroMemo=".$nummemo." and sector=".$sector." and Status=".$estatus." and borrado=0";
					$rs_access = $conexionsql->query($Ccsql1);
					$valor = $rs_access->num_rows;
					$num = $valor; 
					//echo $num;
					
					if ($num!=0)
					{ 
						$año= $añoprovidencia;
						$numero= $numprovidencia;
								
						$Ccsql = "SELECT * FROM ct_tmp_mod_salida_expediente WHERE Anno_Memo=".$Anno_memo." and NroMemo=".$nummemo." and sector=".$sector." and Status=".$estatus." and borrado=0";
						$rs_access = $conexionsql->query($Ccsql);
						$i=1;
						echo '<p></p>';
						echo '<table width="95%" border="0" cellpadding="3" cellspacing="0" align="center">';
						echo '<tr><td bgcolor="#999999"><b>N°';
						echo '</td><td bgcolor="#999999"><b>Año</td>';			
						echo '</td><td bgcolor="#999999"><b>Numero</td>';			
						echo '</td><td bgcolor="#999999"><b>Fecha Notificacion</td>';			
						echo '</td><td bgcolor="#999999"><b>Rif</td>';
						echo '</td><td bgcolor="#999999"><b>Nombre</td>';			
						echo '</td><td bgcolor="#999999"><b>N° Acta.</td>';			
						echo '</td><td bgcolor="#999999"><b>Fecha Acta.</td>';			
						echo '</td><td bgcolor="#999999"><b>Reparo</td>';			
						echo '</td><td bgcolor="#999999"><b>Impto Omitido</td>';			
						echo '</td><td bgcolor="#999999"><b>Fecha Notif. Acta</td>';			
						echo '</td><td bgcolor="#999999">&nbsp;</td>';			
						echo '</b>';
						while ($valor = $rs_access->fetch_array())
							{
								if ($color=="#EFEFEF") {
									$color="#D0D6DF";
								} else {
									$color="#EFEFEF";
								}
								echo '<tr bgcolor="'.$color.'"><td>';
								echo $i;
								echo '</td><td>';
								echo $valor['Anno_Providencia'];
								echo '</td><td>';
								echo $valor['NroAutorizacion'];
								echo '</td><td>';
								echo date("d-m-Y",strtotime($valor['FechaNotificacion']));
								echo '</td><td>';
								echo $valor['Rif'];
								echo '</td><td>';
								echo $valor['Nombre'];
								echo '</td><td>';
								echo $valor['NumActa'];
								echo '</td><td>';
								echo date("d-m-Y",strtotime($valor['FechaActa']));
								echo '</td><td align="right">';
								echo number_format($valor['Monto_Reparo'],2);
								echo '</td><td align="right">';
								echo number_format($valor['Impto_Omitido'],2);
								echo '</td><td>';
								echo date("d-m-Y",strtotime($valor['FechaNotificacionActa']));
								echo '</td><td>';
								if ($admin==1)
								{?>
									<img src="images/delete.png" width="16" height="16" style="cursor:pointer" title="Eliminar" onClick="EliminarExpediente(1,'<?php echo $valor['id']; ?>')"><?php
								} else {
									echo '&nbsp;';
								}
								echo '</td></tr>';
								$_SESSION['scantidad']=$i;
								$_SESSION[VAR_GLOBAL]=$i;
								$i++;
							}
						echo '</table>';
						$_SESSION['statusmemo']=$estatus;
						//guardar_memo(true);
					}
				}
			}
			else
			{
				echo "<script type=\"text/javascript\">
				jAlert('La Providencia NO ha sido notificada ó NO posee Acta de Reparo, por favor verifique');
				</script>";
				buscar_registro($estatus,$añoprovidencia,$numprovidencia,$Anno_memo,$nummemo,$sector);
			}
		}
		else
		{
			//
			echo "<script type=\"text/javascript\">
			jAlert('Los Expedientes SUCESIONES e INVESTIGACIONES No Allanados deben remitirse a la División de Sumario, por favor verifique');
			</script>";
			buscar_registro($estatus,$añoprovidencia,$numprovidencia,$Anno_memo,$nummemo,$sector); 
		}
	}

//******************************
function buscar_registro($estatus,$añoprovidencia,$numprovidencia,$Anno_memo,$nummemo,$sector)
{
	global $conexionsql;
	global $admin;
	//echo "Valores Recibidos: (".$estatus.",".$añoprovidencia.",".$numprovidencia.",".$Anno_memo.",".$nummemo.")";
	//BUSCAR REGISTROS EN EL TEMPORAL
	
///*************************P O R  A Q U ííííííííííííííí******************************************************************************************
	
	//SI NO EXISTE EN EL TEMPORAL BUSCAR EL TABLA
	if ($añoprovidencia!="" and $numprovidencia!="" and $Anno_memo>0 and $nummemo>0)
	{
		$año= $añoprovidencia;
		$numero= $numprovidencia;
		$Ccsql1 = "SELECT count(*) as Total FROM ct_tmp_mod_salida_expediente WHERE Anno_Memo=".$Anno_memo." and NroMemo=".$nummemo." and sector=".$sector." and Status=".$estatus." and borrado=0";
		//echo $Ccsql1;
		$rs_access = $conexionsql->query($Ccsql1);
		$valor = $rs_access->num_rows;
		$num = $valor; 
		//echo $num;
		
		if ($num!=0)
		{ 
			$año= $añoprovidencia;
			$numero= $numprovidencia;
			//echo $año.",".$numero.",".$añoprovidencia.",".$numprovidencia;
			//$conexion = odbc_connect ("LLANOS","Administrador","losllanos");
					
			$Ccsql = "SELECT * FROM ct_tmp_mod_salida_expediente WHERE Anno_Memo=".$Anno_memo." and NroMemo=".$nummemo." and sector=".$sector." and Status=".$estatus." and borrado=0";
			//echo $Ccsql;
			$rs_access = $conexionsql->query($Ccsql);
			$i=1;
			$valor = $rs_access->fetch_array();
			//echo "...Este es el Estatus=: ".$valor['Status'];

			if (substr($valor['Status'], -1)==1)
			{
				$resultado="Conformes";
			}
			if (substr($valor['Status'], -1)==2)
			{
				$resultado="Sancionados";
			}
			if (substr($valor['Status'], -1)==3)
			{
				$resultado="Sancionados Allanados";
			}
			if (substr($valor['Status'], -1)==5)
			{
				$resultado="Sancionados No Allanados";
			}
			if (substr($valor['Status'], -1)==4)
			{
				$resultado="Allanados Parcialmente";
			}
			//echo $resultado;
			$Ccsql = "SELECT * FROM ct_tmp_mod_salida_expediente WHERE Anno_Memo=".$Anno_memo." and NroMemo=".$nummemo." and sector=".$sector." and Status=".$estatus." and borrado=0";
			$rs_access = $conexionsql->query($Ccsql);
			$i=1;

			if ($resultado=="Conformes")
			{
				$año= $añoprovidencia;
				$numero= $numprovidencia;
				//echo $año.",".$numero.",".$añoprovidencia.",".$numprovidencia;
				//$conexion = odbc_connect ("LLANOS","Administrador","losllanos");
				echo '<br />';
				echo '<table width="95%" border="0" cellpadding="3" cellspacing="0" align="center">';
				echo '<tr><td bgcolor="#999999"><b>N°';
				echo '</td><td bgcolor="#999999"><b>Año</td>';			
				echo '</td><td bgcolor="#999999"><b>Numero</td>';			
				echo '</td><td bgcolor="#999999"><b>Fecha Notificacion</td>';			
				echo '</td><td bgcolor="#999999"><b>Rif</td>';
				echo '</td><td bgcolor="#999999"><b>Nombre</td>';
				echo '</td><td bgcolor="#999999">&nbsp;</td>';			
				echo '</b>';
				while ($valor = $rs_access->fetch_array())
					{
						//echo "...Este es el Estatus=: ".$valor['Status']; 
						if ($color=="#EFEFEF") {
							$color="#D0D6DF";
						} else {
							$color="#EFEFEF";
						}
						echo '<tr bgcolor="'.$color.'"><td>';
						echo $i;
						echo '</td><td>';
						echo $valor['Anno_Providencia'];
						echo '</td><td>';
						echo $valor['NroAutorizacion'];
						echo '</td><td>';
						echo date("d-m-Y",strtotime($valor['FechaNotificacion']));
						echo '</td><td>';
						echo $valor['Rif'];
						echo '</td><td>';
						echo $valor['Nombre'];
						echo '</td><td>';
						if ($admin==1)
						{?>
							<img src="images/delete.png" width="16" height="16" style="cursor:pointer" title="Eliminar" onClick="EliminarExpediente(1,'<?php echo $valor['id']; ?>')"><?php
						} else {
							echo '&nbsp;';
						}
						echo '</td></tr>';
						$_SESSION['scantidad']=$i;
						$_SESSION[VAR_GLOBAL]=$i;
						$i++;
					}
				echo '</table>';
				$_SESSION['statusmemo']=$estatus;
			}

			if ($resultado=="Sancionados")
			{

				echo '<p></p>';
				echo '<table width="95%" border="0" cellpadding="3" cellspacing="0" align="center">';
				echo '<tr><td bgcolor="#999999"><b>N°';
				echo '</td><td bgcolor="#999999"><b>Año</td>';			
				echo '</td><td bgcolor="#999999"><b>Numero</td>';			
				echo '</td><td bgcolor="#999999"><b>Fecha Notificacion</td>';			
				echo '</td><td bgcolor="#999999"><b>Rif</td>';
				echo '</td><td bgcolor="#999999"><b>Nombre</td>';			
				echo '</td><td bgcolor="#999999"><b>N° Resol.</td>';			
				echo '</td><td bgcolor="#999999"><b>Fecha Resol.</td>';			
				echo '</td><td bgcolor="#999999"><b>Multas</td>';			
				echo '</td><td bgcolor="#999999">&nbsp;</td>';			
				echo '</b>';
				while ($valor = $rs_access->fetch_array())
					{
						if ($color=="#EFEFEF") {
							$color="#D0D6DF";
						} else {
							$color="#EFEFEF";
						}
						echo '<tr bgcolor="'.$color.'"><td>';
						echo $i;
						echo '</td><td>';
						echo $valor['Anno_Providencia'];
						echo '</td><td>';
						echo $valor['NroAutorizacion'];
						echo '</td><td>';
						echo date("d-m-Y",strtotime($valor['FechaNotificacion']));
						echo '</td><td>';
						echo $valor['Rif'];
						echo '</td><td>';
						echo $valor['Nombre'];
						echo '</td><td>';
						echo $valor['Anno_Resolucion']."-".$valor['NroResolucion'];
						echo '</td><td>';
						echo date("d-m-Y",strtotime($valor['FechaResolucion']));
						echo '</td><td align="right">';
						echo number_format($valor['Multa_DF'],2);
						echo '</td><td>';						if ($admin==1)
						{?>
							<img src="images/delete.png" width="16" height="16" style="cursor:pointer" title="Eliminar" onClick="EliminarExpediente(1,'<?php echo $valor['id']; ?>')"><?php
						} else {
							echo '&nbsp;';
						}
						echo '</td></tr>';
						$_SESSION['scantidad']=$i;
						$_SESSION[VAR_GLOBAL]=$i;
						$i++;
					}
			}
			if ($resultado=="Sancionados Allanados")
			{
				echo '<p></p>';
				echo '<table width="95%" border="0" cellpadding="3" cellspacing="0" align="center">';
				echo '<tr><td bgcolor="#999999"><b>N°';
				echo '</td><td bgcolor="#999999"><b>Año</td>';			
				echo '</td><td bgcolor="#999999"><b>Numero</td>';			
				echo '</td><td bgcolor="#999999"><b>Fecha Notificacion</td>';			
				echo '</td><td bgcolor="#999999"><b>Rif</td>';
				echo '</td><td bgcolor="#999999"><b>Nombre</td>';			
				echo '</td><td bgcolor="#999999"><b>N° Resol.</td>';			
				echo '</td><td bgcolor="#999999"><b>Fecha Resol.</td>';			
				echo '</td><td bgcolor="#999999"><b>Reparo</td>';			
				echo '</td><td bgcolor="#999999"><b>Impto Omitido</td>';			
				echo '</td><td bgcolor="#999999"><b>Multas Reparo</td>';			
				echo '</td><td bgcolor="#999999"><b>Intereses</td>';			
				echo '</td><td bgcolor="#999999"><b>Monto Pagado</td>';			
				echo '</td><td bgcolor="#999999">&nbsp;</td>';			
				echo '</b>';
				while ($valor = $rs_access->fetch_array())
					{
						if ($color=="#EFEFEF") {
							$color="#D0D6DF";
						} else {
							$color="#EFEFEF";
						}
						echo '<tr bgcolor="'.$color.'"><td>';
						echo $i;
						echo '</td><td>';
						echo $valor['Anno_Providencia'];
						echo '</td><td>';
						echo $valor['NroAutorizacion'];
						echo '</td><td>';
						echo date("d-m-Y",strtotime($valor['FechaNotificacion']));
						echo '</td><td>';
						echo $valor['Rif'];
						echo '</td><td>';
						echo $valor['Nombre'];
						echo '</td><td>';
						echo $valor['NroResolucion'];
						echo '</td><td>';
						echo date("d-m-Y",strtotime($valor['FechaResolucion']));
						echo '</td><td align="right">';
						echo number_format($valor['Monto_Reparo'],2);
						echo '</td><td align="right">';
						echo number_format($valor['Impto_Omitido'],2);
						echo '</td><td align="right">';
						echo number_format($valor['Multa_Reparo'],2);
						echo '</td><td align="right">';
						echo number_format($valor['Intereses'],2);
						echo '</td><td align="right">';
						echo number_format($valor['Monto_Pagado'],2);
						echo '</td><td>';						if ($admin==1)
						{?>
							<img src="images/delete.png" width="16" height="16" style="cursor:pointer" title="Eliminar" onClick="EliminarExpediente(1,'<?php echo $valor['id']; ?>')"><?php
						} else {
							echo '&nbsp;';
						}
						echo '</td></tr>';
						$_SESSION['scantidad']=$i;
						$_SESSION[VAR_GLOBAL]=$i;
						$i++;
					}
			}

			if ($resultado=="Allanados Parcialmente")
			{
				echo '<p></p>';
				echo '<table width="95%" border="0" cellpadding="3" cellspacing="0" align="center">';
				echo '<tr><td bgcolor="#999999"><b>N°';
				echo '</td><td bgcolor="#999999"><b>Año</td>';			
				echo '</td><td bgcolor="#999999"><b>Numero</td>';			
				echo '</td><td bgcolor="#999999"><b>Fecha Notificacion</td>';			
				echo '</td><td bgcolor="#999999"><b>Rif</td>';
				echo '</td><td bgcolor="#999999"><b>Nombre</td>';			
				echo '</td><td bgcolor="#999999"><b>N° Resol.</td>';			
				echo '</td><td bgcolor="#999999"><b>Fecha Resol.</td>';			
				echo '</td><td bgcolor="#999999"><b>Reparo</td>';			
				echo '</td><td bgcolor="#999999"><b>Impto Omitido</td>';			
				echo '</td><td bgcolor="#999999"><b>Multas Reparo</td>';			
				echo '</td><td bgcolor="#999999"><b>Intereses</td>';			
				echo '</td><td bgcolor="#999999"><b>Monto Pagado</td>';			
				echo '</td><td bgcolor="#999999">&nbsp;</td>';			
				echo '</b>';
				while ($valor = $rs_access->fetch_array())
					{
						if ($color=="#EFEFEF") {
							$color="#D0D6DF";
						} else {
							$color="#EFEFEF";
						}
						echo '<tr bgcolor="'.$color.'"><td>';
						echo $i;
						echo '</td><td>';
						echo $valor['Anno_Providencia'];
						echo '</td><td>';
						echo $valor['NroAutorizacion'];
						echo '</td><td>';
						echo date("d-m-Y",strtotime($valor['FechaNotificacion']));
						echo '</td><td>';
						echo $valor['Rif'];
						echo '</td><td>';
						echo $valor['Nombre'];
						echo '</td><td>';
						echo $valor['NroResolucion'];
						echo '</td><td>';
						echo date("d-m-Y",strtotime($valor['FechaResolucion']));
						echo '</td><td align="right">';
						echo number_format($valor['Monto_Reparo'],2);
						echo '</td><td align="right">';
						echo number_format($valor['Impto_Omitido'],2);
						echo '</td><td align="right">';
						echo number_format($valor['Multa_Reparo'],2);
						echo '</td><td align="right">';
						echo number_format($valor['Intereses'],2);
						echo '</td><td align="right">';
						echo number_format($valor['Monto_Pagado'],2);
						echo '</td><td>';						if ($admin==1)
						{?>
							<img src="images/delete.png" width="16" height="16" style="cursor:pointer" title="Eliminar" onClick="EliminarExpediente(1,'<?php echo $valor['id']; ?>')"><?php
						} else {
							echo '&nbsp;';
						}
						echo '</td></tr>';
						$_SESSION['scantidad']=$i;
						$_SESSION[VAR_GLOBAL]=$i;
						$i++;
					}
			}
			
			if ($resultado=="Sancionados No Allanados")
			{
				echo '<p></p>';
				echo '<table width="95%" border="0" cellpadding="3" cellspacing="0" align="center">';
				echo '<tr><td bgcolor="#999999"><b>N°';
				echo '</td><td bgcolor="#999999"><b>Año</td>';			
				echo '</td><td bgcolor="#999999"><b>Numero</td>';			
				echo '</td><td bgcolor="#999999"><b>Fecha Notificacion</td>';			
				echo '</td><td bgcolor="#999999"><b>Rif</td>';
				echo '</td><td bgcolor="#999999"><b>Nombre</td>';			
				echo '</td><td bgcolor="#999999"><b>N° Acta.</td>';			
				echo '</td><td bgcolor="#999999"><b>Fecha Acta.</td>';			
				echo '</td><td bgcolor="#999999"><b>Reparo</td>';			
				echo '</td><td bgcolor="#999999"><b>Impto Omitido</td>';			
				echo '</td><td bgcolor="#999999"><b>Fecha Notif. Acta</td>';			
				echo '</td><td bgcolor="#999999">&nbsp;</td>';			
				echo '</b>';
				while ($valor = $rs_access->fetch_array())
					{
						if ($color=="#EFEFEF") {
							$color="#D0D6DF";
						} else {
							$color="#EFEFEF";
						}
						echo '<tr bgcolor="'.$color.'"><td>';
						echo $i;
						echo '</td><td>';
						echo $valor['Anno_Providencia'];
						echo '</td><td>';
						echo $valor['NroAutorizacion'];
						echo '</td><td>';
						echo date("d-m-Y",strtotime($valor['FechaNotificacion']));
						echo '</td><td>';
						echo $valor['Rif'];
						echo '</td><td>';
						echo $valor['Nombre'];
						echo '</td><td>';
						echo $valor['NumActa'];
						echo '</td><td>';
						echo date("d-m-Y",strtotime($valor['FechaActa']));
						echo '</td><td align="right">';
						echo number_format($valor['Monto_Reparo'],2);
						echo '</td><td align="right">';
						echo number_format($valor['Impto_Omitido'],2);
						echo '</td><td>';
						echo date("d-m-Y",strtotime($valor['FechaNotificacionActa']));
						echo '</td><td>';						if ($admin==1)
						{?>
							<img src="images/delete.png" width="16" height="16" style="cursor:pointer" title="Eliminar" onClick="EliminarExpediente(1,'<?php echo $valor['id']; ?>')"><?php
						} else {
							echo '&nbsp;';
						}
						echo '</td></tr>';
						$_SESSION['scantidad']=$i;
						$_SESSION[VAR_GLOBAL]=$i;
						$i++;
					}
			}

			echo '</table>';
		}
	}
	$_SESSION['statusmemo']=$estatus;	
}

///////////FIN FUNCIONES////////////////////////////////////

//PARA SABER SI EXISTEN REGISTROS
$sql_bloquear= "SELECT * FROM ct_tmp_mod_salida_expediente WHERE Anno_Memo=".$Anno_memo." and NroMemo=".$nummemo." and sector=".$sector." AND borrado=0";
$result_bloqueador = $conexionsql->query($sql_bloquear);
$bloquear = $result_bloqueador->num_rows;
if ($bloquear > 0)
{
	?><script type="text/javascript">
		$('#bloquedor').val(1);
	</script><?php
} else {
	?><script type="text/javascript">
		$('#bloquedor').val(0);
	</script><?php
}

?>