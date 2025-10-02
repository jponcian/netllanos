<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Reporte Anexo2</title>
	<script language="javascript" script type="text/javascript" src="datetimepicker_css.js">
	</script>
	<style type="text/css">
		<!--
		body,
		td,
		th {
			font-family: Tahoma, Geneva, sans-serif;
			font-weight: bold;
			color: #000;
			font-size: 12px;
		}

		body {
			background-image: url();
		}
		-->
	</style>
</head>

<body style="background: transparent !important;">
	<form method="get" name="ListadoAnexo2" action="rptanexo2.php" target="ReporteListado">
		<table width="878" border="0" cellpadding="5" cellspacing="0" align="center">
			<tr>
				<td align="center" valign="middle" bgcolor="#0066FF">
					<p>Gerencia Regional de Tributos Internos Región Llanos - Area de Control Tributario<br />
					</p>
				</td>
			</tr>
		</table>
		<table width="878" border="0" cellpadding="5" cellspacing="0" align="center">
			<tr><br /></tr>
			<tr>
				<td>Ingrese fecha inicio a Consultar: </td>
				<td width="548"><input name="ConsultaI" readonly="readonly" type="text" size="10" maxlength="10" />
					<a href="javascript:NewCssCal('ConsultaI','YYYYMMDD')"><img src="../imagenes/cal.gif" width="16" height="16" border="0" alt="Click para selecionar..."></a>
				</td>
			</tr>
			<tr>
				<td>Ingrese fecha final a Consultar: </td>
				<td width="548"><input name="ConsultaF" readonly="readonly" type="text" size="10" maxlength="10" />
					<a href="javascript:NewCssCal('ConsultaF','YYYYMMDD')"><img src="../imagenes/cal.gif" width="16" height="16" border="0" alt="Click para selecionar..."></a>
				</td>
			</tr>
		</table>
		<table width="878" border="0" cellpadding="5" cellspacing="0" align="center">
			<tr>
				<td align="center" bgcolor="#0066FF">
					<input onmouseover=this.style.cursor="hand" type="submit" name="enviar" value="Enviar">
				</td>
			</tr>
		</table>
	</form>
</body>

</html>