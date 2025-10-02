$(document).ready(function(){
	//alert("Doneee");
	//jQuery.datetimepicker.setLocale('es');
$('#btnEliminarRes').attr('disabled',true);
	$('#Aviso').hide();
	$('#fechaNotificacion').datepicker({dateFormat: 'dd/mm/yy'});
	$('#fechaDesde').datepicker({dateFormat: 'dd/mm/yy'});
	$('#fechaHasta').datepicker({dateFormat: 'dd/mm/yy'});
	var sector=0;
	var anno=0;
	var numero=0;

	$('#cbosector').on('change', function(){
		sector = $('#cbosector').val();
	});
	$('#cbosectorLiq').on('change', function(){
		sector = $('#cbosectorLiq').val();
	});

	
	$('#buscarProv').on('click', function(){
		var sector = $('#cbosector').val();
		var anno = $('#annoProvidencia').val();
		var numero = $('#numeroProvidencia').val();
		var datos = "sector=" + sector + '&anno=' + anno + '&numero=' + numero;
		//alert(datos);
		$.ajax({
		   url: "buscar_exp.php",
		   type: "POST",
		   data: datos,
		   dataType:"json",
		   success: function (r) {
				if (r.permitido)
				{
					$('#fechaNotificacion').val(r.fecha);
					$('#fecha').val(r.fecha);
					$('#btnGuardar').attr('disabled',false);
				} else {
					$('#fechaNotificacion').val("");
					$('#fecha').val("");
					$('#btnGuardar').attr('disabled',true);
				}
		   }
		});
	});

	$('#btnGuardar').on('click', function(){
		var accion = 'providencia';
		var sector = $('#cbosector').val();
		var anno = $('#annoProvidencia').val();
		var numero = $('#numeroProvidencia').val();
		var fecha = $('#fechaNotificacion').val();
		var clase = 'alert-danger';
		var datos = "sector=" + sector + "&anno=" + anno + "&numero=" + numero + "&fecha=" + fecha + "&accion=" + accion;
		//alert(datos);
		$.ajax({
		   url: "guardar.php",
		   type: "POST",
		   data: datos,
		   dataType:"json",
		   success: function (r) {
		   		if (r.permitido){ 
					$('#Aviso').removeClass('alert-danger');
	  				$('#Aviso').addClass('alert-success');
		   		} else {
					$('#Aviso').removeClass('alert-success');
	  				$('#Aviso').addClass('alert-danger');
		   		}
				$('#btnGuardar').attr('disabled',true);
				$('#Aviso').text(r.mensaje);
				$('#Aviso').show(600);
				$('#Aviso').delay(3000).hide(600);

		   }
		});
	});

	$('#btnQuitar').on('click', function(){
		var accion = 'quitar';
		var sector = $('#cbosector').val();
		var anno = $('#annoProvidencia').val();
		var numero = $('#numeroProvidencia').val();
		var fecha = $('#fechaNotificacion').val();
		var clase = 'alert-danger';
		var datos = "sector=" + sector + "&anno=" + anno + "&numero=" + numero + "&fecha=" + fecha + "&accion=" + accion;
		//alert(datos);
		$.ajax({
		   url: "quitar.php",
		   type: "POST",
		   data: datos,
		   dataType:"json",
		   success: function (r) {
		   	//alert(r.permitido);
		   		if (r.permitido){ 
					$('#Aviso').removeClass('alert-danger');
	  				$('#Aviso').addClass('alert-success');
		   		} else {
					$('#Aviso').removeClass('alert-success');
	  				$('#Aviso').addClass('alert-danger');
		   		}
				$('#btnQuitar').attr('disabled',true);		
				$('#Aviso').text(r.mensaje);
				$('#Aviso').show(600);
				$('#Aviso').delay(3000).hide(600);

		   }
		});
	});

	$('#buscarLiq').on('click', function(){
		var sector = $('#cbosectorLiq').val();
		var anno = $('#annoProvidenciaLiq').val();
		var numero = $('#numeroProvidenciaLiq').val();
		var sancion = $('#sancion').val();
		var datos = "sector=" + sector + '&anno=' + anno + '&numero=' + numero + '&sancion=' + sancion;
		//alert(datos);
		$.ajax({
		   url: "buscar_liq.php",
		   type: "POST",
		   data: datos,
		   dataType:"json",
		   success: function (r) {
				//alert(r.fechainico);
				if (r.permitido)
				{
					$('#fechaDesde').val(r.fechainico);
					$('#fechaHasta').val(r.fechafin);
					$('#btnGuardarLiq').attr('disabled',false);
				} else {
					$('#fechaDesde').val("");
					$('#fechaHasta').val("");
					$('#btnGuardarLiq').attr('disabled',true);					
				}
		   }
		});
	});

	$('#btnGuardarLiq').on('click', function(){
		var accion = 'periodo';
		var sector = $('#cbosectorLiq').val();
		var anno = $('#annoProvidenciaLiq').val();
		var numero = $('#numeroProvidenciaLiq').val();
		var sancion = $('#sancion').val();
		var periodoinicio = $('#fechaDesde').val();
		var periodofin = $('#fechaHasta').val();
		var clase = 'alert-danger';
		var datos = "sector=" + sector + "&anno=" + anno + "&numero=" + numero + "&periodoinicio=" + periodoinicio + "&periodofin=" + periodofin + "&sancion=" + sancion + "&accion=" + accion;
		//alert(datos);
		$.ajax({
		   url: "guardarliq.php",
		   type: "POST",
		   data: datos,
		   dataType:"json",
		   success: function (r) {
		   		if (r.permitido){ 
					$('#Aviso').removeClass('alert-danger');
	  				$('#Aviso').addClass('alert-success');
		   		} else {
					$('#Aviso').removeClass('alert-success');
	  				$('#Aviso').addClass('alert-danger');
		   		}

				$('#btnGuardarLiq').attr('disabled',true);
				$('#Aviso').text(r.mensaje);
				$('#Aviso').show(600);
				$('#Aviso').delay(3000).hide(600);

		   }
		});
	});

	$('#buscarRes').on('click', function(){
		var sector = $('#cbosectorRes').val();
		var anno = $('#annoProvidenciaRes').val();
		var numero = $('#numeroProvidenciaRes').val();
		var datos = "sector=" + sector + '&anno=' + anno + '&numero=' + numero;
		//alert(datos);
		$.ajax({
		   url: "buscar_res.php",
		   type: "POST",
		   data: datos,
		   dataType:"json",
		   success: function (r) {
				//alert(r.fechainico);
				if (r.permitido)
				{
					$('#fechaEmision').val(r.fecha);
					$('#btnEliminarRes').attr('disabled',false);
				} else {
					$('#fechaEmision').val("");
					$('#btnEliminarRes').attr('disabled',true);					
				}
		   }
		});
	});

$('#btnEliminarRes').on('click', function(){
		var accion = 'eliminar';
		var sector = $('#cbosectorRes').val();
		var anno = $('#annoProvidenciaRes').val();
		var numero = $('#numeroProvidenciaRes').val();
		var clase = 'alert-danger';
		var datos = "sector=" + sector + "&anno=" + anno + "&numero=" + numero + "&accion=" + accion;
		alert(datos);
		$.ajax({
		   url: "eliminarres.php",
		   type: "POST",
		   data: datos,
		   dataType:"json",
		   success: function (r) {
		   		if (r.permitido){ 
					$('#Aviso').removeClass('alert-danger');
	  				$('#Aviso').addClass('alert-success');
		   		} else {
					$('#Aviso').removeClass('alert-success');
	  				$('#Aviso').addClass('alert-danger');
		   		}

				$('#btnEliminarRes').attr('disabled',true);
				$('#Aviso').text(r.mensaje);
				$('#Aviso').show(600);
				$('#Aviso').delay(3000).hide(600);

		   }
		});
	});

	$('#buscarActa').on('click', function(){
		var sector = $('#cbosectorActa').val();
		var anno = $('#annoProvidenciaActa').val();
		var numero = $('#numeroProvidenciaActa').val();
		var datos = "sector=" + sector + '&anno=' + anno + '&numero=' + numero;
		//alert(datos);
		$.ajax({
		   url: "buscar_acta.php",
		   type: "POST",
		   data: datos,
		   dataType:"json",
		   success: function (r) {
				//alert(r.procedimiento);
				if (r.permitido)
				{
					$('#procedimientoActa').val(r.procedimiento);
					$('#tipoActa').val(r.tipo);
					$('#btnGuardarActa').attr('disabled',false);
				} else {
					$('#procedimientoActa').val("");
					$('#tipoActa').val("");
					$('#btnGuardarActa').attr('disabled',true);					
				}
		   }
		});
	});

	$('#btnGuardarActa').on('click', function(){
		var accion = 'acta';
		var sector = $('#cbosectorActa').val();
		var anno = $('#annoProvidenciaActa').val();
		var numero = $('#numeroProvidenciaActa').val();
		var tipo = $('#tipoActa').val();
		var procedimiento = $('#procedimientoActa').val();
		var clase = 'alert-danger';
		var datos = "sector=" + sector + "&anno=" + anno + "&numero=" + numero + "&tipo=" + tipo + "&procedimiento=" + procedimiento + "&accion=" + accion;
		//alert(datos);
		$.ajax({
		   url: "guardarActa.php",
		   type: "POST",
		   data: datos,
		   dataType:"json",
		   success: function (r) {
		   		if (r.permitido){ 
					$('#Aviso').removeClass('alert-danger');
	  				$('#Aviso').addClass('alert-success');
		   		} else {
					$('#Aviso').removeClass('alert-success');
	  				$('#Aviso').addClass('alert-danger');
		   		}
				$('#btnGuardarActa').attr('disabled',true);
				$('#Aviso').text(r.mensaje);
				$('#Aviso').show(600);
				$('#Aviso').delay(3000).hide(600);

		   }
		});
	});

	$('#buscarReversar').on('click', function(){
		var sector = $('#cbosectorReverso').val();
		var anno = $('#annoProvidenciaReverso').val();
		var numero = $('#numeroProvidenciaReverso').val();
		var datos = "sector=" + sector + '&anno=' + anno + '&numero=' + numero;
		//alert(datos);
		$.ajax({
		   url: "buscar_reversar.php",
		   type: "POST",
		   data: datos,
		   dataType:"json",
		   success: function (r) {
				if (r.estatus > 0)
				{
					//alert(r.estatus);
					$('#btnReversar').attr('disabled',false);
				}
		   }
		});
	});


});