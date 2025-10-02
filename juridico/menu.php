<html>

<head>
	<script language="JavaScript" src="../funciones/menu/JSCookMenu.js"></script>
	<link rel="stylesheet" href="../funciones/menu/theme.css" type="text/css">
	<script language="JavaScript" src="../funciones/menu/theme.js"></script>
	<script language="JavaScript">
		<!--
		var MenuPrincipal = [
			[null, 'Gesti�n Coordinador', null, null, '',
				[null, 'Recepci�n Recurso', '1recepcion_recurso.php', '', ''],
				[null, 'Asignar Ponente', '2menu_incluir_ponente.php', '', ''],
				[null, 'Aprobar Recurso', '5_expediente_aprobar.php', '', ''],
				[null, 'Transferir Recurso', '6_expediente_transferir.php', '', '']
			],
			[null, 'Gesti�n Ponente', null, null, '',
				[null, 'Gestion Recurso', '3menu_gestion_ponente.php', '', ''],
				[null, 'Concluir Recurso', '4menu_conclusion_ponente.php', '', '']
			],

			[null, 'Consultas', null, null, '',
				[null, 'Actos Notificados', '8actos_notificados.php', '', '']
			],

			[null, 'Reportes', null, null, '',
				[null, 'Expedientes PDF', '', '', ''],
				[null, 'Transferidas PDF', '7_transferidas_liq.php', '', '']
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