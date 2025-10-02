function validarcampos(campo)
{
	var texto = $(campo).val();
	
	if (texto=="")
	{
		$(campo).addClass('error');
	} else {
		$(campo).removeClass('error');
	}
}

function validarformulario()
{
	var ok=true;
	var factura=$('#numfactura').val();
	var control=$('#numcontrol').val();
	var proveedor=$('#proveedor').val();
	var ffactura=$('#fechafactura').val();
	var fvencimiento=$('#fechavencimiento').val();
	var descripcion=$('#descripcion').val();
	var articulo=$('#codarticulo').val();
	var almacen=$('#almacen').val();
	var unidad=$('#unidad').val();
	var cantidad=$('#cantidad').val();
	var costo=$('#costo').val();
	var descuento=$('#descuento').val();
	
	if (factura=="" || control=="" || proveedor=="" || ffactura=="" || fvencimiento=="" || descripcion=="" || unidad=="" || cantidad=="" || costo=="" || descuento=="")
	{
		ok=false;
		if (factura=="") 
		{
			$('#numfactura').addClass('error');
		} else {
			$('#numfactura').removeClass('error');
		}
		if (control=="")
		{
			$('#numcontrol').addClass('error');
		} else {
			$('#numcontrol').removeClass('error');
		}
		if (proveedor=="") 
		{
			$('#proveedor').addClass('error');
		} else {
			$('#proveedor').removeClass('error');
		}
		if (ffactura=="")
		{
			$('#fechafactura').addClass('error');
		} else {
			$('#fechafactura').removeClass('error');
		}
		if (fvencimiento=="")
		{
			$('#fechavencimiento').addClass('error');
		} else {
			$('#fechavencimiento').removeClass('error');
		}
		if (descripcion=="")
		{
			$('#descripcion').addClass('error');
		} else {
			$('#descripcion').removeClass('error');
		}
		if (articulo=="")
		{
			$('#codarticulo').addClass('error');
		} else {
			$('#codarticulo').removeClass('error');
		}
		if (almacen=="")
		{
			$('#almacen').addClass('error');
		} else {
			$('#almacen').removeClass('error');
		}
		if (unidad=="")
		{
			$('#unidad').addClass('error');
		} else {
			$('#unidad').removeClass('error');
		}
		if (cantidad=="")
		{
			$('#cantidad').addClass('error');
		} else {
			$('#cantidad').removeClass('error');
		}
		if (costo=="")
		{
			$('#costo').addClass('error');
		} else {
			$('#costo').removeClass('error');
		}
		if (descuento=="")
		{
			$('#descuento').addClass('error');
		} else {
			$('#descuento').removeClass('error');
		}
	}
	
	return ok;
	
}