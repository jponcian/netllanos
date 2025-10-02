<?php
ob_end_clean();
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

?>
<?php
if ($_POST['CMDPROCESAR'] == "Procesar") {

	$nombre = date("Ymd") . "_" . date("His");

	$archivo = (isset($_FILES['archivo'])) ? $_FILES['archivo'] : null;
	if ($archivo) {
		//VERIFICAR LA EXTENSION
		if ($archivo) {
			$extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
			$extension = strtolower($extension);
			$extension_correcta = ($extension == 'txt');
			if ($extension_correcta) {
				$ruta_destino_archivo = "txt/{$archivo['name']}";
				$archivo_ok = move_uploaded_file($archivo['tmp_name'], $ruta_destino_archivo);
				//RENOMBRANDO EL ARCHIVO
				$archivoAbierto = fopen("txt/{$archivo['name']}", "r");
				fclose($archivoAbierto);
				rename("txt/{$archivo['name']}", "txt/" . $nombre . ".txt");
				//El archivo se renombrar� correctamente
				$_POST['texto'] = $nombre . ".txt";

				//SUBIR LOS DATOS A LA TABLA EN LA BASE DE DATOS
				$lineas = file('txt/' . $_POST['texto']);
				$i = 1;

				foreach ($lineas as $linea_num => $linea) {
					if ($i > 1) {
						$datos = explode("\t", $linea);

						$tipo_de_pago = trim($datos[0]);
						$cod_forma = trim($datos[1]);
						$forma = trim($datos[2]);
						$cod_dependencia = trim($datos[3]);
						$dependencia = trim($datos[4]);
						$cod_banco = trim($datos[5]);
						$banco = trim($datos[6]);
						$agencia = trim($datos[7]);
						$nombre_agencia = trim($datos[8]);
						$rif = trim($datos[9]);
						$nombre_contribuyente = trim($datos[10]);
						$periodo = trim($datos[11]);
						$documento = trim($datos[12]);
						$monto = trim($datos[13]);
						$monto = str_replace(".", "", $monto);
						$monto = str_replace(",", ".", $monto);
						$fecha_recaudacion = trim($datos[14]);
						$fecha_recaudacion = date("Y/m/d", strtotime($fecha_recaudacion));

						//if ($agencia<>"") { $id_agencia = buscar_agencia($agencia,$cod_banco,$nombre_agencia,$banco); }
						if ($rif <> "") {
							$sql = "SELECT * FROM ce_pagos WHERE Numero=" . $documento;
							$tabla = mysql_query($sql);
							$existe = mysql_num_rows($tabla);
							if ($existe < 1) {
								$guardar = guardar_planilla($rif, $periodo, $cod_forma, $agencia, $fecha_recaudacion, $cod_banco, $monto, $documento);
							}
						}
					}
					$i++;   //suma 1 a $i para siguiente post_id.// 

				}
				echo "<script type=\"text/javascript\">alert('!!!...ARCHIVO TXT CARGADO SATISFACTORIAMENTE...!!!');</script>";
				//***********************************************
			} else {
				echo "<script type=\"text/javascript\">alert('�El archivo es incorrecto, debe seleccionar un archivo .txt!');</script>";
			}
		}
	}
}

function buscar_agencia($codigo, $banco, $descripcion, $nom_banco)
{

	//BUSCAMOS EL BANCO
	$selectbanco = "SELECT banco, descripcion, id_banco FROM a_banco WHERE banco=" . $banco;
	$tabla_banco = mysql_query($selectbanco);
	$existe = mysql_num_rows($tabla_banco);
	if ($existe < 1) {
		//SI EL BANCO NO EXISTE LO REGISTRAMOS
		$add_banco = mysql_query("INSERT INTO a_banco (banco, descripcion) VALUES (" . $banco . ",'" . $nom_banco . "')");
	}

	//BUSCAMOS LA AGENCIA
	$sqlagencia = "SELECT id_agencia FROM a_agencia WHERE id_agencia_especial=" . $codigo . " OR id_agencia_ordinario=" . $codigo;
	$agencia = mysql_query($sqlagencia);
	$cantidad = mysql_num_rows($agencia);
	if ($cantidad < 1) {
		//SI LA AGENCIA NO EXISTE LA REGISTRAMOS
		$addagencia = mysql_query("INSERT INTO a_agencia (id_banco,id_agencia_ordinario,id_agencia_especial,sector) VALUES (" . $id_del_banco . ",999," . $codigo . "," . $_SESSION["SEDE_USUARIO"] . ")");
	}
}

function guardar_planilla($rif, $periodo, $forma, $agencia, $fecha_recaudacion, $banco, $monto, $documento)
{
	//BUSCAMOS EL TIPO DE OBLIGACION
	$sqltipo = mysql_query("SELECT Numero,Tipo,Forma FROM ce_cal_tip_obligaciones WHERE Forma='" . $forma . "'");
	$reg_forma = mysql_fetch_object($sqltipo);

	//DETERMINAMOS SI TIENE QUINCENA
	$sqlquincena = mysql_query("SELECT Rif,Periodo,Quincena FROM ce_calendario WHERE Quincena>0 AND Rif LIKE '%" . substr($rif, 9, 1) . "%' AND Tipo_Impuesto=" . $reg_forma->Numero);
	$reg_quincena = mysql_fetch_object($sqlquincena);

	//BUSCAMOS SI EL CONTRIBUYENTE HA PAGO ALGUNA QUINCENA CON ESE PERIODO
	if ($reg_quincena->Quincena > 0) {
		$sqlbuscar = mysql_query("SELECT Numero FROM ce_pagos WHERE Periodo='" . $periodo . "' AND Rif ='" . $rif . "' AND Tipo_Impuesto=" . $reg_forma->Numero . "");
		$reg_buscar = mysql_fetch_object($sqlbuscar);
		if ($reg_buscar->Numero <> "") {
			$quincena = 2;
		} else {
			$quincena = 1;
		}
	} else {
		$quincena = 0;
	}

	//BUSCAMOS LA FECHA DE VENCIMIENTO
	$sqlfecha = mysql_query("SELECT date_format(Fecha_Ven,'%Y/%m/%d') as Fecha FROM CE_Calendario WHERE Periodo='" . $periodo . "' AND Rif LIKE '%" . substr($rif, 9, 1) . "%' AND Tipo_Impuesto=" . $reg_forma->Numero . " AND Quincena=" . $quincena . "");
	$reg_fecha = mysql_fetch_object($sqlfecha);
	$fecha_vencimiento = $reg_fecha->Fecha;

	//BUSCAMOS EL SECTOR
	$sqlsector = "SELECT id_agencia, sector FROM a_agencia WHERE id_agencia_especial=" . $agencia . " OR id_agencia_ordinario=" . $agencia;
	$sqlsector = mysql_query($sqlsector);
	if ($reg_sector = mysql_fetch_object($sqlsector)) {
		$sector = $reg_sector->sector;
		$id_agencia = $reg_sector->id_agencia;
	} else {
		//EN CASO QUE LA AGENCIA NO ESTE REGISTRADA GUARDAMOS CERO PARA GENERAR REPORTE
		$sector = 0;
		$id_agencia = 0;
	}

	//------- ELIMINAMOS LA PLANILLA
	$sql_del = "DELETE FROM ce_pagos WHERE Rif='" . $rif . "' and Numero=" . $documento . " AND Periodo='" . $periodo . "' AND Quincena=" . $quincena . " AND Fecha_Ven='" . $fecha_vencimiento . "' AND Monto=" . $monto . " AND Sector=" . $sector . ";";
	$sql_del = mysql_query($sql_del);
	echo $sql_del;

	//REGISTRAMOS LA PLANILLA
	$sql_add = "INSERT INTO ce_pagos (Rif,Tipo_Impuesto,Numero,Periodo,Quincena,Fecha_Ven,Fecha_Presentacion,Fecha_Pago,Agencia,Monto,Sector,txt) VALUES ('" . $rif . "'," . $reg_forma->Numero . "," . $documento . ",'" . $periodo . "'," . $quincena . ",'" . $fecha_vencimiento . "','" . $fecha_recaudacion . "','" . $fecha_recaudacion . "'," . $id_agencia . "," . $monto . "," . $sector . ",1))";
	$sql_add = mysql_query($sql_add);
}

?>
<html>

<head>

	<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>

	<title>Carga de Archivo TXT</title>
	<style type="text/css">
		<!--
		.Estilomenun {
			font-family: Verdana, Arial, Helvetica, sans-serif;
			font-size: 12px;
			font-weight: bold;
		}

		body {
			background-image: url();
		}

		.Estilo1 {
			color: #FFFFFF;
			font-weight: bold;
			font-size: 18px;
		}

		.Estilo16 {
			color: #FF0000
		}
		-->
	</style>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

</head>

<body style="background: transparent !important;">
	<p>
		<?php include "../titulo.php"; ?>
	</p>
	<p>
	<div align="center">
		<p align="center">
			<?php
			include "menu.php";
			?>
	</div>
	</p>

	<form name="form1" method="post" action="" enctype="multipart/form-data">
		<table width="500" border=1 align=center>
			<tr>
				<td width="574" height="40" align="center" bgcolor="#FF0000" colspan="2">
					<p class="Estilo7 Estilo1"><u>CARGA DE PLANILLAS (ARCHIVO TXT)</u></p>
				</td>
			</tr>
			<tr>
				<td height="40" align="right">Seleccione el Archivo TXT:</td>
				<td height="40" align="center"><input name="archivo" type="file"></td>
			</tr>
			<tr>
				<td align="center" colspan="2" height="40" bgcolor="#ccc"><input name="CMDPROCESAR" type="submit" value="Procesar"></td>
			</tr>
		</table>
		<input name="texto" type="hidden" value="<?php echo $_POST['texto'] ?>">
	</form>

	<?php include "../pie.php"; ?>


	<p>&nbsp;</p>
</body>

</html>

<?php
//----------
include "../desconexion.php";
//----------

?>