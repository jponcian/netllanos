// JavaScript Document
$(document).ready(function() {

	//alert("JQeury is done now");

	//PARA EVITAR ERRORES DE CACHE IE-11
	$.ajaxSetup({ cache: false });

	$('#fechainicio').datepicker({dateFormat: 'dd/mm/yy'});
	$('#fechafinal').datepicker({dateFormat: 'dd/mm/yy'});
	$('#formimpmemorando').hide();
	$('#formmodmemorando').hide();
	$('#cargarmodificacion').hide();
	$('#formconsultaExp').hide();
	$('#formconsultaAct').hide();

	//Cargamo el menu de Salida de Expedientes
	$('#inc_exp').click(function(){
		//alert("Hizo click en Salida de Expedientes");
		$('#formsalida').show();
		$('#formexp').hide();
		$("#formimpacta").hide();
		$('#formmodmemorando').hide();
		$('#formimpmemorando').hide();
		$('#cargarmodificacion').hide();
		$('#formconsultaExp').hide();
		$('#formconsultaAct').hide();
		$('#barmenu').css("height",$('#contenedor').css("height"));
	});

	$('#cs_exp').on('click', function(){
		$('#formconsultaExp').show();
		$('#formsalida').hide();
		$('#formexp').hide();
		$("#formimpacta").hide();
		$('#formmodmemorando').hide();
		$('#formimpmemorando').hide();
		$('#cargarmodificacion').hide();
		$('#formconsultaAct').hide();
		$('#barmenu').css("height",$('#contenedor').css("height"));		
	});

	$('#cs_acta').on('click', function(){
		$('#formconsultaAct').show();
		$('#formconsultaExp').hide();
		$('#formsalida').hide();
		$('#formexp').hide();
		$("#formimpacta").hide();
		$('#formmodmemorando').hide();
		$('#formimpmemorando').hide();
		$('#cargarmodificacion').hide();
		$('#barmenu').css("height",$('#contenedor').css("height"));		
	});
	
	//Seleccionar tipo de expedientes
	$('#tipoexp').on('change', function(){
		//alert("cambio la lista");
		$('#resultado').empty();
		$('#destino').empty();
		$("#especial").prop("checked", "");
		switch($('#tipoexp').val()) {
			case "VDF":
				$('<option value=""></option>').appendTo('#resultado');
				$('<option value="Conformes">Conformes</option>').appendTo('#resultado');
				$('<option value="Sancionados">Sancionados</option>').appendTo('#resultado');
				break;
			case "Sucesiones":
				$('<option value=""></option>').appendTo('#resultado');
				$('<option value="Conformes">Conformes</option>').appendTo('#resultado');
				$('<option value="Allanados">Allanados</option>').appendTo('#resultado');
				$('<option value="Allanados Parcialmente">Allanados Parcialmente</option>').appendTo('#resultado');
				$('<option value="No Allanados">No Allanados</option>').appendTo('#resultado');
				break;
			case "Investigaciones":
				$('<option value=""></option>').appendTo('#resultado');
				$('<option value="Conformes">Conformes</option>').appendTo('#resultado');
				$('<option value="Sancionados">Sancionados</option>').appendTo('#resultado');
				$('<option value="Allanados">Allanados</option>').appendTo('#resultado');
				$('<option value="Allanados Parcialmente">Allanados Parcialmente</option>').appendTo('#resultado');
				$('<option value="No Allanados">No Allanados</option>').appendTo('#resultado');
				break;
			case "":
				break;
			default:
				//default code block
		}
	});

	
	
	$('#resultado').on('change', function(){
		//alert("cambio la lista");
		$('#destino').empty();
		$("#especial").prop("checked", "");
		var destino = 0;
		if ($('#tipoexp').val() == "VDF" && $('#resultado').val() == "Conformes") { destino = 1; $('#destino3').val(1); $("#especial").attr("disabled", "disabled"); $('#estatus').val(11); $("#chkplazo").attr("disabled", true);}; 
		if ($('#tipoexp').val() == "VDF" && $('#resultado').val() == "Sancionados") { destino = 2; $('#destino3').val(2); $("#especial").removeAttr("disabled"); $('#estatus').val(12); $("#chkplazo").attr("disabled", false);}; 
		if ($('#tipoexp').val() == "Sucesiones" && $('#resultado').val() == "Conformes") { destino = 3; $('#destino3').val(3); $("#especial").attr("disabled", "disabled"); $('#estatus').val(21);  $("#chkplazo").attr("disabled", true);}; 
		if ($('#tipoexp').val() == "Sucesiones" && $('#resultado').val() == "Allanados") { destino = 2; $('#destino3').val(2);  $("#especial").attr("disabled", "disabled"); $('#estatus').val(23);  $("#chkplazo").attr("disabled", false);}; 
		if ($('#tipoexp').val() == "Sucesiones" && $('#resultado').val() == "No Allanados") { destino = 4; $('#destino3').val(4); $("#especial").attr("disabled", "disabled"); $('#estatus').val(25); $("#chkplazo").attr("disabled", true);}; 
		if ($('#tipoexp').val() == "Sucesiones" && $('#resultado').val() == "Allanados Parcialmente") { destino = 5; $('#destino3').val(2); $("#especial").attr("disabled", "disabled"); $('#estatus').val(24); $("#chkplazo").attr("disabled", false);}; 
		if ($('#tipoexp').val() == "Investigaciones" && $('#resultado').val() == "Conformes") { destino = 1; $('#destino3').val(1); $("#especial").attr("disabled", "disabled"); $('#estatus').val(31); $("#chkplazo").attr("disabled", true);}; 
		if ($('#tipoexp').val() == "Investigaciones" && $('#resultado').val() == "Sancionados") { destino = 2; $('#destino3').val(2); $("#especial").removeAttr("disabled"); $('#estatus').val(32);  $("#chkplazo").attr("disabled", false);}; 
		if ($('#tipoexp').val() == "Investigaciones" && $('#resultado').val() == "Allanados") { destino = 2; $('#destino3').val(2); $("#especial").removeAttr("disabled"); $('#estatus').val(33);  $("#chkplazo").attr("disabled", false);}; 
		if ($('#tipoexp').val() == "Investigaciones" && $('#resultado').val() == "No Allanados") { destino = 4; $('#destino3').val(4); $("#especial").attr("disabled", "disabled"); $('#estatus').val(35); $("#chkplazo").attr("disabled", true);}; 
		if ($('#tipoexp').val() == "Investigaciones" && $('#resultado').val() == "Allanados Parcialmente") { destino = 5; $('#destino3').val(2); $("#especial").removeAttr("disabled"); $('#estatus').val(34);  $("#chkplazo").attr("disabled", false);};
		switch(destino) {
			case 1:
				$('#destino').val("Tramitacion");
				$('#destino2').val("Tramitacion");
				break;
			case 2:
				$('#destino').val("Recaudacion - Liquidacion");
				$('#destino2').val("Recaudacion - Liquidacion");

				if($("#fpretencion").is(':checked')) {  
					$('#destino').val("Sumario");
					$('#destino2').val("Sumario");
					$('#estatus').val(55);
		            $('#especial').attr("disabled", true);
		        } else {  
					$('#destino').val("Recaudacion - Liquidacion");
					$('#destino2').val("Recaudacion - Liquidacion");
					//$('#estatus').val(33);
					$('#especial').attr("disabled", false);
		        }

		        if ($('#resultado').val() != "Allanados")
		        {
		        	$("#fpretencion").prop("checked", "");
		        }

				break;

			case 3:
				$('#destino').val("Recaudacion - Sucesiones");
				$('#destino2').val("Recaudacion - Sucesiones");
				break;
			case 4:
				$('#destino').val("Sumario");
				$('#destino2').val("Sumario");
				break;
			case 5:
				$('#destino').val("Sumario/Recaudacion - Liquidacion");
				$('#destino2').val("Sumario/Recaudacion - Liquidacion");

				if($("#fpretencion").is(':checked')) {  
					$('#destino').val("Sumario");
					$('#destino2').val("Sumario");
					$('#estatus').val(65);
		            $('#especial').attr("disabled", true);
		        } else {  
					$('#destino').val("Sumario/Recaudacion - Liquidacion");
					$('#destino2').val("Sumario/Recaudacion - Liquidacion");
					$('#estatus').val(34);
					$('#especial').attr("disabled", false);
		        }

				break;
		}
	});

	$('#especial').on('change', function(){
		if($("#especial").is(':checked')) {  
            if ($('#destino').val()=="Sumario/Recaudacion - Liquidacion")
			{
				$('#destino').val("Sumario/Sujetos Pasivos Especiales"); 
			} else {

				if($("#fpretencion").is(':checked')) {  
					$('#destino').val("Sumario");
					$('#destino2').val("Sumario");
					$('#estatus').val(35);
		        } else {  
					$('#destino').val("Sujetos Pasivos Especiales");
					$('#destino2').val("Sujetos Pasivos Especiales");
					$('#estatus').val(43);
		        }
			}
			if($('#destino3').val()==2) { $('#estatus').val(42); }
			if($('#destino3').val()==3) { $('#estatus').val(43); }
			if($('#destino3').val()==4) { $('#estatus').val(44); }
        } else {  
            $('#destino').val($('#destino2').val());;  
			$('#estatus').val($('#destino3').val());
        }
	});

	$('#fiscapuntual').on('change', function(){
		if($("#fiscapuntual").is(':checked')) {  
            $('#fpretencion').attr("disabled", false);
        } else {  
            $('#fpretencion').attr("disabled", true);
        }
	});

	$('#fpretencion').on('change', function(){
		$('#resultado').val("");
		$('#destino').val("");
		if($("#fpretencion").is(':checked')) {
			$('#resultado').val("Allanados");  
            $('#especial').attr("disabled", true);
			$("#especial").prop("checked", "");
			$('#destino').val("Sumario");
			$('#destino2').val("Sumario");
			$('#estatus').val(55);
        } else {  
            $('#especial').attr("disabled", false);
        }
	});

	$('#chkplazo').on('change', function(){
		if($("#chkplazo").is(':checked')) {  
            $('#plazo25').attr("disabled", false);
            $('#chknoti').attr("disabled", true);
        } else {  
            $('#plazo25').attr("disabled", true);
            $('#chknoti').attr("disabled", false);
        }
	});
	
	$('#chknoti').on('change', function(){
		if($("#chknoti").is(':checked')) {  
            $('#chkplazo').attr("disabled", true);
        } else {  
            $('#chkplazo').attr("disabled", false);
        }
	});

	$('#btnAGREGAREXP').click(function(){
		var nummemo = $('#memo').val(); 
		var fechamemo =  $('#fechamemo').val();
		var tipo = $('#tipoexp').val();
		var resultado =  $('#resultado').val();
		var destino =  $('#destino').val();
		var clausura = $('#clausura').val();
		var añoprovidencia = $('#añoprovidencia').val();
		var numprovidencia = $('#numprovidencia').val();
		var folio = $('#folio').val();
		var fp = 0;
		var estatus = $('#estatus').val();
		var admin = $('#administrador').val();
		var sector = $('#sector').val();
		var chkplazo = 0;
		var noti = 0;

		if($("#chknoti").is(':checked')) {  
            noti = 1;  
        } else {  
            noti = 0;  
        }

		if($("#fpretencion").is(':checked')) {  
            ret = 1;  
        } else {  
            ret = 0;  
        }

		if($("#chkplazo").is(':checked'))
		{
			if($("#especial").is(':checked')) {  
	            esp = 1;  
	        } else {  
	            esp = 0;  
	        }
			estatus = CulminarLapsoEspera25dias(esp);
			chkplazo = 1;
		}
		else
		{
			chkplazo = 0;
		}

		if($("#fiscapuntual").is(':checked')) {  
            fp = 1;  
        } else {  
            fp = 0;  
        }

		if($("#especial").is(':checked')) {  
            esp = 1;  
        } else {  
            esp = 0;  
        }

		//alert(noti);
				
		if (nummemo==0 || fechamemo=="" || tipo=="" || resultado=="" || destino=="" || añoprovidencia=="" || numprovidencia=="")
		{
			jAlert("Existen datos requeridos vacios, por favor verifique","CAMPOS REQUERIDOS VACIOS");
		}
		else
		{
			/*
			$("#registroexp").load('expedientes/agregarexp.php?'+datos, function(){
				$('#barmenu').css("height",$('#contenedor').css("height"));
			});
			*/
			if (noti == 0 && chkplazo == 0 && (resultado == "Sancionados" || resultado == "Allanados"))
			{
				jAlert('Se requiere que marque una opcion "Enviar a Notificación" ó "Remitir a Cobro"',"VALIDAR ENVIO A NOTIFICACION/COBRO");
			}
			else
			{
				var datos = 'nummemo='+nummemo+'&fechamemo='+fechamemo+'&tipo='+tipo+'&resultado='+escape(resultado)+'&destino='+escape(destino)+'&clausura='+clausura+'&annoprovidencia='+añoprovidencia+'&numprovidencia='+numprovidencia+'&folio='+folio+'&fp='+fp+'&estatus='+estatus+'&admin='+admin+'&sector='+sector+'&esp='+esp+'&chkplazo='+chkplazo+'&ret='+ret+'&noti='+noti;
				//alert(datos");
				$("#cargando").css("display", "inline");
				$('#btnAGREGAREXP').attr("disabled", true);
		        $.ajax({  
		            url: 'expedientes/agregarexp.php',  
					type: "GET",
					data: datos,
		            success: function(data) {  
		                $('#registroexp').html(data);
		                bloquear();
		                $('#barmenu').css("height",$('#contenedor').css("height"));
						$('#btnAGREGAREXP').attr("disabled", false);
						$("#cargando").css("display", "none");
		            }  
		        });  
	    	}
		}
		$("#registroexp").fadeIn("slow");
	});

	function bloquear() {
		if ($('#bloquedor').val()==1)
		{
			$('#tipoexp option:not(:selected)').attr('disabled',true);
			$('#clausura option:not(:selected)').attr('disabled',true);
			$('#resultado option:not(:selected)').attr('disabled',true);
			$('#especial').prop('disabled', true);
			$('#fiscapuntual').prop('disabled', true);
		} else {
			$('#tipoexp option:not(:selected)').attr('disabled',false);
			$('#clausura option:not(:selected)').attr('disabled',false);
			$('#resultado option:not(:selected)').attr('disabled',false);
			$("#especial").removeAttr('disabled');
			$("#fiscapuntual").removeAttr('disabled');			
		}
	}

	function ActualizarTamañoVentana() {
		$('#barmenu').css("height",$('#contenedor').css("height"));
	}
	
	$('#genera_memo').on('click',function(){
		var nummemo = $('#memo').val(); 
		var fechamemo =  $('#fechamemo').val();
		var tipo = $('#tipoexp').val();
		var resultado =  $('#resultado').val();
		var destino =  $('#destino').val();
		var clausura = $('#clausura').val();
		var añoprovidencia = $('#añoprovidencia').val();
		var numprovidencia = $('#numprovidencia').val();
		var folio = $('#folio').val();
		var fp = 0;
		var estatus = $('#estatus').val();
		var sector = $('#sector').val();
		var noti = 0;

		if($("#chknoti").is(':checked')) {  
            noti = 1;  
        } else {  
            noti = 0;  
        }

		if($("#fiscapuntual").is(':checked')) {  
            fp = 1;  
        } else {  
            fp = 0;  
        }


		if($("#especial").is(':checked')) {  
            esp = 1;  
        } else {  
            esp = 0;  
        }

		if($("#fpretencion").is(':checked')) {  
            ret = 1;  
        } else {  
            ret = 0;  
        }

		var datos = 'nummemo=' + nummemo + '&sector=' + sector + '&ret=' + ret + '&noti=' + noti;
		//alert(datos);
				
		if (nummemo==0 || fechamemo=="" || tipo=="" || resultado=="" || destino=="" || añoprovidencia=="" || numprovidencia=="")
		{
			jAlert("Existen datos requeridos vacios, por favor verifique","CAMPOS REQUERIDOS VACIOS");
		}
		else
		{
			//alert("Click en generar memo");
			$.ajax({
				url: "expedientes/generarmemo.php",
				type: "POST",
				data: datos,
				dataType:"json",
				success: function (r) {
					if(r.generado == true)
					{
						$('#imprimir').val(1);
						$('#genera_memo').attr("disabled", true);
						$('#nuevo_memo').attr("disabled", false);
						$('#imp_memo').attr("disabled", false);
					}
					$('#txtResultadoExp').html(r.mensaje);
					$('#btnAGREGAREXP').attr("disabled", true);
					$('#barmenu').css("height",$('#contenedor').css("height"));
				}
			});	
		}
	});

	$('#imp_memo').on('click', function(){
		var anno = (new Date).getFullYear();
		var num = $('#memo').val();
		var sector = $('#sector').val();
		var noti = 0;

		if($("#chknoti").is(':checked')) {  
            noti = 1;  
        } else {  
            noti = 0;  
        }

		var datos = "anno=" + anno + '&num=' + num + '&sector=' + sector + '&noti=' + noti;
		//alert(datos);
		window.open("expedientes/rptmemo.php?" + datos,"mywindows");
		$('#memo_impreso').val(1);
		return false;
	});
	
	$('#nuevo_memo').on('click', function(){
		//location.reload();
		var impreso = $('#memo_impreso').val();
		var numero = $('#memo').val();
		var num = parseInt(numero) + 1;
		var noti = 0;

		if($("#chknoti").is(':checked')) {  
            noti = 1;  
        } else {  
            noti = 0;  
        }

		if (impreso == 1)
		{
			$('#salidaExpediente').reset();
			$('#bloquedor').val(0);
			bloquear();
			$('#registroexp').empty();
			$('#memo').val(num);
			$('#memo_impreso').val(0);
			$('#nuevo_memo').attr("disabled", true);
			$('#imp_memo').attr("disabled", true);
			$('#genera_memo').attr("disabled", false);
			$('#btnAGREGAREXP').attr("disabled", false);
			$('#txtResultadoExp').html("");
		} else {
			jAlert("Antes de Generar un Nuevo Memorando, por favor debe imprimir el memorando actual","IMPRIMIR MEMORANDO");
		}
		$('#barmenu').css("height",$('#contenedor').css("height"));
	});

	$('#rimp_exp').click(function(){
		//alert("Hizo click 111");
		$('#formimpmemorando').show();
		$('#formmodmemorando').hide();
		$('#formimpacta').hide();
		$("#formexp").hide();
		$('#formsalida').hide();
		$('#cargarmodificacion').hide();
		$('#formconsultaExp').hide();
		$('#formconsultaAct').hide();
		ActualizarTamañoVentana();
	});

	$('#mod_exp').click(function(){
		//alert("Hizo click 1112");
		$('#formmodmemorando').show();
		$('#formimpmemorando').hide();
		$('#formimpacta').hide();
		$("#formexp").hide();
		$('#formsalida').hide();
		$('#cargarmodificacion').hide();
		$('#formconsultaExp').hide();
		$('#formconsultaAct').hide();
		ActualizarTamañoVentana();
	});


	$('#cargar_memo').click(function(){
		var memo = $('#nummemoimp').val();
		var anno = $('#añomemoimp').val();
		var sector = $('#sector').val();
		var noti = 0;

		if($("#chknoti").is(':checked')) {  
            noti = 1;  
        } else {  
            noti = 0;  
        }

		var datos = "memo=" + memo + '&anno=' + anno + '&sector=' + sector + '&noti=' + noti;
		//alert(datos);
		$.ajax({
		   url: "expedientes/cargarmemo.php",
		   type: "POST",
		   data: datos,
		   dataType:"json",
		   success: function (r) {
				$('#txtResultadoimpMemo').html(r.mensaje);
				if (r.permitido==true)
				{
					$('#reimp_memo').attr("disabled", false);
					$('#idmemoimp').val(r.ID);
					$('#reimpnum').val($('#nummemoimp').val());
					$('#reimpaño').val($('#añomemoimp').val());
				} else {
					$('#reimp_memo').attr("disabled", true);
					$('#idmemoimp').val(0);
					$('#reimpnum').val(0);
					$('#reimpaño').val(0);					
				}
				$('#barmenu').css("height",$('#contenedor').css("height"));
		   }
		});
	});

	$('#reimp_memo').on('click', function(){
		var permitido = $('#idmemoimp').val(); 
		var num = $('#reimpnum').val();
		var anno = $('#reimpaño').val();
		var sector = $('#sector').val();
		var noti = 0;

		if($("#chknoti").is(':checked')) {  
            noti = 1;  
        } else {  
            noti = 0;  
        }

		//alert(noti);
		var datos = "num=" + num + '&anno=' + anno + '&sector=' + sector + '&noti=' + noti;
		window.open("expedientes/rptmemo.php?" + datos,"mywindows");
		$('#memo_impreso').val(1);
		return false;
	});

	$('#nummemoimp').on('keypress', function(){
		if ($('#nummemoimp').val() != $('#reimpnum').val())
		{
			$('#memo_impreso').val(0);
			$('#txtResultadoimpMemo').html("");
			$('#reimp_memo').attr("disabled", true);
			$('#idmemoimp').val(0);
			$('#reimpnum').val(0);
			$('#reimpaño').val(0);
		}
	});

	$('#nummemoimp').on('change', function(){
		if ($('#nummemoimp').val() != $('#reimpnum').val())
		{
			$('#memo_impreso').val(0);
			$('#txtResultadoimpMemo').html("");
			$('#reimp_memo').attr("disabled", true);
			$('#idmemoimp').val(0);
			$('#reimpnum').val(0);
			$('#reimpaño').val(0);
		}
	});

	$('#precargar_memo').on('click', function(){
		var memo = $('#nummemomod').val();
		var anno = $('#añomemomod').val();
		var sector = $('#sector').val();
		var datos = "memo=" + memo + '&anno=' + anno + '&sector=' + sector;
		//alert(datos);
		$.ajax({
		   url: "expedientes/cargarmemomod.php",
		   type: "POST",
		   data: datos,
		   dataType:"json",
		   success: function (r) {
				$('#txtResultadoimpModMemo').html(r.mensaje);
				if (r.permitido==true)
				{
					//alert(r.ID);
					$('#mod_memo').attr("disabled", false);
					$('#idmemomod').val(r.ID);
					$('#valorid').val(parseInt(r.ID) + 1);
					//alert($('#valorid').val());
					$('#modmpnum').val($('#nummemomod').val());
					$('#modpaño').val($('#añomemomod').val());
				} else {
					$('#mod_memo').attr("disabled", true);
					$('#idmemomod').val(0);
					$('#modmpnum').val(0);
					$('#modpaño').val(0);					
				}
				$('#barmenu').css("height",$('#contenedor').css("height"));
		   }
		});
	});

	$('#btn_consulta').on('click', function(){
		var accion = 0;
		var admin = $('#administrador').val();
		var sede = $('#sede').val();
		var rif = $('#num_Rif').val();
		if ($("#chkprovidencia").is(':checked')) { accion = 1}
		if ($("#chkmemorando").is(':checked')) { accion = 2}
		if ($("#chkdestino").is(':checked')) { accion = 3}
		if ($("#chkrif").is(':checked')) { accion = 4}
		// alert(accion);
		if (accion == 1)
		{
			var num = $('#numprov').val();
			var anno = $('#añoprov').val();
			var datos = "accion="+accion+"&num="+num+"&anno="+anno+"&admin="+admin+"&sede="+sede;
		}
		if (accion == 2)
		{
			var num = $('#nummemo').val();
			var anno = $('#añomemo').val();
			var datos = "accion="+accion+"&num="+num+"&anno="+anno+"&admin="+admin+"&sede="+sede;
		}
		if (accion == 3)
		{
			var destino = $('#division').val();
			var ini = $('#fechainicio').val();
			var fin = $('#fechafinal').val();
			var datos = "accion="+accion+"&destino="+destino+"&ini="+ini+"&fin="+fin+"&admin="+admin+"&sede="+sede;
		}
		if (accion == 4)
		{
			// var rif = $('#num_Rif').val();
			var datos = "accion="+accion+"&rif="+rif+"&admin="+admin+"&sede="+sede;
		}
		// alert(datos);
		$("#detalleconsulta").load('expedientes/detalleconsulta.php?'+datos, function(){
			$('#barmenu').css("height",$('#contenedor').css("height"));
		});		
	});

	$('#btnconsulta').on('click', function(){
		var accion = 0;
		var admin = $('#administrador').val();
		var sede = $('#sede').val();
		if ($("#chkacta").is(':checked')) { accion = 1}
		if ($("#chkfecha").is(':checked')) { accion = 2}
		if ($("#chkrif_A").is(':checked')) { accion = 3}
		//alert(accion);
		if (accion == 1)
		{
			var num = $('#numacta').val();
			var anno = $('#añoacta').val();
			var datos = "accion="+accion+"&num="+num+"&anno="+anno+"&admin="+admin+"&sede="+sede;
		}
		if (accion == 2)
		{
			var ini = $('#fecha_inicio').val();
			var fin = $('#fecha_final').val();
			var datos = "accion="+accion+"&ini="+ini+"&fin="+fin+"&admin="+admin+"&sede="+sede;
		}
		if (accion == 3)
		{
			var rif = $('#num_Rif').val();
			var datos = "accion="+accion+"&rif="+rif+"&admin="+admin+"&sede="+sede;
		}
		//alert(datos);
		$("#detalleconsulta_f").load('detalleconsulta.php?'+datos, function(){
			$('#barmenu').css("height",$('#contenedor').css("height"));
		});		
	});

	$('#chkprovidencia').on('click', function(){
        $("#chkprovidencia").prop("checked", "checked");
        $("#chkmemorando").prop("checked", "");  
        $("#chkdestino").prop("checked", "");  
        $("#chkrif").prop("checked", "");  
	});

	$('#chkmemorando').on('click', function(){
        $("#chkmemorando").prop("checked", "checked");
        $("#chkprovidencia").prop("checked", "");  
        $("#chkdestino").prop("checked", "");  
        $("#chkrif").prop("checked", "");  
	});

	$('#chkdestino').on('click', function(){
        $("#chkdestino").prop("checked", "checked");
        $("#chkmemorando").prop("checked", "");  
        $("#chkprovidencia").prop("checked", "");  
        $("#chkrif").prop("checked", "");  
	});

	$('#chkrif').on('click', function(){
        $("#chkrif").prop("checked", "checked");
        $("#chkmemorando").prop("checked", "");  
        $("#chkdestino").prop("checked", "");  
        $("#chkprovidencia").prop("checked", "");  
	});

	$('#chkacta').on('click', function(){
        $("#chkacta").prop("checked", "checked");
        $("#chkrif_A").prop("checked", "");  
        $("#chkfecha").prop("checked", "");  
	});

	$('#chkfecha').on('click', function(){
        $("#chkfecha").prop("checked", "checked");
        $("#chkrif_A").prop("checked", "");  
        $("#chkacta").prop("checked", "");  
	});

	$('#chkrif_A').on('click', function(){
        $("#chkrif_A").prop("checked", "checked");
        $("#chkfecha").prop("checked", "");  
        $("#chkacta").prop("checked", "");  
	});

	$('#mod_memo').on('click', function(){
		//alert("1981989198981");
		var num = $('#modmpnum').val();
		var anno = $('#modpaño').val();
		var admin = $('#administrador').val();
		var sector = $('#sector').val();
		var datos = "num=" + num + '&anno=' + anno + '&admin=' + admin + '&sector=' + sector;
		//alert(datos);
		$('#formmodmemorando').hide();
		$('#cargarmodificacion').show();
		$("#cargarmodificacion").load('expedientes/mod_memo.php?'+datos, function(){
			$('#barmenu').css("height",$('#contenedor').css("height"));
		});
	});

	BorrarTemporal();

	function BorrarTemporal() {
		ActualizarTamañoVentana();
		var sector = $('#sector').val();
		var datos = 'sector='+sector;
		//alert("Se borra el temporal");
		$.ajax({
			url: 'expedientes/borrartemporal.php',
			type: 'POST',
			data: datos,
			success: function (r) {}
		});

	}

//...............................................FIN DE LAS INSTRUCCIONES Jquery..............................................
});

function EliminarExpediente(accion,id) {
	if (accion == 0)
	{
		datos = "id="+id;
		//alert(datos);

		jConfirm("!!!... ¿Estás seguro de querer eliminar el registro? ...!!!", "Confirmar Eliminar", function(r) {  
		    if(r) {  
				$.ajax({
					url: 'expedientes/eliminarexp.php',
					type: 'POST',
					data: datos,
					dataType: 'json',
					success: function (data) {
						if (data.proceso == false) {
							jAlert(data.mensaje, "Problemas para eliminar el registro");
						} else {
							var num = $('#memo').val();
							var anno = (new Date).getFullYear();
							var admin = $('#administrador').val();
							var sector = $('#sector').val();
							var datos = "num=" + num + '&anno=' + anno + '&admin=' + admin + '&sector=' + sector;
							$("#registroexp").load('expedientes/cargarexp.php?'+datos, function(){
								$('#barmenu').css("height",$('#contenedor').css("height"));
								if ($('#bloquedor').val()==1)
								{
									$('#tipoexp option:not(:selected)').attr('disabled',true);
									$('#clausura option:not(:selected)').attr('disabled',true);
									$('#resultado option:not(:selected)').attr('disabled',true);
									$('#especial').prop('disabled', true);
									$('#fiscapuntual').prop('disabled', true);
								} else {
									$('#tipoexp option:not(:selected)').attr('disabled',false);
									$('#clausura option:not(:selected)').attr('disabled',false);
									$('#resultado option:not(:selected)').attr('disabled',false);
									$("#especial").removeAttr('disabled');
									$("#fiscapuntual").removeAttr('disabled');			
								}
							});

						}
					}
				});
		    }  
		}); 
	} else {
		datos = "id="+id;
		//alert(datos);

		jConfirm("!!!... ¿Estás seguro de querer eliminar el registro? ...!!!", "Confirmar Eliminar", function(r) {  
		    if(r) {  
				$.ajax({
					url: 'expedientes/eliminarmod.php',
					type: 'POST',
					data: datos,
					dataType: 'json',
					success: function (data) {
						if (data.proceso == false) {
							jAlert(data.mensaje, "Problemas para eliminar el registro");
						} else {
							var num = $('#modmpnum').val();
							var anno = $('#modpaño').val();
							var admin = $('#administrador').val();
							var sector = $('#sector').val();
							var datos = "num=" + num + '&anno=' + anno + '&admin=' + admin + '&sector=' + sector;
							$("#modregistroexp").load('expedientes/cargartemporalmod.php?'+datos, function(){
								$('#barmenu').css("height",$('#contenedor').css("height"));
							});
							if ($('#bloquedor').val()==1)
							{
								$('#guardar_mod').attr("disabled", true);
							} else {
								$("#guardar_mod").removeAttr('disabled');			
							}
						}
					}
				});
		    }  
		}); 
	}
}

function CulminarLapsoEspera25dias(esp){
	//*****VARIABLES A UTILIZAR*******
	if ($('#plazo25').val() == 'Totalmente')
	{
		//SI PAGO ENVIAMOS EL EXPEDIENTE A ARCHIVO
		estatus = 91;
	}

	if ($('#plazo25').val() == 'Parcialmente' || $('#plazo25').val() == 'No')
	{
		//SI NO PAGO O PAGO PARCIALMENTE ENVIAMOS EL EXPEDIENTE A COBRANZA
		if (esp == 0)
		{
			//SI ES CONTRIBUYENTES ORDINARIO A RECAUDACION
			estatus = 92;
		}
		else
		{
			//SI ES CONTRIBUYENTES ESPECIAL A SUJETOS PASIVOS ESPECIALES
			estatus = 94;
		}
	}

	return (estatus);
}