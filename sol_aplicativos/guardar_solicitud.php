<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();
header('Content-Type: application/json');
include "../conexion.php";
include "../auxiliar.php";

// Conectar a la BD illanos en localhost puerto 3306 para funcionarios
try {
    $pdo_func = new PDO('mysql:host=localhost;port=3306;dbname=illanos;charset=utf8', 'root', '');
    $pdo_func->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Error conectando a la BD illanos en localhost puerto 3306']);
    exit();
}

if (!isset($_SESSION['conexionsqli'])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Datos JSON inválidos']);
    exit();
}
$funcionarios = isset($data['funcionarios']) ? $data['funcionarios'] : [];

if (empty($funcionarios)) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'No hay funcionarios']);
    exit();
}

$count_func = 0;
$count_app = 0;

echo "Funcionarios recibidos: " . json_encode($funcionarios) . "\n";

$mysqli = $_SESSION['conexionsqli'];

if (!$mysqli || !is_object($mysqli)) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Conexión a BD principal no disponible']);
    exit();
}

echo "DB: " . $mysqli->query("SELECT DATABASE()")->fetch_row()[0] . "\n";

// Insertar solicitud
$sql = "INSERT INTO sol_solicitudes (cedula_solicitante) VALUES (?)";
$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Error en consulta solicitud: ' . $mysqli->error]);
    exit();
}
$stmt->bind_param('i', $_SESSION['CEDULA_USUARIO']);
$stmt->execute();
$idSolicitud = $mysqli->insert_id;
$stmt->close();

if ($idSolicitud == 0) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Error insertando solicitud']);
    exit();
}

foreach ($funcionarios as $func) {
    if (empty($func['cedula']) || empty($func['aplicativos']))
        continue;

    // Obtener datos del funcionario de la BD illanos
    $stmt_func = $pdo_func->prepare("SELECT CONCAT(f.nombre, ' ', f.apellido) as nombre, '' as Cargo, d.id_divi as id_division, dv.descripcion as division FROM funcionarios f INNER JOIN designacion d ON f.id = d.id_func LEFT JOIN division dv ON d.id_divi = dv.id WHERE d.estatus = 'activo' AND f.cedula = ?");
    $stmt_func->execute([$func['cedula']]);
    $emp = $stmt_func->fetch(PDO::FETCH_ASSOC);
    echo "Emp para cedula {$func['cedula']}: " . json_encode($emp) . "\n";
    if (!$emp)
        continue;    // Insertar funcionario
    $sql = "INSERT INTO sol_funcionarios (id_solicitud, cedula, nombre, cargo, division, usuario) VALUES (?, ?, ?, ?, ?, ?)";
    echo "Consulta sol_funcionarios: $sql\n";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Error en consulta funcionario: ' . $mysqli->error]);
        exit();
    }
    $usuario = isset($func['usuario']) ? $func['usuario'] : '';
    $stmt->bind_param('isssss', $idSolicitud, $func['cedula'], $emp['nombre'], $emp['Cargo'], $emp['division'], $usuario);
    if (!$stmt->execute()) {
        echo "Execute failed for func: " . $stmt->error . "\n";
    }
    $idFuncionario = $mysqli->insert_id;
    echo "idFuncionario: $idFuncionario\n";
    $stmt->close();

    if ($idFuncionario == 0) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Error insertando funcionario']);
        exit();
    }

    $count_func++;

    // Insertar aplicativos
    foreach ($func['aplicativos'] as $appId) {
        echo "Procesando appId: $appId\n";
        $accion = 'INCLUSION';
        $sql = "INSERT INTO sol_aplicativos_solicitados (id_funcionario, id_aplicativo, id_solicitud, accion) VALUES (?, ?, ?, ?)";
        echo "Consulta sol_aplicativos_solicitados: $sql\n";
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Error en consulta aplicativo: ' . $mysqli->error]);
            exit();
        }
        $stmt->bind_param('iiis', $idFuncionario, $appId, $idSolicitud, $accion);
        if (!$stmt->execute()) {
            echo "Execute failed for app: " . $stmt->error . "\n";
        }
        $stmt->close();

        if ($stmt->errno) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Error insertando aplicativo: ' . $stmt->error]);
            exit();
        }

        $count_app++;
    }
}

ob_end_clean();
echo json_encode(['success' => true, 'id' => $idSolicitud, 'pdf_url' => 'generar_pdf.php?id=' . $idSolicitud, 'count_func' => $count_func, 'count_app' => $count_app]);
