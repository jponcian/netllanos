$(document).ready(function(){

	$('#incluir').on('click', function(){
		var id = $('#valorid').val();
		var nummemo = $('#modmemo').val(); 
		var fechamemo =  $('#modfechamemo').val();
		var tipo = $('#modtipoexp').val();
		var resultado =  $('#modresultado').val();
		var destino =  $('#moddestino').val();
		var clausura = $('#modclausura').val();
		var añoprovidencia = $('#modañoprovidencia').val();
		var numprovidencia = $('#modnumprovidencia').val();
		var folio = $('#modfolio').val();
		var fp = 0;
		var estatus = $('#modestatus').val();
		var admin = $('#administrador').val();
		var sector = $('#sector').val();
		if($("#modfiscapuntual").is(':checked')) {  
            fp = 1;  
        } else {  
            fp = 0;  
        }

		var datos = 'nummemo='+nummemo+'&fechamemo='+fechamemo+'&tipo='+tipo+'&resultado='+escape(resultado)+'&destino='+escape(destino)+'&clausura='+clausura+'&annoprovidencia='+añoprovidencia+'&numprovidencia='+numprovidencia+'&folio='+folio+'&fp='+fp+'&estatus='+estatus+'&admin='+admin+'&id='+id+'&sector='+sector;
		//alert(datos);
		if (nummemo==0 || fechamemo=="" || tipo=="" || resultado=="" || destino=="" || añoprovidencia=="" || numprovidencia=="")
		{
			jAlert("Existen datos requeridos vacios, por favor verifique","CAMPOS REQUERIDOS VACIOS");
		}
		else
		{

			$("#cargando_mod").css("display", "inline");
			$('#incluir').attr("disabled", true);
	        $.ajax({  
	            url: 'expedientes/agregarexpmod.php',  
				type: "GET",
				data: datos,
	            success: function(data) {  
	                $('#modregistroexp').html(data);
					if ($('#bloquedor').val()==0)
					{
						$('#guardar_mod').attr("disabled", true);
					} else {
						$("#guardar_mod").removeAttr('disabled');			
					}
					$('#incluir').attr("disabled", false);
					$("#cargando_mod").css("display", "none");
	            }  
	        });  
		}
		$("#modregistroexp").fadeIn("slow");
		$('#barmenu').css("height",$('#contenedor').css("height"));
	});

	$('#guardar_mod').on('click', function(){
		//alert("Guardar Modificación");
		var nummemo = $('#modmemo').val(); 
		var fechamemo =  $('#modfechamemo').val();
		var sector = $('#sector').val();
		var datos = 'nummemo='+nummemo+'&fechamemo='+fechamemo+'&sector='+sector;
		//alert(datos);
		$.ajax({
			url: 'expedientes/guardarmod.php',
			type: 'post',
			dataType: 'json',
			success: function (data) {
				if (data.proceso == true)
				{
					$('#modimp_memo').attr("disabled", false);
					$('#incluir').attr("disabled", true);
					$('#guardar_mod').attr("disabled", true);
				} else {
					$('#modimp_memo').attr("disabled", true);
					$('#incluir').attr("disabled", false);
					$('#guardar_mod').attr("disabled", false);
				}
				$('#txtmodResultadoExp').html(data.mensaje);
				$('#barmenu').css("height",$('#contenedor').css("height"));
			}
		});
	});

	$('#modimp_memo').on('click', function(){
		//alert("Imprimir Memorando Modificado");
		var anno = (new Date).getFullYear();
		var num = $('#modmemo').val();
		var sector = $('#sector').val();
		var datos = "anno=" + anno + '&num=' + num + '&sector=' + sector;
		//alert(datos);
		window.open("expedientes/rptmemo.php?" + datos),"mywindows";
		$('#modimprimir').val(1);
		return false;
	});
});
