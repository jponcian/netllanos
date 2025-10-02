<?php
session_start();
?>
<html>

<head>
	<title>Listar Anexo2</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
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

				//$dbsector="LLANOS";
				$FechaI = $_GET['ConsultaI'];
				$FechaF = $_GET['ConsultaF'];
				$nombresector = $sector;
				echo '<h2>ANEXO 2: Periodo desde ' . date("d-m-Y", strtotime($FechaI)) . ' hasta ' . date("d-m-Y", strtotime($FechaF)) . '</h2>';

				$i = 0;
				echo '<table style="border:1px solid #123456" width="100%" cellspacing=0>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Fecha Operativo</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Nombre Operativo</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Nombre Region</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Nombre Sector</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>N�</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Numero</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Impuesto</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Fecha Emision</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Notificacion</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Nombre</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Rif</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Domicilio</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Cant. Suc.</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Cadena Tienda</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Act. Econ.</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Fiscal</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Cedula Fiscal</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Supervisor</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Cedula Supervisor</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Coordinador</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Cedula Coordinador</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Telf. Cont.</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Tipo Maq. Fiscal</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Mod. Maq. Fiscal</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>MF SI Cumple</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>MF NO Cumple</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Incumplimientos MF</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Multas DF SI</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Multas DF NO</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Cierre SI</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Cierre NO</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Dias Cierre</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Notificacion Cierre</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Monto Multas Bs.</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Incumplimientos DF</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Observaciones</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>100#01</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>100#02</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>100#03</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>100#04</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>101#01</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>101#02</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>101#03</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>101#04</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>101#05</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>101#06</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>101#07</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>101#08</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>101#09</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>101#10</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>101#11</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>102#01</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>102#02</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>102#03</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>102#04</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>102#05</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>102#06</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>102#07</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>102#08</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>103#01</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>103#02</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>103#03</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>103#04</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>103#05</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>103#06</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>103#07</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>104#01</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>104#02</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>104#03</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>104#04</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>104#05</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>104#06</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>104#07</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>104#08</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>104#09</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>104#10</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>104#11</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>104#12</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>104#13</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>104#14</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>105#01</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>105#02</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>105#03</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>105#04</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>105#05</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>106#01</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>106#02</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>106#03</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>107#01</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>107#02</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>107#03</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>107#04</td>';
				echo '</td><td width="10" bgcolor="#FF0000"><b>108</td>';
				echo '</td><td width="30" bgcolor="#FF0000"><b>Programa</td>';

				$Ccsql = "
				SELECT fis_anexo2.Anno_Providencia, fis_anexo2.NroAutorizacion, fis_anexo2.FechaEmision, fis_anexo2.FechaNotificacion, 
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
			";

				//echo $Ccsql;
				$resultado = mysql_query($Ccsql);

				while ($valor = mysql_fetch_array($resultado)) {
					echo '<tr><td>';
					echo date("d-m-Y", strtotime($valor['FechaOperativo']));
					echo '</td><td>';
					echo $valor['NombreOperativo'];
					echo '</td><td>';
					echo 'LOS LLANOS';
					echo '</td><td>';
					echo $valor['Sede'];
					echo '</td><td>';
					echo '';
					echo '</td><td>';
					echo $valor['Anno_Providencia'] . "-" . $valor['NroAutorizacion'];
					echo '</td><td>';
					echo $valor['Tributo'];
					echo '</td><td>';
					echo date("d-m-Y", strtotime($valor['FechaEmision']));
					echo '</td><td>';
					echo date("d-m-Y", strtotime($valor['FechaNotificacion']));
					echo '</td><td>';
					echo $valor['Nombre'];
					echo '</td><td>';
					echo $valor['Rif'];
					echo '</td><td>';
					echo $valor['Domicilio'];
					echo '</td><td>';
					echo $valor['Cant_sucursales'];
					echo '</td><td>';
					echo $valor['Cadena_Tienda'];
					echo '</td><td>';
					echo $valor['Actividad_Economica'];
					echo '</td><td>';
					echo $valor['Fiscal_Actuante'];
					echo '</td><td>';
					echo $valor['CedulaFiscal'];
					echo '</td><td>';
					echo $valor['Supervisor'];
					echo '</td><td>';
					echo $valor['CedulaSupervisor'];
					echo '</td><td>';
					echo $valor['Coordinador'];
					echo '</td><td>';
					echo $valor['CedulaCoord'];
					echo '</td><td>';
					echo $valor['Tlf_Contacto_Coord'];
					echo '</td><td>';
					echo $valor['Tipo_Maq_fiscal'];
					echo '</td><td>';
					echo $valor['Mod_Maq_fiscal'];
					echo '</td><td>';
					if ($valor['Tipo_Maq_fiscal'] == 'NO APLICA') {
						echo '';
						echo '</td><td>';
						echo '';
						echo '</td><td>';
					} else {
						if ($valor['MF_Cumple_Req'] == 'SI') {
							echo '1';
							echo '</td><td>';
							echo '';
							echo '</td><td>';
						} else {
							echo '';
							echo '</td><td>';
							echo '1';
							echo '</td><td>';
						}
					}
					echo $valor['MF_Incumplimientos'];
					echo '</td><td>';
					if ($valor['DF_Multas'] == 'SI') {
						echo '1';
						echo '</td><td>';
						echo '';
						echo '</td><td>';
					} else {
						echo '';
						echo '</td><td>';
						echo '1';
						echo '</td><td>';
					}
					if ($valor['DF_Clausura'] == 'SI') {
						echo '1';
						echo '</td><td>';
						echo '';
						echo '</td><td>';
					} else {
						if ($valor['DF_Multas'] == 'SI') {
							echo '';
							echo '</td><td>';
							echo '1';
							echo '</td><td>';
						} else {
							echo '';
							echo '</td><td>';
							echo '';
							echo '</td><td>';
						}
					}
					echo $valor['Dias_clausura'];
					echo '</td><td>';
					if ($valor['Res_Clausura_Notificacion'] == "") {
						echo $valor['Res_Clausura_Notificacion'];
					} else {
						echo date("d-m-Y", strtotime($valor['Res_Clausura_Notificacion']));
					}
					echo '</td><td align="right">';
					echo number_format($valor['MontoMultas'], 2);
					echo '</td><td>';
					echo $valor['DF_Incumplimientos'];
					echo '</td><td>';
					echo $valor['Observaciones'];
					echo '</td><td>';

					echo imprime_articulo(substr($valor['art100'], 0, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art100'], 1, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art100'], 2, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art100'], 3, 1));
					echo '</td><td>';

					echo imprime_articulo(substr($valor['art101'], 0, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art101'], 1, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art101'], 2, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art101'], 3, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art101'], 4, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art101'], 5, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art101'], 6, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art101'], 7, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art101'], 8, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art101'], 9, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art101'], 10, 1));
					echo '</td><td>';

					echo imprime_articulo(substr($valor['art102'], 0, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art102'], 1, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art102'], 2, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art102'], 3, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art102'], 4, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art102'], 5, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art102'], 6, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art102'], 7, 1));
					echo '</td><td>';

					echo imprime_articulo(substr($valor['art103'], 0, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art103'], 1, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art103'], 2, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art103'], 3, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art103'], 4, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art103'], 5, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art103'], 6, 1));
					echo '</td><td>';

					echo imprime_articulo(substr($valor['art104'], 0, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art104'], 1, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art104'], 2, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art104'], 3, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art104'], 4, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art104'], 5, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art104'], 6, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art104'], 7, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art104'], 8, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art104'], 9, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art104'], 10, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art104'], 11, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art104'], 12, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art104'], 13, 1));
					echo '</td><td>';

					echo imprime_articulo(substr($valor['art105'], 0, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art105'], 1, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art105'], 2, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art105'], 3, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art105'], 4, 1));
					echo '</td><td>';

					echo imprime_articulo(substr($valor['art106'], 0, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art106'], 1, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art106'], 2, 1));
					echo '</td><td>';

					echo imprime_articulo(substr($valor['art107'], 0, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art107'], 1, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art107'], 2, 1));
					echo '</td><td>';
					echo imprime_articulo(substr($valor['art107'], 3, 1));
					echo '</td><td>';

					echo imprime_articulo($valor['art108']);
					echo '</td><td>';

					echo $valor['Programa'];
					echo '</td></tr>';
				}
				?>
			</td>
		</tr>
	</table>

	<?php

	function imprime_articulo($valor)
	{
		if ($valor == 1) {
			$string = "X";
		} else {
			$string = "";
		}
		return $string;
	}
	?>
</body>

</html>