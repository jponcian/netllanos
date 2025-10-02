<?php
session_start();
//////session_register("conexion");
$_SESSION['conexion'] = odbc_connect("LLANOS", "Administrador", "losllanos");
set_time_limit(300);
?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=9" />
    <title>Reporte Semanal Fiscalizaciones Puntuales</title>
</head>

<body style="background: transparent !important;">
    <div id="periodo" style="display:none">Desde: <input type="text" id="txtInicio" name="txtInicio" style="display:none"> Hasta: <input type="text" id="txtFin" name="txtFin"></div>
    <div id="titulo" style="display:block;">PROVIDENCIAS EMITIDAS
        <br />
        <table width="80%" border="1">
            <tbody>
                <tr>
                    <th scope="col">SECTOR/SEDE</th>
                    <th scope="col">AÑO PROV.</th>
                    <th scope="col">NRO PROV.</th>
                    <th scope="col">EMISION</th>
                    <th scope="col">PROGRAMA</th>
                    <th scope="col">TIPO</th>
                </tr>

                <!--CARGAMOS LOS DATOS DE LA SEDE-->
                <?php
                $_GET['txtInicio'] = "2015-07-06";
                $_GET['txtFin'] = "2015-07-10";
                $inicio = $_GET['ini'];
                $hasta = $_GET['fin'];
                $FPN = 0;
                $FPR = 0;
                $AB = 0;
                $INV = 0;
                $i = 1;
                $_SESSION['conexion'] = odbc_connect("LLANOS", "Admin", "losllanos");
                /*$SQL = "
SELECT *
FROM CS_Emitidas_RS
WHERE FechaEmision Between #2015/06/01# And #2015/07/13#
";*/
                $SQL = "
SELECT Providencia.Año_Providencia as Anno, Providencia.NroAutorizacion, Providencia.FechaEmision, [Tipo Autorizacion].Siglas2, [Tipo Autorizacion].Tipo
FROM [Tipo Autorizacion] INNER JOIN Providencia ON [Tipo Autorizacion].Tipo = Providencia.TipoAutorizacion
WHERE (((Providencia.FechaEmision) Between #" . $inicio . "# And #" . $hasta . "#) AND (([Tipo Autorizacion].Tipo) Between 1000 And 1999));
";

                $result = odbc_exec($_SESSION['conexion'],  utf8_decode($SQL));
                //echo $SQL.'<br/>';

                while ($reg_emitidas_sede = odbc_fetch_object($result)) {
                    //echo "paso por aqui";
                ?>
                    <tr>
                        <td>SEDE</td>
                        <td><?php echo $reg_emitidas_sede->Anno ?></td>
                        <td><?php echo $reg_emitidas_sede->NroAutorizacion ?></td>
                        <td><?php echo date("d-m-Y", strtotime($reg_emitidas_sede->FechaEmision)) ?></td>
                        <td><?php echo $reg_emitidas_sede->Siglas2 ?></td>
                        <td><?php echo $reg_emitidas_sede->Tipo ?></td>
                    </tr>
                    <?php
                    switch ($reg_emitidas_sede->Siglas2) {
                        case "FPN":
                            $FPN = $FPN + 1;
                            break;
                        case "FPR":
                            $FPR = $FPR + 1;
                            break;
                        case "AB":
                            $AB = $AB + 1;
                            break;
                        case "IF":
                            $INV = $INV + 1;
                            break;
                    }
                    $i = $i + 1;
                }
                odbc_close($_SESSION['conexion']);
                //echo "FPN: ".$FPN. " - FPR: ".$FPR." - AB: ".$AB." - INV: ".$INV.'<br/>';

                //SECTOR SAN FERNANDO DE APURE
                $_SESSION['conexion'] = odbc_connect("SFA", "Admin", "losllanos");
                $SQLsfa = "
SELECT Providencia.Año_Providencia as Anno, Providencia.NroAutorizacion, Providencia.FechaEmision, [Tipo Autorizacion].Siglas2, [Tipo Autorizacion].Tipo
FROM [Tipo Autorizacion] INNER JOIN Providencia ON [Tipo Autorizacion].Tipo = Providencia.TipoAutorizacion
WHERE (((Providencia.FechaEmision) Between #" . $inicio . "# And #" . $hasta . "#) AND (([Tipo Autorizacion].Tipo) Between 1000 And 1999));
";

                $resultsfa = odbc_exec($_SESSION['conexion'],  utf8_decode($SQLsfa));
                //echo $SQL.'<br/>';

                while ($reg_emitidas_sfa = odbc_fetch_object($resultsfa)) {
                    //echo "paso por aqui";
                    ?>
                    <tr>
                        <td>SFA</td>
                        <td><?php echo $reg_emitidas_sfa->Anno ?></td>
                        <td><?php echo $reg_emitidas_sfa->NroAutorizacion ?></td>
                        <td><?php echo date("d-m-Y", strtotime($reg_emitidas_sfa->FechaEmision)) ?></td>
                        <td><?php echo $reg_emitidas_sfa->Siglas2 ?></td>
                        <td><?php echo $reg_emitidas_sfa->Tipo ?></td>
                    </tr>
                    <?php
                    switch ($reg_emitidas_sfa->Siglas2) {
                        case "FPN":
                            $FPN = $FPN + 1;
                            break;
                        case "FPR":
                            $FPR = $FPR + 1;
                            break;
                        case "AB":
                            $AB = $AB + 1;
                            break;
                        case "IF":
                            $INV = $INV + 1;
                            break;
                    }
                    $i = $i + 1;
                }
                odbc_close($_SESSION['conexion']);
                //echo "FPN: ".$FPN. " - FPR: ".$FPR." - AB: ".$AB." - INV: ".$INV.'<br/>';

                //SECTOR SAN JUAN DE LOS MORROS
                $_SESSION['conexion'] = odbc_connect("SJM", "Admin", "losllanos");
                $SQLsjm = "
SELECT Providencia.Año_Providencia as Anno, Providencia.NroAutorizacion, Providencia.FechaEmision, [Tipo Autorizacion].Siglas2, [Tipo Autorizacion].Tipo
FROM [Tipo Autorizacion] INNER JOIN Providencia ON [Tipo Autorizacion].Tipo = Providencia.TipoAutorizacion
WHERE (((Providencia.FechaEmision) Between #" . $inicio . "# And #" . $hasta . "#) AND (([Tipo Autorizacion].Tipo) Between 1000 And 1999));
";

                $resultsjm = odbc_exec($_SESSION['conexion'],  utf8_decode($SQLsjm));
                //echo $SQL.'<br/>';

                while ($reg_emitidas_sjm = odbc_fetch_object($resultsjm)) {
                    //echo "paso por aqui";
                    ?>
                    <tr>
                        <td>SJM</td>
                        <td><?php echo $reg_emitidas_sjm->Anno ?></td>
                        <td><?php echo $reg_emitidas_sjm->NroAutorizacion ?></td>
                        <td><?php echo date("d-m-Y", strtotime($reg_emitidas_sjm->FechaEmision)) ?></td>
                        <td><?php echo $reg_emitidas_sjm->Siglas2 ?></td>
                        <td><?php echo $reg_emitidas_sjm->Tipo ?></td>
                    </tr>
                    <?php
                    switch ($reg_emitidas_sjm->Siglas2) {
                        case "FPN":
                            $FPN = $FPN + 1;
                            break;
                        case "FPR":
                            $FPR = $FPR + 1;
                            break;
                        case "AB":
                            $AB = $AB + 1;
                            break;
                        case "IF":
                            $INV = $INV + 1;
                            break;
                    }
                    $i = $i + 1;
                }
                odbc_close($_SESSION['conexion']);
                //echo "FPN: ".$FPN. " - FPR: ".$FPR." - AB: ".$AB." - INV: ".$INV.'<br/>';

                //SECTOR VALLE DE LA PASCUA
                $_SESSION['conexion'] = odbc_connect("VLP", "Admin", "losllanos");
                $SQLvlp = "
SELECT Providencia.Año_Providencia as Anno, Providencia.NroAutorizacion, Providencia.FechaEmision, [Tipo Autorizacion].Siglas2, [Tipo Autorizacion].Tipo
FROM [Tipo Autorizacion] INNER JOIN Providencia ON [Tipo Autorizacion].Tipo = Providencia.TipoAutorizacion
WHERE (((Providencia.FechaEmision) Between #" . $inicio . "# And #" . $hasta . "#) AND (([Tipo Autorizacion].Tipo) Between 1000 And 1999));
";

                $resultvlp = odbc_exec($_SESSION['conexion'],  utf8_decode($SQLvlp));
                //echo $SQL.'<br/>';

                while ($reg_emitidas_vlp = odbc_fetch_object($resultvlp)) {
                    //echo "paso por aqui";
                    ?>
                    <tr>
                        <td>VLP</td>
                        <td><?php echo $reg_emitidas_vlp->Anno ?></td>
                        <td><?php echo $reg_emitidas_vlp->NroAutorizacion ?></td>
                        <td><?php echo date("d-m-Y", strtotime($reg_emitidas_vlp->FechaEmision)) ?></td>
                        <td><?php echo $reg_emitidas_vlp->Siglas2 ?></td>
                        <td><?php echo $reg_emitidas_vlp->Tipo ?></td>
                    </tr>
                    <?php
                    switch ($reg_emitidas_vlp->Siglas2) {
                        case "FPN":
                            $FPN = $FPN + 1;
                            break;
                        case "FPR":
                            $FPR = $FPR + 1;
                            break;
                        case "AB":
                            $AB = $AB + 1;
                            break;
                        case "IF":
                            $INV = $INV + 1;
                            break;
                    }
                    $i = $i + 1;
                }
                odbc_close($_SESSION['conexion']);
                //echo "FPN: ".$FPN. " - FPR: ".$FPR." - AB: ".$AB." - INV: ".$INV.'<br/>';

                //UNIDAD ALTAGRACIA DE ORITUCO
                $_SESSION['conexion'] = odbc_connect("ALT", "Admin", "losllanos");
                $SQLalt = "
SELECT Providencia.Año_Providencia as Anno, Providencia.NroAutorizacion, Providencia.FechaEmision, [Tipo Autorizacion].Siglas2, [Tipo Autorizacion].Tipo
FROM [Tipo Autorizacion] INNER JOIN Providencia ON [Tipo Autorizacion].Tipo = Providencia.TipoAutorizacion
WHERE (((Providencia.FechaEmision) Between #" . $inicio . "# And #" . $hasta . "#) AND (([Tipo Autorizacion].Tipo) Between 1000 And 1999));
";

                $resultalt = odbc_exec($_SESSION['conexion'],  utf8_decode($SQLalt));
                //echo $SQL.'<br/>';

                while ($reg_emitidas_alt = odbc_fetch_object($resultalt)) {
                    //echo "paso por aqui";
                    ?>
                    <tr>
                        <td>ALT</td>
                        <td><?php echo $reg_emitidas_alt->Anno ?></td>
                        <td><?php echo $reg_emitidas_alt->NroAutorizacion ?></td>
                        <td><?php echo date("d-m-Y", strtotime($reg_emitidas_alt->FechaEmision)) ?></td>
                        <td><?php echo $reg_emitidas_alt->Siglas2 ?></td>
                        <td><?php echo $reg_emitidas_alt->Tipo ?></td>
                    </tr>
                <?php
                    switch ($reg_emitidas_alt->Siglas2) {
                        case "FPN":
                            $FPN = $FPN + 1;
                            break;
                        case "FPR":
                            $FPR = $FPR + 1;
                            break;
                        case "AB":
                            $AB = $AB + 1;
                            break;
                        case "IF":
                            $INV = $INV + 1;
                            break;
                    }
                    $i = $i + 1;
                }
                odbc_close($_SESSION['conexion']);
                //echo "FPN: ".$FPN. " - FPR: ".$FPR." - AB: ".$AB." - INV: ".$INV.'<br/>';

                ?>
            </tbody>
        </table>
    </div>
    <div id="resumenemitidas" style="display:none;">
        <?php
        global $FPN;
        global $FPR;
        global $AB;
        global $INV;
        echo "FPN: " . $FPN . " - FPR: " . $FPR . " - AB: " . $AB . " - INV: " . $INV . '<br/>';
        ?>
    </div>

    <div id="notificadas" style="display:block;">PROVIDENCIAS NOTIFICADAS
        <table width="80%" border="1">
            <tbody>
                <tr>
                    <th scope="col">SECTOR/SEDE</th>
                    <th scope="col">AÑO PROV.</th>
                    <th scope="col">NRO PROV.</th>
                    <th scope="col">NOTIFICACION</th>
                    <th scope="col">PROGRAMA</th>
                    <th scope="col">TIPO</th>
                </tr>


                <!--CARGAMOS LOS DATOS DE LA SEDE-->
                <?php
                $NFPN = 0;
                $NFPR = 0;
                $NAB = 0;
                $NINV = 0;
                $i = 1;
                $_SESSION['conexion'] = odbc_connect("LLANOS", "Admin", "losllanos");
                /*$SQL = "
    SELECT *
    FROM CS_Emitidas_RS
    WHERE FechaEmision Between #2015/06/01# And #2015/07/13#
    ";*/
                $SQL = "
SELECT Providencia.Año_Providencia as Anno, Providencia.NroAutorizacion, Providencia.FechaNotificacion, [Tipo Autorizacion].Siglas2, [Tipo Autorizacion].Tipo
FROM [Tipo Autorizacion] INNER JOIN Providencia ON [Tipo Autorizacion].Tipo = Providencia.TipoAutorizacion
WHERE (((Providencia.FechaNotificacion) Between #" . $inicio . "# And #" . $hasta . "#) AND (([Tipo Autorizacion].Tipo) Between 1000 And 1999));
    ";

                $result = odbc_exec($_SESSION['conexion'],  utf8_decode($SQL));
                //echo $SQL.'<br/>';

                while ($reg_notificadas_sede = odbc_fetch_object($result)) {
                    //echo "paso por aqui";
                ?>
                    <tr>
                        <td>SEDE</td>
                        <td><?php echo $reg_notificadas_sede->Anno ?></td>
                        <td><?php echo $reg_notificadas_sede->NroAutorizacion ?></td>
                        <td><?php echo date("d-m-Y", strtotime($reg_notificadas_sede->FechaNotificacion)) ?></td>
                        <td><?php echo $reg_notificadas_sede->Siglas2 ?></td>
                        <td><?php echo $reg_notificadas_sede->Tipo ?></td>
                    </tr>
                    <?php
                    switch ($reg_notificadas_sede->Siglas2) {
                        case "FPN":
                            $NFPN = $NFPN + 1;
                            break;
                        case "FPR":
                            $NFPR = $NFPR + 1;
                            break;
                        case "AB":
                            $NAB = $NAB + 1;
                            break;
                        case "IF":
                            $NINV = $NINV + 1;
                            break;
                    }
                    $i = $i + 1;
                }
                odbc_close($_SESSION['conexion']);
                //echo "FPN: ".$NFPN. " - FPR: ".$NFPR." - AB: ".$NAB." - INV: ".$NINV.'<br/>';

                //SECTOR SAN FERNANDO DE APURE
                $_SESSION['conexion'] = odbc_connect("SFA", "Admin", "losllanos");
                /*$SQL = "
    SELECT *
    FROM CS_Emitidas_RS
    WHERE FechaEmision Between #2015/06/01# And #2015/07/13#
    ";*/
                $SQLsfa = "
SELECT Providencia.Año_Providencia as Anno, Providencia.NroAutorizacion, Providencia.FechaNotificacion, [Tipo Autorizacion].Siglas2, [Tipo Autorizacion].Tipo
FROM [Tipo Autorizacion] INNER JOIN Providencia ON [Tipo Autorizacion].Tipo = Providencia.TipoAutorizacion
WHERE (((Providencia.FechaNotificacion) Between #" . $inicio . "# And #" . $hasta . "#) AND (([Tipo Autorizacion].Tipo) Between 1000 And 1999));
    ";

                $resultsfa = odbc_exec($_SESSION['conexion'],  utf8_decode($SQLsfa));
                //echo $SQL.'<br/>';

                while ($reg_notificadas_sfa = odbc_fetch_object($resultsfa)) {
                    //echo "paso por aqui";
                    ?>
                    <tr>
                        <td>SFA</td>
                        <td><?php echo $reg_notificadas_sfa->Anno ?></td>
                        <td><?php echo $reg_notificadas_sfa->NroAutorizacion ?></td>
                        <td><?php echo date("d-m-Y", strtotime($reg_notificadas_sfa->FechaNotificacion)) ?></td>
                        <td><?php echo $reg_notificadas_sfa->Siglas2 ?></td>
                        <td><?php echo $reg_notificadas_sfa->Tipo ?></td>
                    </tr>
                    <?php
                    switch ($reg_notificadas_sfa->Siglas2) {
                        case "FPN":
                            $NFPN = $NFPN + 1;
                            break;
                        case "FPR":
                            $NFPR = $NFPR + 1;
                            break;
                        case "AB":
                            $NAB = $NAB + 1;
                            break;
                        case "IF":
                            $NINV = $NINV + 1;
                            break;
                    }
                    $i = $i + 1;
                }
                odbc_close($_SESSION['conexion']);
                //echo "FPN: ".$NFPN. " - FPR: ".$NFPR." - AB: ".$NAB." - INV: ".$NINV.'<br/>';

                //SECTOR SAN JUAN DE LOS MORROS
                $_SESSION['conexion'] = odbc_connect("SJM", "Admin", "losllanos");
                /*$SQL = "
    SELECT *
    FROM CS_Emitidas_RS
    WHERE FechaEmision Between #2015/06/01# And #2015/07/13#
    ";*/
                $SQLsjm = "
SELECT Providencia.Año_Providencia as Anno, Providencia.NroAutorizacion, Providencia.FechaNotificacion, [Tipo Autorizacion].Siglas2, [Tipo Autorizacion].Tipo
FROM [Tipo Autorizacion] INNER JOIN Providencia ON [Tipo Autorizacion].Tipo = Providencia.TipoAutorizacion
WHERE (((Providencia.FechaNotificacion) Between #" . $inicio . "# And #" . $hasta . "#) AND (([Tipo Autorizacion].Tipo) Between 1000 And 1999));
    ";

                $resultsjm = odbc_exec($_SESSION['conexion'],  utf8_decode($SQLsjm));
                //echo $SQL.'<br/>';

                while ($reg_notificadas_sjm = odbc_fetch_object($resultsjm)) {
                    //echo "paso por aqui";
                    ?>
                    <tr>
                        <td>SJM</td>
                        <td><?php echo $reg_notificadas_sjm->Anno ?></td>
                        <td><?php echo $reg_notificadas_sjm->NroAutorizacion ?></td>
                        <td><?php echo date("d-m-Y", strtotime($reg_notificadas_sjm->FechaNotificacion)) ?></td>
                        <td><?php echo $reg_notificadas_sjm->Siglas2 ?></td>
                        <td><?php echo $reg_notificadas_sjm->Tipo ?></td>
                    </tr>
                    <?php
                    switch ($reg_notificadas_sjm->Siglas2) {
                        case "FPN":
                            $NFPN = $NFPN + 1;
                            break;
                        case "FPR":
                            $NFPR = $NFPR + 1;
                            break;
                        case "AB":
                            $NAB = $NAB + 1;
                            break;
                        case "IF":
                            $NINV = $NINV + 1;
                            break;
                    }
                    $i = $i + 1;
                }
                odbc_close($_SESSION['conexion']);
                //echo "FPN: ".$NFPN. " - FPR: ".$NFPR." - AB: ".$NAB." - INV: ".$NINV.'<br/>';


                //SECTOR VALLE DE LA PASCUA
                $_SESSION['conexion'] = odbc_connect("VLP", "Admin", "losllanos");
                /*$SQL = "
    SELECT *
    FROM CS_Emitidas_RS
    WHERE FechaEmision Between #2015/06/01# And #2015/07/13#
    ";*/
                $SQLvlp = "
SELECT Providencia.Año_Providencia as Anno, Providencia.NroAutorizacion, Providencia.FechaNotificacion, [Tipo Autorizacion].Siglas2, [Tipo Autorizacion].Tipo
FROM [Tipo Autorizacion] INNER JOIN Providencia ON [Tipo Autorizacion].Tipo = Providencia.TipoAutorizacion
WHERE (((Providencia.FechaNotificacion) Between #" . $inicio . "# And #" . $hasta . "#) AND (([Tipo Autorizacion].Tipo) Between 1000 And 1999));
    ";

                $resultvlp = odbc_exec($_SESSION['conexion'],  utf8_decode($SQLvlp));
                //echo $SQL.'<br/>';

                while ($reg_notificadas_vlp = odbc_fetch_object($resultvlp)) {
                    //echo "paso por aqui";
                    ?>
                    <tr>
                        <td>VLP</td>
                        <td><?php echo $reg_notificadas_vlp->Anno ?></td>
                        <td><?php echo $reg_notificadas_vlp->NroAutorizacion ?></td>
                        <td><?php echo date("d-m-Y", strtotime($reg_notificadas_vlp->FechaNotificacion)) ?></td>
                        <td><?php echo $reg_notificadas_vlp->Siglas2 ?></td>
                        <td><?php echo $reg_notificadas_vlp->Tipo ?></td>
                    </tr>
                    <?php
                    switch ($reg_notificadas_vlp->Siglas2) {
                        case "FPN":
                            $NFPN = $NFPN + 1;
                            break;
                        case "FPR":
                            $NFPR = $NFPR + 1;
                            break;
                        case "AB":
                            $NAB = $NAB + 1;
                            break;
                        case "IF":
                            $NINV = $NINV + 1;
                            break;
                    }
                    $i = $i + 1;
                }
                odbc_close($_SESSION['conexion']);
                //echo "FPN: ".$NFPN. " - FPR: ".$NFPR." - AB: ".$NAB." - INV: ".$NINV.'<br/>';

                //UNIDAD ALTAGRACIA DE ORITUCO
                $_SESSION['conexion'] = odbc_connect("ALT", "Admin", "losllanos");
                /*$SQL = "
    SELECT *
    FROM CS_Emitidas_RS
    WHERE FechaEmision Between #2015/06/01# And #2015/07/13#
    ";*/
                $SQLalt = "
SELECT Providencia.Año_Providencia as Anno, Providencia.NroAutorizacion, Providencia.FechaNotificacion, [Tipo Autorizacion].Siglas2, [Tipo Autorizacion].Tipo
FROM [Tipo Autorizacion] INNER JOIN Providencia ON [Tipo Autorizacion].Tipo = Providencia.TipoAutorizacion
WHERE (((Providencia.FechaNotificacion) Between #" . $inicio . "# And #" . $hasta . "#) AND (([Tipo Autorizacion].Tipo) Between 1000 And 1999));
    ";

                $resultalt = odbc_exec($_SESSION['conexion'],  utf8_decode($SQLalt));
                //echo $SQL.'<br/>';

                while ($reg_notificadas_alt = odbc_fetch_object($resultalt)) {
                    //echo "paso por aqui";
                    ?>
                    <tr>
                        <td>ALT</td>
                        <td><?php echo $reg_notificadas_alt->Anno ?></td>
                        <td><?php echo $reg_notificadas_alt->NroAutorizacion ?></td>
                        <td><?php echo date("d-m-Y", strtotime($reg_notificadas_alt->FechaNotificacion)) ?></td>
                        <td><?php echo $reg_notificadas_alt->Siglas2 ?></td>
                        <td><?php echo $reg_notificadas_alt->Tipo ?></td>
                    </tr>
                <?php
                    switch ($reg_notificadas_alt->Siglas2) {
                        case "FPN":
                            $NFPN = $NFPN + 1;
                            break;
                        case "FPR":
                            $NFPR = $NFPR + 1;
                            break;
                        case "AB":
                            $NAB = $NAB + 1;
                            break;
                        case "IF":
                            $NINV = $NINV + 1;
                            break;
                    }
                    $i = $i + 1;
                }
                odbc_close($_SESSION['conexion']);
                //echo "FPN: ".$NFPN. " - FPR: ".$NFPR." - AB: ".$NAB." - INV: ".$NINV.'<br/>';

                ?>
            </tbody>
        </table>
    </div>
    <div id="resumennotificadas" style="display:none;">
        <?php
        global $NFPN;
        global $NFPR;
        global $NAB;
        global $NINV;
        echo "FPN: " . $NFPN . " - FPR: " . $NFPR . " - AB: " . $NAB . " - INV: " . $NINV . '<br/>';
        ?>
    </div>
    </div>

    <div id="ActasNotificadas" style="display:block;">ACTAS NOTIFICADAS
        <table width="80%" border="1">
            <tbody>
                <tr>
                    <th scope="col">NUMERO</th>
                    <th scope="col">FECHA</th>
                    <th scope="col">SECTOR/SEDE</th>
                    <th scope="col">AÑO PROV.</th>
                    <th scope="col">NRO PROV.</th>
                    <th scope="col">NOTIFICACION</th>
                    <th scope="col">REPARO</th>
                    <th scope="col">IMPTO OMITIDO</th>
                    <th scope="col">MULTA REPARO</th>
                    <th scope="col">INTERESES</th>
                    <th scope="col">TIPO ACTA</th>
                    <th scope="col">RPOGRAMA</th>
                </tr>
                <?php
                $ANFPN = 0;
                $ANFPR = 0;
                $ANAB = 0;
                $ANINV = 0;
                $REPAROFPN = 0;
                $IMPTOFPN = 0;
                $MULTASFPN = 0;
                $INTERESFPN = 0;
                $REPAROFPR = 0;
                $IMPTOFPR = 0;
                $MULTASFPR = 0;
                $INTERESFPR = 0;
                $REPAROAB = 0;
                $IMPTOAB = 0;
                $MULTASAB = 0;
                $INTERESAB = 0;
                $REPAROINV = 0;
                $IMPTOINV = 0;
                $MULTASINV = 0;
                $INTERESINV = 0;
                //SEDE DE LA GERENCIA
                $_SESSION['conexion'] = odbc_connect("LLANOS", "Admin", "losllanos");
                /*$SQL = "
    SELECT *
    FROM CS_Emitidas_RS
    WHERE FechaEmision Between #2015/06/01# And #2015/07/13#
    ";*/
                $SQL = "
SELECT [Actas de Reparo].Numero, [Actas de Reparo].Fecha, [Actas de Reparo].Año_Providencia as Anno, [Actas de Reparo].NroAutorizacion, [Actas de Reparo].FechaNotificacion, [Actas de Reparo].Reparo, [Actas de Reparo].ImpuestoOmitido, [Actas de Reparo].ImpuestPagado, [Actas de Reparo].MultaReparo, [Actas de Reparo].Intereses, [Actas de Reparo].Conformidad, [Tipo Autorizacion].Siglas2
FROM [Tipo Autorizacion] INNER JOIN (Providencia INNER JOIN [Actas de Reparo] ON (Providencia.NroAutorizacion = [Actas de Reparo].NroAutorizacion) AND (Providencia.Año_Providencia = [Actas de Reparo].Año_Providencia)) ON [Tipo Autorizacion].Tipo = Providencia.TipoAutorizacion
WHERE ((([Actas de Reparo].FechaNotificacion) Between #" . $inicio . "# And #" . $hasta . "#));
    ";

                $result = odbc_exec($_SESSION['conexion'],  utf8_decode($SQL));
                //echo $SQL.'<br/>';

                while ($reg_actas_sede = odbc_fetch_object($result)) {
                    //echo "paso por aqui";
                ?>
                    <tr>
                        <td><?php echo $reg_actas_sede->Numero ?></td>
                        <td><?php echo date("d-m-Y", strtotime($reg_actas_sede->Fecha)) ?></td>
                        <td>SEDE</td>
                        <td><?php echo $reg_actas_sede->Anno ?></td>
                        <td><?php echo $reg_actas_sede->NroAutorizacion ?></td>
                        <td><?php echo date("d-m-Y", strtotime($reg_actas_sede->FechaNotificacion)) ?></td>
                        <td><?php echo number_format($reg_actas_sede->Reparo, 2, ',', '.') ?></td>
                        <td><?php echo number_format($reg_actas_sede->ImpuestoOmitido, 2, ',', '.') ?></td>
                        <td><?php echo number_format($reg_actas_sede->MultaReparo, 2, ',', '.') ?></td>
                        <td><?php echo number_format($reg_actas_sede->Intereses, 2, ',', '.') ?></td>
                        <td><?php echo $reg_actas_sede->Conformidad ?></td>
                        <td><?php echo $reg_actas_sede->Siglas2 ?></td>
                    </tr>
                    <?php
                    switch ($reg_actas_sede->Siglas2) {
                        case "FPN":
                            $ANFPN = $ANFPN + 1;
                            $REPAROFPN = $REPAROFPN + $reg_actas_sede->Reparo;
                            $IMPTOFPN = $IMPTOFPN + $reg_actas_sede->ImpuestoOmitido;
                            $MULTASFPN = $MULTASFPN + $reg_actas_sede->MultaReparo;
                            $INTERESFPN = $INTERESFPN + $reg_actas_sede->Intereses;
                            break;
                        case "FPR":
                            $ANFPR = $ANFPR + 1;
                            $REPAROFPR = $REPAROFPR + $reg_actas_sede->Reparo;
                            $IMPTOFPR = $IMPTOFPR + $reg_actas_sede->ImpuestoOmitido;
                            $MULTASFPR = $MULTASFPR + $reg_actas_sede->MultaReparo;
                            $INTERESFPR = $INTERESFPR + $reg_actas_sede->Intereses;
                            break;
                        case "AB":
                            $ANAB = $ANAB + 1;
                            $REPAROAB = $REPAROAB + $reg_actas_sede->Reparo;
                            $IMPTOAB = $IMPTOAB + $reg_actas_sede->ImpuestoOmitido;
                            $MULTASAB = $MULTASAB + $reg_actas_sede->MultaReparo;
                            $INTERESAB = $INTERESAB + $reg_actas_sede->Intereses;
                            break;
                        case "IF":
                            $ANINV = $ANINV + 1;
                            $REPAROINV = $REPAROINV + $reg_actas_sede->Reparo;
                            $IMPTOINV = $IMPTOINV + $reg_actas_sede->ImpuestoOmitido;
                            $MULTASINV = $MULTASINV + $reg_actas_sede->MultaReparo;
                            $INTERESINV = $INTERESINV + $reg_actas_sede->Intereses;
                            break;
                    }
                }

                //***************SFA***********************
                $_SESSION['conexion'] = odbc_connect("SFA", "Admin", "losllanos");
                /*$SQL = "
    SELECT *
    FROM CS_Emitidas_RS
    WHERE FechaEmision Between #2015/06/01# And #2015/07/13#
    ";*/
                $SQLsfa = "
SELECT [Actas de Reparo].Numero, [Actas de Reparo].Fecha, [Actas de Reparo].Año_Providencia as Anno, [Actas de Reparo].NroAutorizacion, [Actas de Reparo].FechaNotificacion, [Actas de Reparo].Reparo, [Actas de Reparo].ImpuestoOmitido, [Actas de Reparo].ImpuestPagado, [Actas de Reparo].MultaReparo, [Actas de Reparo].Intereses, [Actas de Reparo].Conformidad, [Tipo Autorizacion].Siglas2
FROM [Tipo Autorizacion] INNER JOIN (Providencia INNER JOIN [Actas de Reparo] ON (Providencia.NroAutorizacion = [Actas de Reparo].NroAutorizacion) AND (Providencia.Año_Providencia = [Actas de Reparo].Año_Providencia)) ON [Tipo Autorizacion].Tipo = Providencia.TipoAutorizacion
WHERE ((([Actas de Reparo].FechaNotificacion) Between #" . $inicio . "# And #" . $hasta . "#));
    ";

                $resultsfa = odbc_exec($_SESSION['conexion'],  utf8_decode($SQLsfa));
                //echo $SQL.'<br/>';

                while ($reg_actas_sfa = odbc_fetch_object($resultsfa)) {
                    //echo "paso por aqui";
                    ?>
                    <tr>
                        <td><?php echo $reg_actas_sfa->Numero ?></td>
                        <td><?php echo date("d-m-Y", strtotime($reg_actas_sfa->Fecha)) ?></td>
                        <td>SFA</td>
                        <td><?php echo $reg_actas_sfa->Anno ?></td>
                        <td><?php echo $reg_actas_sfa->NroAutorizacion ?></td>
                        <td><?php echo date("d-m-Y", strtotime($reg_actas_sfa->FechaNotificacion)) ?></td>
                        <td><?php echo number_format($reg_actas_sfa->Reparo, 2, ',', '.') ?></td>
                        <td><?php echo number_format($reg_actas_sfa->ImpuestoOmitido, 2, ',', '.') ?></td>
                        <td><?php echo number_format($reg_actas_sfa->MultaReparo, 2, ',', '.') ?></td>
                        <td><?php echo number_format($reg_actas_sfa->Intereses, 2, ',', '.') ?></td>
                        <td><?php echo $reg_actas_sfa->Conformidad ?></td>
                        <td><?php echo $reg_actas_sfa->Siglas2 ?></td>
                    </tr>
                    <?php
                    switch ($reg_actas_sfa->Siglas2) {
                        case "FPN":
                            $ANFPN = $ANFPN + 1;
                            $REPAROFPN = $REPAROFPN + $reg_actas_sfa->Reparo;
                            $IMPTOFPN = $IMPTOFPN + $reg_actas_sfa->ImpuestoOmitido;
                            $MULTASFPN = $MULTASFPN + $reg_actas_sfa->MultaReparo;
                            $INTERESFPN = $INTERESFPN + $reg_actas_sfa->Intereses;
                            break;
                        case "FPR":
                            $ANFPR = $ANFPR + 1;
                            $REPAROFPR = $REPAROFPR + $reg_actas_sfa->Reparo;
                            $IMPTOFPR = $IMPTOFPR + $reg_actas_sfa->ImpuestoOmitido;
                            $MULTASFPR = $MULTASFPR + $reg_actas_sfa->MultaReparo;
                            $INTERESFPR = $INTERESFPR + $reg_actas_sfa->Intereses;
                            break;
                        case "AB":
                            $ANAB = $ANAB + 1;
                            $REPAROAB = $REPAROAB + $reg_actas_sfa->Reparo;
                            $IMPTOAB = $IMPTOAB + $reg_actas_sfa->ImpuestoOmitido;
                            $MULTASAB = $MULTASAB + $reg_actas_sfa->MultaReparo;
                            $INTERESAB = $INTERESAB + $reg_actas_sfa->Intereses;
                            break;
                        case "IF":
                            $ANINV = $ANINV + 1;
                            $REPAROINV = $REPAROINV + $reg_actas_sfa->Reparo;
                            $IMPTOINV = $IMPTOINV + $reg_actas_sfa->ImpuestoOmitido;
                            $MULTASINV = $MULTASINV + $reg_actas_sfa->MultaReparo;
                            $INTERESINV = $INTERESINV + $reg_actas_sfa->Intereses;
                            break;
                    }
                }
                //*****************************************

                //***************SJM***********************
                $_SESSION['conexion'] = odbc_connect("SJM", "Admin", "losllanos");
                /*$SQL = "
    SELECT *
    FROM CS_Emitidas_RS
    WHERE FechaEmision Between #2015/06/01# And #2015/07/13#
    ";*/
                $SQLsjm = "
SELECT [Actas de Reparo].Numero, [Actas de Reparo].Fecha, [Actas de Reparo].Año_Providencia as Anno, [Actas de Reparo].NroAutorizacion, [Actas de Reparo].FechaNotificacion, [Actas de Reparo].Reparo, [Actas de Reparo].ImpuestoOmitido, [Actas de Reparo].ImpuestPagado, [Actas de Reparo].MultaReparo, [Actas de Reparo].Intereses, [Actas de Reparo].Conformidad, [Tipo Autorizacion].Siglas2
FROM [Tipo Autorizacion] INNER JOIN (Providencia INNER JOIN [Actas de Reparo] ON (Providencia.NroAutorizacion = [Actas de Reparo].NroAutorizacion) AND (Providencia.Año_Providencia = [Actas de Reparo].Año_Providencia)) ON [Tipo Autorizacion].Tipo = Providencia.TipoAutorizacion
WHERE ((([Actas de Reparo].FechaNotificacion) Between #" . $inicio . "# And #" . $hasta . "#));
    ";

                $resultsjm = odbc_exec($_SESSION['conexion'],  utf8_decode($SQLsjm));
                //echo $SQL.'<br/>';

                while ($reg_actas_sjm = odbc_fetch_object($resultsjm)) {
                    //echo "paso por aqui";
                    ?>
                    <tr>
                        <td><?php echo $reg_actas_sjm->Numero ?></td>
                        <td><?php echo date("d-m-Y", strtotime($reg_actas_sjm->Fecha)) ?></td>
                        <td>SJM</td>
                        <td><?php echo $reg_actas_sjm->Anno ?></td>
                        <td><?php echo $reg_actas_sjm->NroAutorizacion ?></td>
                        <td><?php echo date("d-m-Y", strtotime($reg_actas_sjm->FechaNotificacion)) ?></td>
                        <td><?php echo number_format($reg_actas_sjm->Reparo, 2, ',', '.') ?></td>
                        <td><?php echo number_format($reg_actas_sjm->ImpuestoOmitido, 2, ',', '.') ?></td>
                        <td><?php echo number_format($reg_actas_sjm->MultaReparo, 2, ',', '.') ?></td>
                        <td><?php echo number_format($reg_actas_sjm->Intereses, 2, ',', '.') ?></td>
                        <td><?php echo $reg_actas_sjm->Conformidad ?></td>
                        <td><?php echo $reg_actas_sjm->Siglas2 ?></td>
                    </tr>
                    <?php
                    switch ($reg_actas_sjm->Siglas2) {
                        case "FPN":
                            $ANFPN = $ANFPN + 1;
                            $REPAROFPN = $REPAROFPN + $reg_actas_sjm->Reparo;
                            $IMPTOFPN = $IMPTOFPN + $reg_actas_sjm->ImpuestoOmitido;
                            $MULTASFPN = $MULTASFPN + $reg_actas_sjm->MultaReparo;
                            $INTERESFPN = $INTERESFPN + $reg_actas_sjm->Intereses;
                            break;
                        case "FPR":
                            $ANFPR = $ANFPR + 1;
                            $REPAROFPR = $REPAROFPR + $reg_actas_sjm->Reparo;
                            $IMPTOFPR = $IMPTOFPR + $reg_actas_sjm->ImpuestoOmitido;
                            $MULTASFPR = $MULTASFPR + $reg_actas_sjm->MultaReparo;
                            $INTERESFPR = $INTERESFPR + $reg_actas_sjm->Intereses;
                            break;
                        case "AB":
                            $ANAB = $ANAB + 1;
                            $REPAROAB = $REPAROAB + $reg_actas_sjm->Reparo;
                            $IMPTOAB = $IMPTOAB + $reg_actas_sjm->ImpuestoOmitido;
                            $MULTASAB = $MULTASAB + $reg_actas_sjm->MultaReparo;
                            $INTERESAB = $INTERESAB + $reg_actas_sjm->Intereses;
                            break;
                        case "IF":
                            $ANINV = $ANINV + 1;
                            $REPAROINV = $REPAROINV + $reg_actas_sjm->Reparo;
                            $IMPTOINV = $IMPTOINV + $reg_actas_sjm->ImpuestoOmitido;
                            $MULTASINV = $MULTASINV + $reg_actas_sjm->MultaReparo;
                            $INTERESINV = $INTERESINV + $reg_actas_sjm->Intereses;
                            break;
                    }
                }
                //*****************************************

                //***************VLP***********************
                $_SESSION['conexion'] = odbc_connect("VLP", "Admin", "losllanos");
                /*$SQL = "
    SELECT *
    FROM CS_Emitidas_RS
    WHERE FechaEmision Between #2015/06/01# And #2015/07/13#
    ";*/
                $SQLvlp = "
SELECT [Actas de Reparo].Numero, [Actas de Reparo].Fecha, [Actas de Reparo].Año_Providencia as Anno, [Actas de Reparo].NroAutorizacion, [Actas de Reparo].FechaNotificacion, [Actas de Reparo].Reparo, [Actas de Reparo].ImpuestoOmitido, [Actas de Reparo].ImpuestPagado, [Actas de Reparo].MultaReparo, [Actas de Reparo].Intereses, [Actas de Reparo].Conformidad, [Tipo Autorizacion].Siglas2
FROM [Tipo Autorizacion] INNER JOIN (Providencia INNER JOIN [Actas de Reparo] ON (Providencia.NroAutorizacion = [Actas de Reparo].NroAutorizacion) AND (Providencia.Año_Providencia = [Actas de Reparo].Año_Providencia)) ON [Tipo Autorizacion].Tipo = Providencia.TipoAutorizacion
WHERE ((([Actas de Reparo].FechaNotificacion) Between #" . $inicio . "# And #" . $hasta . "#));
    ";

                $resultvlp = odbc_exec($_SESSION['conexion'],  utf8_decode($SQLvlp));
                //echo $SQL.'<br/>';

                while ($reg_actas_vlp = odbc_fetch_object($resultvlp)) {
                    //echo "paso por aqui";
                    ?>
                    <tr>
                        <td><?php echo $reg_actas_vlp->Numero ?></td>
                        <td><?php echo date("d-m-Y", strtotime($reg_actas_vlp->Fecha)) ?></td>
                        <td>VLP</td>
                        <td><?php echo $reg_actas_vlp->Anno ?></td>
                        <td><?php echo $reg_actas_vlp->NroAutorizacion ?></td>
                        <td><?php echo date("d-m-Y", strtotime($reg_actas_vlp->FechaNotificacion)) ?></td>
                        <td><?php echo number_format($reg_actas_vlp->Reparo, 2, ',', '.') ?></td>
                        <td><?php echo number_format($reg_actas_vlp->ImpuestoOmitido, 2, ',', '.') ?></td>
                        <td><?php echo number_format($reg_actas_vlp->MultaReparo, 2, ',', '.') ?></td>
                        <td><?php echo number_format($reg_actas_vlp->Intereses, 2, ',', '.') ?></td>
                        <td><?php echo $reg_actas_vlp->Conformidad ?></td>
                        <td><?php echo $reg_actas_vlp->Siglas2 ?></td>
                    </tr>
                    <?php
                    switch ($reg_actas_vlp->Siglas2) {
                        case "FPN":
                            $ANFPN = $ANFPN + 1;
                            $REPAROFPN = $REPAROFPN + $reg_actas_vlp->Reparo;
                            $IMPTOFPN = $IMPTOFPN + $reg_actas_vlp->ImpuestoOmitido;
                            $MULTASFPN = $MULTASFPN + $reg_actas_vlp->MultaReparo;
                            $INTERESFPN = $INTERESFPN + $reg_actas_vlp->Intereses;
                            break;
                        case "FPR":
                            $ANFPR = $ANFPR + 1;
                            $REPAROFPR = $REPAROFPR + $reg_actas_vlp->Reparo;
                            $IMPTOFPR = $IMPTOFPR + $reg_actas_vlp->ImpuestoOmitido;
                            $MULTASFPR = $MULTASFPR + $reg_actas_vlp->MultaReparo;
                            $INTERESFPR = $INTERESFPR + $reg_actas_vlp->Intereses;
                            break;
                        case "AB":
                            $ANAB = $ANAB + 1;
                            $REPAROAB = $REPAROAB + $reg_actas_vlp->Reparo;
                            $IMPTOAB = $IMPTOAB + $reg_actas_vlp->ImpuestoOmitido;
                            $MULTASAB = $MULTASAB + $reg_actas_vlp->MultaReparo;
                            $INTERESAB = $INTERESAB + $reg_actas_vlp->Intereses;
                            break;
                        case "IF":
                            $ANINV = $ANINV + 1;
                            $REPAROINV = $REPAROINV + $reg_actas_vlp->Reparo;
                            $IMPTOINV = $IMPTOINV + $reg_actas_vlp->ImpuestoOmitido;
                            $MULTASINV = $MULTASINV + $reg_actas_vlp->MultaReparo;
                            $INTERESINV = $INTERESINV + $reg_actas_vlp->Intereses;
                            break;
                    }
                }
                //*****************************************

                //***************ALT***********************
                $_SESSION['conexion'] = odbc_connect("ALT", "Admin", "losllanos");
                /*$SQL = "
    SELECT *
    FROM CS_Emitidas_RS
    WHERE FechaEmision Between #2015/06/01# And #2015/07/13#
    ";*/
                $SQLalt = "
SELECT [Actas de Reparo].Numero, [Actas de Reparo].Fecha, [Actas de Reparo].Año_Providencia as Anno, [Actas de Reparo].NroAutorizacion, [Actas de Reparo].FechaNotificacion, [Actas de Reparo].Reparo, [Actas de Reparo].ImpuestoOmitido, [Actas de Reparo].ImpuestPagado, [Actas de Reparo].MultaReparo, [Actas de Reparo].Intereses, [Actas de Reparo].Conformidad, [Tipo Autorizacion].Siglas2
FROM [Tipo Autorizacion] INNER JOIN (Providencia INNER JOIN [Actas de Reparo] ON (Providencia.NroAutorizacion = [Actas de Reparo].NroAutorizacion) AND (Providencia.Año_Providencia = [Actas de Reparo].Año_Providencia)) ON [Tipo Autorizacion].Tipo = Providencia.TipoAutorizacion
WHERE ((([Actas de Reparo].FechaNotificacion) Between #" . $inicio . "# And #" . $hasta . "#));
    ";

                $resultalt = odbc_exec($_SESSION['conexion'],  utf8_decode($SQLalt));
                //echo $SQL.'<br/>';

                while ($reg_actas_alt = odbc_fetch_object($resultalt)) {
                    //echo "paso por aqui";
                    ?>
                    <tr>
                        <td><?php echo $reg_actas_alt->Numero ?></td>
                        <td><?php echo date("d-m-Y", strtotime($reg_actas_alt->Fecha)) ?></td>
                        <td>ALT</td>
                        <td><?php echo $reg_actas_alt->Anno ?></td>
                        <td><?php echo $reg_actas_alt->NroAutorizacion ?></td>
                        <td><?php echo date("d-m-Y", strtotime($reg_actas_alt->FechaNotificacion)) ?></td>
                        <td><?php echo number_format($reg_actas_alt->Reparo, 2, ',', '.') ?></td>
                        <td><?php echo number_format($reg_actas_alt->ImpuestoOmitido, 2, ',', '.') ?></td>
                        <td><?php echo number_format($reg_actas_alt->MultaReparo, 2, ',', '.') ?></td>
                        <td><?php echo number_format($reg_actas_alt->Intereses, 2, ',', '.') ?></td>
                        <td><?php echo $reg_actas_alt->Conformidad ?></td>
                        <td><?php echo $reg_actas_alt->Siglas2 ?></td>
                    </tr>
                <?php
                    switch ($reg_actas_alt->Siglas2) {
                        case "FPN":
                            $ANFPN = $ANFPN + 1;
                            $REPAROFPN = $REPAROFPN + $reg_actas_alt->Reparo;
                            $IMPTOFPN = $IMPTOFPN + $reg_actas_alt->ImpuestoOmitido;
                            $MULTASFPN = $MULTASFPN + $reg_actas_alt->MultaReparo;
                            $INTERESFPN = $INTERESFPN + $reg_actas_alt->Intereses;
                            break;
                        case "FPR":
                            $ANFPR = $ANFPR + 1;
                            $REPAROFPR = $REPAROFPR + $reg_actas_alt->Reparo;
                            $IMPTOFPR = $IMPTOFPR + $reg_actas_alt->ImpuestoOmitido;
                            $MULTASFPR = $MULTASFPR + $reg_actas_alt->MultaReparo;
                            $INTERESFPR = $INTERESFPR + $reg_actas_alt->Intereses;
                            break;
                        case "AB":
                            $ANAB = $ANAB + 1;
                            $REPAROAB = $REPAROAB + $reg_actas_alt->Reparo;
                            $IMPTOAB = $IMPTOAB + $reg_actas_alt->ImpuestoOmitido;
                            $MULTASAB = $MULTASAB + $reg_actas_alt->MultaReparo;
                            $INTERESAB = $INTERESAB + $reg_actas_alt->Intereses;
                            break;
                        case "IF":
                            $ANINV = $ANINV + 1;
                            $REPAROINV = $REPAROINV + $reg_actas_alt->Reparo;
                            $IMPTOINV = $IMPTOINV + $reg_actas_alt->ImpuestoOmitido;
                            $MULTASINV = $MULTASINV + $reg_actas_alt->MultaReparo;
                            $INTERESINV = $INTERESINV + $reg_actas_alt->Intereses;
                            break;
                    }
                }
                //*****************************************
                ?>
            </tbody>
        </table>
    </div>
    <div id="ResumenActasNot" style="display:none;">
        <?php
        global $ANFPN;
        global $ANFPR;
        global $ANAB;
        global $ANINV;
        global $REPAROFPN;
        global $IMPTOFPN;
        global $MULTASFPN;
        global $INTERESFPN;
        global $REPAROFPR;
        global $IMPTOFPR;
        global $MULTASFPR;
        global $INTERESFPR;
        global $REPAROAB;
        global $IMPTOAB;
        global $MULTASAB;
        global $INTERESAB;
        global $REPAROINV;
        global $IMPTOINV;
        global $MULTASINV;
        global $INTERESINV;
        echo "FPN: " . $ANFPN . "-" . number_format($REPAROFPN, 2, ',', '.') . "-" . number_format($IMPTOFPN, 2, ',', '.') . "-" . number_format($MULTASFPN, 2, ',', '.') . "-" . number_format($INTERESFPN, 2, ',', '.') . '<br/>';
        echo "FPR: " . $ANFPR . "-" . number_format($REPAROFPR, 2, ',', '.') . "-" . number_format($IMPTOFPR, 2, ',', '.') . "-" . number_format($MULTASFPR, 2, ',', '.') . "-" . number_format($INTERESFPR, 2, ',', '.') . '<br/>';
        echo "AB: " . $ANAB . "-" . number_format($REPAROAB, 2, ',', '.') . "-" . number_format($IMPTOAB, 2, ',', '.') . "-" . number_format($MULTASAB, 2, ',', '.') . "-" . number_format($INTERESAB, 2, ',', '.') . '<br/>';
        echo "INV: " . $ANINV . "-" . number_format($REPAROINV, 2, ',', '.') . "-" . number_format($IMPTOINV, 2, ',', '.') . "-" . number_format($MULTASINV, 2, ',', '.') . "-" . number_format($INTERESINV, 2, ',', '.') . '<br/>';
        ?>
    </div>

    <div id="ActasPagos" style="display:block;">ACTAS PAGOS
        <table width="80%" border="1">
            <tbody>
                <tr>
                    <th scope="col">SECTOR/SEDE</th>
                    <th scope="col">NUMERO</th>
                    <th scope="col">AÑO PROV.</th>
                    <th scope="col">NRO PROV.</th>
                    <th scope="col">FECHA PAGO</th>
                    <th scope="col">MONTO</th>
                    <th scope="col">FECHA ACTA</th>
                    <th scope="col">PROGRAMA</th>
                </tr>
                <?php
                $APFPN = 0;
                $APFPR = 0;
                $APAB = 0;
                $APINV = 0;
                $PAGOFPN = 0;
                $PAGOFPR = 0;
                $PAGOAB = 0;
                $PAGOINV = 0;
                //SEDE DE LA GERENCIA
                $_SESSION['conexion'] = odbc_connect("LLANOS", "Admin", "losllanos");
                /*$SQL = "
    SELECT *
    FROM CS_Emitidas_RS
    WHERE FechaEmision Between #2015/06/01# And #2015/07/13#
    ";*/
                $SQL = "
SELECT Actas_Detalle_Pagos.Numero, Actas_Detalle_Pagos.Año_Providencia AS Anno, Actas_Detalle_Pagos.NroAutorizacion, Actas_Detalle_Pagos.Tributo, Actas_Detalle_Pagos.FechaPago, Actas_Detalle_Pagos.Monto, [Tipo Autorizacion].Siglas2, [Actas de Reparo].Fecha
FROM [Actas de Reparo] INNER JOIN (([Tipo Autorizacion] INNER JOIN Providencia ON [Tipo Autorizacion].Tipo = Providencia.TipoAutorizacion) INNER JOIN Actas_Detalle_Pagos ON (Providencia.Año_Providencia = Actas_Detalle_Pagos.Año_Providencia) AND (Providencia.NroAutorizacion = Actas_Detalle_Pagos.NroAutorizacion)) ON ([Actas de Reparo].NroAutorizacion = Actas_Detalle_Pagos.NroAutorizacion) AND ([Actas de Reparo].Año_Providencia = Actas_Detalle_Pagos.Año_Providencia)
WHERE (((Actas_Detalle_Pagos.FechaPago) Between #" . $inicio . "# And #" . $hasta . "#));
    ";

                $result = odbc_exec($_SESSION['conexion'],  utf8_decode($SQL));
                //echo $SQL.'<br/>';

                while ($reg_pagos_sede = odbc_fetch_object($result)) {
                    //echo "paso por aqui";
                ?>
                    <tr>
                        <td>SEDE</td>
                        <td><?php echo $reg_pagos_sede->Numero ?></td>
                        <td><?php echo $reg_pagos_sede->Anno ?></td>
                        <td><?php echo $reg_pagos_sede->NroAutorizacion ?></td>
                        <td><?php echo date("d-m-Y", strtotime($reg_pagos_sede->FechaPago)) ?></td>
                        <td><?php echo number_format($reg_pagos_sede->Monto, 2, ',', '.') ?></td>
                        <td><?php echo date("d-m-Y", strtotime($reg_pagos_sede->Fecha)) ?></td>
                        <td><?php echo $reg_pagos_sede->Siglas2 ?></td>
                    </tr>
                    <?php
                    switch ($reg_pagos_sede->Siglas2) {
                        case "FPN":
                            $APFPN = $APFPN + 1;
                            $PAGOFPN = $PAGOFPN + $reg_pagos_sede->Monto;
                            break;
                        case "FPR":
                            $APFPR = $APFPR + 1;
                            $PAGOFPR = $PAGOFPR + $reg_pagos_sede->Monto;
                            break;
                        case "AB":
                            $APAB = $APAB + 1;
                            $PAGOAB = $PAGOAB + $reg_pagos_sede->Monto;
                            break;
                        case "IF":
                            $APINV = $APINV + 1;
                            $PAGOINV = $PAGOINV + $reg_pagos_sede->Monto;
                            break;
                    }
                }

                //******************SFA****************************
                $_SESSION['conexion'] = odbc_connect("SFA", "Admin", "losllanos");
                /*$SQL = "
    SELECT *
    FROM CS_Emitidas_RS
    WHERE FechaEmision Between #2015/06/01# And #2015/07/13#
    ";*/
                $SQLsfa = "
SELECT Actas_Detalle_Pagos.Numero, Actas_Detalle_Pagos.Año_Providencia AS Anno, Actas_Detalle_Pagos.NroAutorizacion, Actas_Detalle_Pagos.Tributo, Actas_Detalle_Pagos.FechaPago, Actas_Detalle_Pagos.Monto, [Tipo Autorizacion].Siglas2, [Actas de Reparo].Fecha
FROM [Actas de Reparo] INNER JOIN (([Tipo Autorizacion] INNER JOIN Providencia ON [Tipo Autorizacion].Tipo = Providencia.TipoAutorizacion) INNER JOIN Actas_Detalle_Pagos ON (Providencia.Año_Providencia = Actas_Detalle_Pagos.Año_Providencia) AND (Providencia.NroAutorizacion = Actas_Detalle_Pagos.NroAutorizacion)) ON ([Actas de Reparo].NroAutorizacion = Actas_Detalle_Pagos.NroAutorizacion) AND ([Actas de Reparo].Año_Providencia = Actas_Detalle_Pagos.Año_Providencia)
WHERE (((Actas_Detalle_Pagos.FechaPago) Between #" . $inicio . "# And #" . $hasta . "#));
    ";

                $resultsfa = odbc_exec($_SESSION['conexion'],  utf8_decode($SQLsfa));
                //echo $SQL.'<br/>';

                while ($reg_pagos_sfa = odbc_fetch_object($resultsfa)) {
                    //echo "paso por aqui";
                    ?>
                    <tr>
                        <td>SFA</td>
                        <td><?php echo $reg_pagos_sfa->Numero ?></td>
                        <td><?php echo $reg_pagos_sfa->Anno ?></td>
                        <td><?php echo $reg_pagos_sfa->NroAutorizacion ?></td>
                        <td><?php echo date("d-m-Y", strtotime($reg_pagos_sfa->FechaPago)) ?></td>
                        <td><?php echo number_format($reg_pagos_sfa->Monto, 2, ',', '.') ?></td>
                        <td><?php echo date("d-m-Y", strtotime($reg_pagos_sfa->Fecha)) ?></td>
                        <td><?php echo $reg_pagos_sfa->Siglas2 ?></td>
                    </tr>
                    <?php
                    switch ($reg_pagos_sfa->Siglas2) {
                        case "FPN":
                            $APFPN = $APFPN + 1;
                            $PAGOFPN = $PAGOFPN + $reg_pagos_sfa->Monto;
                            break;
                        case "FPR":
                            $APFPR = $APFPR + 1;
                            $PAGOFPR = $PAGOFPR + $reg_pagos_sfa->Monto;
                            break;
                        case "AB":
                            $APAB = $APAB + 1;
                            $PAGOAB = $PAGOAB + $reg_pagos_sfa->Monto;
                            break;
                        case "IF":
                            $APINV = $APINV + 1;
                            $PAGOINV = $PAGOINV + $reg_pagos_sfa->Monto;
                            break;
                    }
                }
                //*************************************************

                //******************SJM****************************
                //SEDE DE LA GERENCIA
                $_SESSION['conexion'] = odbc_connect("SJM", "Admin", "losllanos");
                /*$SQL = "
    SELECT *
    FROM CS_Emitidas_RS
    WHERE FechaEmision Between #2015/06/01# And #2015/07/13#
    ";*/
                $SQLsjm = "
SELECT Actas_Detalle_Pagos.Numero, Actas_Detalle_Pagos.Año_Providencia AS Anno, Actas_Detalle_Pagos.NroAutorizacion, Actas_Detalle_Pagos.Tributo, Actas_Detalle_Pagos.FechaPago, Actas_Detalle_Pagos.Monto, [Tipo Autorizacion].Siglas2, [Actas de Reparo].Fecha
FROM [Actas de Reparo] INNER JOIN (([Tipo Autorizacion] INNER JOIN Providencia ON [Tipo Autorizacion].Tipo = Providencia.TipoAutorizacion) INNER JOIN Actas_Detalle_Pagos ON (Providencia.Año_Providencia = Actas_Detalle_Pagos.Año_Providencia) AND (Providencia.NroAutorizacion = Actas_Detalle_Pagos.NroAutorizacion)) ON ([Actas de Reparo].NroAutorizacion = Actas_Detalle_Pagos.NroAutorizacion) AND ([Actas de Reparo].Año_Providencia = Actas_Detalle_Pagos.Año_Providencia)
WHERE (((Actas_Detalle_Pagos.FechaPago) Between #" . $inicio . "# And #" . $hasta . "#));
    ";

                $resultsjm = odbc_exec($_SESSION['conexion'],  utf8_decode($SQLsjm));
                //echo $SQL.'<br/>';

                while ($reg_pagos_sjm = odbc_fetch_object($resultsjm)) {
                    //echo "paso por aqui";
                    ?>
                    <tr>
                        <td>SJM</td>
                        <td><?php echo $reg_pagos_sjm->Numero ?></td>
                        <td><?php echo $reg_pagos_sjm->Anno ?></td>
                        <td><?php echo $reg_pagos_sjm->NroAutorizacion ?></td>
                        <td><?php echo date("d-m-Y", strtotime($reg_pagos_sjm->FechaPago)) ?></td>
                        <td><?php echo number_format($reg_pagos_sjm->Monto, 2, ',', '.') ?></td>
                        <td><?php echo date("d-m-Y", strtotime($reg_pagos_sjm->Fecha)) ?></td>
                        <td><?php echo $reg_pagos_sjm->Siglas2 ?></td>
                    </tr>
                    <?php
                    switch ($reg_pagos_sjm->Siglas2) {
                        case "FPN":
                            $APFPN = $APFPN + 1;
                            $PAGOFPN = $PAGOFPN + $reg_pagos_sjm->Monto;
                            break;
                        case "FPR":
                            $APFPR = $APFPR + 1;
                            $PAGOFPR = $PAGOFPR + $reg_pagos_sjm->Monto;
                            break;
                        case "AB":
                            $APAB = $APAB + 1;
                            $PAGOAB = $PAGOAB + $reg_pagos_sjm->Monto;
                            break;
                        case "IF":
                            $APINV = $APINV + 1;
                            $PAGOINV = $PAGOINV + $reg_pagos_sjm->Monto;
                            break;
                    }
                }
                //*************************************************

                //******************VLP****************************
                //SEDE DE LA GERENCIA
                $_SESSION['conexion'] = odbc_connect("VLP", "Admin", "losllanos");
                /*$SQL = "
    SELECT *
    FROM CS_Emitidas_RS
    WHERE FechaEmision Between #2015/06/01# And #2015/07/13#
    ";*/
                $SQLvlp = "
SELECT Actas_Detalle_Pagos.Numero, Actas_Detalle_Pagos.Año_Providencia AS Anno, Actas_Detalle_Pagos.NroAutorizacion, Actas_Detalle_Pagos.Tributo, Actas_Detalle_Pagos.FechaPago, Actas_Detalle_Pagos.Monto, [Tipo Autorizacion].Siglas2, [Actas de Reparo].Fecha
FROM [Actas de Reparo] INNER JOIN (([Tipo Autorizacion] INNER JOIN Providencia ON [Tipo Autorizacion].Tipo = Providencia.TipoAutorizacion) INNER JOIN Actas_Detalle_Pagos ON (Providencia.Año_Providencia = Actas_Detalle_Pagos.Año_Providencia) AND (Providencia.NroAutorizacion = Actas_Detalle_Pagos.NroAutorizacion)) ON ([Actas de Reparo].NroAutorizacion = Actas_Detalle_Pagos.NroAutorizacion) AND ([Actas de Reparo].Año_Providencia = Actas_Detalle_Pagos.Año_Providencia)
WHERE (((Actas_Detalle_Pagos.FechaPago) Between #" . $inicio . "# And #" . $hasta . "#));
    ";

                $resultvlp = odbc_exec($_SESSION['conexion'],  utf8_decode($SQLvlp));
                //echo $SQL.'<br/>';

                while ($reg_pagos_vlp = odbc_fetch_object($resultvlp)) {
                    //echo "paso por aqui";
                    ?>
                    <tr>
                        <td>VLP</td>
                        <td><?php echo $reg_pagos_vlp->Numero ?></td>
                        <td><?php echo $reg_pagos_vlp->Anno ?></td>
                        <td><?php echo $reg_pagos_vlp->NroAutorizacion ?></td>
                        <td><?php echo date("d-m-Y", strtotime($reg_pagos_vlp->FechaPago)) ?></td>
                        <td><?php echo number_format($reg_pagos_vlp->Monto, 2, ',', '.') ?></td>
                        <td><?php echo date("d-m-Y", strtotime($reg_pagos_vlp->Fecha)) ?></td>
                        <td><?php echo $reg_pagos_vlp->Siglas2 ?></td>
                    </tr>
                    <?php
                    switch ($reg_pagos_vlp->Siglas2) {
                        case "FPN":
                            $APFPN = $APFPN + 1;
                            $PAGOFPN = $PAGOFPN + $reg_pagos_vlp->Monto;
                            break;
                        case "FPR":
                            $APFPR = $APFPR + 1;
                            $PAGOFPR = $PAGOFPR + $reg_pagos_vlp->Monto;
                            break;
                        case "AB":
                            $APAB = $APAB + 1;
                            $PAGOAB = $PAGOAB + $reg_pagos_vlp->Monto;
                            break;
                        case "IF":
                            $APINV = $APINV + 1;
                            $PAGOINV = $PAGOINV + $reg_pagos_vlp->Monto;
                            break;
                    }
                }
                //*************************************************

                //******************ALT****************************
                //UNIDAD ALTAGRACIA DE ORITUCO
                $_SESSION['conexion'] = odbc_connect("ALT", "Admin", "losllanos");
                /*$SQL = "
    SELECT *
    FROM CS_Emitidas_RS
    WHERE FechaEmision Between #2015/06/01# And #2015/07/13#
    ";*/
                $SQLalt = "
SELECT Actas_Detalle_Pagos.Numero, Actas_Detalle_Pagos.Año_Providencia AS Anno, Actas_Detalle_Pagos.NroAutorizacion, Actas_Detalle_Pagos.Tributo, Actas_Detalle_Pagos.FechaPago, Actas_Detalle_Pagos.Monto, [Tipo Autorizacion].Siglas2, [Actas de Reparo].Fecha
FROM [Actas de Reparo] INNER JOIN (([Tipo Autorizacion] INNER JOIN Providencia ON [Tipo Autorizacion].Tipo = Providencia.TipoAutorizacion) INNER JOIN Actas_Detalle_Pagos ON (Providencia.Año_Providencia = Actas_Detalle_Pagos.Año_Providencia) AND (Providencia.NroAutorizacion = Actas_Detalle_Pagos.NroAutorizacion)) ON ([Actas de Reparo].NroAutorizacion = Actas_Detalle_Pagos.NroAutorizacion) AND ([Actas de Reparo].Año_Providencia = Actas_Detalle_Pagos.Año_Providencia)
WHERE (((Actas_Detalle_Pagos.FechaPago) Between #" . $inicio . "# And #" . $hasta . "#));
    ";

                $resultalt = odbc_exec($_SESSION['conexion'],  utf8_decode($SQLalt));
                //echo $SQL.'<br/>';

                while ($reg_pagos_alt = odbc_fetch_object($resultalt)) {
                    //echo "paso por aqui";
                    ?>
                    <tr>
                        <td>ALT</td>
                        <td><?php echo $reg_pagos_alt->Numero ?></td>
                        <td><?php echo $reg_pagos_alt->Anno ?></td>
                        <td><?php echo $reg_pagos_alt->NroAutorizacion ?></td>
                        <td><?php echo date("d-m-Y", strtotime($reg_pagos_alt->FechaPago)) ?></td>
                        <td><?php echo number_format($reg_pagos_alt->Monto, 2, ',', '.') ?></td>
                        <td><?php echo date("d-m-Y", strtotime($reg_pagos_alt->Fecha)) ?></td>
                        <td><?php echo $reg_pagos_alt->Siglas2 ?></td>
                    </tr>
                <?php
                    switch ($reg_pagos_alt->Siglas2) {
                        case "FPN":
                            $APFPN = $APFPN + 1;
                            $PAGOFPN = $PAGOFPN + $reg_pagos_alt->Monto;
                            break;
                        case "FPR":
                            $APFPR = $APFPR + 1;
                            $PAGOFPR = $PAGOFPR + $reg_pagos_alt->Monto;
                            break;
                        case "AB":
                            $APAB = $APAB + 1;
                            $PAGOAB = $PAGOAB + $reg_pagos_alt->Monto;
                            break;
                        case "IF":
                            $APINV = $APINV + 1;
                            $PAGOINV = $PAGOINV + $reg_pagos_alt->Monto;
                            break;
                    }
                }
                //*************************************************


                ?>
            </tbody>
        </table>
    </div>
    <div id="ResumenPagos" style="display:none;">
        <?php
        global $APFPN;
        global $APFPR;
        global $APAB;
        global $APINV;
        global $PAGOFPN;
        global $PAGOFPR;
        global $PAGOAB;
        global $PAGOINV;
        echo "FPN: " . $APFPN . "-" . number_format($PAGOFPN, 2, ',', '.') . '<br/>';
        echo "FPR: " . $APFPR . "-" . number_format($PAGOFPR, 2, ',', '.') . '<br/>';
        echo "AB: " . $APAB . "-" . number_format($PAGOAB, 2, ',', '.') . '<br/>';
        echo "INV: " . $APINV . "-" . number_format($PAGOINV, 2, ',', '.') . '<br/>';
        ?>
    </div>

    <div id="CuadroResumen" class="CSSTableGenerator">
        <p align="center"><strong>RESUMEN DE FISCALIZACIONES PUNTUALES DESDE EL <?php echo " " . date("d-m-Y", strtotime($inicio)) . " " ?> HASTA <?php echo " " . date("d-m-Y", strtotime($hasta)) ?></strong></p>
        <table width="80%" border="1" align="center">
            <tbody>
                <tr>
                    <th scope="col">PROGRAMA</th>
                    <th scope="col">PROV EMI</th>
                    <th scope="col">PROV NOT</th>
                    <th scope="col">ACTAS NOT</th>
                    <th scope="col">PROD. POTENCIAL</th>
                    <th scope="col">PROD. EFECTIVA</th>
                </tr>
                <tr>
                    <th scope="row">FPN</th>
                    <td align="center"><?php echo $FPN ?></td>
                    <td align="center"><?php echo $NFPN ?></td>
                    <td align="center"><?php echo $ANFPN ?></td>
                    <td align="right"><?php echo number_format($IMPTOFPN, 2, ',', '.') ?></td>
                    <td align="right"><?php echo number_format($PAGOFPN, 2, ',', '.') ?></td>
                </tr>
                <tr>
                    <th scope="row">FPR</th>
                    <td align="center"><?php echo $FPR ?></td>
                    <td align="center"><?php echo $NFPR ?></td>
                    <td align="center"><?php echo $ANFPR ?></td>
                    <td align="right"><?php echo number_format($IMPTOFPR, 2, ',', '.') ?></td>
                    <td align="right"><?php echo number_format($PAGOFPR, 2, ',', '.') ?></td>
                </tr>
                <tr>
                    <th scope="row">AB</th>
                    <td align="center"><?php echo $AB ?></td>
                    <td align="center"><?php echo $NAB ?></td>
                    <td align="center"><?php echo $ANAB ?></td>
                    <td align="right"><?php echo number_format($IMPTOAB, 2, ',', '.') ?></td>
                    <td align="right"><?php echo number_format($PAGOAB, 2, ',', '.') ?></td>
                </tr>
                <tr>
                    <th scope="row">INV</th>
                    <td align="center"><?php echo $INV ?></td>
                    <td align="center"><?php echo $NINV ?></td>
                    <td align="center"><?php echo $ANINV ?></td>
                    <td align="right"><?php echo number_format($IMPTOINV, 2, ',', '.') ?></td>
                    <td align="right"><?php echo number_format($PAGOINV, 2, ',', '.') ?></td>
                </tr>
                <tr>
                    <th scope="row">TOTALES</th>
                    <td align="center"><?php echo ($FPN + $FPR + $AB + $INV); ?></td>
                    <td align="center"><?php echo ($NFPN + $NFPR + $NAB + $NINV); ?></td>
                    <td align="center"><?php echo ($ANFPN + $ANFPR + $ANAB + $ANINV); ?></td>
                    <td align="right"><?php echo number_format(($IMPTOFPN + $IMPTOFPR + $IMPTOAB + $IMPTOINV), 2, ',', '.'); ?></td>
                    <td align="right"><?php echo number_format(($PAGOFPN + $PAGOFPR + $PAGOAB + $PAGOINV), 2, ',', '.'); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

</body>

</html>