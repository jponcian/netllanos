<?php
/**
 * Obtiene la tasa del Euro y su fecha desde la web del BCV.
 * Utiliza DOMDocument y DOMXPath para un parsing más robusto del HTML.
 * @return array|null Un array con 'tasa' y 'fecha' (YYYY-MM-DD) o null si falla.
 */
if (!function_exists('obtenerTasaEuroBCV')) {
    function obtenerTasaEuroBCV()
    {
        $url = 'https://www.bcv.org.ve/';
        // Deshabilitar la verificación SSL es un riesgo de seguridad.
        // Se mantiene por compatibilidad, pero lo ideal es configurar el entorno correctamente.
        $opciones = ['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]];
        $contexto = stream_context_create($opciones);
        // Se suprime el warning de file_get_contents para manejar el error manualmente.
        $html = @file_get_contents($url, false, $contexto);
        if ($html === false) {
            // error_log("No se pudo obtener el contenido de la página del BCV.");
            return null;
        }
        $doc = new DOMDocument();
        // Suprimir warnings de HTML mal formado.
        @$doc->loadHTML($html);
        $xpath = new DOMXPath($doc);
        $tasaNode = $xpath->query('//div[@id="euro"]//strong')->item(0);
        $fechaNode = $xpath->query('//span[contains(@class, "date-display-single")]')->item(0);
        if (!$tasaNode || !$fechaNode) {
            // error_log("No se encontraron los nodos de tasa o fecha en el HTML del BCV.");
            return null;
        }
        // Procesar Tasa
        $tasaStr = $tasaNode->textContent;
        $tasaLimpia = str_replace('.', '', $tasaStr);
        $tasa = (float) str_replace(',', '.', $tasaLimpia);
        // Procesar Fecha
        $fechaStr = trim($fechaNode->textContent);
        // La siguiente lógica para parsear la fecha es frágil.
        // Si la extensión 'intl' está disponible, sería mejor usar IntlDateFormatter.
        // Ejemplo:
        // $fmt = new IntlDateFormatter('es_VE', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
        // $timestamp = $fmt->parse(explode(',', $fechaStr)[1]);
        $meses_es = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        $meses_en = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        // Normalizar a minúsculas para un reemplazo más robusto
        $fechaStr_en = str_replace($meses_es, $meses_en, strtolower($fechaStr));
        // Eliminar el día de la semana (ej. "Lunes, ")
        $fechaSinDia = preg_replace('/^[^,]+,\s*/', '', $fechaStr_en);
        $timestamp = strtotime($fechaSinDia);
        $fecha = $timestamp ? date('Y-m-d', $timestamp) : null;
        if ($tasa > 0 && $fecha) {
            return ['tasa' => $tasa, 'fecha' => $fecha];
        }
        return null;
    }
}
// Check if the main update function already exists to avoid redeclaration errors
if (!function_exists('run_bcv_update')) {
    /**
     * Conecta a la base de datos, obtiene la tasa del BCV y la inserta si es nueva.
     * NOTA DE ARQUITECTURA: Este script se ejecuta en cada carga de página donde es incluido.
     * Esto es ineficiente. Se recomienda encarecidamente convertir esto en un script
     * independiente que se ejecute periódicamente a través de una tarea programada (cron job),
     * por ejemplo, una vez al día.
     */
    function run_bcv_update()
    {
        $db_host = 'localhost';
        $db_name = 'losllanos';
        $db_user = 'root';
        $db_pass = '';
        $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
        try {
            $conn = new PDO($dsn, $db_user, $db_pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log("DB Connection failed in run_bcv_update: " . $e->getMessage());
            return;
        }
        // Primero, verificar si ya existe un registro para hoy o una fecha futura.
        // Si es así, no es necesario continuar con el scraping y la actualización.
        $hoy = date('Y-m-d');
        $stmt_check = $conn->prepare("SELECT COUNT(*) FROM a_moneda_cambio WHERE FechaAplicacion >= ?");
        $stmt_check->execute([$hoy]);
        $count = $stmt_check->fetchColumn();
        // Si se encuentra al menos un registro, se detiene la ejecución de la función.
        if ($count > 0) {
            return;
        }
        $datosBCV = obtenerTasaEuroBCV();
        if ($datosBCV) {
            // Usar prepared statements para todas las consultas.
            $stmt_ultima_fecha = $conn->prepare("SELECT MAX(FechaAplicacion) FROM a_moneda_cambio WHERE moneda = ?");
            $stmt_ultima_fecha->execute(['EUR']);
            $ultima_fecha_db = $stmt_ultima_fecha->fetchColumn();
            // Proceder solo si la fecha obtenida es más reciente que la de la base de datos.
            if (!$ultima_fecha_db || $datosBCV['fecha'] > $ultima_fecha_db) {
                $sql = "INSERT INTO a_moneda_cambio (FechaAplicacion, moneda, descripcion, valor, usuario) VALUES (:fecha, 'EUR', 'Euro', :tasa, :usuario)";
                $stmt = $conn->prepare($sql);
                $usuario = '0';
                if (
                    $stmt->execute([
                        ':fecha' => $datosBCV['fecha'],
                        ':tasa' => $datosBCV['tasa'],
                        ':usuario' => $usuario
                    ])
                ) {
                    // Inserción exitosa, sin mensajes de aviso.
                }
            }
        }
    }
    // Run the update process
    // ADVERTENCIA: Esto se ejecuta en cada inclusión del archivo. Ver nota en la función run_bcv_update().
    run_bcv_update();
}
?>