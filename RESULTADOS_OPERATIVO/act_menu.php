<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script type="text/javascript" src="jquery.js"></script>

	<script type="text/javascript" src="ddaccordion.js">
		/***********************************************
		 * Accordion Content script- (c) Dynamic Drive DHTML code library (www.dynamicdrive.com)
		 * Visit http://www.dynamicDrive.com for hundreds of DHTML scripts
		 * This notice must stay intact for legal use
		 ***********************************************/
	</script>


	<script type="text/javascript">
		ddaccordion.init({
			headerclass: "submenuheader", //Shared CSS class name of headers group
			contentclass: "submenu", //Shared CSS class name of contents group
			revealtype: "click", //Reveal content when user clicks or onmouseover the header? Valid value: "click", "clickgo", or "mouseover"
			mouseoverdelay: 200, //if revealtype="mouseover", set delay in milliseconds before header expands onMouseover
			collapseprev: false, //Collapse previous content (so only one open at any time)? true/false 
			defaultexpanded: [], //index of content(s) open by default [index1, index2, etc] [] denotes no content
			onemustopen: false, //Specify whether at least one header should be open always (so never all headers closed)
			animatedefault: false, //Should contents open by default be animated into view?
			persiststate: true, //persist state of opened contents within browser session?
			toggleclass: ["", ""], //Two CSS classes to be applied to the header when it's collapsed and expanded, respectively ["class1", "class2"]
			togglehtml: ["suffix", "<img src='images/plus.gif' class='statusicon' />", "<img src='images/minus.gif' class='statusicon' />"], //Additional HTML added to the header when it's collapsed and expanded, respectively  ["position", "html1", "html2"] (see docs)
			animatespeed: "fast", //speed of animation: integer in milliseconds (ie: 200), or keywords "fast", "normal", or "slow"
			oninit: function(headers, expandedindices) { //custom code to run when headers have initalized
				//do nothing
			},
			onopenclose: function(header, index, state, isuseractivated) { //custom code to run whenever a header is opened or closed
				//do nothing
			}
		})
	</script>


	<style type="text/css">
		.glossymenu {
			margin: 5px 0;
			padding: 0;
			width: 230px;
			/*width of menu*/
			border: 1px solid #333;
			border-bottom-width: 0;
		}

		.glossymenu a.menuitem {
			background: #333;
			font: bold 12px 'Arial', Helvetica, sans-serif;
			color: white;
			display: block;
			position: relative;
			/*To help in the anchoring of the ".statusicon" icon image*/
			width: auto;
			padding: 4px 0;
			padding-left: 10px;
			text-decoration: none;
		}


		.glossymenu a.menuitem:visited,
		.glossymenu .menuitem:active {
			color: white;
		}

		.glossymenu a.menuitem .statusicon {
			/*CSS for icon image that gets dynamically added to headers*/
			position: absolute;
			top: 5px;
			right: 5px;
			border: none;
		}

		.glossymenu div.submenu {
			/*DIV that contains each sub menu*/
			background: white;
		}

		.glossymenu div.submenu ul {
			/*UL of each sub menu*/
			list-style-type: none;
			margin: 0;
			padding: 0;
		}

		.glossymenu div.submenu ul li {
			border-bottom: 1px solid #333;
		}

		.glossymenu div.submenu ul li a {
			display: block;
			font: normal 12px 'Arial', Helvetica, sans-serif;
			color: black;
			text-decoration: none;
			height: 20px;
			padding: 2px 0;
			padding-left: 10px;
		}

		.glossymenu div.submenu ul li a:hover {
			background: #F00;
			color: #FFF;
			colorz: white;
		}
	</style>
</head>

<body style="background: transparent !important;">

	<div class="glossymenu">
		<a class="menuitem submenuheader">Resultados de Operativos</a>
		<div class="submenu">
			<ul>
				<li><a href="<?php echo ($_SERVER['PHP_SELF']); ?>?idpagina=1" title="Incluir Resultados">Incluir</a></li>
				<li><a href="<?php echo ($_SERVER['PHP_SELF']); ?>?idpagina=2" title="Modificar Resultados">Modificar</a></li>
				<li><a href="<?php echo ($_SERVER['PHP_SELF']); ?>?idpagina=3" title="Consultar Resultados">Consultas</a></li>
				<li><a href='../fiscalizacion/menuprincipal.php'>Salir</a></li>
			</ul>
		</div>
		<!--
<a class="menuitem submenuheader">Control de Expedientes</a>
<div class="submenu">
	<ul>
	<li><a href="<?php echo ($_SERVER['PHP_SELF']); ?>?idpagina=4" title="Memorando para Remitir Expediente">Remitir Expediente</a></li>
	<li><a href="<?php echo ($_SERVER['PHP_SELF']); ?>?idpagina=5" title="Memorando para Remitir Resolución Cierre">Remitir Resolución Cierre</a></li>
	<li><a href="<?php echo ($_SERVER['PHP_SELF']); ?>?idpagina=6" title="Reimpremir Memorando">Reimprimir Memorando</a></li>
	<li><a href="<?php echo ($_SERVER['PHP_SELF']); ?>?idpagina=7" title="Consultas">Consultas</a></li>
	</ul>
</div>
<a class="menuitem submenuheader" >Control de Gestión</a>
<div class="submenu">
	<ul>
	<li><a href="<?php echo ($_SERVER['PHP_SELF']); ?>?idpagina=8" title="Consultar Providencias Emitidas">Providencias Emitidas</a></li>
	<li><a href="<?php echo ($_SERVER['PHP_SELF']); ?>?idpagina=9" title="Consultar Providencias Notificadas">Providencias Notificadas</a></li>
	<li><a href="<?php echo ($_SERVER['PHP_SELF']); ?>?idpagina=10" title="Consultar Providencias Concluidas">Providencias Concluidas</a></li>
	<li><a href="<?php echo ($_SERVER['PHP_SELF']); ?>?idpagina=11" title="Consultar Providencias Anuladas">Providencias Anuladas</a></li>
	<li><a href="<?php echo ($_SERVER['PHP_SELF']); ?>?idpagina=12" title="Consultar Providencias en Proceso">Providencias en Proceso</a></li>
	</ul>
</div>
<a class="menuitem submenuheader">Siger Fiscalización</a>
<div class="submenu">
	<ul>
	<li><a href="#">Generar</a></li>
	<li><a href="#">Consultas</a></li>
	</ul>
</div>
-->
	</div>


</body>

</html>