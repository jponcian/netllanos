// JavaScript Document
	$(document).ready(function(){
		//alert("Done");
		$('#FechaOperativo').datepicker({dateFormat: 'dd/mm/yy'});
		$('#NotificacionCierre').datepicker({dateFormat: 'dd/mm/yy'});
		
		// Damos formato a la Ventana de Diálogo
		$('#wraper').dialog({
			// Indica si la ventana se abre de forma automática
			autoOpen: false,
			// Indica si la ventana es modal
			modal: true,
			// Largo
			width: 600,
			// Alto
			height: 'auto',

			closeOnEscape: false

		});

   		$(".ui-dialog-titlebar-close").hide();

		$('#iva').css('display','none');
		
		$('#nacional').on('click', function(){
			//alert(this.checked);
			if (this.checked) 
			{
				$('#wraper').dialog('open');
			} else {
				$("#nacional").prop("checked", "checked");
				$('#wraper').dialog('open');
			}
		});

		$('#btnmodal').on('click', function(){
			$('#wraper').dialog('close');			
			if ($('#txt100').val()=="0000" && $('#txt101').val()=="00000000000" && $('#txt102').val()=="00000000" && $('#txt103').val()=="0000000" && $('#txt104').val()=="00000000000000" && $('#txt105').val()=="00000" && $('#txt106').val()=="000" && $('#txt107').val()=="0000" && $('#txt108').val()=="0") 
			{
				$("#nacional").prop("checked", "");
			}
		});
		
		$('#txt100').val("0000");
		$('#txt101').val("00000000000");
		$('#txt102').val("00000000");
		$('#txt103').val("0000000");
		$('#txt104').val("00000000000000");
		$('#txt105').val("00000");
		$('#txt106').val("000");
		$('#txt107').val("0000");
		$('#txt108').val("0");
		
		$('.100').on('click', function(){
			var t1001="0";
			var t1002="0";
			var t1003="0";
			var t1004="0";
			if ($('#100_1').is(":checked"))
			{
				t1001 = "1";
			}
			if ($('#100_2').is(":checked"))
			{
				t1002 = "1";
			}
			if ($('#100_3').is(":checked"))
			{
				t1003 = "1";
			}
			if ($('#100_4').is(":checked"))
			{
				t1004 = "1";
			}
			$('#txt100').val(t1001 + t1002 + t1003 + t1004);
		});

		$('.101').on('click', function(){
			var t1011="0";
			var t1012="0";
			var t1013="0";
			var t1014="0";
			var t1015="0";
			var t1016="0";
			var t1017="0";
			var t1018="0";
			var t1019="0";
			var t10110="0";
			var t10111="0";
			if ($('#101_1').is(":checked"))
			{
				t1011 = "1";
			}
			if ($('#101_2').is(":checked"))
			{
				t1012 = "1";
			}
			if ($('#101_3').is(":checked"))
			{
				t1013 = "1";
			}
			if ($('#101_4').is(":checked"))
			{
				t1014 = "1";
			}
			if ($('#101_5').is(":checked"))
			{
				t1015 = "1";
			}
			if ($('#101_6').is(":checked"))
			{
				t1016 = "1";
			}
			if ($('#101_7').is(":checked"))
			{
				t1017 = "1";
			}
			if ($('#101_8').is(":checked"))
			{
				t1018 = "1";
			}
			if ($('#101_9').is(":checked"))
			{
				t1019 = "1";
			}
			if ($('#101_10').is(":checked"))
			{
				t10110 = "1";
			}
			if ($('#101_11').is(":checked"))
			{
				t10111 = "1";
			}
			$('#txt101').val(t1011 + t1012 + t1013 + t1014 + t1015 + t1016 + t1017 + t1018 + t1019 + t10110 + t10111);
		});

		$('.102').on('click', function(){
			var t1021="0";
			var t1022="0";
			var t1023="0";
			var t1024="0";
			var t1025="0";
			var t1026="0";
			var t1027="0";
			var t1028="0";
			if ($('#102_1').is(":checked"))
			{
				t1021 = "1";
			}
			if ($('#102_2').is(":checked"))
			{
				t1022 = "1";
			}
			if ($('#102_3').is(":checked"))
			{
				t1023 = "1";
			}
			if ($('#102_4').is(":checked"))
			{
				t1024 = "1";
			}
			if ($('#102_5').is(":checked"))
			{
				t1025 = "1";
			}
			if ($('#102_6').is(":checked"))
			{
				t1026 = "1";
			}
			if ($('#102_7').is(":checked"))
			{
				t1027 = "1";
			}
			if ($('#102_8').is(":checked"))
			{
				t1028 = "1";
			}
			$('#txt102').val(t1021 + t1022 + t1023 + t1024 + t1025 + t1026 + t1027 + t1028);
		});

		$('.103').on('click', function(){
			var t1031="0";
			var t1032="0";
			var t1033="0";
			var t1034="0";
			var t1035="0";
			var t1036="0";
			var t1037="0";
			var t1038="0";
			if ($('#103_1').is(":checked"))
			{
				t1031 = "1";
			}
			if ($('#103_2').is(":checked"))
			{
				t1032 = "1";
			}
			if ($('#103_3').is(":checked"))
			{
				t1033 = "1";
			}
			if ($('#103_4').is(":checked"))
			{
				t1034 = "1";
			}
			if ($('#103_5').is(":checked"))
			{
				t1035 = "1";
			}
			if ($('#103_6').is(":checked"))
			{
				t1036 = "1";
			}
			if ($('#103_7').is(":checked"))
			{
				t1037 = "1";
			}
			$('#txt103').val(t1031 + t1032 + t1033 + t1034 + t1035 + t1036 + t1037);
		});

		$('.104').on('click', function(){
			var t1041="0";
			var t1042="0";
			var t1043="0";
			var t1044="0";
			var t1045="0";
			var t1046="0";
			var t1047="0";
			var t1048="0";
			var t1049="0";
			var t10410="0";
			var t10411="0";
			var t10412="0";
			var t10413="0";
			var t10414="0";
			if ($('#104_1').is(":checked"))
			{
				t1041 = "1";
			}
			if ($('#104_2').is(":checked"))
			{
				t1042 = "1";
			}
			if ($('#104_3').is(":checked"))
			{
				t1043 = "1";
			}
			if ($('#104_4').is(":checked"))
			{
				t1044 = "1";
			}
			if ($('#104_5').is(":checked"))
			{
				t1045 = "1";
			}
			if ($('#104_6').is(":checked"))
			{
				t1046 = "1";
			}
			if ($('#104_7').is(":checked"))
			{
				t1047 = "1";
			}
			if ($('#104_8').is(":checked"))
			{
				t1048 = "1";
			}
			if ($('#104_9').is(":checked"))
			{
				t1049 = "1";
			}
			if ($('#104_10').is(":checked"))
			{
				t10410 = "1";
			}
			if ($('#104_11').is(":checked"))
			{
				t10411 = "1";
			}
			if ($('#104_12').is(":checked"))
			{
				t10412 = "1";
			}
			if ($('#104_13').is(":checked"))
			{
				t10413 = "1";
			}
			if ($('#104_14').is(":checked"))
			{
				t10414 = "1";
			}
			$('#txt104').val(t1041 + t1042 + t1043 + t1044 + t1045 + t1046 + t1047 + t1048 + t1049 + t10410 + t10411 + t10412 + t10413 + t10414);
		});

		$('.105').on('click', function(){
			var t1051="0";
			var t1052="0";
			var t1053="0";
			var t1054="0";
			var t1055="0";
			if ($('#105_1').is(":checked"))
			{
				t1051 = "1";
			}
			if ($('#105_2').is(":checked"))
			{
				t1052 = "1";
			}
			if ($('#105_3').is(":checked"))
			{
				t1053 = "1";
			}
			if ($('#105_4').is(":checked"))
			{
				t1054 = "1";
			}
			if ($('#105_5').is(":checked"))
			{
				t1055 = "1";
			}
			$('#txt105').val(t1051 + t1052 + t1053 + t1054 + t1055);
		});

		$('.106').on('click', function(){
			var t1061="0";
			var t1062="0";
			var t1063="0";
			if ($('#106_1').is(":checked"))
			{
				t1061 = "1";
			}
			if ($('#106_2').is(":checked"))
			{
				t1062 = "1";
			}
			if ($('#106_3').is(":checked"))
			{
				t1063 = "1";
			}
			$('#txt106').val(t1061 + t1062 + t1063);
		});

		$('.107').on('click', function(){
			var t1071="0";
			var t1072="0";
			var t1073="0";
			var t1074="0";
			if ($('#107_1').is(":checked"))
			{
				t1071 = "1";
			}
			if ($('#107_2').is(":checked"))
			{
				t1072 = "1";
			}
			if ($('#107_3').is(":checked"))
			{
				t1073 = "1";
			}
			if ($('#107_4').is(":checked"))
			{
				t1074 = "1";
			}
			$('#txt107').val(t1071 + t1072 + t1073 + t1074);
		});

		$('.108').on('click', function(){
			var t108="0";
			if (this.checked)
			{
				t108 = "1";
			}
			$('#txt108').val(t108);
		});

		$('#modificar').on('click', function(){
			var datos = $('#form1').serialize();
			//alert(datos);
			var modificacion = $('#modificacion').val();
			var verificar = $('#añoProvidencia').val() + $('#numProvidencia').val();
			//alert(datos);
			//alert(verificar + " - " + modificacion);
			if (modificacion == verificar)
			{
				$.ajax({
					url: 'modAnexo2.inc.php',
					type: 'POST',
					data: datos,
					dataType: 'json',
					success: function (r) {
						if (r.permitido == true)
						{
							if ($('#nacional').is(":checked") || $('#id').val()>0)
							{
								$.ajax({
									url: 'modAnexo2art.inc.php',
									type: 'POST',
									data: datos,
									dataType: 'json',
									success: function (r) {
										//$("#nacional").prop("checked", "");
							            $("input[type=checkbox]").each(function(){
							                $(this).prop('checked',"");
							            });            	
										$('#form1').reset();
										alert(r.mensaje);
									}
								});
							}
							else
							{
								alert(r.mensaje);
							}
						}
						else
						{
							alert(r.mensaje);
						}
					}
				});
			}
			else
			{
				alert("Ha cambiado los datos base de la busqueda (Año y Número de Providencia), presione el boton buscar nuevamente");
			}
		});

		$('#buscar').on('click', function(){
			var datos = $('#form1').serialize();
			//alert(datos);
			$.ajax({
				url: 'buscarmod.inc.php',
				type: 'POST',
				data: datos,
				dataType: 'json',
				success: function (d) {
					if (d.permitido == true)
					{
						$('#modificacion').val(d.db_año + d.db_numero);
						$('#añoProvidencia').val(d.db_año);
						$('#numProvidencia').val(d.db_numero);
						$('#Emision').val(d.db_emision);
						$('#Notificacion0').val(d.db_notificacion);
						$('#Nombre').val(d.db_nombre);
						$('#sujeto').val(d.db_nombre);
						$('#numRIF').val(d.db_rif);
						$('#Domicilio').val(d.db_domicilio);
						$('#CedFiscal').val(d.db_cedfiscal);
						$('#Fiscal').val(d.db_fiscal);
						$('#CedSuper').val(d.db_cedsuper);
						$('#Super').val(d.db_super);
						$('#Programa').val(d.db_programa);
						$('#TipoTributo').val(d.db_tributo);
						$('#CedCoord').val(d.CI_Coord);
						$('#Coord').val(d.NOM_Coord);
						$('#TlfCoord').val(d.TLF_Coord);
						$('#CantSucursal').val(d.CantSucursal);
						$('#CadenaTienda').val(d.CadenaTienda);
						$('#Actividad').val(d.Actividad);
						$('#TipoMF').val(d.TipoMF);
						$('#ModeloMF').val(d.ModeloMF);
						$('#CumpleMF').val(d.CumpleMF);
						$('#SancionesMF').val(d.SancionesMF);
						$('#MultasDF').val(d.MultasDF);
						$('#FechaOperativo').val(d.FechaOperativo);
						$('#NombreOperativo').val(d.NombreOperativo);
						$('#Clausura').val(d.Clausura);
						$('#DiasClausura').val(d.DiasClausura);
						$('#MontoSanciones').val(d.MontoSanciones);
						$('#NotificacionCierre').val(d.NotificacionCierre);
						$('#Sanciones').val(d.Sanciones);
						$('#Observaciones').val(d.Observaciones);
						$('#txt100').val(d.db_100);
						$('#txt101').val(d.db_101);
						$('#txt102').val(d.db_102);
						$('#txt103').val(d.db_103);
						$('#txt104').val(d.db_104);
						$('#txt105').val(d.db_105);
						$('#txt106').val(d.db_106);
						$('#txt107').val(d.db_107);
						$('#txt108').val(d.db_108);
						$('#Notificacion').val(d.db_notificacion);
						$('#Rif').val(d.db_rif);
						$('#Tributo').val(d.db_tributo);
						$('#NombreOperativo').val(d.db_operativo);
						var tipoTributos = $('#TipoTributo').val();
						if (tipoTributos.search("IVA")==-1)
						{
							$('#iva').css('display','none');
						}
						else
						{
							$('#iva').css('display','');
						}
						if ($('#TipoMF').val()=="NO APLICA")
						{
							 $("#ModeloMF").attr('disabled', 'disabled');
							 $("#CumpleMF").attr('disabled', 'disabled');
							 $("#SancionesMF").attr('disabled', 'disabled');
						}
						else
						{
							 $("#ModeloMF").removeAttr("disabled");
							 $("#CumpleMF").removeAttr("disabled");
							 $("#SancionesMF").removeAttr("disabled");
							 if ($("#CumpleMF").val()=="NO")
							 {
								 $("#SancionesMF").attr('disabled', 'disabled');
							 }
							 else
							 {
								 $("#SancionesMF").removeAttr("disabled");
							 }
						}
						$('#id').val(d.db_id);
						seleccionar_articulos();
					} else {
						alert(d.mensaje);
					}
				}
			});
		});

		$.fn.reset = function () {
			$(this).each (function() { this.reset(); });
			$('#txt100').val("0000");
			$('#txt101').val("00000000000");
			$('#txt102').val("00000000");
			$('#txt103').val("0000000");
			$('#txt104').val("00000000000000");
			$('#txt105').val("00000");
			$('#txt106').val("000");
			$('#txt107').val("0000");
			$('#txt108').val("0");
		}

		function seleccionar_articulos(){
			if ($('#txt100').val()=="0000" && $('#txt101').val()=="00000000000" && $('#txt102').val()=="00000000" && $('#txt103').val()=="0000000" && $('#txt104').val()=="00000000000000" && $('#txt105').val()=="00000" && $('#txt106').val()=="000" && $('#txt107').val()=="0000" && $('#txt108').val()=="0") 
			{
				$("#nacional").prop("checked", "");
			}
			else
			{
				var datos = $('#form1').serialize();
				$("#nacional").prop("checked", "checked");
				//alert(datos);
				$.ajax({
					url: 'check.inc.php',
					type: 'POST',
					data: datos,
					dataType: 'json',
					success: function (chk) {

						//ART 108
						if (chk.art_108 == 1)
						{
							$("#108_1").prop("checked", "checked");
						} else {
							$("#108_1").prop("checked", "");
						}

						//ART 100
						if (chk.art100_1=="1")
						{
							$("#100_1").prop("checked", "checked");
						} else {
							$("#100_1").prop("checked", "");
						}
						if (chk.art100_2=="1")
						{
							$("#100_2").prop("checked", "checked");
						} else {
							$("#100_2").prop("checked", "");
						}
						if (chk.art100_3=="1")
						{
							$("#100_3").prop("checked", "checked");
						} else {
							$("#100_3").prop("checked", "");
						}
						if (chk.art100_4=="1")
						{
							$("#100_4").prop("checked", "checked");
						} else {
							$("#100_4").prop("checked", "");
						}

						//ART 101
						if (chk.art101_1=="1")
						{
							$("#101_1").prop("checked", "checked");
						} else {
							$("#101_1").prop("checked", "");
						}
						if (chk.art101_2=="1")
						{
							$("#101_2").prop("checked", "checked");
						} else {
							$("#101_2").prop("checked", "");
						}
						if (chk.art101_3=="1")
						{
							$("#101_3").prop("checked", "checked");
						} else {
							$("#101_3").prop("checked", "");
						}
						if (chk.art101_4=="1")
						{
							$("#101_4").prop("checked", "checked");
						} else {
							$("#101_4").prop("checked", "");
						}
						if (chk.art101_5=="1")
						{
							$("#101_5").prop("checked", "checked");
						} else {
							$("#101_5").prop("checked", "");
						}
						if (chk.art101_6=="1")
						{
							$("#101_6").prop("checked", "checked");
						} else {
							$("#101_6").prop("checked", "");
						}
						if (chk.art101_7=="1")
						{
							$("#101_7").prop("checked", "checked");
						} else {
							$("#101_7").prop("checked", "");
						}
						if (chk.art101_8=="1")
						{
							$("#101_8").prop("checked", "checked");
						} else {
							$("#101_8").prop("checked", "");
						}
						if (chk.art101_9=="1")
						{
							$("#101_9").prop("checked", "checked");
						} else {
							$("#101_9").prop("checked", "");
						}
						if (chk.art101_10=="1")
						{
							$("#101_10").prop("checked", "checked");
						} else {
							$("#101_10").prop("checked", "");
						}
						if (chk.art101_11=="1")
						{
							$("#101_11").prop("checked", "checked");
						} else {
							$("#101_11").prop("checked", "");
						}

						//ART 102
						if (chk.art102_1=="1")
						{
							$("#102_1").prop("checked", "checked");
						} else {
							$("#102_1").prop("checked", "");
						}
						if (chk.art102_2=="1")
						{
							$("#102_2").prop("checked", "checked");
						} else {
							$("#102_2").prop("checked", "");
						}
						if (chk.art102_3=="1")
						{
							$("#102_3").prop("checked", "checked");
						} else {
							$("#102_3").prop("checked", "");
						}
						if (chk.art102_4=="1")
						{
							$("#102_4").prop("checked", "checked");
						} else {
							$("#102_4").prop("checked", "");
						}
						if (chk.art102_5=="1")
						{
							$("#102_5").prop("checked", "checked");
						} else {
							$("#102_5").prop("checked", "");
						}
						if (chk.art102_6=="1")
						{
							$("#102_6").prop("checked", "checked");
						} else {
							$("#102_6").prop("checked", "");
						}
						if (chk.art102_7=="1")
						{
							$("#102_7").prop("checked", "checked");
						} else {
							$("#102_7").prop("checked", "");
						}
						if (chk.art102_8=="1")
						{
							$("#102_8").prop("checked", "checked");
						} else {
							$("#102_8").prop("checked", "");
						}

						//ART 103
						if (chk.art103_1=="1")
						{
							$("#103_1").prop("checked", "checked");
						} else {
							$("#103_1").prop("checked", "");
						}
						if (chk.art103_2=="1")
						{
							$("#103_2").prop("checked", "checked");
						} else {
							$("#103_2").prop("checked", "");
						}
						if (chk.art103_3=="1")
						{
							$("#103_3").prop("checked", "checked");
						} else {
							$("#103_3").prop("checked", "");
						}
						if (chk.art103_4=="1")
						{
							$("#103_4").prop("checked", "checked");
						} else {
							$("#103_4").prop("checked", "");
						}
						if (chk.art103_5=="1")
						{
							$("#103_5").prop("checked", "checked");
						} else {
							$("#103_5").prop("checked", "");
						}
						if (chk.art103_6=="1")
						{
							$("#103_6").prop("checked", "checked");
						} else {
							$("#103_6").prop("checked", "");
						}
						if (chk.art103_7=="1")
						{
							$("#103_7").prop("checked", "checked");
						} else {
							$("#103_7").prop("checked", "");
						}

						//ART 104
						if (chk.art104_1=="1")
						{
							$("#104_1").prop("checked", "checked");
						} else {
							$("#104_1").prop("checked", "");
						}
						if (chk.art104_2=="1")
						{
							$("#104_2").prop("checked", "checked");
						} else {
							$("#104_2").prop("checked", "");
						}
						if (chk.art104_3=="1")
						{
							$("#104_3").prop("checked", "checked");
						} else {
							$("#104_3").prop("checked", "");
						}
						if (chk.art104_4=="1")
						{
							$("#104_4").prop("checked", "checked");
						} else {
							$("#104_4").prop("checked", "");
						}
						if (chk.art104_5=="1")
						{
							$("#104_5").prop("checked", "checked");
						} else {
							$("#104_5").prop("checked", "");
						}
						if (chk.art104_6=="1")
						{
							$("#104_6").prop("checked", "checked");
						} else {
							$("#104_6").prop("checked", "");
						}
						if (chk.art104_7=="1")
						{
							$("#104_7").prop("checked", "checked");
						} else {
							$("#104_7").prop("checked", "");
						}
						if (chk.art104_8=="1")
						{
							$("#104_8").prop("checked", "checked");
						} else {
							$("#104_8").prop("checked", "");
						}
						if (chk.art104_9=="1")
						{
							$("#104_9").prop("checked", "checked");
						} else {
							$("#104_9").prop("checked", "");
						}
						if (chk.art104_10=="1")
						{
							$("#104_10").prop("checked", "checked");
						} else {
							$("#104_10").prop("checked", "");
						}
						if (chk.art104_11=="1")
						{
							$("#104_11").prop("checked", "checked");
						} else {
							$("#104_11").prop("checked", "");
						}
						if (chk.art104_12=="1")
						{
							$("#104_12").prop("checked", "checked");
						} else {
							$("#104_12").prop("checked", "");
						}
						if (chk.art104_13=="1")
						{
							$("#104_13").prop("checked", "checked");
						} else {
							$("#104_13").prop("checked", "");
						}
						if (chk.art104_14=="1")
						{
							$("#104_14").prop("checked", "checked");
						} else {
							$("#104_14").prop("checked", "");
						}

						//ART 105
						if (chk.art105_1=="1")
						{
							$("#105_1").prop("checked", "checked");
						} else {
							$("#105_1").prop("checked", "");
						}
						if (chk.art105_2=="1")
						{
							$("#105_2").prop("checked", "checked");
						} else {
							$("#105_2").prop("checked", "");
						}
						if (chk.art105_3=="1")
						{
							$("#105_3").prop("checked", "checked");
						} else {
							$("#105_3").prop("checked", "");
						}
						if (chk.art105_4=="1")
						{
							$("#105_4").prop("checked", "checked");
						} else {
							$("#105_4").prop("checked", "");
						}
						if (chk.art105_5=="1")
						{
							$("#105_5").prop("checked", "checked");
						} else {
							$("#105_5").prop("checked", "");
						}

						//ART 106
						if (chk.art106_1=="1")
						{
							$("#106_1").prop("checked", "checked");
						} else {
							$("#106_1").prop("checked", "");
						}
						if (chk.art106_2=="1")
						{
							$("#106_2").prop("checked", "checked");
						} else {
							$("#106_2").prop("checked", "");
						}
						if (chk.art106_3=="1")
						{
							$("#106_3").prop("checked", "checked");
						} else {
							$("#106_3").prop("checked", "");
						}

						//ART 107
						if (chk.art107_1=="1")
						{
							$("#107_1").prop("checked", "checked");
						} else {
							$("#107_1").prop("checked", "");
						}
						if (chk.art107_2=="1")
						{
							$("#107_2").prop("checked", "checked");
						} else {
							$("#107_2").prop("checked", "");
						}
						if (chk.art107_3=="1")
						{
							$("#107_3").prop("checked", "checked");
						} else {
							$("#107_3").prop("checked", "");
						}
						if (chk.art107_4=="1")
						{
							$("#107_4").prop("checked", "checked");
						} else {
							$("#107_4").prop("checked", "");
						}


					}
				});
			}
		}

	});