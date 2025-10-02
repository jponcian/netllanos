<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
  header("Location: index.php?errorusuario=val");
  exit();
}
$acceso = 5;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

//------------------
// --- PARA GUARDAR
if ($_POST['CMDGUARDAR'] == "Guardar") {
  if ($_POST['OFECHA'] <> '') {
    //---------------
    $consulta_x = "UPDATE fis_actas SET fecha_notificacion = '" . voltea_fecha($_POST['OFECHA']) . "', status=1, usuario= " . $_SESSION['CEDULA_USUARIO'] . " WHERE id_sector=" . $_SESSION['SEDE'] . " AND anno_prov=" . $_SESSION['ANNO_PRO'] . " AND num_prov=" . $_SESSION['NUM_PRO'] . ";";
    $tabla_x = mysql_query($consulta_x);
    // MENSAJE DE GUARDADO
    echo "<script type=\"text/javascript\">alert('���Acta Modificada Exitosamente!!!');</script>";
  } else {
    echo "<script type=\"text/javascript\">alert('���Por favor seleccione la Fecha de Notificaci�n!!!');</script>";
  }
}
?>
<html>

<head>
  <title>Incluir Fecha de Notificacion - Actas</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="X-UA-Compatible" content="IE=9" />
  <link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
  <script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
  <!--<meta http-equiv="refresh" content="10">-->

</head>

<body style="background: transparent !important;">
  <p>
    <?php include "../titulo.php"; ?>

  </p>
  <p>
    <?php
    include "menu.php";
    ?>

  <form name="form1" method="post" action="">
    </p>
    <p>&nbsp;</p>
    <table width="60%" border="1" align="center">
      <tr>
        <td align="center" class="TituloTabla" colspan="9"><span><u>Datos de la Providencia</u></span></td>
      </tr>
      <tr>
        <td width="10%" bgcolor="#CCCCCC"><strong>A�o:</strong></td>
        <td width="10%"><label>
            <div align="center"><span class="Estilo15">
                <?php
                $consulta = "SELECT * FROM vista_providencias WHERE anno=" . $_SESSION['ANNO_PRO'] . " AND numero=" . $_SESSION['NUM_PRO'] . " AND sector =" . $_SESSION['SEDE'] . ";";
                $tabla = mysql_query($consulta);
                $registro = mysql_fetch_object($tabla);

                echo $registro->anno;
                $tipo = $registro->tipo;
                ?>
              </span></div>
          </label></td>
        <td width="10%" bgcolor="#CCCCCC"><strong>N&uacute;mero:</strong></td>
        <td width="10%"><label>
            <div align="center"><span class="Estilo15"><?php echo $registro->numero; ?></span></div>
          </label></td>
        <td width="10%" bgcolor="#CCCCCC"><strong>Fecha:</strong></td>
        <td width="10%"><label>
            <div align="center"><span class="Estilo15"><?php echo voltea_fecha($registro->fecha_emision); ?></span></div>
          </label></td>
        <td width="10%" bgcolor="#CCCCCC"><strong>Sector:</strong></td>
        <td><label>
            <div align="center"><span class="Estilo15"><?php echo strtoupper($registro->nombre); ?></span></div>
          </label></td>
      </tr>
    </table>

    <table width="60%" border="1" align="center">
      <tr>
        <td bgcolor="#CCCCCC"><strong>Rif: </strong></td>
        <td><label>
            <div align="center"><span class="Estilo15"><?php echo formato_rif($registro->rif); ?></span></div>
          </label></td>
        <td bgcolor="#CCCCCC"><strong>Contribuyente:</strong></td>
        <td><label><span class="Estilo15"><?php echo $registro->contribuyente; ?></span></label></td>
      </tr>
      <tr>
        <td bgcolor="#CCCCCC"><strong>Cedula:</strong></td>
        <td><label>
            <div align="center"><span class="Estilo15"><?php echo formato_cedula($registro->ci_supervisor); ?></span></div>
          </label></td>
        <td bgcolor="#CCCCCC"><strong>Supervisor:</strong></td>
        <td><label><span class="Estilo15"><?php echo $registro->Nombres . " " . $registro->Apellidos; ?></span></label></td>
      </tr>
      <tr>
        <td bgcolor="#CCCCCC"><strong>Cedula:</strong></td>
        <td><label>
            <div align="center"><span class="Estilo15"><?php echo formato_cedula($registro->ci_fiscal1); ?></span></div>
          </label></td>
        <td bgcolor="#CCCCCC"><strong>Fiscal:</strong></td>
        <td><label><span class="Estilo15"><?php echo $registro->Nombres1 . " " . $registro->Apellidos1; ?></span></label></td>
      </tr>
    </table>
    <p>
      <label></label>
    <form name="form5" method="post" action="#vista">
      <table width="60%" border=1 align=center>
        <tbody>
          <tr>
            <td class="TituloTabla" height="27" colspan="6" align="center"><span><u>Acta(s) actual(es) aplicada(s) al Contribuyente</u></span> </td>
          </tr>
          <tr>
            <td bgcolor="#CCCCCC">
              <div align="center" class="Estilo8"><strong>Acta</strong></div>
            </td>
            <td bgcolor="#CCCCCC">
              <div align="center" class="Estilo8"><strong>Fecha Registro </strong></div>
            </td>
            <td bgcolor="#CCCCCC">
              <div align="center" class="Estilo8"><strong>Monto Reparo</strong></div>
            </td>
            <td bgcolor="#CCCCCC">
              <div align="center" class="Estilo8"><strong>Impuesto Pagado</strong></div>
            </td>
            <td bgcolor="#CCCCCC">
              <div align="center" class="Estilo8"><strong>Impuesto Omitido</strong></div>
            </td>
            <td bgcolor="#CCCCCC">
              <div align="center" class="Estilo8"><strong>Fecha Not</strong></div>
            </td>
          </tr>
          <?php $i = 0;

          $consulta_x = "SELECT Sum(fis_actas_detalle.reparo) as monto_reparo, Sum(fis_actas_detalle.impuesto_pagado) as monto_pagado, Sum(fis_actas_detalle.impuesto_omitido) as monto_impuesto, expedientes_fiscalizacion.anno, expedientes_fiscalizacion.numero, expedientes_fiscalizacion.sector, fis_actas.numero as acta, fis_actas.fecha, a_tributos.nombre, fis_actas.fecha_notificacion FROM fis_actas INNER JOIN expedientes_fiscalizacion ON expedientes_fiscalizacion.anno = fis_actas.anno_prov AND fis_actas.num_prov = expedientes_fiscalizacion.numero AND expedientes_fiscalizacion.sector = fis_actas.id_sector INNER JOIN fis_actas_detalle ON fis_actas_detalle.id_acta = fis_actas.id_acta INNER JOIN a_tributos ON fis_actas_detalle.tributo = a_tributos.id_tributo WHERE expedientes_fiscalizacion.numero = " . $_SESSION['NUM_PRO'] . " AND expedientes_fiscalizacion.anno = " . $_SESSION['ANNO_PRO'] . " AND expedientes_fiscalizacion.sector = " . $_SESSION['SEDE'] . " GROUP BY expedientes_fiscalizacion.anno, expedientes_fiscalizacion.numero, expedientes_fiscalizacion.sector";
          $tabla_x = mysql_query($consulta_x);
          //---------------
          while ($registro_x = mysql_fetch_object($tabla_x)) {
            $i++;
          ?>
            <tr>
              <td>
                <div align="center"><span class="Estilo15">
                    <?php
                    list($resolucion) = funcion_acta_reparo($_SESSION['SEDE'], $_SESSION['ANNO_PRO'], $_SESSION['NUM_PRO']);
                    echo $resolucion;
                    ?>
                  </span></div>
              </td>
              <td>
                <div align="center"><span class="Estilo15"><?php echo voltea_fecha($registro_x->fecha); ?></span></div>
              </td>
              <td>
                <div align="center"><span class="Estilo15"><?php echo formato_moneda($registro_x->monto_reparo); ?></span></div>
              </td>
              <td>
                <div align="center"><span class="Estilo15"><?php echo formato_moneda($registro_x->monto_pagado); ?></span></div>
              </td>
              <td>
                <div align="center"><span class="Estilo15"><?php echo formato_moneda($registro_x->monto_impuesto); ?></span></div>
              </td>
              <td>
                <div align="center"><span class="Estilo15">
                    <?php
                    if ($registro_x->fecha_notificacion > '01-01-2000') {
                      $_POST['OFECHA'] = voltea_fecha($registro_x->fecha_notificacion);
                    } else {
                      $_POST['OFECHA'] = $_POST['OFECHA'];
                    }
                    ?><input onclick='javascript:scwShow(this,event);' type="text" name="OFECHA" size="8" readonly value="<?php echo $_POST['OFECHA']; ?>" />
                  </span></div>
              </td>
            </tr>
          <?php
          }
          ?>
        </tbody>
      </table>
      <p align="center">
        <input type="submit" class="boton" name="CMDGUARDAR" value="Guardar">
      </p>
    </form>
    <p align="center">Haga Click en la fecha de Notificaci&oacute;n para Agregar o Cambiar la fecha y presione Guardar.</p>
    <p>&nbsp;</p>
    </td>

    <p>
      <?php include "../pie.php"; ?>
    </p>
    <p>&nbsp;</p>
</body>

</html>