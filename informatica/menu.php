<html>

<head>
	<script language="JavaScript" src="../funciones/menu/JSCookMenu.js"></script>
	<link rel="stylesheet" href="../funciones/menu/theme.css" type="text/css">
	<script language="JavaScript" src="../funciones/menu/theme.js"></script>
	<script language="JavaScript">
		<!--
		var MenuPrincipal = [

			[null, 'Funcionarios', null, null, '',
				[null, 'Gestion', '4_gestion_datos_funcionario.php', '', ''],
				[null, 'Nuevo Ingreso', '5_nuevos.php', '1', '']
			],

			[null, 'Sistema', null, null, '',
				[null, 'Roles', '1_gestion_roles.php', '', ''],
				[null, 'Accesos', '2_gestion_accesos.php', '', ''],
				[null, 'Origenes', '7_gestion_origenes.php', '', '']
			],

			[null, 'Base de Datos', null, null, '',
				[null, 'Importar Planillas', '6_importar_planillas.php', '', '']
			],

			[null, 'Consultas', null, null, '',
				[null, 'Perfil por Funcionario', '3_gestion_accesos_completos.php', '', ''],
				[null, 'Bienes en Reparacion', '10_soporte_tecnico.php', '', ''],
				[null, 'Localizar Bien Nacional', '9_historial_bien.php', '', '']
			],

			[null, 'Reportes', null, null, '',
				[null, 'Inventario Bienes', '8_menu_reportes.php', '', '']
			],

			[null, 'Reversos', null, null, '',
				[null, 'Expedientes', '../fiscalizacion/19_providencia_reversar.php', '', '']
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
			margin-top: -40px;
		}
	</style>
</head>

<body style="background: transparent !important;">
	<p>&nbsp; </p>
	<div id="MenuAplicacion" align="center">
	</div>
	<p>
		<script language="JavaScript">
			<!--
			cmDraw('MenuAplicacion', MenuPrincipal, 'hbr', cmThemeGray, 'ThemeGray');
			-->
		</script>
	</p>
	<p>&nbsp;</p>
</body>

</html>