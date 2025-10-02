<?php
session_start();
include "../../auxiliar.php";
include('../../conexion.php');
?>
<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=Edge" />
	<!-------------------------------------------------------------->
	<script language="javascript" src="../../lib/jquery/jquery-3.7.1.min.js"></script>
	<script language="javascript" src="../../lib/bootstrap-5/js/bootstrap.min.js"></script>
	<script language="javascript" src="../../lib/sweetalert2.all.min.js"></script>
	<!-------------------------------------------------------------->
	<link rel="stylesheet" href="../../lib/bootstrap-5/css/bootstrap.min.css">
	<link rel="stylesheet" href="../../lib/sweetalert2.min.css">
	<!-------------------------------------------------------------->
</head>
<?php
$fecha = fecha_a_numero("" . date('Y/m/') . "01");
//$dia=dia($fecha);
$mes = date('m');
//$anno=anno($fecha);
$nombre = date('N', ($fecha));
?>

<body style="background: transparent !important;">
	<table class="table table-striped table-hover">
		<thead class="thead-dark">
			<tr align="center">
				<th scope="col" colspan="7"><?php echo date('F'); ?></th>
			</tr>
			<tr align="center">
				<th scope="col">L</th>
				<th scope="col">M</th>
				<th scope="col">M</th>
				<th scope="col">J</th>
				<th scope="col">V</th>
				<th scope="col">S</th>
				<th scope="col">D</th>
				<!--
		<th scope="col">LUNES</th>
		<th scope="col">MARTES</th>
		<th scope="col">MIERCOLES</th>
		<th scope="col">JUEVES</th>
		<th scope="col">VIERNES</th>
		<th scope="col">SABADO</th>
		<th scope="col">DOMINGO</th>
-->
			</tr>
		</thead>
		<tbody>
			<tr align="center">
				<?php
				//------------ PARA RELLENAR
				$i = 1;
				while ($i < $nombre) {
					echo '<td></td>';
					//-----------
					$i++;
				}
				//------------
				while (date('m', $fecha) == $mes) {
					if (date('N', $fecha) == 1) {
						echo "</tr><tr align='center'>";
					}
					//-----------
					echo "<td>"; //"<h6>".date('d/m/Y',$fecha)."</h6>";
				?>
					<div class="card" style="width: 10rem;">
						<div class="card-body">
							<h5 class="card-title"><?php echo date('d', $fecha); ?></h5>
							<?php
							//-----------
							$consulta = "SELECT * FROM z_empleados WHERE (month(fecha_nac) = " . date('m', $fecha) . " and day(fecha_nac) = " . date('d', $fecha) . ");";
							$tabla = mysql_query($consulta);
							//-----------
							if (mysql_num_rows($tabla) > 0) {
								//echo ' <span class="badge text-bg-success">'.mysql_num_rows($tabla).'</span>';
								while ($registro_s = mysql_fetch_object($tabla)) {
							?>
									<p class="card-text">
										<?php
										echo "" . '<div onclick="foto(' . "'" . $registro_s->Nombres . ' ' . $registro_s->Apellidos . "'" . ',' . "'" . $registro_s->Cargo . "'" . ')" class="bg-primary text-white">' . $registro_s->Nombres . '</div>';
										?>
									</p>
							<?php
								}
							} else {
								echo "" . '';
							}
							//-----------
							?>
						</div>
					</div>
				<?php
					echo "</td>";
					//-----------
					$fecha = $fecha + 86400;
				}
				?>
			</tr>
		</tbody>
	</table>
</body>

</html>
<script language="javascript">
	function foto(empleado, cargo) {
		Swal.fire({
			title: empleado,
			text: cargo,
			imageUrl: "https://unsplash.it/400/200",
			imageWidth: 300,
			imageHeight: 200,
			imageAlt: "Foto"
		});
	}
</script>