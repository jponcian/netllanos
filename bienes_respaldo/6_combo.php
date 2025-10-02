<?php
session_start();
include "../conexion.php";

if (!isset($_SESSION['conexionsqli'])) {
	// Manejar el error de conexión como sea apropiado
	die("Error de conexión a la base de datos.");
}

$tipo = isset($_POST['tipo']) ? (int) $_POST['tipo'] : 0;

if ($tipo === 1) {
	$sede = isset($_GET['sede']) ? (int) $_GET['sede'] : 0;

	echo '<option value="0">Seleccione</option>';

	if ($sede > 0) {
		$consulta = "SELECT division_actual, id_division_actual 
                     FROM vista_bienes_reasignaciones_solicitadas 
                     WHERE por_reasignar = 2 AND id_sector_actual = ? 
                     GROUP BY id_division_actual";

		if ($stmt = mysqli_prepare($_SESSION['conexionsqli'], $consulta)) {
			mysqli_stmt_bind_param($stmt, "i", $sede);
			mysqli_stmt_execute($stmt);
			$resultado = mysqli_stmt_get_result($stmt);

			while ($registro = mysqli_fetch_array($resultado)) {
				echo '<option value="' . htmlspecialchars($registro['id_division_actual']) . '">' . htmlspecialchars($registro['division_actual']) . '</option>';
			}
			mysqli_stmt_close($stmt);
		}
	}
}

if ($tipo === 2) {
	$sede = isset($_GET['sede']) ? (int) $_GET['sede'] : 0;
	$division = isset($_GET['division']) ? (int) $_GET['division'] : 0;

	echo '<option value="0">Seleccione</option>';

	if ($sede > 0 && $division > 0) {
		$consulta = "SELECT division_destino, id_division_destino 
                     FROM vista_bienes_reasignaciones_solicitadas 
                     WHERE por_reasignar = 2 AND id_sector_actual = ? AND id_division_actual = ? 
                     GROUP BY id_division_destino";

		if ($stmt = mysqli_prepare($_SESSION['conexionsqli'], $consulta)) {
			mysqli_stmt_bind_param($stmt, "ii", $sede, $division);
			mysqli_stmt_execute($stmt);
			$resultado = mysqli_stmt_get_result($stmt);

			while ($registro = mysqli_fetch_array($resultado)) {
				echo '<option value="' . htmlspecialchars($registro['id_division_destino']) . '">' . htmlspecialchars($registro['division_destino']) . '</option>';
			}
			mysqli_stmt_close($stmt);
		}
	}
}
?>