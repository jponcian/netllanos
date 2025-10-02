<?php
session_start();
?>
<html>

<head>
	<title>Listar Anexo2</title>
	<meta http-equiv="Content-Type" content="text/html" charset="UTF-8">
	<style type="text/css">
		<!--
		body,
		td,
		th {
			font-size: 12px;
		}
		-->
	</style>
</head>

<body style="background: transparent !important;">
	<table width="100%" border="0" style="font:Arial, Helvetica, sans-serif" colspacing="5">
		<tr>
			<td>
				<?php
				include "../conexion.php";
				include "../funciones/auxiliar_php.php";
				mysql_query("SET NAMES 'utf8'");
				//$dbsector="LLANOS";
				$FechaI = $_GET['ConsultaI'];
				$FechaF = $_GET['ConsultaF'];
				$nombresector = $sector;
				echo '<h2>ANEXO 2: Periodo desde ' . date("d-m-Y", strtotime($FechaI)) . ' hasta ' . date("d-m-Y", strtotime($FechaF)) . '</h2>';

				$i = 0;
				echo '<table style="border:1px solid #123456" width="100%" cellspacing=0>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Tipo</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>#</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Operativo</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Nombre Sector</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Numero</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Impuesto</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Emision</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Notificacion</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Nombre</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Rif</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Sector Economico</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Fiscal</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>MF SI</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>MF NO</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Sancionado</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Clausurado</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Conforme</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>En Proceso</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Prod Potencial Bs.</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Observaciones</td>';

				$Ccsql = "
				SELECT fis_anexo2.TipoOperativo, fis_anexo2.Anno_Providencia, fis_anexo2.NroAutorizacion, fis_anexo2.FechaEmision, fis_anexo2.FechaNotificacion, 
				fis_anexo2.Nombre, fis_anexo2.Rif, fis_anexo2.Domicilio, fis_anexo2.Cant_sucursales, fis_anexo2.Cadena_Tienda, 
				fis_anexo2.Actividad_Economica, fis_anexo2.CedulaFiscal, fis_anexo2.Fiscal_Actuante, fis_anexo2.CedulaSupervisor, 
				fis_anexo2.Supervisor, fis_anexo2.CedulaCoord, fis_anexo2.Coordinador, fis_anexo2.Tlf_Contacto_Coord, fis_anexo2.Tipo_Maq_fiscal, 
				fis_anexo2.Mod_Maq_fiscal, fis_anexo2.MF_Cumple_Req, fis_anexo2.MF_Incumplimientos, fis_anexo2.DF_Multas, fis_anexo2.DF_Clausura, 
				fis_anexo2.Dias_clausura, fis_anexo2.Res_Clausura_Notificacion, fis_anexo2.MontoMultas, fis_anexo2.DF_Incumplimientos, 
				fis_anexo2.Observaciones, fis_anexo2.Programa, fis_anexo2.Tributo, fis_anexo2.FechaOperativo, fis_anexo2.NombreOperativo, fis_anexo2.Sede, 
				fis_anexo2_art.art100, fis_anexo2_art.art101, fis_anexo2_art.art102, fis_anexo2_art.art103, fis_anexo2_art.art104, fis_anexo2_art.art105, 
				fis_anexo2_art.art106, fis_anexo2_art.art107, fis_anexo2_art.art108, fis_anexo2.FechaProceso
				FROM fis_anexo2_art INNER JOIN fis_anexo2 ON (fis_anexo2_art.Anno = fis_anexo2.Anno_Providencia) AND 
				(fis_anexo2_art.NroAutorizacion = fis_anexo2.NroAutorizacion) AND (fis_anexo2_art.Sede = fis_anexo2.Sede)
				WHERE fis_anexo2.FechaProceso>='" . $FechaI . "' and fis_anexo2.FechaProceso<='" . $FechaF . "'; 
			"; //echo $Ccsql ;

				//echo $Ccsql;
				$resultado = mysql_query($Ccsql);
				$i = 1;
				while ($valor = mysql_fetch_array($resultado)) {
					echo '<tr><td>';
					echo utf8_decode($valor['TipoOperativo']);
					echo '</td><td>';
					echo $i;
					echo '</td><td>';
					echo utf8_decode($valor['NombreOperativo']);
					echo '</td><td>';
					echo $valor['Sede'];
					echo '</td><td>';
					echo $valor['Anno_Providencia'] . "-" . $valor['NroAutorizacion'];
					echo '</td><td>';
					echo utf8_decode($valor['Tributo']);
					echo '</td><td>';
					echo date("d-m-Y", strtotime($valor['FechaEmision']));
					echo '</td><td>';
					echo date("d-m-Y", strtotime($valor['FechaNotificacion']));
					echo '</td><td>';
					echo ($valor['Nombre']);
					echo '</td><td>';
					echo $valor['Rif'];
					echo '</td><td>';
					echo utf8_decode($valor['Actividad_Economica']);
					echo '</td><td>';

					echo utf8_decode($valor['Fiscal_Actuante']);
					echo '</td><td align="center">';

					if ($valor['Tipo_Maq_fiscal'] == 'NO APLICA') {
						$mfsi = "";
						$mfno = "x";
					} else {
						$mfsi = "x";
						$mfno = "";
					}
					echo $mfsi;
					echo '</td><td align="center">';
					echo $mfno;

					echo '</td><td align="center">';

					if ($valor['DF_Multas'] == 'SI') {
						$multasi = "x";
						$multano = "";
					} else {
						$multasi = "";
						$multano = "x";
					}
					echo $multasi;
					echo '</td><td align="center">';
					//echo $multano;
					//echo '</td><td>';

					if ($valor['DF_Clausura'] == 'SI') {
						echo 'x';
						echo '</td><td align="center">';
					} else {
						echo '';
						echo '</td><td align="center">';
					}


					if ($valor['MontoMultas'] > 0) {
						echo '';
						echo '</td><td align="center">';
					} else {
						echo 'x';
						echo '</td><td align="center">';
					}

					//echo $valor['Dias_clausura'];
					//echo '</td><td>';

					echo '1';
					echo '</td><td align="right">';

					/*
					if ($valor['DF_Multas']=='NO')
						{
							echo '1';
							echo '</td><td>';						
						}
					else
						{
							echo '';
							echo '</td><td>';						
						}
					*/

					$valor_tc = valor_TASACAMBIO($valor['FechaNotificacion']);
					$monto_ut = $valor['MontoMultas'] * $valor_tc;

					echo $monto_ut;

					//echo $valor['MontoMultas'];
					echo '</td><td>';

					/*$articulos = imprime_articulo($valor['Anno_Providencia'],$valor['NroAutorizacion'],$valor['Sede']);
					echo $articulos;
					echo '</td><td align="right">';
					echo $valor['DF_Incumplimientos'];
					echo '</td><td>';
					*/
					echo utf8_decode($valor['Observaciones']);
					echo '</td>';
					$i = $i + 1;
				}
				?>
			</td>
		</tr>
	</table>

	<?php

	function valor_UT($fecha)
	{
		$sector = "SELECT ValorUT FROM a_valorut WHERE FechaAplicacion <= '" . $fecha . "' ORDER BY FechaAplicacion DESC Limit 1";
		$tabla = mysql_query($sector);
		$UT = mysql_fetch_object($tabla);
		return $UT->ValorUT;
	}

	function valor_TASACAMBIO($fecha)
	{
		$sector = "SELECT valor as ValorTC FROM a_moneda_cambio WHERE FechaAplicacion <= '" . $fecha . "' ORDER BY FechaAplicacion DESC Limit 1";
		$tabla = mysql_query($sector);
		$TC = mysql_fetch_object($tabla);
		return $TC->ValorTC;
	}

	function imprime_articulo($anno, $numero, $sede)
	{
		$sql = "SELECT art100, art101, art102, art103, art104, art105, art106, art107, art108 FROM fis_anexo2_art WHERE Anno = $anno AND NroAutorizacion = $numero AND Sede = '$sede'";
		$result = mysql_query($sql);
		$art = mysql_fetch_object($result);

		//ARTICULO 100
		$art100 = extraer_articulos($art->art100, '100');

		//ARTICULO 101
		$art101 = extraer_articulos($art->art101, '101');

		//ARTICULO 102
		$probar = "'" . $art->art102 . "'";
		$art102 = extraer_articulos($art->art102, '102');

		//ARTICULO 103
		$art103 = extraer_articulos($art->art103, '103');

		//ARTICULO 104
		$art104 = extraer_articulos($art->art104, '104');

		//ARTICULO 105
		$art105 = extraer_articulos($art->art105, '105');

		//ARTICULO 106
		$art106 = extraer_articulos($art->art106, '106');

		//ARTICULO 107
		$art107 = extraer_articulos($art->art107, '107');

		//ARTICULO 108
		$art108 = extraer_articulos($art->art108, '108');

		if (strlen($art100) <> '') {
			$art100 = $art100 . ', ';
		} else {
			$art100 = '';
		}
		if (strlen($art101) <> '') {
			$art101 = $art101 . ', ';
		} else {
			$art101 = '';
		}
		if (strlen($art102) <> '') {
			$art102 = $art102 . ', ';
		} else {
			$art102 = '';
		}
		if (strlen($art103) <> '') {
			$art103 = $art103 . ', ';
		} else {
			$art103 = '';
		}
		if (strlen($art104) <> '') {
			$art104 = $art104 . ', ';
		} else {
			$art104 = '';
		}
		if (strlen($art105) <> '') {
			$art105 = $art105 . ', ';
		} else {
			$art105 = '';
		}
		if (strlen($art106) <> '') {
			$art106 = $art106 . ', ';
		} else {
			$art106 = '';
		}
		if (strlen($art107) <> '') {
			$art107 = $art107 . ', ';
		} else {
			$art107 = '';
		}
		if (strlen($art108) <> '') {
			if (strlen($art108) > 3) {
				$art108 = '108';
			}
		} else {
			$art108 = '';
		}



		$string = $art100 . $art101 . $art102 . $art103 . $art104 . $art105 . $art107 . $art108;
		$string = trim($string);
		if (substr($strin, 0, 2) == ', ') {
			$n = strlen($string) - 1;
			$string = substr($strin, 2, $n);
		}

		$n = strlen($string);
		if (substr($string, -1) == ',') {
			$string = substr($string, 0, $n - 1);
		}

		return $string;
	}

	function extraer_articulos($articulo, $texto)
	{
		$var100 = $texto;
		$cadena = $articulo;
		for ($i = 0; $i < strlen($cadena) + 1; $i++) {
			if ($cadena[$i] == 1) {
				if ($i == 0) {
					$var100 = $texto . '#' . sprintf("%002s", $i + 1);
				} else {
					if (strlen($var100) > 3) {
						$var100 = $var100 . ', ' . $texto . '#' . sprintf("%002s", $i + 1);
					} else {
						$var100 = $texto . '#' . sprintf("%002s", $i + 1);
					}
				}
			}
		}

		if (strlen($var100) > 3) {
			$var100 = $var100;
		} else {
			$var100 = '';
		}

		return $var100;
	}
	?>
</body>

</html>