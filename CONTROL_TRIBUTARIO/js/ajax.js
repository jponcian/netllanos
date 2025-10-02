function Buscador(){
	var xmlhttp=false;
	try {
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
		try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (E) {
			xmlhttp = false;
  		}
	}

	if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
		xmlhttp = new XMLHttpRequest();
	}
	return xmlhttp;
}


function BuscarProveedor(){
	var cod,nom,rif,est,tel,ciu;
	cod = document.getElementById('codproveedor').value;
	nom = document.getElementById('nombre').value;
	rif = document.getElementById('nif').value;
	est = document.getElementById('cboProvincias').value;
	tel = document.getElementById('telefono').value;
	ciu = document.getElementById('localidad').value;
	<!--alert(est);-->
	c = document.getElementById('rejilla');
	ajax = Buscador();
	ajax.open("GET","proveedores/buscarproveedor.php?cod="+cod+"&nom="+nom+"&rif="+rif+"&est="+est+"&tel="+tel+"&ciu="+ciu);
	ajax.onreadystatechange=function() {
		if (ajax.readyState == 4) {
			c.innerHTML= ajax.responseText;
		}
	}
	ajax.send(null)
}

function BuscarArticulo(){
	var cod,ref,cat,des,alm;
	cod = document.getElementById('codarticulo').value;
	ref = document.getElementById('referencia').value;
	cat = document.getElementById('cboFamilias').value;
	des = document.getElementById('descripcion').value;
	alm = document.getElementById('cboUbicacion').value;
	<!--alert(est);-->
	c = document.getElementById('rejilla');
	ajax = Buscador();
	ajax.open("GET","articulos/buscararticulo.php?cod="+cod+"&ref="+ref+"&cat="+cat+"&des="+des+"&alm="+alm);
	ajax.onreadystatechange=function() {
		if (ajax.readyState == 4) {
			c.innerHTML= ajax.responseText;
		}
	}
	ajax.send(null)
}

function BuscarCliente(){
	var cod,nom,rif,est,tel,ciu;
	cod = document.getElementById('codcliente').value;
	nom = document.getElementById('nombre').value;
	rif = document.getElementById('nif').value;
	est = document.getElementById('cboProvincias').value;
	tel = document.getElementById('telefono').value;
	ciu = document.getElementById('localidad').value;
	<!--alert(est);-->
	c = document.getElementById('rejilla');
	ajax = Buscador();
	ajax.open("GET","clientes/buscarcliente.php?cod="+cod+"&nom="+nom+"&rif="+rif+"&est="+est+"&tel="+tel+"&ciu="+ciu);
	ajax.onreadystatechange=function() {
		if (ajax.readyState == 4) {
			c.innerHTML= ajax.responseText;
		}
	}
	ajax.send(null)
}

function BuscarCategoria(){
	var cod,nom;
	cod = document.getElementById('codcategoria').value;
	nom = document.getElementById('nombre').value;
	<!--alert(est);-->
	c = document.getElementById('rejilla');
	ajax = Buscador();
	ajax.open("GET","categorias/buscarcategoria.php?cod="+cod+"&nom="+nom);
	ajax.onreadystatechange=function() {
		if (ajax.readyState == 4) {
			c.innerHTML= ajax.responseText;
		}
	}
	ajax.send(null)
}

function BuscarEstado(){
	var cod,nom;
	cod = document.getElementById('codestado').value;
	nom = document.getElementById('nombre').value;
	<!--alert(est);-->
	c = document.getElementById('rejilla');
	ajax = Buscador();
	ajax.open("GET","estados/buscarestado.php?cod="+cod+"&nom="+nom);
	ajax.onreadystatechange=function() {
		if (ajax.readyState == 4) {
			c.innerHTML= ajax.responseText;
		}
	}
	ajax.send(null)
}

function BuscarEntidad(){
	var cod,nom;
	cod = document.getElementById('codentidad').value;
	nom = document.getElementById('nombre').value;
	<!--alert(est);-->
	c = document.getElementById('rejilla');
	ajax = Buscador();
	ajax.open("GET","entidades/buscarentidad.php?cod="+cod+"&nom="+nom);
	ajax.onreadystatechange=function() {
		if (ajax.readyState == 4) {
			c.innerHTML= ajax.responseText;
		}
	}
	ajax.send(null)
}

function BuscarAlmacen(){
	var cod,nom;
	cod = document.getElementById('codalmacen').value;
	nom = document.getElementById('nombre').value;
	<!--alert(est);-->
	c = document.getElementById('rejilla');
	ajax = Buscador();
	ajax.open("GET","almacenes/buscaralmacen.php?cod="+cod+"&nom="+nom);
	ajax.onreadystatechange=function() {
		if (ajax.readyState == 4) {
			c.innerHTML= ajax.responseText;
		}
	}
	ajax.send(null)
}

function BuscarFP(){
	var cod,nom;
	cod = document.getElementById('codformapago').value;
	nom = document.getElementById('nombre').value;
	<!--alert(est);-->
	c = document.getElementById('rejilla');
	ajax = Buscador();
	ajax.open("GET","formapago/buscarformapago.php?cod="+cod+"&nom="+nom);
	ajax.onreadystatechange=function() {
		if (ajax.readyState == 4) {
			c.innerHTML= ajax.responseText;
		}
	}
	ajax.send(null)
}

function BuscarUnidad(){
	var cod,nom;
	cod = document.getElementById('codunidad').value;
	nom = document.getElementById('nombre').value;
	<!--alert(est);-->
	c = document.getElementById('rejilla');
	ajax = Buscador();
	ajax.open("GET","unidadmedida/buscarunidadmedida.php?cod="+cod+"&nom="+nom);
	ajax.onreadystatechange=function() {
		if (ajax.readyState == 4) {
			c.innerHTML= ajax.responseText;
		}
	}
	ajax.send(null)
}

function BuscarImpuesto(){
	var cod,nom;
	cod = document.getElementById('codimpuesto').value;
	nom = document.getElementById('nombre').value;
	<!--alert(est);-->
	c = document.getElementById('rejilla');
	ajax = Buscador();
	ajax.open("GET","impuestos/buscarimpuesto.php?cod="+cod+"&nom="+nom);
	ajax.onreadystatechange=function() {
		if (ajax.readyState == 4) {
			c.innerHTML= ajax.responseText;
		}
	}
	ajax.send(null)
}

function BuscarCompra(){
	var cod,nom,numf,sta,ini,fin;
	//alert("Siiii");
	cod = document.getElementById('codproveedor').value;
	nom = document.getElementById('nombre').value;
	numf = document.getElementById('numfactura').value;
	sta = document.getElementById('cboEstados').value;
	ini = document.getElementById('fechainicio').value;
	fin = document.getElementById('fechafin').value;
	<!--alert(est);-->
	c = document.getElementById('rejilla');
	ajax = Buscador();
	ajax.open("GET","compras/buscarcompra.php?cod="+cod+"&nom="+nom+"&numf="+numf+"&sta="+sta+"&ini="+ini+"&fin="+fin);
	ajax.onreadystatechange=function() {
		if (ajax.readyState == 4) {
			c.innerHTML= ajax.responseText;
		}
	}
	ajax.send(null)
}

function ajaxFunction()
  {
  var xmlHttp;
  try
    {
    // Firefox, Opera 8.0+, Safari
    xmlHttp=new XMLHttpRequest();
    return xmlHttp;
    }
  catch (e)
    {
    // Internet Explorer
    try
      {
      xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
      return xmlHttp;
      }
    catch (e)
      {
      try
        {
        xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
        return xmlHttp;
        }
      catch (e)
        {
        alert("Your browser does not support AJAX!");
        return false;
        }
      }
    }
  }
function validarproveedor()    
{
	var cod,titulo,texto,ajax,resul;
	//
	cod = document.getElementById('codproveedor').value;
	titulo = document.getElementById('nombre');
	texto = document.getElementById('nif');
	//
	ajax=ajaxFunction();
	ajax.open("GET","proveedores/comprobarproveedor.php?cod="+cod,true);
	//
	ajax.send(null);
	ajax.onreadystatechange=function()
	 {
	  if(ajax.readyState==3)
		{
			document.getElementById('capa').innerHTML="Cargando...";
		}          
	  else if(ajax.readyState==4)
		{
			document.getElementById('capa').innerHTML="";
			//
			resul=ajax.responseText.split('|');
			if (titulo.value=resul[0]!="")
			{
				titulo.value=resul[0];
				texto.value=resul[1];
			} else {
				titulo.value=resul[0];
				texto.value=resul[1];
				alert("Codigo de Proveedor No Registrado");
			}
		}    
	 }
} 

function validarcliente()    
{
	var cod,titulo,texto,ajax,resul;
	//
	cod = document.getElementById('codcliente').value;
	titulo = document.getElementById('nombre');
	texto = document.getElementById('nif');
	//
	ajax=ajaxFunction();
	ajax.open("GET","clientes/comprobarcliente.php?cod="+cod,true);
	//
	ajax.send(null);
	ajax.onreadystatechange=function()
	 {
	  if(ajax.readyState==3)
		{
			document.getElementById('capa').innerHTML="Cargando...";
		}          
	  else if(ajax.readyState==4)
		{
			document.getElementById('capa').innerHTML="";
			//
			resul=ajax.responseText.split('|');
			if (titulo.value=resul[0]!="")
			{
				titulo.value=resul[0];
				texto.value=resul[1];
			} else {
				titulo.value=resul[0];
				texto.value=resul[1];
				alert("Codigo de Cliente No Registrado");
			}
		}    
	 }
} 

function validarcategoria()    
{
	var cod,titulo,texto,ajax,resul;
	//
	cod = document.getElementById('codcategoria').value;
	titulo = document.getElementById('nombre');
	texto = document.getElementById('codcategoria');
	//
	ajax=ajaxFunction();
	ajax.open("GET","categorias/comprobarcategoria.php?cod="+cod,true);
	//
	ajax.send(null);
	ajax.onreadystatechange=function()
	 {
	  if(ajax.readyState==3)
		{
			document.getElementById('capa').innerHTML="Cargando...";
		}          
	  else if(ajax.readyState==4)
		{
			document.getElementById('capa').innerHTML="";
			//
			resul=ajax.responseText.split('|');
			if (titulo.value=resul[0]!="")
			{
				titulo.value=resul[0];
				texto.value=resul[1];
			} else {
				titulo.value=resul[0];
				texto.value=resul[1];
				alert("Codigo de Categoria No Registrado");
			}
		}    
	 }
} 

function validarestado()    
{
	var cod,titulo,texto,ajax,resul;
	//
	cod = document.getElementById('codestado').value;
	titulo = document.getElementById('nombre');
	texto = document.getElementById('codestado');
	//
	ajax=ajaxFunction();
	ajax.open("GET","estados/comprobarestado.php?cod="+cod,true);
	//
	ajax.send(null);
	ajax.onreadystatechange=function()
	 {
	  if(ajax.readyState==3)
		{
			document.getElementById('capa').innerHTML="Cargando...";
		}          
	  else if(ajax.readyState==4)
		{
			document.getElementById('capa').innerHTML="";
			//
			resul=ajax.responseText.split('|');
			if (titulo.value=resul[0]!="")
			{
				titulo.value=resul[0];
				texto.value=resul[1];
			} else {
				titulo.value=resul[0];
				texto.value=resul[1];
				alert("Codigo de Estado No Registrado");
			}
		}    
	 }
} 

function validarentidad()    
{
	var cod,titulo,texto,ajax,resul;
	//
	cod = document.getElementById('codentidad').value;
	titulo = document.getElementById('nombre');
	texto = document.getElementById('codentidad');
	//
	ajax=ajaxFunction();
	ajax.open("GET","entidades/comprobarentidad.php?cod="+cod,true);
	//
	ajax.send(null);
	ajax.onreadystatechange=function()
	 {
	  if(ajax.readyState==3)
		{
			document.getElementById('capa').innerHTML="Cargando...";
		}          
	  else if(ajax.readyState==4)
		{
			document.getElementById('capa').innerHTML="";
			//
			resul=ajax.responseText.split('|');
			if (titulo.value=resul[0]!="")
			{
				titulo.value=resul[0];
				texto.value=resul[1];
			} else {
				titulo.value=resul[0];
				texto.value=resul[1];
				alert("Codigo de Entidad Bancaria No Registrado");
			}
		}    
	 }
} 

function validardato(cod,vnom,vcod,pagina,bd)    
{
	var cod,titulo,texto,ajax,resul,vnom,vcod,pagina,bd;
	//
	cod = document.getElementById(vcod).value;
	titulo = document.getElementById(vnom);
	texto = document.getElementById(vcod);
	//
	ajax=ajaxFunction();
	ajax.open("GET",pagina+"?cod="+cod,true);
	//
	ajax.send(null);
	ajax.onreadystatechange=function()
	 {
	  if(ajax.readyState==3)
		{
			document.getElementById('capa').innerHTML="Cargando...";
		}          
	  else if(ajax.readyState==4)
		{
			document.getElementById('capa').innerHTML="";
			//
			resul=ajax.responseText.split('|');
			if (titulo.value=resul[0]!="")
			{
				titulo.value=resul[0];
				texto.value=resul[1];
			} else {
				titulo.value=resul[0];
				texto.value=resul[1];
				alert("Codigo de "+bd+" No Registrado");
			}
		}    
	 }
} 

function validararticulo()    
{
	var cod,titulo,texto,texto1,ajax,resul;
	//
	cod = document.getElementById('codarticulo').value;
	titulo = document.getElementById('referencia');
	texto = document.getElementById('codarticulo');
	texto1 = document.getElementById('descripcion');
	//
	ajax=ajaxFunction();
	ajax.open("GET","articulos/comprobararticulo.php?cod="+cod,true);
	//
	ajax.send(null);
	ajax.onreadystatechange=function()
	 {
	  if(ajax.readyState==3)
		{
			document.getElementById('capa').innerHTML="Cargando...";
		}          
	  else if(ajax.readyState==4)
		{
			document.getElementById('capa').innerHTML="";
			//
			resul=ajax.responseText.split('|');
			if (titulo.value=resul[0]!="")
			{
				titulo.value=resul[0];
				texto.value=resul[1];
				texto1.value=resul[2];
			} else {
				titulo.value=resul[0];
				texto.value=resul[1];
				texto1.value=resul[2];
				alert("Codigo de Articulo No Registrado");
			}
		}    
	 }
} 


