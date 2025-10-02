<?php
// Función principal para convertir número a letras
function numeroALetras($numero, $esDecimal = false) {
    $valor = (string)intval($numero);
    if ($valor === '0') return $esDecimal ? 'cero' : 'Cero';

    $unidades = ['', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve'];
    $decenas = ['', 'diez', 'veinte', 'treinta', 'cuarenta', 'cincuenta', 'sesenta', 'setenta', 'ochenta', 'noventa'];
    $centenas = ['', 'ciento', 'doscientos', 'trescientos', 'cuatrocientos', 'quinientos', 'seiscientos', 'setecientos', 'ochocientos', 'novecientos'];
    $especiales = [11 => 'once', 12 => 'doce', 13 => 'trece', 14 => 'catorce', 15 => 'quince', 16 => 'dieciséis', 17 => 'diecisiete', 18 => 'dieciocho', 19 => 'diecinueve', 20 => 'veinte', 21 => 'veintiuno', 22 => 'veintidós', 23 => 'veintitrés', 24 => 'veinticuatro', 25 => 'veinticinco', 26 => 'veintiséis', 27 => 'veintisiete', 28 => 'veintiocho', 29 => 'veintinueve'];

    $numero = ltrim($valor, '0');
    $longitud = strlen($numero);
    $resultado = '';

    if ($longitud > 15) { // Aumentado para billones
        return "Número demasiado grande";
    }

    $grupos = str_split(str_pad($numero, ceil($longitud / 3) * 3, '0', STR_PAD_LEFT), 3);
    $numGrupos = count($grupos);

    $sufijos = ['', 'mil', 'millón', 'billón', 'trillón'];

    foreach ($grupos as $i => $grupo) {
        $num = intval($grupo);
        if ($num === 0) continue;

        $textoGrupo = '';
        $c = intval($grupo[0]);
        $d = intval($grupo[1]);
        $u = intval($grupo[2]);
        $du = $d * 10 + $u;

        if ($c > 0) {
            $textoGrupo .= ($num === 100) ? 'cien' : $centenas[$c];
            if ($du > 0) $textoGrupo .= ' ';
        }

        if ($du > 0) {
            if (isset($especiales[$du])) {
                $textoGrupo .= $especiales[$du];
            } else {
                $textoGrupo .= $decenas[$d];
                if ($u > 0 && $d > 0) { // Solo añadir 'y' si hay decena y unidad
                    $textoGrupo .= ' y ' . $unidades[$u];
                } elseif ($u > 0 && $d == 0) { // Añadir unidad si no hay decena
                    $textoGrupo .= $unidades[$u];
                }
            }
        }
        
        $sufijoIdx = $numGrupos - $i - 1;
        if ($sufijoIdx > 0) {
            if ($num === 1 && $sufijoIdx === 1) { // 1000 -> mil
                 $textoGrupo = 'mil';
            } elseif ($num === 1 && $sufijoIdx > 1) { // 1.000.000 -> un millón
                $textoGrupo = 'un';
            } else {
                // Apocopar "uno" a "un" antes de "mil", "millones", etc.
                if (substr($textoGrupo, -3) === 'uno') {
                    $textoGrupo = substr($textoGrupo, 0, -1);
                }
            }
            
            $sufijo = $sufijos[$sufijoIdx];
            if ($sufijoIdx > 1 && $num > 1) { // Pluralizar millón, billón, etc.
                $sufijo = str_replace('ón', 'ones', $sufijo);
            }

            if ($sufijoIdx === 1 && $textoGrupo === 'mil') {
                 $resultado .= $textoGrupo . ' ';
            } else {
                 $resultado .= $textoGrupo . ' ' . $sufijo . ' ';
            }

        } else {
            $resultado .= $textoGrupo;
        }
    }
    
    return trim($resultado);
}

$texto_procesado = '';
$texto_original = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $texto_original = $_POST['texto'];
    
    $texto_procesado = preg_replace_callback(
        '/(?<=^|\s|[(])(\d{1,3}(?:[.]\d{3})*,\d{1,2})(?=$|\s|[.,!?;:)])/' ,
        function($matches) {
            $numero_str = $matches[0];
            
            // La validación ahora la hace la expresión regular principal.

            $limpio = str_replace('.', '', $numero_str);
            $limpio = str_replace(',', '.', $limpio);
            $numero_float = floatval($limpio);

            $entero = intval($numero_float);
            $decimal = round(($numero_float - $entero) * 100);

            $letras_entero = numeroALetras($entero);
            $letras_decimal = numeroALetras($decimal, true);
            if ($decimal === 0) $letras_decimal = 'cero';

            // Convertir a Title Case (cada palabra capitalizada) y corregir conjunciones
            $letras_entero_capitalizadas = str_replace(' Y ', ' y ', ucwords($letras_entero));
            $letras_decimal_capitalizadas = str_replace(' Y ', ' y ', ucwords($letras_decimal));

            // Caso especial para "Uno" -> "Un" cuando es el número 1.
            if ($letras_entero_capitalizadas === 'Uno') {
                $letras_entero_capitalizadas = 'Un';
            }

            $texto_final = $letras_entero_capitalizadas . ' Bolívares con ' . $letras_decimal_capitalizadas . ' Céntimos (Bs. ' . $numero_str . ')';
            
            return $texto_final;
        },
        $texto_original
    );
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convertidor de Números a Letras</title>
    <link href="../plugins/bootstrap-5/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
        }
        textarea {
            height: 200px;
            font-family: monospace;
        }
        .resultado {
            background-color: #e9ecef;
            border: 1px solid #ced4da;
            padding: 15px;
            border-radius: 5px;
            min-height: 200px;
            white-space: pre-wrap;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4 text-center">Convertidor de Cifras a Letras</h1>
        <p class="text-center text-muted">Pega tu texto de Word aquí. El sistema encontrará las cantidades monetarias (ej: 1.234,56) y las convertirá a letras con el formato solicitado.</p>
        
        <form action="" method="POST">
            <div class="mb-3">
                <label for="texto" class="form-label">Texto de entrada:</label>
                <textarea class="form-control" id="texto" name="texto" required><?php echo htmlspecialchars($texto_original); ?></textarea>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">Convertir</button>
            </div>
        </form>

        <?php if (!empty($texto_procesado)): ?>
        <div class="mt-5">
            <h2 class="mb-3">Texto Resultante:</h2>
            <div class="resultado">
                <?php echo htmlspecialchars($texto_procesado); ?>
            </div>
            <button class="btn btn-secondary mt-3" onclick="copiarResultado()">Copiar Resultado</button>
        </div>
        <?php endif; ?>
    </div>

    <script>
        function copiarResultado() {
            const resultadoDiv = document.querySelector('.resultado');
            const texto = resultadoDiv.innerText;
            navigator.clipboard.writeText(texto).then(() => {
                alert('¡Texto copiado al portapapeles!');
            }, (err) => {
                alert('Error al copiar el texto: ', err);
            });
        }
    </script>
</body>
</html>
