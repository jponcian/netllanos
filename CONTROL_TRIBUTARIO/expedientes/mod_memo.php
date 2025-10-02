<script type="text/javascript" src="funciones/funciones_modificacion_exp.js"></script>


<?php
	include "../conexion.php";
//include "../auxiliar.php";

?>

				<div id="formModsalida">
					<form class="contact_form" action="" id="modsalidaExpediente" > 
						<div> 
							<ul> 
								<li> 
									<h2>Modificar Salida de Expedientes Division de Fiscalización</h2> 
									<span class="required_notification">* Datos requeridos</span> 
								</li> 
								<li> 
									<label for="memo">N° de Memorando:</label> 
									<?php
										$numeromemo = $_GET['num'];	
										$año = $_GET['anno'];
										$sector = $_GET['sector'];
										$hoy = date("Y/m/d");
										$admin = $_GET['admin'];

										$borrar = "DELETE FROM ct_tmp_mod_salida_expediente WHERE sector=".$sector;
										$procesar = $conexionsql->query($borrar);
									
										//CARGARMOS EL TEMPORAL DE MODIFICACION********************************************************
										$insert = "INSERT INTO ct_tmp_mod_salida_expediente (id, Anno_memo, NroMemo, sector, FechaEmision, Anno_Providencia, NroAutorizacion, Rif, Nombre, 
										FechaNotificacion, FechaRecepcion, Division, Anno_Resolucion, NroResolucion, FechaResolucion, Monto_Reparo, Impto_Omitido, 
										Multa_Reparo, Intereses, Multa_DF, Monto_Pagado, NumActa, FechaActa, FechaNotificacionActa, Status, Clausurado, FP, Tipo, ESPECIAL, Contenido, 
										Folio) SELECT id, Anno_memo, NroMemo, sector, FechaEmision, Anno_Providencia, NroAutorizacion, Rif, Nombre, FechaNotificacion, FechaRecepcion, 
										Division, Anno_Resolucion, NroResolucion, FechaResolucion, Monto_Reparo, Impto_Omitido, Multa_Reparo, Intereses, Multa_DF, Monto_Pagado, 
										NumActa, FechaActa, FechaNotificacionActa, Status, Clausurado, FP, Tipo, ESPECIAL, Contenido, Folio FROM ct_salida_Expediente 
										WHERE Anno_memo=".$año." AND NroMemo=".$numeromemo." AND sector=".$sector;
										//echo $insert.'<br/>';
										$guardar = $conexionsql->query($insert);
										//*********************************************************************************************

										$registros = "SELECT * FROM ct_tmp_mod_salida_expediente WHERE Anno_memo=$año AND NroMemo=$numeromemo AND sector=$sector AND borrado=0";

										$resultregistros = $conexionsql->query($registros);
										$valor = $resultregistros->fetch_object();
										if ($valor->FP == 0)
										{
											$chk = "";
										} else {
											$chk = "checked";
										}
										if ($valor->Clausurado == 0)
										{
											$cierre = "No";
										} else {
											$cierre = "Sí";
										}
									?> 
									<input type="text" id="modmemo" readonly value="<?php echo $numeromemo ?>" />
								</li> 
								<li> 
									<label for="modfechamemo">Fecha Memorando:</label> 
									<input type="text" id="modfechamemo" readonly value="<?php echo date("Y/m/d", strtotime($valor->FechaEmision)) ?>" /> 
									<!--<span class="form_hint">Formato correcto: "2015/01/01"</span> -->
								</li> 
								<li> 
									<label for="modtipoexp">Tipo de Expediente:</label> 
									  <?php
											$status = $valor->Status;
									  		switch ($valor->Status) {
									  			case 11:
									  				$tipoexp = "VDF";
									  				$resultado = "Conformes";
									  				$destino = "Tramitacion";
									  				break;
									  			case 21:
									  				$tipoexp = "Sucesiones";
									  				$destino = "Recaudacion - Sucesiones";
									  				$resultado = "Conformes";
									  				break;
									  			case 31:
									  				$tipoexp = "Investigaciones";
									  				$destino = "Tramitacion";
									  				$resultado = "Conformes";
									  				break;
									  			case 12:
									  				$tipoexp = "VDF";
									  				$destino = "Recaudacion - Liquidacion";
									  				$resultado = "Sancionados"; 
									  				break;
									  			case 22:
									  				$tipoexp = "Sucesiones";
									  				$destino = "Recaudacion - Liquidacion";
									  				$resultado = "Sancionados"; 
									  				break;
									  			case 32:
									  				$tipoexp = "Investigaciones";
									  				$destino = "Recaudacion - Liquidacion";
									  				$resultado = "Sancionados"; 
									  				break;
									  			case 23:
									  				$tipoexp = "Sucesiones";
									  				$resultado = "Allanados";
									  				$destino = "Recaudacion - Liquidacion";
									  				break;
									  			case 33:
									  				$tipoexp = "Investigaciones";
									  				$resultado = "Allanados";
									  				$destino = "Recaudacion - Liquidacion";
									  				break;
									  			case 25:
									  				$tipoexp = "Sucesiones";
									  				$resultado = "No Allanados";
									  				$destino = "Sumario";
									  				break;
									  			case 35:
									  				$tipoexp = "Investigaciones";
									  				$resultado = "No Allanados";
									  				$destino = "Sumario";
									  				break;
									  			case 24:
									  				$tipoexp = "Sucesiones";
									  				$resultado = "Allanados Parcialmente";
									  				$destino = "Sumario/Recaudacion - Liquidacion";
									  				break;
									  			case 34:
									  				$tipoexp = "Investigaciones";
									  				$resultado = "Allanados Parcialmente";
									  				$destino = "Sumario/Recaudacion - Liquidacion";
									  				break;
									  			case 42:
									  				$tipoexp = "VDF";
									  				$destino = "Sujetos Pasivos Especiales";
									  				$resultado = "Sancionados"; 
									  			case 43:
									  				$tipoexp = "Investigaciones";
									  				$resultado = "Allanados";
									  				$destino = "Sujetos Pasivos Especiales";
									  				break;
									  				break;
									  			case 44:
									  				$tipoexp = "Investigaciones";
									  				$resultado = "Allanados Parcialmente";
									  				$destino = "Sumario/Sujetos Pasivos Especiales";
									  				break;
									  		}
											
                                        ?>
									<input type="text" name="modtipoexp" id="modtipoexp" value="<?php echo $tipoexp ?>" readonly/>
								</li>
								<li> 
									<label for="modfiscapuntual">Fisc. Puntuales::</label> 
									<input type="checkbox" name="modfiscapuntual" id="modfiscapuntual" <?php echo $chk ?>  disabled/> 
								</li> 
								<li> 
									<label for="modresultado">Resultado:</label> 
									<input type="text" name="modresultado" id="modresultado" value="<?php echo $resultado ?>" readonly/>
								</li>
								<li> 
									<label for="moddestino">Destino:</label> 
									<input type="text" name="moddestino" id="moddestino" value="<?php echo $destino ?>" readonly /> 
								</li>								<li> 
									<label for="clausura">Clausurado:</label> 
									<input type="text" name="modclausura" id="modclausura" value="<?php echo $cierre ?>" readonly/>
								</li>
								<div id="documentosregistrodoc" style="margin-top: 5px;">
									<table width="90%" border="0" align="center" bgcolor="#999999">
									  <tr bgcolor="#333333">
									    <td align="center" style="color: #FFF">Año Providencia</td>
									    <td align="center" style="color: #FFF">Numero Providencia</td>
									    <td align="center" style="color: #FFF">Folio</td>
									    <td align="center" style="color: #FFF"></td>
									  </tr>
									  <tr>
									    <td align="center">
									    	<input name="modañoprovidencia" type="text" id="modañoprovidencia" maxlength="4" value="" style="text-align: center;">
									    </td>
									    <td align="center">
									    	<input name="modnumprovidencia" type="text" id="modnumprovidencia" maxlength="4" value="" style="text-align: center;">
									    </td>
									    <td align="center">
									    	<input name="modfolio" type="text" id="modfolio" maxlength="4" value="0" style="text-align: center;">
									    </td>
									    <td align="center">
										    <button class="botonagregar" type="button" name="incluir" id="incluir" >Incluir</button>
										</td>
									  </tr>
									</table>
									<div id="cargando_mod" style="display:none; width:100%;text-align: center;"><p align="center"><img src="images/loader.gif"></p></div>
									<div id="modregistroexp" style="margin-top: 5px;">
										<?php
											buscar_registro($valor->Status,0,0,$valor->Anno_memo,$valor->NroMemo,$sector); 
										?>
									</div>
								</div> 
								<div id="botonera" align="center">
								<li> 
									<button class="submit" type="button" id="guardar_mod">Guardar Modificacion</button>  
									<button class="submit" type="button" id="modimp_memo" disabled>Imprimir Memorando</button>
									<p align="center" id="txtmodResultadoExp" style="color: red;"></p> 
								</li> 
                                <input type="hidden" name="modestatus" id="modestatus" value="<?php echo $status ?>" />
                                <input type="hidden" name="modimprimir" id="modimprimir" value="0" />
                                <input type="hidden" name="valor_id" id="valo_rid" value="">
								</div>
							</ul> 
						</div> 
					</form>
				</div>

<?php
//******************************
function buscar_registro($estatus,$añoprovidencia,$numprovidencia,$Anno_memo,$nummemo,$sector)
{
	global $conexionsql;
	global $admin;
	//echo "Valores Recibidos: (".$estatus.",".$añoprovidencia.",".$numprovidencia.",".$Anno_memo.",".$nummemo.")";
	//BUSCAR REGISTROS EN EL TEMPORAL
	
///*************************P O R  A Q U ííííííííííííííí******************************************************************************************
	
	//SI NO EXISTE EN EL TEMPORAL BUSCAR EL TABLA
	if ($estatus!="" and $Anno_memo>0 and $nummemo>0)
	{
		$año= $añoprovidencia;
		$numero= $numprovidencia;
		$Ccsql1 = "SELECT count(*) as Total FROM ct_tmp_mod_salida_expediente WHERE Anno_Memo=".$Anno_memo." and NroMemo=".$nummemo." and sector=".$sector." and Status=".$estatus." AND borrado=0";
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
					
			$Ccsql = "SELECT * FROM ct_tmp_mod_salida_expediente WHERE Anno_Memo=".$Anno_memo." and NroMemo=".$nummemo." and sector=".$sector." and Status=".$estatus." AND borrado=0";
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
			$anno = date("Y", strtotime($valor['fecha_emision']));

			//echo $resultado;
			$Ccsql = "SELECT * FROM ct_tmp_mod_salida_expediente WHERE Anno_Memo=".$Anno_memo." and NroMemo=".$nummemo." and sector=".$sector." and Status=".$estatus." AND borrado=0";
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
$sql_bloquear= "SELECT * FROM ct_tmp_mod_salida_expediente WHERE Anno_Memo=".$año." and NroMemo=".$numeromemo." and sector=".$sector;
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