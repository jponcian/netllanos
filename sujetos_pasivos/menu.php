<html>

<head>
	<script language="JavaScript" src="../funciones/menu/JSCookMenu.js"></script>
	<link rel="stylesheet" href="../funciones/menu/theme.css" type="text/css">
	<script language="JavaScript" src="../funciones/menu/theme.js"></script>
	<script language="JavaScript">
		var MenuPrincipal = [

			/*[null,'Planillas',null,null,'',
				[null,'Ingreso','incluir_planillas.php','',''],
				[null,'Eliminar','eliminar_planillas.php','',''],
				[null,'Ingreso Txt','carga_colector.php','','']
			],*/

			[null, 'Providencia', null, null, '',
				[null, 'Registrar', 'registrar_providencia.php', '', ''],
				[null, 'Modificar', 'modificar_providencia.php', '', ''],
				[null, 'Reimprimir', '9_providencia_reimprimir.php', '', ''],
				[null, 'Anular', 'anular_providencia.php', '', '']
			],

			[null, 'Expediente', null, null, '',
				//[null,'Contribuyentes','incluircontribuyente.php','',''],
				[null, 'Registrar', '1registrar_expediente.php', '', ''],
				[null, 'Modificar', '2modificar_expediente.php', '', ''],
				[null, 'Concluir', '18_expediente_concluir.php', '', ''],
				[null, 'Reversar', '19_expediente_reversar.php', '', ''],
				[null, 'Anular', 'anular_expediente.php', '', '']
			],

			[null, 'Sanciones', null, null, '',
				[null, 'Multas', '16_incluir_sancion_sel_pro.php', '', ''],
				[null, 'Intereses', '17_incluir_interes_sel_pro.php', '', ''],
				[null, 'Intereses Complementarios', '3_incluir_interes_sel_pro.php', '', '']
			],

			[null, 'Resolucion', null, null, '',
				[null, 'Aprobar', '20_expediente_aprobar.php', '', ''],
				[null, 'Imprimir', '21_imprimir_resolucion.php', '', ''],
				[null, 'Transferir', '22_expediente_transferir.php', '', '']
			],

			[null, 'Coordinador', null, null, '',
				[null, 'Nueva Resoluci&oacute;n', '26_nueva_resolucion_sel_pro.php', '', ''],
				[null, 'Reversar Expediente', '27_reversar_expediente.php', '', '']
			],

			[null, 'Actas de Cobro', null, null, '',
				[null, 'Registrar', '4_acta_registrar.php', '', ''],
				[null, 'Modificar', '5_acta_modificar.php', '', ''],
				[null, 'Imprimir', '6_acta_reimprimir.php', '', '']
			],

			[null, 'Consultas', null, null, '',
				/*[null,'Listado Extemporaneos','1_listado_extemporaneos.php','',''],*/
				[null, 'Historial Contribuyente', '26cons_estadocta.php', '', '']

			],

			[null, 'Formatos', null, null, '',
				[null, 'Autorizaciones', '29imprimir_autorizaciones.php', '', ''],
				[null, 'Portada / Sanciones', '28reimprimir_portada.php', '', '']
			],

			[null, 'Reportes', null, null, '',
				/*[null,'Control Bancario','23-1rep_banco_planillas.php','',''],
				[null,'Planillas de Pago','23-2rep_planillas_pago.php','',''],*/
				[null, 'Providencias', '7bmenu_reportes.php', '', ''],
				[null, 'Resoluciones', '7amenu_reportes.php', '', ''],
				[null, 'Transferidas Liquidacion', '25_transferidas_liq.php', '', '']
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