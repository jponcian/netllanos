<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
	header("Location: index.php?errorusuario=val");
	exit();
}

$acceso = 87;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------
//------ ORIGEN DEL FUNCIONARIO
include "../funciones/origen_funcionario.php";
//--------------------
//echo voltea_fecha($_POST['OINICIO']);
$_SESSION['INICIO'] = voltea_fecha($_POST['OINICIO']);
$_SESSION['FIN'] = voltea_fecha($_POST['OFIN']);
$_SESSION['SEDE'] = $_POST['OSEDE'];

?>

<html>

<head>
    <title>Menú Reportes</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="6menu_reportes.css">
    <script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
</head>

<body>
    <?php include "../titulo.php"; ?>
    
    <div class="menu-container">
        <?php include "menu.php"; ?>
    </div>

    <div class="form-container">
        <div class="form-header">
            <h2>Transferidas a Liquidación</h2>
            <p>Seleccionar Fechas o Periodos</p>
        </div>

        <form name="form69" method="post" action="" class="form-fields">
            <div class="field-row">
                <label for="OSEDE">Sector:</label>
                <select name="OSEDE" id="OSEDE" onchange="this.form.submit()">
                    <option value="0">Seleccione</option>
                    <?php
                    $consulta_x = 'SELECT id_sector, nombre FROM z_sectores WHERE id_sector<=5;';
                    $tabla_x = mysqli_query($_SESSION['conexionsqli'], $consulta_x);
                    while ($registro_x = mysqli_fetch_array($tabla_x)) {
                        $selected = (isset($_POST['OSEDE']) && $_POST['OSEDE'] == $registro_x['id_sector']) ? 'selected' : '';
                        echo "<option value="{$registro_x['id_sector']}" $selected>{$registro_x['nombre']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="field-row">
                <label for="OINICIO">Desde:</label>
                <input onclick="scwShow(this,event);" type="text" name="OINICIO" id="OINICIO" size="10" readonly value="<?php echo isset($_POST['OINICIO']) ? htmlspecialchars($_POST['OINICIO']) : ''; ?>">
                
                <label for="OFIN">Hasta:</label>
                <input onclick="scwShow(this,event);" type="text" name="OFIN" id="OFIN" size="10" readonly value="<?php echo isset($_POST['OFIN']) ? htmlspecialchars($_POST['OFIN']) : ''; ?>">
            </div>

            <div class="form-actions">
                <input type="submit" class="boton" name="CMDCARGAR" value="Cargar">
            </div>
        </form>

        <?php
        if (isset($_POST['CMDCARGAR']) && $_POST['CMDCARGAR'] == 'Cargar') {
            $consulta = "SELECT liquidacion.rif FROM liquidacion WHERE fecha_transferencia_a_liq BETWEEN '{$_SESSION['INICIO']}' AND '{$_SESSION['FIN']}' AND liquidacion.sector = {$_SESSION['SEDE']} AND (origen_liquidacion={$origenUT})";
            $tabla = mysqli_query($_SESSION['conexionsqli'], $consulta);
            
            if ($tabla && mysqli_num_rows($tabla) > 0) {
                echo '<div class="report-link">';
                echo '<form name="form4" method="post" action="Reportes/transferidas_a_liquidacion.php" target="_blank">';
                echo '<input type="submit" class="boton" name="CMDCONCLUIDOSEXCEL" value="Ver Reporte">';
                echo '</form>';
                echo '</div>';
            } else {
                echo '<div class="info-message"><p><strong>¡No Existe Información para esas Fechas!</strong></p></div>';
            }
        }
        ?>
    </div>

    <?php include "../pie.php"; ?>
</body>

</html>