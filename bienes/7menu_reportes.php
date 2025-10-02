<?php
// 7* Módulo Reportes de Bienes - Modernizado
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 77; // Validación de acceso
include "../validacion_usuario.php";

// Conexión mysqli desde sesión
$mysqli = isset($_SESSION['conexionsqli']) && $_SESSION['conexionsqli'] instanceof mysqli ? $_SESSION['conexionsqli'] : null;

// Persistir selección al recargar (POST) y mantener en sesión
$selSede = isset($_POST['OSEDE']) ? intval($_POST['OSEDE']) : (isset($_SESSION['SEDE']) ? intval($_SESSION['SEDE']) : 0);
$selDiv = isset($_POST['ODIVISION']) ? intval($_POST['ODIVISION']) : (isset($_SESSION['DIVISION']) ? intval($_SESSION['DIVISION']) : 0);
$selArea = isset($_POST['OAREA']) ? intval($_POST['OAREA']) : (isset($_SESSION['AREA']) ? intval($_SESSION['AREA']) : 0);

// Guardar en sesión solo si viene de un POST explícito
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$_SESSION['SEDE'] = $selSede;
	$_SESSION['DIVISION'] = $selDiv;
	$_SESSION['AREA'] = $selArea;
}

// Obtener lista de sedes
$sedes = [];
if ($mysqli) {
	$sql_sedes = (!empty($_SESSION['ADMINISTRADOR']) || intval($_SESSION['SEDE_USUARIO']) == 1)
		? "SELECT id_sector, nombre FROM z_sectores WHERE id_sector <= 5 ORDER BY id_sector"
		: "SELECT id_sector, nombre FROM z_sectores WHERE id_sector = " . intval($_SESSION['SEDE_USUARIO']) . " ORDER BY id_sector";

	if ($res_sedes = $mysqli->query($sql_sedes)) {
		while ($r = $res_sedes->fetch_assoc()) {
			$sedes[] = $r;
		}
	}
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Menú de Reportes - Bienes</title>
	<?php include "../funciones/headNew.php"; ?>
	<style>
		.card-header .card-title {
			font-weight: 700;
			color: #dc3545;
			position: relative;
		}

		.card-header .card-title::after {
			content: '';
			display: block;
			height: 3px;
			width: 140px;
			margin-top: 4px;
			border-radius: 2px;
			background: linear-gradient(90deg, #dc3545, #bd2130);
		}

		.btn-reporte {
			border-radius: 8px;
			border: 1px solid transparent;
			padding: 0.6em 1.2em;
			font-size: 1em;
			font-weight: 500;
			font-family: inherit;
			cursor: pointer;
			transition: all 0.25s;
			color: #ffffff;
			margin: 0 5px;
		}

		.btn-reporte:hover {
			filter: brightness(1.1);
			transform: translateY(-2px);
		}

		.btn-reporte-pdf {
			background-color: #dc3545;
		}

		.btn-reporte-html {
			background-color: #c82333;
		}

		.divisor-select-botones {
			border-left: 1px solid #e0e0e0;
			min-height: 100px;
		}

		@media (max-width: 767.98px) {
			.divisor-select-botones {
				display: none !important;
			}
		}

		/* Ajusta el CSS para select2 de OSEDE, ODIVISION y OAREA con anchos 20% mayores */
		#OSEDE.select2-hidden-accessible+.select2-container {
			min-width: 168px;
			width: 192px !important;
			max-width: 100%;
		}

		#ODIVISION.select2-hidden-accessible+.select2-container {
			min-width: 264px;
			width: 288px !important;
			max-width: 100%;
		}

		#OAREA.select2-hidden-accessible+.select2-container {
			min-width: 384px;
			width: 120% !important;
			max-width: 100%;
		}

		#OSEDE.select2-hidden-accessible+.select2-container .select2-selection--single {
			min-width: 168px;
			width: 192px !important;
			max-width: 100%;
			box-sizing: border-box;
		}

		#ODIVISION.select2-hidden-accessible+.select2-container .select2-selection--single {
			min-width: 264px;
			width: 288px !important;
			max-width: 100%;
			box-sizing: border-box;
		}

		#OAREA.select2-hidden-accessible+.select2-container .select2-selection--single {
			min-width: 384px;
			width: 120% !important;
			max-width: 100%;
			box-sizing: border-box;
		}

		@media (max-width: 767.98px) {

			#OSEDE.select2-hidden-accessible+.select2-container,
			#ODIVISION.select2-hidden-accessible+.select2-container,
			#OAREA.select2-hidden-accessible+.select2-container,
			#OSEDE.select2-hidden-accessible+.select2-container .select2-selection--single,
			#ODIVISION.select2-hidden-accessible+.select2-container .select2-selection--single,
			#OAREA.select2-hidden-accessible+.select2-container .select2-selection--single {
				min-width: 100% !important;
				width: 100% !important;
			}
		}

		/* Agrega estilos para los títulos de los visores */
		.visor-titulo-pdf {
			color: #dc3545;
			font-weight: bold;
		}

		.visor-titulo-html {
			color: #007bff;
			font-weight: bold;
		}
	</style>
</head>

<body style="background: transparent !important;">
	<div class="container-fluid py-3">


		<div class="row">
			<div class="col-12 col-lg-10 col-xl-8 mx-auto">
				<div class="card shadow-sm">
					<div class="card-header">
						<span class="card-title"><i class="fas fa-file-alt mr-2"></i> Generar Inventario</span>
					</div>
					<div class="card-body">
						<div class="d-flex flex-column flex-md-row align-items-stretch justify-content-between">
							<form name="form1" id="form1" method="post" class="flex-grow-1">
								<p class="text-muted small">Seleccione los filtros para generar el reporte de
									inventario.</p>
								<div class="form-row align-items-end">
									<div class="form-group col-12 col-md-4 mb-2 mb-md-0">
										<label for="OSEDE" class="small font-weight-bold">Dependencia</label>
										<select name="OSEDE" id="OSEDE" class="form-control form-control-sm select2">
											<option value="0">Todas</option>
											<?php foreach ($sedes as $s): ?>
												<option value="<?php echo (int) $s['id_sector']; ?>" <?php echo ($selSede == (int) $s['id_sector']) ? 'selected' : ''; ?>>
													<?php echo htmlspecialchars($s['nombre']); ?>
												</option>
											<?php endforeach; ?>
										</select>
									</div>
									<div class="form-group col-12 col-md-4 mb-2 mb-md-0">
										<label for="ODIVISION" class="small font-weight-bold">División</label>
										<select name="ODIVISION" id="ODIVISION"
											class="form-control form-control-sm select2">
											<option value="0">Todas</option>
										</select>
									</div>
									<div class="form-group col-12 col-md-4">
										<label for="OAREA" class="small font-weight-bold">Área</label>
										<select name="OAREA" id="OAREA" class="form-control form-control-sm select2">
											<option value="0">Todas</option>
										</select>
									</div>
								</div>
							</form>
							<div class="d-none d-md-block align-self-stretch mx-3 divisor-select-botones"></div>
							<div class="d-flex flex-column align-items-center justify-content-center mt-3 mt-md-0 ml-md-3"
								style="min-width:150px;">
								<button type="button" class="btn-reporte btn-reporte-pdf mb-2 w-100"
									onclick="abrirReporte('pdf')">
									<i class="fas fa-file-pdf mr-2"></i> Generar PDF
								</button>
								<button type="button" class="btn-reporte btn-reporte-html w-100"
									onclick="abrirReporte('html')">
									<i class="fas fa-file-alt mr-2"></i> Generar HTML
								</button>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Visor de HTML embebido, independiente y ancho -->
	<div id="visor-html-container" class="container-fluid mt-4" style="display:none; max-width: 98vw;">
		<div class="row justify-content-center">
			<div class="col-12 col-lg-11 col-xl-10">
				<div class="d-flex justify-content-between align-items-center mb-2">
					<span class="visor-titulo-html"><i class="fas fa-file-alt mr-2"></i>Vista previa HTML</span>
					<button id="btnCopiarHTML" class="btn btn-sm btn-danger" title="Copiar tabla HTML"><i
							class="fas fa-copy"></i> Copiar tabla</button>
				</div>
				<div
					style="border:1px solid #ccc; border-radius:6px; overflow:hidden; min-height:600px; background:#f8f9fa;">
					<iframe id="visorHTML" src="" style="width:100%; min-height:600px; border:none;"></iframe>
				</div>
			</div>
		</div>
	</div>

	<!-- Visor de PDF embebido, ahora independiente y más ancho -->
	<div id="visor-pdf-container" class="container-fluid mt-4" style="display:none; max-width: 98vw;">
		<div class="row justify-content-center">
			<div class="col-12 col-lg-11 col-xl-10">
				<div class="d-flex justify-content-between align-items-center mb-2">
					<span class="visor-titulo-pdf"><i class="fas fa-file-pdf mr-2"></i>Vista previa PDF</span>
				</div>
				<div
					style="border:1px solid #ccc; border-radius:6px; overflow:hidden; min-height:600px; background:#f8f9fa;">
					<iframe id="visorPDF" src="" style="width:100%; min-height:600px; border:none;"></iframe>
				</div>
			</div>
		</div>
	</div>

	<?php include "../funciones/footNew.php"; ?>
	<script>
		// Helpers
		const toast = (icon, msg) => Swal.fire({ position: 'bottom-end', icon, title: msg, showConfirmButton: false, timer: 2500, toast: true });

		function abrirReporte(tipo) {
			const sede = document.getElementById('OSEDE').value || 0;
			const division = document.getElementById('ODIVISION').value || 0;
			const area = document.getElementById('OAREA').value || 0;
			if (tipo === 'html') {
				const url = `reportes/inventario_html.php?area=${encodeURIComponent(area)}&division=${encodeURIComponent(division)}&sede=${encodeURIComponent(sede)}`;
				document.getElementById('visorHTML').src = url;
				document.getElementById('visor-html-container').style.display = '';
				// Scroll automático al visor HTML
				setTimeout(() => {
					const visor = document.getElementById('visor-html-container');
					const menu = document.querySelector('.navbar, .main-header, header, .menu-superior');
					let offset = 0;
					if (menu) {
						offset = menu.offsetHeight + 12;
					}
					const top = visor.getBoundingClientRect().top + window.pageYOffset - offset;
					window.scrollTo({ top, behavior: 'smooth' });
				}, 300);
			} else if (tipo === 'pdf') {
				const url = `reportes/x_inventario.php?area=${encodeURIComponent(area)}&division=${encodeURIComponent(division)}&sede=${encodeURIComponent(sede)}`;
				document.getElementById('visorPDF').src = url;
				document.getElementById('visor-pdf-container').style.display = '';
				setTimeout(() => {
					const visor = document.getElementById('visor-pdf-container');
					const menu = document.querySelector('.navbar, .main-header, header, .menu-superior');
					let offset = 0;
					if (menu) {
						offset = menu.offsetHeight + 12;
					}
					const top = visor.getBoundingClientRect().top + window.pageYOffset - offset;
					window.scrollTo({ top, behavior: 'smooth' });
				}, 300);
			}
		}

		// Carga de combos anidados por AJAX
		function cargarDivisiones(sedeId) {
			toast('info', 'Cargando divisiones...');
			return $.ajax({
				type: 'POST',
				url: `7_combo.php?sede=${sedeId}`,
				data: 'tipo=1'
			}).done(function (resp) {
				$('#ODIVISION').html(resp);
			});
		}

		function cargarAreas(sedeId, divisionId) {
			toast('info', 'Cargando áreas...');
			return $.ajax({
				type: 'POST',
				url: `7_combo.php?sede=${sedeId}&division=${divisionId}`,
				data: 'tipo=3'
			}).done(function (resp) {
				$('#OAREA').html(resp);
			});
		}

		document.addEventListener('DOMContentLoaded', function () {
			// Inicializar Select2
			if ($.fn.select2) {
				$('.select2').select2({ width: '100%' });
			}

			// Valores iniciales desde PHP
			const selSede = <?php echo (int) $selSede; ?>;
			const selDiv = <?php echo (int) $selDiv; ?>;
			const selArea = <?php echo (int) $selArea; ?>;

			// Restaurar combos si hay selecciones previas
			if (selSede > 0) {
				cargarDivisiones(selSede).done(() => {
					if (selDiv > 0) {
						$('#ODIVISION').val(String(selDiv)).trigger('change.select2');
						cargarAreas(selSede, selDiv).done(() => {
							if (selArea > 0) {
								$('#OAREA').val(String(selArea)).trigger('change.select2');
							}
						});
					}
				});
			}

			// Eventos de cambio para combos dependientes y recarga dinámica
			$('#OSEDE').on('change', function () {
				const sedeId = $(this).val();
				$('#ODIVISION').html('<option value="0">Todas</option>').trigger('change.select2');
				$('#OAREA').html('<option value="0">Todas</option>').trigger('change.select2');
				if (sedeId > 0) {
					cargarDivisiones(sedeId);
				}
			});

			$('#ODIVISION').on('change', function () {
				const sedeId = $('#OSEDE').val();
				const divisionId = $(this).val();
				$('#OAREA').html('<option value="0">Todas</option>').trigger('change.select2');
				if (sedeId > 0 && divisionId > 0) {
					cargarAreas(sedeId, divisionId);
				}
			});
		});

		document.getElementById('btnCopiarHTML').onclick = function (e) {
			e.preventDefault();
			const iframe = document.getElementById('visorHTML');
			try {
				const doc = iframe.contentDocument || iframe.contentWindow.document;
				// Busca la primera tabla visible
				const tabla = doc.querySelector('table');
				if (tabla) {
					// Crea un range y selecciona la tabla
					const range = doc.createRange();
					range.selectNode(tabla);
					const selection = doc.getSelection();
					selection.removeAllRanges();
					selection.addRange(range);
					// Copia al portapapeles
					doc.execCommand('copy');
					selection.removeAllRanges();
					toast('success', '¡Tabla copiada al portapapeles!');
				} else {
					toast('warning', 'No se encontró ninguna tabla para copiar.');
				}
			} catch (err) {
				toast('error', 'No se pudo copiar la tabla.');
			}
		};
	</script>
</body>

</html>