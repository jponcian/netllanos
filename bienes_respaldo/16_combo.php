<?php
session_start();
include "../conexion.php";

if (!isset($_SESSION['conexionsqli'])) {
    // Manejar el error de conexión, si es necesario
    exit('Error de conexión.');
}

$conn = $_SESSION['conexionsqli'];
$sede = isset($_GET['sede']) ? intval($_GET['sede']) : 0;
$division_actual = isset($_GET['division']) ? intval($_GET['division']) : 0;
$tipo = isset($_POST['tipo']) ? intval($_POST['tipo']) : 0;

$output = '<option value="0">-- Seleccione --</option>';

// Cargar Divisiones
if ($tipo === 1) {
    $sql = "";
    if ($_SESSION['ADMINISTRADOR'] > 0 || $division_actual == 9) {
        $sql = "SELECT z.division, z.descripcion 
                FROM bn_areas b 
                INNER JOIN z_jefes_detalle z ON z.division = b.division 
                WHERE z.id_sector = ? 
                GROUP BY z.division, z.descripcion 
                ORDER BY z.division ASC, z.descripcion ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $sede);
    } else {
        $sql = "SELECT z.division, z.descripcion 
                FROM bn_areas b 
                INNER JOIN z_jefes_detalle z ON z.division = b.division 
                WHERE z.id_sector = ? AND z.division = ? 
                GROUP BY z.division, z.descripcion 
                ORDER BY z.division ASC, z.descripcion ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $sede, $_SESSION['DIVISION_USUARIO']);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $output .= '<option value="' . $row['division'] . '">' . htmlspecialchars($row['descripcion']) . '</option>';
    }
    $stmt->close();
}

// Cargar Funcionarios
if ($tipo === 3) {
    $sql = "SELECT cedula, Nombres, Apellidos FROM z_empleados WHERE sector = ? AND division = ? ORDER BY Nombres";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $sede, $division_actual);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $output .= '<option value="' . $row['cedula'] . '">' . htmlspecialchars($row['Nombres'] . ' ' . $row['Apellidos']) . '</option>';
    }
    $stmt->close();
}

echo $output;
?>