<?php
session_start();
include "../conexion.php";
// ----
if ($_SESSION['VERIFICADO'] != "SI") {
    header("Location: index.php?errorusuario=val");
    exit();
}
$acceso = 43;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

$status = 11;
$status2 = 99;

//---------- ORIGEN DEL FUNCIONARIO 
include "../funciones/origen_funcionario.php";

// PARA AJUSTAR VALOR UT TODAS
if ($_POST['CMDAJUSTAR'] == "Ajustar Valor UT") {
    ///////// ACTUALIZAR LA UT DE UNA VEZ AL APROBAR
    $consulta = "UPDATE liquidacion SET monto_bs=(monto_ut*" . $_SESSION['VALOR_UT_ACTUAL'] . ") WHERE id_tributo<>99 AND id_tributo2<>99 AND id_resolucion<=0 AND status>=" . $status . " AND status<" . $status2 . " AND origen_liquidacion=0" . $_SESSION['ORIGEN'] . " AND anno_expediente=0" . $_SESSION['ANNO_PRO'] . " AND num_expediente=0" . $_SESSION['NUM_PRO'] . " AND sector=0" . $_SESSION['SEDE'] . ";";
    $tabla = mysql_query($consulta); //echo $consulta;
    // MENSAJE
    echo "<script type=\"text/javascript\">alert('Planillas Ajustadas Exitosamente!!!');</script>";
}
?>
<html>

<head>
    <title>Imprimir Expediente</title>
</head>

<body style="background: transparent !important;">
    <p>
        <?php include "../titulo.php"; ?>
    </p>
    <p align="center">
        <?php
        include "menu.php";
        ?>
    </p>
    <div align="center">
        <form name="form1" method="post" action="">
            <?php
            include "0_seleccion_expediente.php";
            ?>
        </form>
        <br>
        <div id="div1">
            <?php
            include "0_expediente_liquidacion1.php";
            ?>
        </div>
        <br>
        <div id="div2">
            <?php
            include "0_sanciones_aplicadas_liquidacion1.php";
            ?>
        </div>
        <br>
        <div id="div3">
            <?php
            include "0_imprimir_botones.php";
            ?>
        </div>
    </div>
    <p>
        <?php include "../pie.php"; ?>
    </p>
    <p>&nbsp;</p>
</body>

</html>
<script language="JavaScript">
    $('#div1').hide();
    $('#div2').hide();
    $('#div3').hide();
    //--------------------------------------------
    function cargar_combo1(val) {
        $.ajax({
            type: "POST",
            url: '0_combo_origen.php?status1=11&status2=99',
            data: 'id=' + val,
            success: function(resp) {
                $('#OORIGEN').html(resp);
            }
        });
        alertify.message("Por favor espere la carga de datos...");
    }
    //--------------------------------------------
    function cargar_combo2(val) {
        $.ajax({
            type: "POST",
            url: '0_combo_anno.php?status1=11&status2=99&sede=' + document.form1.OSEDE.value,
            data: 'id=' + val,
            success: function(resp) {
                $('#OANNO').html(resp);
            }
        });
        alertify.message("Por favor espere la carga de datos...");
    }
    //--------------------------------------------
    function cargar_combo3(val) {
        $.ajax({
            type: "POST",
            url: '0_combo_numero.php?status1=11&status2=99&sede=' + document.form1.OSEDE.value + '&origen=' + document.form1.OORIGEN.value,
            data: 'id=' + val,
            success: function(resp) {
                $('#ONUMERO').html(resp);
            }
        });
        alertify.message("Por favor espere la carga de datos...");
    }
    //--------------------------------------------
    function cargar(numero) {
        var sede = document.getElementById("OSEDE").value;
        var origen = document.getElementById("OORIGEN").value;
        var anno = document.getElementById("OANNO").value;
        //-------------
        $('#div1').show();
        $('#div2').show();
        $('#div3').show();
        $('#div1').load('0_expediente_liquidacion1.php?sede=' + sede + '&origen=' + origen + '&anno=' + anno + '&numero=' + numero);
        $('#div2').load('0_sanciones_aplicadas_liquidacion1.php?sede=' + sede + '&origen=' + origen + '&anno=' + anno + '&numero=' + numero + '&status1=11&status2=99');
        $('#div3').load('0_imprimir_botones.php?status1=11&status2=99');
        alertify.success("Carga Completada...");
    }
</script>