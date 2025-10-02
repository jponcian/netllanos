<html>

<head>
	<script language="JavaScript" src="../funciones/menu/JSCookMenu.js"></script>
	<link rel="stylesheet" href="../funciones/menu/theme.css" type="text/css">
	<script language="JavaScript" src="../funciones/menu/theme.js"></script>
	<script language="JavaScript">
		<!--
		var MenuPrincipal = [

			[null, 'Gesti&oacute;n Coordinador', null, null, '',
				[null, 'Importar', '1importar_planillas.php', '', ''],
				[null, 'Asignar Notificador', '2menu_incluir_not.php', '', ''],
				[null, 'Reasignar Notificador', '8menu_incluir_not.php', '', ''],
				[null, 'Gesti&oacute;n Planillas', '9menu_incluir_fecha_not.php', '', ''],
				[null, 'Transferir a Cobro', '4transferir_planillas.php', '', '']
			],

			[null, 'Gesti&oacute;n Notificador', null, null, '',
				[null, 'Gesti&oacute;n Planillas', '3menu_incluir_fecha_not.php', '', '']
			],

			[null, 'Consultas', null, null, '',
				[null, 'Status Contribuyente', '0status_contribuyente.php', '', '']
			],

			[null, 'Reportes', null, null, '',
				[null, 'Planillas Excel', '7menu_reportes.php', '', ''],
				[null, 'Recibidas PDF', '5menu_reportes.php', '', ''],
				[null, 'Asignadas PDF', '10menu_reportes.php', '', ''],
				[null, 'Transferidas PDF', '6menu_reportes.php', '', '']
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
</body>

</html>