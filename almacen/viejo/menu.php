<html>

<head>
	<script language="JavaScript" src="../funciones/menu/JSCookMenu.js"></script>
	<link rel="stylesheet" href="../funciones/menu/theme.css" type="text/css">
	<script language="JavaScript" src="../funciones/menu/theme.js"></script>
	<script language="JavaScript">
		<!--
		var MenuPrincipal = [

			[null, 'Ingresos', '1ingreso.php', null, null],

			[null, 'Ventas', null, null, '',
				[null, 'Registrar', '3-ventas-sel.php', '', ''],
				[null, 'Cerrar', '4-ventas-cerrar.php', '', '']
			],

			[null, 'Consultas', null, null, '',
				[null, 'Ventas', '7consulta_ventas.php', '', '']
			],

			[null, 'Reportes', null, null, '',
				[null, 'Inventario', '8reporte_inventario.php', '', ''],
				[null, 'Ventas', '8reporte_ventas.php', '', ''],
				[null, 'Otros', '8reporte_otros.php', '', '']
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