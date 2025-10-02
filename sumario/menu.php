<html>

<head>
	<script language="JavaScript" src="../funciones/menu/JSCookMenu.js"></script>
	<link rel="stylesheet" href="../funciones/menu/theme.css" type="text/css">
	<script language="JavaScript" src="../funciones/menu/theme.js"></script>
	<script language="JavaScript">
		<!--
		var MenuPrincipal = [
			[null, 'Gesti&oacute;n Coordinador', null, null, '',
				[null, 'Importar', '1importar_expediente.php', '', ''],
				[null, 'Recepci&oacute;n Descargo', '2menu_asignar_descargo.php', '', ''],
				[null, 'Asignar Ponente', '3menu_incluir_ponente.php', '', ''],
				[null, 'Aprobar', '6aprobar_expediente.php', '', ''],
				[null, 'Transferir Liquidacion', '8expediente_transferir.php', '', '']
			],
			[null, 'Gesti&oacute;n Ponente', null, null, '',
				[null, 'Gestion Expediente', '4menu_gestion_ponente.php', '', ''],
				[null, 'Incluir Pago', '4.2menu_incluir_pago.php', '', ''],
				[null, 'Registrar Ajuste', '4.3menu_ajustes_ponente.php', '', ''],
				[null, 'Concluir Expediente', '5menu_conclusion_ponente.php', '', '']
			],

			[null, 'Sanciones', null, null, '',
				[null, 'Multas', '16_incluir_sancion_sel_pro.php', '', ''],
				[null, 'Manuales', '10ingresar_liq_manuales_sel.php', '', '']
				/*,
						[null,'Intereses','17_incluir_interes_sel_pro.php','','']*/
			],
			/*[null,'Consultas',null,null,'',
				[null,'Status Contribuyente','0status_contribuyente.php','','']
			],*/
			[null, 'Reportes', null, null, '',
				[null, 'Fiscalizaciones(Excel)', '7reporte_fiscalizaciones.php', '', ''],
				[null, 'Recibidos PDF', '9menu_reportes.php', '', ''],
				[null, 'Transferidas PDF', '9transferidas_liq.php', '', '']
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