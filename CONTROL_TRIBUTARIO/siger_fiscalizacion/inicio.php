<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<title>SIGER FISCALIZACION</title>
	<link rel="stylesheet" href="jquery/jquery-ui/jquery-ui.css" type="text/css" />
	<link rel="stylesheet" href="css/estilos.css" type="text/css" />
	<!-- include the core styles -->
	<link rel="stylesheet" href="alertify/alertify.css" />
	<!-- include a theme, can be included into the core instead of 2 separate files -->
	<link rel="stylesheet" href="alertify/alertify.default.css" />
</head>
<style>

</style>

<body style="background: transparent !important;">

	<head>
		<div class="encabezado">
			<img src="images/logocr.gif" alt="">
			<h2 class="titulo">Sistema de Gestión - Siger Fiscalización - Control Tributario</h2>
		</div>
	</head>
	<!--
	<nav>
		Barra de Navegacion
	</nav>
	-->
	<section>
		<div class="contenedor">
			<form action="" name="frmperiodo" id="frmperiodo" class="frmperiodo">
				<div class="centrar" id="periodo">
					<ul>
						<li>
							<h2>Periodo del Informe</h2>
							<span class="required_notification">* Datos requeridos</span>
						</li>
						<li>
							<label for="finicio">Indique el Mes: </label>
							<!--<input type="text" id="finicio" maxlength="10" readonly="readonly" required>
							<span class="form_hint">Formato correcto: "00/00/0000"</span>-->
							<?php
							$meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
							$actual = (int)date("m");
							?>
							<select name="cboMeses" id="cboMeses">
								<?php
								$anno_act = date("Y");
								if ($anno_act == 2017) {
									$x = 4;
								} else {
									$x = 0;
								}
								for ($i = $x; $i <= 11; $i++) { ?>
									<option value="<?php echo $i + 1; ?>" <?php if (($i + 1) == $actual) echo 'selected'; ?>><?php echo $meses[$i]; ?></option>
								<?php } ?>
							</select> <?php echo $_GET['cboMeses']; ?>
						</li>
						<li>
							<label for="ffin">Indique el Año: </label>
							<!--<input type="text" id="ffin" maxlength="10" readonly="readonly" required>
							<span class="form_hint">Formato correcto: "00/00/0000"</span>-->
							<?php $anno_actual = date("Y"); ?>
							<select name="cboAnnos" id="cboAnnos">
								<?php
								if ($anno_actual == 2017) {
									$x = 0;
								} else {
									$x = $anno_actual - 2017;
								}
								for ($i = $anno_actual - $x; $i <= $anno_actual; $i++) { ?>
									<option value="<?php echo $i; ?>" <?php if ($i == $anno_actual) echo 'selected'; ?>><?php echo $i; ?></option>
								<?php } ?>
							</select>
						</li>
						<p class="Aviso">!!!... Ejecutar uno a uno los botones en el orden indicado, por favor no recargar la pagina mientras el proceso esta CARGANDO ...!!!</p>
					</ul>
				</div>
			</form>

			<div class="clasediv" id="casosproceso">
				<h3 class="TituloDiv">1. Casos en Proceso</h3>
				<button class="boton botonoptions" id="btnproceso">1.1 Casos en Proceso</button>
				<button class="boton botonoptions" id="btnformatoproceso">1.2 Formato Casos en Proceso</button>
			</div>

			<div class="clasediv" id="informe_gestion">
				<h3 class="TituloDiv">2. Resultados Mensual</h3>
				<button class="boton botonoptions" id="btninformegestion">2.1 Informe de Gestión</button>
				<button class="boton botonoptions" id="btnoperativos">2.2 Operativos</button>
				<button class="boton botonoptions" id="btnresultadomensual">2.3 Resultado Mensual</button>
			</div>

			<div class="clasediv" id="fiscalizaciones">
				<h3 class="TituloDiv">3. Fiscalizaciones</h3>
				<button class="boton botonoptions" id="btnpuntuales">3.1 Reporte de Fiscalizaciones</button>
				<button class="boton botonoptions" id="btnotrosprogramas">3.2 Otros Programas de Control Fiscal</button>
			</div>

			<div class="clasediv" id="siger">
				<h3 class="TituloDiv">4. Siger Fiscalizacion</h3>
				<button class="boton botonoptions" id="btnsigerFF">4.1 Fuerza Fiscal</button>
				<button class="boton botonoptions" id="btnsiger42">4.2 Generar Siger</button>
			</div>

			<div class="clasediv" id="practicar">
				<h3 class="TituloDiv">5. Practicar Fiscalizacion</h3>
				<button class="boton botonoptions" id="btnpf">5.1 Generar Practicar Fiscalización</button>
			</div>

			<div class="clasediv" id="verificacion">
				<h3 class="TituloDiv">6. Verificar Deberes Formales</h3>
				<button class="boton botonoptions" id="btnvdf">6.1 Generar Verificar Deberes Formales</button>
			</div>


			<div class="clasediv" id="controlesinternos">
				<h3 class="TituloDiv">7. Controles Internos</h3>
				<button class="boton botonoptions" id="btn_controles">7.1 Generar Controles</button>
			</div>

			<div class="clasediv" id="reportes">
				<h3 class="TituloDiv">Reportes Generados</h3>
				<article class="resultados" id="contenido__rpt"></article>
			</div>

		</div>
	</section>
	<!--
	<footer>
		Pie de página
	</footer>
	-->

	<!-- Libreria jQuery -->
	<script type='text/javascript' src="jquery/jquery.js"></script>
	<script type='text/javascript' src="jquery/jquery-ui/jquery-ui.js"></script>
	<script type='text/javascript' src="jquery/jquery-blockUI/jquery.blockUI.js"></script>
	<script src="alertify/alertify.min.js"></script>
	<script type='text/javascript' src="funciones/funciones_jquery.js"></script>
</body>

</html>