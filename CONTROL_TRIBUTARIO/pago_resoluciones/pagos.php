<?php
session_start();

//CONEXION A MYSQL con MySQLi
set_time_limit(0);
$rif = $_GET['rif'];
$bdd = $_SESSION[BDD];
//Establecemos la conexion a la base de datos
$con = new mysqli('localhost', 'root','', $bdd);
$con->query("SET NAMES 'utf8'");
$csql = "SELECT contribuyente FROM contribuyentes WHERE rif = '".$rif."'";
$result = $con->query($csql);
$nombre = $result->fetch_object();

$sql = "SELECT
liquidacion.id_liquidacion,
liquidacion.fecha_pag,
liquidacion.agencia_pag,
liquidacion.usuario,
liquidacion.anno_expediente,
liquidacion.num_expediente,
liquidacion.periodoinicio,
liquidacion.periodofinal,
liquidacion.id_sancion,
round((liquidacion.monto_ut / liquidacion.concurrencia * liquidacion.especial),2) AS ut,
round((liquidacion.monto_bs / liquidacion.concurrencia * liquidacion.especial),2) AS monto,
liquidacion.liquidacion,
liquidacion.planilla_notificacion,
liquidacion.planilla,
liquidacion.fecha_not,
contribuyentes.contribuyente
FROM
liquidacion
INNER JOIN contribuyentes ON contribuyentes.rif = liquidacion.rif
WHERE liquidacion.rif = '".$rif."' AND  liquidacion.origen_liquidacion = 4 AND
liquidacion.fecha_not >= liquidacion.fecha_transferencia_a_liq
ORDER BY liquidacion.planilla DESC";

//echo $sql;
?>
<div class="container-fluid table-responsive">

	<div class="panel panel-default">
	  <div class="panel-heading">
	    <h3 class="panel-title">SUJETO PASIVO:</h3>
	  </div>
	  <div class="panel-body">
	    <strong><?php echo $nombre->contribuyente; ?></strong>
	  </div>
	</div>

	
<table width="100%" border="1" class="table table-hover table-condensed">
  <tr>
    <th>Planilla Liquidación</th>
    <th>N° Planilla Pago</th> 
    <th>Notificación</th>
    <th>Monto</th>
    <th>Fecha Pago</th>
    <th>Agencia</th>
    <th>Acción</th>
  </tr>
<?php
$tabla = $con->query($sql);
while ($reg = $tabla->fetch_object())
{
	?>
	  <tr>
	    <td align="center"><?php echo $reg->liquidacion; ?></td>
	    <td align="center"><?php echo $reg->planilla_notificacion; ?></td> 
	    <td align="center"><?php echo date("d-m-Y",strtotime($reg->fecha_not)); ?></td>
	    <td align="right"><?php echo number_format($reg->monto, 2, ',', '.'); ?></td>
	    <?php
	    if($reg->fecha_pag != Null)
	    {
	    	?>
			    <td align="center"><?php echo date("d-m-Y",strtotime($reg->fecha_pag)); ?></td>
			    <td align="center">
			    <?php 
			    	$nombre = BuscarAgencia ($reg->agencia_pag, $con);
					$nom_agencia = str_replace("BANCO DE ", "", $nombre); 
					$nom_agencia = str_replace("BANCO ", "", $nom_agencia); 
			    	echo $nom_agencia; 
			    ?>
			    </td>
			    <td><span></span></td>
	    	<?php
	    } else {
	    	?>
		    <td align="center"><input size="10" type="text" class="dataPicker centrarTxt" id="<?php echo 'txt'.$reg->id_liquidacion; ?>" name="<?php echo 'txt'.$reg->id_liquidacion; ?>" value=""></td>
		    <td align="center">
		    	<?php
					$asql="SELECT CONCAT(a_banco.Descripcion, '(',a_agencia.id_agencia_ordinario, ' - ', a_agencia.id_agencia_especial, ')') AS agencia, a_agencia.id_agencia FROM a_agencia INNER JOIN a_banco ON a_banco.id_banco = a_agencia.id_banco INNER JOIN z_sectores ON z_sectores.id_sector = a_agencia.sector where a_agencia.id_agencia_especial is not null GROUP BY a_agencia.id_agencia, a_banco.Descripcion, a_agencia.id_agencia_especial, a_agencia.id_agencia_ordinario ORDER BY a_banco.Descripcion, a_agencia.id_agencia_ordinario ASC";
					$resultado = $con->query($asql);
					?>
						<select id="cbo<?php echo $reg->id_liquidacion; ?>">
					<?php
						while($lista = $resultado->fetch_object())
						{
							?>
								<option value="<?php echo $lista->id_agencia; ?>">
								<?php
									$nom_agencia = str_replace("BANCO DE ", "", $lista->agencia); 
									$nom_agencia = str_replace("BANCO ", "", $nom_agencia); 
									echo $nom_agencia; 
								?>									
								</option>
							<?php
						}
				?>
				</select>
		    	<!-- <input class="centrarTxt" type="text" id="<?php echo 'agencia' + $reg->id_liquidacion; ?>" name="<?php echo 'agencia' + $reg->id_liquidacion; ?>" value=""></td> -->
		    <td align="center"><button id="<?php echo $reg->id_liquidacion; ?>" class="InputGrabar btn btn-danger btn-sm">Grabar</button></td>
	    	<?php
	    }
	    ?>
	  </tr>
	<?php
}
?>
</table>
</div>


<?php

function NombreSujeto($rif) {
	$csql = "SELECT contribuyente FROM contribuyentes WHERE rif = '".$rif."'";
	echo $csql;
	$result = $con->query($csql);
	$nombre = $result->fetch_object();
	return $nombre->contribuyente;
}

function BuscarAgencia ($codigo, $con) {
	$asql="SELECT CONCAT(a_banco.Descripcion, '(',a_agencia.id_agencia_ordinario, ' - ', a_agencia.id_agencia_especial, ')') AS agencia, a_agencia.id_agencia FROM a_agencia INNER JOIN a_banco ON a_banco.id_banco = a_agencia.id_banco INNER JOIN z_sectores ON z_sectores.id_sector = a_agencia.sector where a_agencia.id_agencia = ".$codigo." GROUP BY a_agencia.id_agencia, a_banco.Descripcion, a_agencia.id_agencia_especial, a_agencia.id_agencia_ordinario ORDER BY a_banco.Descripcion, a_agencia.id_agencia_ordinario ASC"; //echo $asql;
	$resultado = $con->query($asql);
	$nombre = $resultado->fetch_object();
	return $nombre->agencia;
}



?>