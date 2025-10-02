$(document).ready(function(){
	//código a ejecutar cuando el DOM está listo para recibir instrucciones.
	// alert("JQuery ready");

	$.datepicker.setDefaults($.datepicker.regional['es']);

	$('#btnBuscar').on('click', function(e){
		e.preventDefault();
		// alert('Hizo click');
		var rif = $('#txtRif').val();
		// alert(rif);
		if (rif.length > 0)
		{
			$("#divResoluciones").load('pago_resoluciones/pagos.php?rif=' + rif, function(data){
				$('.dataPicker').datepicker();
			});
		}
	});

	$("#divResoluciones").on("click", ".InputGrabar", function(e){
		e.preventDefault();
		// alert($(this).attr('id'));

		var rif = $('#txtRif').val();
		var id = $(this).attr('id');
		var fechasol = $('#txt' + id).val();
		var array_fechasol = fechasol.split("/")  //esta linea esta bien y te genera el arreglo
		var anno = parseInt(array_fechasol[2]); // porque repites el nombre dos veces con una basta
		var mes = parseInt(array_fechasol[1]); 
		var dia  = parseInt(array_fechasol[0]);
		var fecha_pago = anno + "/" + mes + "/" + dia;
		var agencia_pago = $('#cbo' + id).val();
		var user = 8632565;
		// alert(fecha_pago);
		var parametros = "id=" + id + "&fecha_pago=" + fecha_pago + "&agencia_pago=" + agencia_pago + "&usuario=" + user;
		// alert(parametros);

		var comprobar = $('#txt' + id).val().length * $('#cbo' + id).val().length
		// alert(comprobar);

		if (comprobar > 0)
		{

			if(confirm('¿Estas seguro de actualizar el pago?'))
			{
			    $.ajax({
			       url: "pago_resoluciones/actualizar.php",
			       type: "POST",
			       dataType:"json",
			       data: parametros,
			       success: function(r) {
						if (r.permitido)
						{
							$("#divResoluciones").load('pago_resoluciones/pagos.php?rif=' + rif, function(data){
								$('.dataPicker').datepicker();
							});					
						}
			       }
			    });
			}
			else
			{
				return false;
			}
		} else {
			alert("Debe ingresar la fecha y la agencia bancaria");
		}
	});

	// $("#divResoluciones").on("click", ".dataPicker", function(e){
	// 	e.preventDefault();
	// 	alert($(this).attr('id'));
	// 	$('.dataPicker').datepicker();
	// });
});