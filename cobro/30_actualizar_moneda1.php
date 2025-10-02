<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
    header("Location: index.php?errorusuario=val");
    exit();
}

// --- INICIO DE LA AUTOMATIZACIÓN ---

/**
 * Obtiene la tasa del Euro y su fecha desde la web del BCV.
 * @return array|null Un array con 'tasa' y 'fecha' (YYYY-MM-DD) o null si falla.
 */
function obtenerTasaEuroBCV() {
    $url = 'https://www.bcv.org.ve/';
    $opciones = ['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]];
    $contexto = stream_context_create($opciones);
    $html = @file_get_contents($url, false, $contexto);

    if ($html === false) {
        return null; // No se pudo obtener el contenido de la página.
    }

    $patronTasa = '~<img src="/sites/default/files/euro-04_2.png".*?<strong>\s*([\d,.]+)\s*</strong>~s';
    $patronFecha = '~<span class="date-display-single"[^>]+>([^<]+)</span>~';

    $tasa = null;
    $fecha = null;

    if (preg_match($patronTasa, $html, $matchesTasa)) {
        $tasaLimpia = str_replace('.', '', $matchesTasa[1]);
        $tasa = (float) str_replace(',', '.', $tasaLimpia);
    }

    if (preg_match($patronFecha, $html, $matchesFecha)) {
        $fechaStr = trim($matchesFecha[1]);
        $meses_es = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $meses_en = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $fechaStr_en = str_replace($meses_es, $meses_en, $fechaStr);
        $timestamp = strtotime(preg_replace('~^[^,]+,~', '', $fechaStr_en));
        if ($timestamp) {
            $fecha = date('Y-m-d', $timestamp);
        }
    }

    if ($tasa && $fecha) {
        return ['tasa' => $tasa, 'fecha' => $fecha];
    }

    return null;
}

$mensaje_automatico = "";
// Usar la conexión mysqli de la sesión
$conn = $_SESSION['conexionsqli'];

if ($conn && !$conn->connect_error) {
    $datosBCV = obtenerTasaEuroBCV();

    if ($datosBCV) {
        $stmt_ultima_fecha = $conn->query("SELECT MAX(FechaAplicacion) as ultima_fecha FROM a_moneda_cambio WHERE moneda = 'EUR'");
        $ultima_fecha_db = $stmt_ultima_fecha->fetch_column(0);

        if (!$ultima_fecha_db || $datosBCV['fecha'] > $ultima_fecha_db) {
            $sql = "INSERT INTO a_moneda_cambio (FechaAplicacion, moneda, descripcion, valor, usuario) VALUES (?, 'EUR', 'Euro', ?, ?)";
            $stmt = $conn->prepare($sql);
            $usuario = 'AUTOMATICO_BCV';
            $stmt->bind_param('sds', $datosBCV['fecha'], $datosBCV['tasa'], $usuario);
            if ($stmt->execute()) {
                 $mensaje_automatico = "¡Éxito! Tasa del Euro actualizada automáticamente a " . $datosBCV['tasa'] . " para la fecha " . date('d-m-Y', strtotime($datosBCV['fecha'])) . ".";
            } else {
                $mensaje_automatico = "Error al insertar la nueva tasa en la base de datos.";
            }
            $stmt->close();
        } else {
            $mensaje_automatico = "La tasa de cambio para el Euro ya está actualizada.";
        }
    } else {
        $mensaje_automatico = "No se pudo obtener la tasa de cambio del BCV. Puede intentarlo manually.";
    }
} else {
    $mensaje_automatico = "Error de conexión a la base de datos. No se pudo realizar la actualización automática.";
}

// --- FIN DE LA AUTOMATIZACIÓN ---


// --- LÓGICA PARA EL FORMULARIO MANUAL (Adaptada a mysqli) ---
if (isset($_POST['Guardar']) && $_POST['Guardar'] == 'Guardar') {
    if ($conn && !$conn->connect_error) {
        $stmt_ultima_fecha = $conn->query("SELECT MAX(FechaAplicacion) as ultima_fecha FROM a_moneda_cambio");
        $ultima_fecha_db = $stmt_ultima_fecha->fetch_column(0);
        
        $fecha_post = $_POST['ofecha'];
        $monto_post = (float) $_POST['omonto'];
        
        if ($fecha_post <= $ultima_fecha_db) {
            echo "<script>alert('La Fecha que desea cargar es inferior o igual a la última registrada!');</script>";
        } elseif ($fecha_post > date('Y-m-d')) {
            echo "<script>alert('La Fecha que desea cargar no puede ser mayor a la actual!');</script>";
        } else {
            list($moneda, $descripcion) = explode('-', $_POST['OTIPO']);

            $stmt_delete = $conn->prepare("DELETE FROM a_moneda_cambio WHERE FechaAplicacion = ? AND moneda = ?");
            $stmt_delete->bind_param('ss', $fecha_post, $moneda);
            $stmt_delete->execute();
            $stmt_delete->close();

            if ($monto_post > 0) {
                $sql = "INSERT INTO a_moneda_cambio (FechaAplicacion, moneda, descripcion, valor, usuario) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $usuario_manual = $_SESSION['CEDULA_USUARIO'];
                $stmt->bind_param('sssds', $fecha_post, $moneda, $descripcion, $monto_post, $usuario_manual);
                if($stmt->execute()){
                    echo "<script>alert('Valor Actualizado Exitosamente!');</script>";
                } else {
                    echo "<script>alert('Error al guardar manualmente.');</script>";
                }
                $stmt->close();
            }
        }
    } else {
        echo "<script>alert('Error de conexión a la base de datos.');</script>";
    }
}

?>
<html>
<head>
    <title>Actualizar Moneda</title>
    <meta http-equiv="X-UA-Compatible" content="IE=9" />
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body style="background: transparent !important;">
    <div class="container mt-4" style="background: transparent !important;">
        <?php include "../titulo.php"; ?>

        <?php if (!empty($mensaje_automatico)): ?>
            <div class="alert alert-info text-center" role="alert">
                <strong>Info BCV:</strong> <?php echo htmlspecialchars($mensaje_automatico); ?>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-danger text-white text-center">
                        Moneda de Mayor Valor Publicada por el Banco Central de Venezuela
                    </div>
                    <div class="card-body">
                        <form name="form1" method="post" action="">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="omonto" class="form-label"><strong>Monto (Bs):</strong></label>
                                    <input name="omonto" id="omonto" type="text" class="form-control" placeholder="Ingrese" size="10">
                                </div>
                                <div class="col-md-4">
                                    <label for="OTIPO" class="form-label"><strong>Moneda:</strong></label>
                                    <select name="OTIPO" id="OTIPO" class="form-select">
                                        <option value="EUR-Euro">Euro</option>
                                        <option value="USD-Dolar">Dolar</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="ofecha" class="form-label"><strong>Fecha Reg:</strong></label>
                                    <input name="ofecha" id="ofecha" type="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" size="8">
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-md-4 d-grid">
                                    <input name="Guardar" type="submit" class="btn btn-primary" value="Guardar">
                                </div>
                            </div>
                        </form>
                        <div class="text-center mt-4">
                            <a target="_blank" rel="noopener noreferrer" href="https://www.bcv.org.ve/" class="btn btn-info">
                                ::Ver Página del BCV::
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        // --- MOSTRAR ÚLTIMO VALOR REGISTRADO ---
        if ($conn && !$conn->connect_error) {
            $resultado = $conn->query("SELECT * FROM a_moneda_cambio ORDER BY FechaAplicacion DESC LIMIT 1");
            $ultimo_registro = $resultado->fetch_assoc();
        } else {
            $ultimo_registro = null;
        }
        ?>
        <?php if ($ultimo_registro): ?>
        <div class="row justify-content-center mt-4">
            <div class="col-md-8">
                <div class="alert alert-success text-center" role="alert">
                    <h4 class="alert-heading">
                        El &Uacute;ltimo valor de la moneda fue de: <strong><?php echo number_format($ultimo_registro['valor'], 4, ',', '.'); ?> Bs</strong>
                        de Fecha: <strong><?php echo date('d-m-Y', strtotime($ultimo_registro['FechaAplicacion'])); ?></strong>,
                        Por: <strong><?php echo htmlspecialchars($ultimo_registro['usuario']); ?></strong>
                    </h4>
                </div>
            </div>
        </div>
        <?php else: ?>
             <div class="alert alert-warning text-center">No se pudo mostrar el último valor. Error de conexión.</div>
        <?php endif; ?>

        <?php include "../pie.php"; ?>
    </div>
</body>
</html>
