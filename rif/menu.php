<html>

<head>
	<script language="JavaScript" src="../funciones/menu/JSCookMenu.js"></script>
	<link rel="stylesheet" href="../funciones/menu/theme.css" type="text/css">
	<script language="JavaScript" src="../funciones/menu/theme.js"></script>
	<script language="JavaScript">
		<!--
		var MenuPrincipal = [
			//[null,'Contribuyente',null,null,'',
			//	[null,'Contribuyentes','incluircontribuyente.php','','']
			//],
			[null, 'Expediente', null, null, '',
				[null, 'Registrar', 'registrar_expediente.php', '', ''],
				[null, 'Modificar', 'modificar_expediente.php', '', ''],
				[null, 'Concluir', '18_expediente_concluir.php', '', ''],
				[null, 'Reversar', '19_expediente_reversar.php', '', ''],
				[null, 'Anular', 'anular_expediente.php', '', '']
			],
			[null, 'Sanciones', null, null, '',
				[null, 'Multas', '16_incluir_sancion_sel_pro.php', '', '']
			],
			[null, 'Resolucion', null, null, '',
				[null, 'Aprobar', '20_expediente_aprobar.php', '', ''],
				[null, 'Imprimir', '21_imprimir_resolucion.php', '', ''],
				[null, 'Transferir', '22_expediente_transferir.php', '', '']
			],

			[null, 'Coordinador', null, null, '',
				[null, 'Reversar Expediente', '26_expediente_reversar.php', '', '']
			],

			[null, 'Reportes', null, null, '',
				[null, 'Transferidas Liquidaci�n', '25_transferidas_liq.php', '', '']
			],

			[null, 'Salida', 'salida.php', null, null]
		];
		-->
	</script>
	<style type="text/css">
		body {
			background-color: rgb(255, 255, 255);
			/*    background-image: url(../imagenes/superior.png);*/
			background-repeat: no-repeat;
			margin: 0px;
		}

		#MenuAplicacion {
			margin-left: 10px;
			margin-top: 0px;
		}
	</style>
</head>

<body style="background: transparent !important;">
	<div id="MenuAplicacion" align="center">
	</div>
	<p>
		<script language="JavaScript">
			<!--
			cmDraw('MenuAplicacion', MenuPrincipal, 'hbr', cmThemeGray, 'ThemeGray');
			-->
		</script>
	</p>
	<p>&nbsp; </p>
</body>

</html>