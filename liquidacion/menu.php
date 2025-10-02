<html>

<head>
	<script language="JavaScript" src="../funciones/menu/JSCookMenu.js"></script>
	<link rel="stylesheet" href="../funciones/menu/theme.css" type="text/css">
	<script language="JavaScript" src="../funciones/menu/theme.js"></script>
	<script language="JavaScript">
		var MenuPrincipal = [

			[null, 'Expedientes', null, null, '',
				[null, 'Aprobar', '1aprobar_expedientes.php', '', ''],
				[null, 'Imprimir', '2imprimir_expedientes.php', '', ''],
				[null, 'Transferir', '3transferir_expedientes.php', '', '']
			],

			[null, 'Liquidaciones', null, null, '',
				[null, 'Manuales', '5ingresar_liq_manuales_sel.php', '', ''],
				[null, 'Recursos Juridico', '9imprimir_recursos.php', '', ''],
				[null, 'Eliminar Secuenciales', '6reversar_liquidaciones.php', '', '']
			],

			[null, 'Consultas', null, null, '',
				[null, 'Status Contribuyente', '0status_contribuyente.php', '', '']
			],

			[null, 'Reportes', null, null, '',
				[null, 'Liquidaciones', '7menu_reportes.php', '', ''],
				[null, 'Secuenciales', '8menu_reportes.php', '', ''],
				[null, 'Transferidas', '4menu_reportes.php', '', '']
			],

			[null, 'Salida', 'salida.php', null, null]
		];
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
			cmDraw('MenuAplicacion', MenuPrincipal, 'hbr', cmThemeGray, 'ThemeGray');
		</script>
	</p>
	<p>&nbsp; </p>
</body>

</html>