<?php
if (phpversion() == "4.1.10") {
    ////session_register('accion');
}
$_SESSION["accion"] = 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Incluir Resultados Operativos</title>
    <script language="javascript" script type="text/javascript" src="datetimepicker_css.js"></script>
    <script type="text/javascript" src="jquery/jquery.js"></script>
    <script type="text/javascript" src="jquery_ui/js/jquery-ui-1.10.4.custom.js"></script>
    <link rel="stylesheet" href="jquery_ui/css/jquery-ui-1.10.4.custom.min.css">
    <link rel="stylesheet" href="estilo_tabla.css">
    <script type="text/javascript" src="funciones.js"></script>

    <script type="text/javascript">
        function validaMultasDF() {
            var Multado = document.form1.MultasDF.selectedIndex;
            if (Multado == 1) {
                document.form1.Clausura.selectedIndex = 1;
                document.form1.Clausura.disabled = "disabled";
                document.form1.DiasClausura.disabled = "disabled";
                document.form1.NotificacionCierre.disabled = false;
                document.form1.MontoSanciones.value = 0;
                document.form1.MontoSanciones.disabled = true;
                document.form1.Sanciones.value = "CONFORME";
                $sancionesDF = "CONFORME";
                return $sancionesDF;
                document.form1.Sanciones.disabled = true;
            } else {
                document.form1.Clausura.selectedIndex = 0;
                document.form1.Clausura.disabled = "";
                document.form1.DiasClausura.disabled = "";
                document.form1.MontoSanciones.value = "";
                document.form1.MontoSanciones.disabled = false;
                document.form1.Sanciones.value = "";
                document.form1.Sanciones.disabled = false;
            }
        }

        function validaNoAplica() {
            var MaquinaFiscalNA = document.form1.TipoMF.selectedIndex;
            if (MaquinaFiscalNA == 0) {
                document.form1.CumpleMF.selectedIndex = 0;
                document.form1.ModeloMF.disabled = true;
                document.form1.CumpleMF.disabled = "disabled";
                document.form1.SancionesMF.disabled = true;
            } else {
                document.form1.CumpleMF.selectedIndex = 1;
                document.form1.ModeloMF.disabled = false;
                document.form1.CumpleMF.disabled = "";
                document.form1.SancionesMF.disabled = false;
            }
        }

        function validaClausura() {
            var aplicaClausura = document.form1.Clausura.selectedIndex;
            if (aplicaClausura == 0) {
                document.form1.DiasClausura.disabled = "";
                document.form1.NotificacionCierre.disabled = false;
            } else {
                document.form1.DiasClausura.disabled = "disabled";
                document.form1.NotificacionCierre.disabled = false;
            }
        }

        function validaCumpleMF() {
            var validaIncumpMF = document.form1.CumpleMF.selectedIndex;
            if (validaIncumpMF == 0) {
                document.form1.SancionesMF.disabled = true;
            } else {
                document.form1.SancionesMF.disabled = false;
            }
        }

        function confirmar() {
            if (confirm("¿Desea modificar ésta información?"))
                return true;
            else
                return false;
        }
    </script>
</head>
<style type="text/css">
    .fontsize {
        font-size: 9px;
    }

    .resaltar {
        color: red;
        font-weight: bold;
        font-size: 14px;
    }
</style>

<body style="background: transparent !important;">


    <form id="form1" name="form1" method="post" action="">
        <div id="wraper" title="Anexo 2 Articulado - Operativos Nacionales">
            <div id="anexo2articulado" align="left" class="fontsize">
                <article id="100">
                    <strong>Articulo 100: </strong>
                    <input type="checkbox" name="100_1" id="100_1" value="100_1" class="100">Num 1
                    <input type="checkbox" name="100_2" id="100_2" value="100_2" class="100">Num 2
                    <input type="checkbox" name="100_3" id="100_3" value="100_3" class="100">Num 3
                    <input type="checkbox" name="100_4" id="100_4" value="100_4" class="100">Num 4
                </article>
                <article id="101">
                    <hr>
                    <strong>Articulo 101: </strong>
                    <input type="checkbox" name="101_1" id="101_1" value="101_1" class="101">Num 1
                    <input type="checkbox" name="101_2" id="101_2" value="101_2" class="101">Num 2
                    <input type="checkbox" name="101_3" id="101_3" value="101_3" class="101">Num 3
                    <input type="checkbox" name="101_4" id="101_4" value="101_4" class="101">Num 4
                    <input type="checkbox" name="101_5" id="101_5" value="101_5" class="101">Num 5
                    <input type="checkbox" name="101_6" id="101_6" value="101_6" class="101">Num 6
                    <input type="checkbox" name="101_7" id="101_7" value="101_7" class="101">Num 7
                    <input type="checkbox" name="101_8" id="101_8" value="101_8" class="101">Num 8
                    <input type="checkbox" name="101_9" id="101_9" value="101_9" class="101">Num 9
                    <input type="checkbox" name="101_10" id="101_10" value="101_10" class="101">Num 10
                    <input type="checkbox" name="101_11" id="101_11" value="101_11" class="101">Num 11
                </article>
                <article id="102">
                    <hr>
                    <strong>Articulo 102: </strong>
                    <input type="checkbox" name="102_1" id="102_1" value="102_1" class="102">Num 1
                    <input type="checkbox" name="102_2" id="102_2" value="102_2" class="102">Num 2
                    <input type="checkbox" name="102_3" id="102_3" value="102_3" class="102">Num 3
                    <input type="checkbox" name="102_4" id="102_4" value="102_4" class="102">Num 4
                    <input type="checkbox" name="102_5" id="102_5" value="102_5" class="102">Num 5
                    <input type="checkbox" name="102_6" id="102_6" value="102_6" class="102">Num 6
                    <input type="checkbox" name="102_7" id="102_7" value="102_7" class="102">Num 7
                    <input type="checkbox" name="102_8" id="102_8" value="102_8" class="102">Num 8
                </article>
                <article id="103">
                    <hr>
                    <strong>Articulo 103: </strong>
                    <input type="checkbox" name="103_1" id="103_1" value="103_1" class="103">Num 1
                    <input type="checkbox" name="103_2" id="103_2" value="103_2" class="103">Num 2
                    <input type="checkbox" name="103_3" id="103_3" value="103_3" class="103">Num 3
                    <input type="checkbox" name="103_4" id="103_4" value="103_4" class="103">Num 4
                    <input type="checkbox" name="103_5" id="103_5" value="103_5" class="103">Num 5
                    <input type="checkbox" name="103_6" id="103_6" value="103_6" class="103">Num 6
                    <input type="checkbox" name="103_7" id="103_7" value="103_7" class="103">Num 7
                </article>
                <article id="104">
                    <hr>
                    <strong>Articulo 104: </strong>
                    <input type="checkbox" name="104_1" id="104_1" value="104_1" class="104">Num 1
                    <input type="checkbox" name="104_2" id="104_2" value="104_2" class="104">Num 2
                    <input type="checkbox" name="104_3" id="104_3" value="104_3" class="104">Num 3
                    <input type="checkbox" name="104_4" id="104_4" value="104_4" class="104">Num 4
                    <input type="checkbox" name="104_5" id="104_5" value="104_5" class="104">Num 5
                    <input type="checkbox" name="104_6" id="104_6" value="104_6" class="104">Num 6
                    <input type="checkbox" name="104_7" id="104_7" value="104_7" class="104">Num 7
                    <input type="checkbox" name="104_8" id="104_8" value="104_8" class="104">Num 8
                    <input type="checkbox" name="104_9" id="104_9" value="104_9" class="104">Num 9
                    <input type="checkbox" name="104_10" id="104_10" value="104_10" class="104">Num 10
                    <input type="checkbox" name="104_11" id="104_11" value="104_11" class="104">Num 11
                    <input type="checkbox" name="104_12" id="104_12" value="104_12" class="104">Num 12
                    <input type="checkbox" name="104_13" id="104_13" value="104_13" class="104">Num 13
                    <input type="checkbox" name="104_14" id="104_14" value="104_14" class="104">Num 14
                </article>
                <article id="105">
                    <hr>
                    <strong>Articulo 105: </strong>
                    <input type="checkbox" name="105_1" id="105_1" value="105_1" class="105">Num 1
                    <input type="checkbox" name="105_2" id="105_2" value="105_2" class="105">Num 2
                    <input type="checkbox" name="105_3" id="105_3" value="105_3" class="105">Num 3
                    <input type="checkbox" name="105_4" id="105_4" value="105_4" class="105">Num 4
                    <input type="checkbox" name="105_5" id="105_5" value="105_5" class="105">Num 5
                </article>
                <article id="106">
                    <hr>
                    <strong>Articulo 106: </strong>
                    <input type="checkbox" name="106_1" id="106_1" value="106_1" class="106">Num 1
                    <input type="checkbox" name="106_2" id="106_2" value="106_2" class="106">Num 2
                    <input type="checkbox" name="106_3" id="106_3" value="106_3" class="106">Num 3
                </article>
                <article id="107">
                    <hr>
                    <strong>Articulo 107: </strong>
                    <input type="checkbox" name="107_1" id="107_1" value="107_1" class="107">Num 1
                    <input type="checkbox" name="107_2" id="107_2" value="107_2" class="107">Num 2
                    <input type="checkbox" name="107_3" id="107_3" value="107_3" class="107">Num 3
                    <input type="checkbox" name="107_4" id="107_4" value="107_4" class="107">Num 4
                </article>
                <article id="108">
                    <hr>
                    <strong>Articulo 108: </strong>
                    <input type="checkbox" name="108_1" id="108_1" value="108_1" class="108">Art 108
                    <hr>
                </article>
                <article id="conforme">
                    <hr>
                    <strong>Conforme: </strong>
                    <input type="checkbox" name="conforme" id="conforme" value="conforme" class="108">Conforme
                    <hr>
                </article>


            </div>
            <div id="botonera">
                <p style="background-color: yellow;"><strong>Para desmarcar el tilde en Operativo Nacional, deben estar desmarcadas todas las opciones</strong></p>
                <input id="btnmodal" name="btnmodal" type="submit" value="Aceptar" />
            </div>
        </div>

        <div id="encabezado" class="CSSTableGeneratorinc">
            <table>
                <tr>
                    <td bgcolor="#333333" colspan="2" style="font-size: 14px; color: #FFF; text-align: center;">Incluir Resultado Operativo - Providencia Administrativa</td>
                </tr>
                <tr>
                    <td width="35%">Ingrese año de providencia: </td>
                    <td width="65%"><input name="añoProvidencia" id="añoProvidencia" type="text" size="6" maxlength="4" value="" /></td>
                </tr>
                <tr>
                    <td>Ingrese número de providencia:</td>
                    <td><input name="numProvidencia" id="numProvidencia" type="text" size="6" maxlength="4" value="" />
                        <input onmouseover=this.style.cursor="hand" type="button" name="buscar" id="buscar" value="..." onmouseout="mostrariva()" />
                    </td>
                </tr>
                <tr>
                    <td>Fecha de Notificación:</td>
                    <td><input name="Notificacion0" id="Notificacion0" readonly="readonly" type="text" size="10" maxlength="10" value="" disabled="disabled" /></td>
                </tr>
                <tr>
                    <td>Número de R.I.F.:</td>
                    <td><input name="numRIF" id="numRIF" readonly="readonly" type="text" size="12" maxlength="10" value="" disabled="disabled" /><input name="sujeto" id="sujeto" type="text" value="" size="60" readonly disabled="disabled" /></td>
                <tr>
                    <td>Fecha del Operativo: </td>
                    <td><input name="FechaOperativo" id="FechaOperativo" readonly="readonly" type="text" size="10" maxlength="10" value="<?php echo $_POST['FechaOperativo']; ?>" />
                    </td>
                </tr>
                <tr>
                    <td>Nombre del Operativo:</td>
                    <?php
                    $consulta_x = 'SELECT codigo, descripcion FROM fis_operativos_anexo2 ORDER BY codigo DESC;';
                    $tabla_x = mysql_query($consulta_x);
                    ?>
                    <td>
                        <p><select name="NombreOperativo" id="NombreOperativo" language="javascript">
                                <?php
                                while ($registro_x = mysql_fetch_object($tabla_x)) {
                                ?>
                                    <option value="<?php echo $registro_x->descripcion; ?>"><?php echo $registro_x->descripcion; ?></option>
                                <?php
                                }
                                ?>
                            </select>&nbsp; Operativo Nacional <input type="checkbox" id="nacional" name="nacional">
                    </td>
                    </p>
                </tr>
                <tr>
                    <td>Cantidad de Sucursales:</td>
                    <td>
                        <p><select name="CantSucursal" id="CantSucursal" language="javascript">
                                <option>0</option>
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                                <option>5</option>
                                <option>6</option>
                                <option>7</option>
                                <option>8</option>
                                <option>9</option>
                                <option>10</option>
                            </select></p>
                    </td>
                </tr>
                <tr>
                    <td>Cadena de Tienda/Franquicia: </td>
                    <td><input name="CadenaTienda" id="CadenaTienda" type="text" size="80" maxlength="250" value="<?php echo $_POST['CadenaTienda']; ?>" onfocus="mostrariva()" /></td>
                </tr>
                <tr>
                    <td>Sector Económico:</td>
                    <td><select name="Actividad" id="Actividad" language="javascript">
                            <option value="0" selected><--Seleccione--></option>
                            <?php
                            include "../conexion.php";


                            $sector = "SELECT descripcion FROM fis_sector_economico";
                            echo $sector;
                            $tabla = mysql_query($sector);
                            while ($valor = mysql_fetch_object($tabla)) { ?>
                                <option value="<?php echo $valor->descripcion ?>"><?php echo $valor->descripcion ?></option>
                            <?php
                            }
                            ?>
                        </select></td>
                </tr>
            </table>
        </div>
        <div id="iva" class="CSSTableGeneratorinc">
            <table>
                <tr>
                    <td>Datos Contribuyentes I.V.A.</td>
                </tr>
                <tr>
                    <td width="35%">Tipo de Maquina Fiscal: </td>
                    <td width="65%">
                        <p><select name="TipoMF" language="javascript" onChange="validaNoAplica()">
                                <option>NO APLICA</option>
                                <option>REGISTRADORA</option>
                                <option>IMPRESORA</option>
                                <option>PUNTO DE VENTA</option>
                                <option selected="selected">NO TIENE</option>
                            </select></p>
                    </td>
                </tr>
                <tr>
                    <td>Modelo Maquina Fiscal: </td>
                    <td><input name="ModeloMF" type="text" size="60" maxlength="50" <?php if ($_POST[TipoMF] == "NO APLICA") {
                                                                                        echo 'disabled="disabled"';
                                                                                    } ?> value="<?php echo $_POST['ModeloMF']; ?>" /></td>
                </tr>
                <tr>
                    <td>Maquina Fiscal Cumple Requisitos: </td>
                    <td>
                        <p><select name="CumpleMF" language="javascript" onChange="validaCumpleMF()">
                                <option>SI</option>
                                <option>NO</option>
                            </select></p>
                    </td>
                </tr>
                <tr>
                    <td>Incumplimientos Maquina Fiscal: </td>
                    <td><input name="SancionesMF" type="text" size="80" maxlength="250" <?php if ($_POST[TipoMF] == "NO APLICA" or $_POST[CumpleMF] == "NO") {
                                                                                            echo 'disabled="disabled"';
                                                                                        } ?> value="<?php echo $_POST['SancionesMF']; ?>" /> </td>
                </tr>
            </table>
        </div>
        <div id="flotante" class="CSSTableGeneratorinc">
            <table>
                <tr>
                    <td>Datos Multas Deberes Formales</td>
                <tr>
                    <td>Multas por Deberes Formales: </td>
                    <td>
                        <p><select name="MultasDF" language="javascript" onchange="validaMultasDF()">
                                <option>SI</option>
                                <option>NO</option>
                            </select></p>
                    </td>
                </tr>
                <tr>
                    <td>Clausura: </td>
                    <td>
                        <p><select name="Clausura" language="javascript" onChange="validaClausura()">
                                <option>SI</option>
                                <option>NO</option>
                            </select></p>
                    </td>
                </tr>
                <tr>
                    <td>Dias de Clausura: </td>
                    <td>
                        <p><select name="DiasClausura" language="javascript">

                                <?php
                                for ($i = 1; $i <= 50; $i++) {
                                    echo '<option>' . $i . '</option>';
                                }
                                ?>
                            </select></p>
                    </td>
                </tr>
                <tr>
                    <td>Fecha de Notificación Resolucion Clausura: </td>
                    <td><input name="NotificacionCierre" id="NotificacionCierre" readonly="readonly" type="text" size="10" maxlength="10" value="<?php echo $_POST['NotificacionCierre']; ?>" />
                    </td>
                </tr>
                <tr>
                    <td width="35%">Monto Multas (<span class="resaltar">Cantidad en EUROS</span>): </td>
                    <td width="65%"><input name="MontoSanciones" style="text-align:right" type="text" size="10" maxlength="10" value="<?php echo $_POST['MontoSanciones']; ?>" /> (Utilice PUNTO "." no COMA "," para separar solamente decimales Ejemplo: 1234.50)</td>
                </tr>
                <tr>
                    <td>Incumplimientos Deberes Formales: </td>
                    <td><input name="Sanciones" type="text" size="80" maxlength="250" value="<?php echo $_POST['Sanciones']; ?>" /></td>
                </tr>
                <tr>
                    <td style="color:#FF0000">Observaciones: (Incluir descripcion actividad económica)</td>
                    <td><input name="Observaciones" type="text" size="80" maxlength="250" value="<?php echo $_POST['Observaciones']; ?>" /></td>
                </tr>
            </table><br />
            <table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#666666" bgcolor="#999999" align="center" style="font:Arial, Helvetica, sans-serif; fsont-size:12px">
                <tr>
                    <td align="center">
                        <?php
                        if ($_SESSION["accion"] == 0) {
                            echo '<input onmouseover=this.style.cursor="hand" type="button" name="agregar" id="agregar" value="Agregar" >';
                        } else {
                            echo '<input onmouseover=this.style.cursor="hand" type="button" name="modificar" id="modificar" value="Modificar" >';
                        }

                        ?>
                    </td>
                </tr>
            </table>
        </div>
        <input name="TipoTributo" id="TipoTributo" type="hidden" value="" />
        <input name="Emision" id="Emision" type="hidden" size="6" maxlength="4" value="" />
        <input name="Notificacion" id="Notificacion" type="hidden" size="6" maxlength="4" value="" />
        <input name="Nombre" id="Nombre" type="hidden" size="6" maxlength="4" value="" />
        <input name="Rif" id="Rif" type="hidden" size="6" maxlength="4" value="" />
        <input name="Domicilio" id="Domicilio" type="hidden" size="6" maxlength="4" value="" />
        <input name="CedFiscal" id="CedFiscal" type="hidden" size="6" maxlength="4" value="" />
        <input name="Fiscal" id="Fiscal" type="hidden" size="6" maxlength="4" value="" />
        <input name="CedSuper" id="CedSuper" type="hidden" size="6" maxlength="4" value="" />
        <input name="Super" id="Super" type="hidden" size="6" maxlength="4" value="" />
        <input name="Programa" id="Programa" type="hidden" size="6" maxlength="4" value="" />
        <input name="Tributo" id="Tributo" type="hidden" size="6" maxlength="4" value="" />
        <input name="CedCoord" id="CedCoord" type="hidden" size="6" maxlength="4" value="" />
        <input name="Coord" id="Coord" type="hidden" size="6" maxlength="4" value="" />
        <input name="TlfCoord" id="TlfCoord" type="hidden" size="6" maxlength="4" value="" />
        <input type="hidden" name="txt100" id="txt100" value="" />
        <input type="hidden" name="txt101" id="txt101" value="" />
        <input type="hidden" name="txt102" id="txt102" value="" />
        <input type="hidden" name="txt103" id="txt103" value="" />
        <input type="hidden" name="txt104" id="txt104" value="" />
        <input type="hidden" name="txt105" id="txt105" value="" />
        <input type="hidden" name="txt106" id="txt106" value="" />
        <input type="hidden" name="txt107" id="txt107" value="" />
        <input type="hidden" name="txt108" id="txt108" value="" />
    </form>
</body>

</html>