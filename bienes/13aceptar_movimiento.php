<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 114;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------
?>
<!DOCTYPE html>
<html>

<head>
	<?php include "../funciones/headNew.php"; ?>
	<title>Aprobar Movimiento Bienes</title>
</head>

<body style="background: transparent !important;">
	<form name="form1" id="form1" method="post">
		<div class="mx-auto" style="width:80%;">
			<div class="card mb-2">
				<div class="card-header bg-danger text-white text-center py-2">
					<u>Aprobar Movimiento de Bien(es) Nacional(es)</u>
				</div>
				<div class="card-body py-2">
					<div class="row align-items-center">
						<div class="col-md-4 my-1">
							<label for="OSEDE" class="form-label small"><strong>Dependencia:</strong></label>
							<select name="OSEDE" id="OSEDE" class="form-select form-select-sm"
								onchange="cargar_combo(1, this.value);">
								<option value="0">Seleccione</option>
								<?php
								$consulta_x = 'SELECT id_sector_actual, sector_actual FROM vista_bienes_reasignaciones_solicitadas WHERE por_reasignar = 2 GROUP BY id_sector_actual';
								$tabla_x = mysqli_query($_SESSION['conexionsqli'], $consulta_x);
								while ($registro_x = mysqli_fetch_array($tabla_x)) {
									echo '<option value="' . $registro_x['id_sector_actual'] . '">' . $registro_x['sector_actual'] . '</option>';
								}
								?>
							</select>
						</div>
						<div class="col-md-6 my-1">
							<label for="ODIVISION" class="form-label small"><strong>División:</strong></label>
							<select name="ODIVISION" id="ODIVISION" class="form-select form-select-sm"
								onchange="cargar_tabla();">
								<option value="0">Seleccione</option>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="div1" class="mt-3"></div>
	</form>
	<?php include "../funciones/footNew.php"; ?>

	<script language="JavaScript">
		function procesarAccion(accion) {
			var formData = $('#form1').serializeArray();
			formData.push({ name: 'accion', value: accion });

			var title = (accion === 'aprobar') ? '¿Aprobar Reasignación?' : '¿Devolver Reasignación?';
			var text = (accion === 'aprobar') ? 'Los bienes seleccionados serán asignados a su nueva área.' : 'Los bienes seleccionados serán devueltos a su origen.';
			var confirmButtonText = (accion === 'aprobar') ? 'Sí, ¡aprobar!' : 'Sí, ¡devolver!';

			Swal.fire({
				title: title,
				text: text,
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#dc3545',
				cancelButtonColor: '#6c757d',
				confirmButtonText: confirmButtonText,
				cancelButtonText: 'Cancelar'
			}).then((result) => {
				if (result.isConfirmed) {
					$.ajax({
						type: "POST",
						url: "13_procesar_aceptacion.php",
						data: $.param(formData),
						dataType: 'json',
						success: function (response) {
							if (response.success) {
								Swal.fire('¡Éxito!', response.message, 'success').then(() => {
									cargar_tabla();
								});
							} else {
								Swal.fire('Error', response.message, 'error');
							}
						},
						error: function () {
							Swal.fire('Error de Comunicación', 'No se pudo conectar con el servidor.', 'error');
						}
					});
				}
			});
		}

		function cargar_tabla() {
			var sede = $('#OSEDE').val();
			var div1 = $('#ODIVISION').val();
			if (sede > 0 && div1 > 0) {
				$('#div1').load('13a_tabla_aceptar.php?sede1=' + sede + '&div1=' + div1);
			} else {
				$('#div1').html('');
			}
		}

		function cargar_combo(tipo, val) {
			$('#div1').html('');
			Swal.fire({
				position: 'bottom-end',
				icon: 'info',
				title: 'Por favor espere la carga de datos...',
				showConfirmButton: false,
				timer: 1500,
				toast: true
			});
			$.ajax({
				type: "POST",
				url: '13_combo.php?sede=' + val,
				data: 'tipo=' + tipo,
				success: function (resp) { $('#ODIVISION').html(resp); }
			});
		}
	</script>
</body>

</html>