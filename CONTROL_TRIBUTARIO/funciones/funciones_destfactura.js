// JavaScript Document
$(document).ready(function(){
	$('#formexp').hide();
	$('#formsalida').hide();
	$('#formimpacta').hide();
	$("#registrodoc").hide();
	$('#modiexp').hide();

	if ($('#ocultarmenu').val()==1)
	{
		$('#facturas').show();
		$('#expedientes').hide();
	} 
	
	if ($('#ocultarmenu').val()==2)
	{
		$('#facturas').hide();
		$('#expedientes').show();
	}
	
	if ($('#ocultarmenu').val()==0 || $('#ocultarmenu').val()==3)
	{
		$('#expedientes').hide();
	}

	$('#formconsultaAct').hide();
	ActualizarTamaño();
	//$('#cedfunc').val(8632565);
	//$('#nomfunc').val('GUSTAVO GARCIA');
	//$('#cargofunc').val('PROFESIONAL TRIBUTARIO');
	$('#fechasol').datepicker({dateFormat: 'dd/mm/yy'});
	$('#fecha_inicio').datepicker({dateFormat: 'dd/mm/yy'});
	$('#fecha_final').datepicker({dateFormat: 'dd/mm/yy'});
	$('#nueva_acta').attr("disabled", true);
	$('#imp_acta').attr("disabled", true);
	$('#reimp_acta').attr("disabled", true);
	$('#actagenearada').val(0);

	$('#nueva_acta').click(function(){
		//location.reload();
		var impreso = $('#acta_impresa').val();
		var numero = $('#acta').val();
		var num = parseInt(numero) + 1;
		if (impreso == 1)
		{
			$('#destfactura').reset();
			$('#registrodoc').empty();
			$('#acta').val(num);
			$('#acta_impresa').val(0);
			$('#nueva_acta').attr("disabled", true);
			$('#imp_acta').attr("disabled", true);
			$('#genera_acta').attr("disabled", false);
			$('#txtResultado').html("");
		} else {
			jAlert("Antes de Generar una Nueva Acta, por favor debe imprimir el acta actual","IMPRIMIR ACTA");
		}
		ActualizarTamaño();
	});

	$('#imp_acta').click(function(){
		var id = $('#txtid').val();
		var cargo = $('#cargofunc').val();
		var sector = $('#sector').val();
		var datos = "id=" + id + '&cargo=' + cargo + '&sector=' + sector;
		window.open("rptacta.php?" + datos,"mywindows");
		$('#acta_impresa').val(1);
		$('#barmenu').css("height",$('#contenedor').css("height"));
		return false;
	});

	$('#cargar_acta').click(function(){
		var acta = $('#numactaimp').val();
		var anno = $('#añoactaimp').val();
		var sector = $('#reimp_sector').val();
		var datos = "acta=" + acta + '&anno=' + anno + '&sector=' + sector;
		//alert(datos);
		$.ajax({
		   url: "cargaracta.php",
		   type: "POST",
		   data: datos,
		   dataType:"json",
		   success: function (r) {
				$('#txtResultadoimp').html(r.mensaje);
				$('#idactaimp').val(r.ID);
				$('#cargoimp').val(r.cargo);
				if (r.permitido==true)
				{
					$('#reimp_acta').attr("disabled", false);
				}
		   }
		});
	});

	$('#reimp_acta').click(function(){
		var id = $('#idactaimp').val();
		var cargo = $('#cargoimp').val();
		var sector = $('#reimp_sector').val();
		var datos = "id=" + id + '&cargo=' + cargo + '&sector=' + sector;
		window.open("rptacta.php?"+datos, "mywindows");
		$('#acta_impresa').val(1);
		return false;
	});

	$('#destruccion').click(function(){
		$('#formexp').hide();
		$('#formsalida').hide();
		$('#facturas').show();
		$('#expedientes').hide();		
		$('#formexp').hide();
		$("#modiexp").hide();
		$('#formimpmemorando').hide();
		$('#formmodmemorando').hide();
		$('#cargarmodificacion').hide();
		$('#formconsultaExp').hide();
		$('#formconsultaAct').hide();
		ActualizarTamaño();
	});

	$('#salida').click(function(){
		$('#expedientes').show();
		$('#menurecepcion').hide();		
		$('#facturas').hide();
		$('#siger').hide();
		$('#formexp').hide();
		$("#modiexp").hide();
		$('#formimpmemorando').hide();
		$('#formmodmemorando').hide();
		$('#cargarmodificacion').hide();
		$('#formconsultaAct').hide();
		ActualizarTamaño();
	});


	$('#inc_acta').click(function(){
		//alert("Hizo click 1");
		$('#formexp').show();
		$("#formimpacta").hide();
		$('#formsalida').hide();
		$('#formimpmemorando').hide();
		$('#formmodmemorando').hide();
		$('#cargarmodificacion').hide();
		$('#formconsultaAct').hide();
		ActualizarTamaño();

		//$('#formexp').fadeIn("slow");
	});

	/*$('#mod_acta').click(function(){
		//alert("Hizo click 2");
		$("#modiexp").show();
		$('#formexp').hide();
		ActualizarTamaño();
	});*/

	$('#rimp_acta').click(function(){
		//alert("Hizo click 1");
		$('#formimpacta').show();
		$("#formexp").hide();
		$('#formsalida').hide();
		$('#formimpmemorando').hide();
		$('#formmodmemorando').hide();
		$('#cargarmodificacion').hide();
		$('#formconsultaAct').hide();
		ActualizarTamaño();
	});
	/*
	$('#cs_acta').click(function(){
		//alert("Hizo click 2");
		$("#formexp").toggle();
		ActualizarTamaño();
	});*/

	$ ("li.menu").click(function() {
		//alert("Clases Add");
		$("li.menu.active").removeClass("active");
		$(this).addClass("active");
	});

	$('#btnAGREGARDOC').click(function(){
		var f = new Date();
		var h = f.getHours();
		var m = f.getMinutes();
		var hora = h + ":" + m;
		var numacta = $('#acta').val(); 
		var rif =  $('#rif').val();
		var documento = $('#tipodoc').val();
		var inicio =  $('#txtNUMINICIO').val();
		var fin =  $('#txtNUMFINAL').val();
		var nombre = $('#nombresujeto').val();
		var cedula = $('#cedula').val();
		var nombre_rep = $('#nombrerp').val();
		var num_solicitud = $('#numsolicitud').val();
		var fechasol = $('#fechasol').val();
		var sector = $('#sector').val();
		if (rif == "" || inicio == 0 || fin == 0 || nombre == "" || cedula == "" || nombre_rep == "" || num_solicitud == "" || fechasol == "")
		{
			jAlert("Existen datos requeridos vacios, por favor verifique","CAMPOS REQUERIDOS VACIOS");
		}
		else
		{
			$("#registrodoc").load('agregardoc.php?numacta='+numacta+'&rif='+rif+'&documento='+documento+'&inicio='+inicio+'&fin='+fin+'&sector='+sector+'&accion=0', function(){
				if ($('#bloquedor').val()==1)
				{
					$('#persona option:not(:selected)').attr('disabled',true);
					$('#tiposol option:not(:selected)').attr('disabled',true);
					$('#rif').prop('readonly', true);
					$('#cedula').prop('readonly', true);
					$('#nombrerp').prop('readonly', true);
					$('#numsolicitud').prop('readonly', true);
					$('#fechasol').prop('readonly', true);
					$('#fechasol').datepicker("destroy");
				} else {
					$('#persona option:not(:selected)').attr('disabled',false);
					$('#tiposol option:not(:selected)').attr('disabled',false);
					$('#rif').removeAttr('readonly');
					$('#cedula').removeAttr('readonly');
					$('#nombrerp').removeAttr('readonly');
					$('#numsolicitud').removeAttr('readonly');
					$('#fechasol').datepicker();
				}
				ActualizarTamaño();
			});
		}
		$("#registrodoc").fadeIn("slow");
	});

	$('#rif').on('change', function() {
		var rif = $('#rif').val();
		var datos = 'rif='+rif; 
		//alert(datos);
		$.ajax({

		   url: "contribuyente.php",
		   type: "POST",
		   data: datos,
		   dataType:"json",
		   success: function (r) {
		   		//alert("Permitido: " + r.permitido + " " + r.nombrerazon);
				if (r.permitido==true){
			   		$('#nombresujeto').val(r.nombrerazon);
				} else {
					$('#rif').val("");
					$('#nombresujeto').val("");
					jAlert(r.mensaje,"REGISTRO DE CONTRIBUYENTE");
					$( "#rif" ).focus();
				}
		   }
		});

	});

	$('#genera_acta').on('click', function() {
		var f = new Date();
		var h = f.getHours();
		var m = f.getMinutes();
		var hora = h + ":" + m;
		var numacta = $('#acta').val(); 
		var cedfunc = $('#cedfunc').val();
		var nomfunc = $('#nomfunc').val();
		var persona = $('#persona').val();
		var nomrep = $('#nombrerp').val(); 
		var cedrep = $('#cedula').val(); 
		var rif =  $('#rif').val();
		var nombre = $('#nombresujeto').val();
		var tiposol = $('#tiposol').val();
		var num_solicitud = $('#numsolicitud').val();
		var fechasol = $('#fechasol').val();
		var documento = $('#tipodoc').val();
		var inicio =  $('#txtNUMINICIO').val();
		var fin =  $('#txtNUMFINAL').val();
		var sector = $('#sector').val();
		var datos = 'hora='+hora+'&numacta='+numacta+'&cedfunc='+cedfunc+'&nomfunc='+nomfunc+'&persona='+persona+'&nomrep='+nomrep+'&cedrep='+cedrep+'&rif='+rif+'&nombre='+nombre+'&tiposol='+tiposol+'&num_solicitud='+num_solicitud+'&fechasol='+fechasol+'&documento='+documento+'&inicio='+inicio+'&fin='+fin+'&sector='+sector;
		//alert(datos);
		$.ajax({
			url: "agregar_acta.php",
			type: "POST",
			dataType:"json",
			data: datos,
			success: function(r)
			{
				if ((r.permitido)==true)
				{
					//jAlert(r.mensaje, "INFORMACION DEL SISTEMA");
					$('#actagenearada').val(1);
					$('#txtResultado').html("!!!...Acta Generada Satisfactoriamente...!!!");
					$('#txtid').val(r.ID);
					//$("#destfactura").reset();
				} else {
					jAlert(r.mensaje, "INFORMACION DEL SISTEMA");
					$('#txtResultado').html("");
					$('#txtid').val("");
				}
				$('#barmenu').css("height",$('#contenedor').css("height"));
				verificar_acta_generada();
			}
		})
	});

	$('#numsolicitud').on('change', function() {
		var solicitud = $('#numsolicitud').val();
		var datos = "solicitud=" + solicitud;
		//jAlert(datos);
		$.ajax({
			url: "buscarsolicitud.php",
			type: "POST",
			dataType: "json",
			data: datos,
			success: function (r) {
				if ((r.permitido)==false)
				{
					$('#numsolicitud').val("");
					jAlert(r.mensaje,"SOLICITUD YA REGISTRADA");
					$( "#numsolicitud" ).focus();
				}
			}
		});
	});

	$.fn.reset = function () {
		$(this).each (function() { this.reset(); });
	}

	function verificar_acta_generada(){
		if ($('#actagenearada').val()==1)
		{
			$('#genera_acta').attr("disabled", true);
			$('#nueva_acta').attr("disabled", false);
			$('#imp_acta').attr("disabled", false);		
		}
		else
		{
			$('#genera_acta').attr("disabled", false);	
			$('#nueva_acta').attr("disabled", true);
			$('#imp_acta').attr("disabled", true);		
		}
	}

	function ActualizarTamaño() {
		$('#barmenu').css("height",$('#contenedor').css("height"));
	}

});
