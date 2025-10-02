<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

//-----------
if ($_SESSION['VERIFICADO'] != "SI") {
  header("Location: index.php?errorusuario=val");
  exit();
}
//------------------
?>
<html>

<head>
  <!--
<script language="javascript" src="../lib/jquery/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="../lib/select2/select2.min.css"/>
<script language="javascript" src="../lib/select2/select2.min.js"></script>
-->

  <meta http-equiv="X-UA-Compatible" content="IE=9" />
  <title>Incluir Sanci&oacute;n</title>
  <!--<link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />-->
  <!--<script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>-->
  <!--<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>-->

  <style type="text/css">
    <!--
    .Estilomenun {
      font-family: Verdana, Arial, Helvetica, sans-serif;
      font-size: 12px;
      font-weight: bold;
    }

    body {
      background-image: url();
    }

    .Estilo7 {
      font-size: 18px;
      font-weight: bold;
      color: #FFFFFF;
    }

    .Estilo15 {
      font-size: 14px;
    }

    .Estilo16 {
      font-size: 12px
    }
    -->
  </style>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body style="background: transparent !important;">
  <p>
    <?php include "../titulo.php"; ?>
    <?php
    //--------- BUSCAMOS LOS DATOS DE LA PROVIDENCIA
    $consulta_00 = "SELECT * FROM vista_exp_especiales WHERE anno=" . $_SESSION['ANNO_PRO'] . " AND numero=" . $_SESSION['NUM_PRO'] . " AND sector =" . $_SESSION['SEDE'] . ";";
    $tabla_00 = mysql_query($consulta_00);
    $registro_00 = mysql_fetch_object($tabla_00);
    //-----------
    $tipo = $registro_00->tipo;
    $rif = $registro_00->rif;

    //-----------
    if ($_POST['CMDGUARDAR'] == 'Guardar') {
      //-----------------
      include "../funciones/0_calculo_multas2.php";
      //-----------------
      if ($ut_aplicadas > 0) {
        //--- BUSCAR LA SANCION QUE NO ESTE REPETIDA
        include "../funciones/0_guardar_sancion_esp.php";
        //-----------
      }
    }
    ?>
  </p>
  <div align="center">
    <p align="center">
      <?php
      include "menu.php";
      ?>
  </div>
  </p>
  <form name="form1" method="post" action="#vista">
    <table width="60%" border="1" align="center">
      <tr>
        <td align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7"><u>Datos del Expediente </u></span></td>
      </tr>
      <tr>
        <td width="10%" bgcolor="#CCCCCC"><strong>A�o:</strong></td>
        <td width="10%"><label>
            <div align="center"><span class="Estilo15">
                <?php
                echo $registro_00->anno;
                ?>
              </span></div>
          </label></td>
        <td width="10%" bgcolor="#CCCCCC"><strong>N&uacute;mero:</strong></td>
        <td width="10%"><label>
            <div align="center"><span class="Estilo15"><?php echo $registro_00->numero; ?></span></div>
          </label></td>
        <td width="10%" bgcolor="#CCCCCC"><strong>Fecha:</strong></td>
        <td width="10%"><label>
            <div align="center"><span class="Estilo15"><?php echo voltea_fecha($registro_00->FechaRegistro); ?></span></div>
          </label></td>
        <td width="10%" bgcolor="#CCCCCC"><strong>Sector:</strong></td>
        <td><label>
            <div align="center"><span class="Estilo15"><?php echo strtoupper($registro_00->nombre); ?></span></div>
          </label></td>
      </tr>
    </table>

    <table width="60%" border="1" align="center">
      <tr>
        <td bgcolor="#CCCCCC"><strong>Rif: </strong></td>
        <td><label>
            <div align="center"><span class="Estilo15"><?php echo $registro_00->rif; ?></span></div>
          </label></td>
        <td bgcolor="#CCCCCC"><strong>Contribuyente:</strong></td>
        <td><label><span class="Estilo15"><?php echo $registro_00->contribuyente; ?></span></label></td>
      </tr>
      <tr>
        <td bgcolor="#CCCCCC"><strong>Cedula:</strong></td>
        <td><label>
            <div align="center"><span class="Estilo15"><?php echo $registro_00->coordinador; ?></span></div>
          </label></td>
        <td bgcolor="#CCCCCC"><strong>Supervisor:</strong></td>
        <td><label><span class="Estilo15"><?php echo $registro_00->nombrecoordinador; ?></span></label></td>
      </tr>
      <tr>
        <td bgcolor="#CCCCCC"><strong>Cedula:</strong></td>
        <td><label>
            <div align="center"><span class="Estilo15"><?php echo $registro_00->funcionario; ?></span></div>
          </label></td>
        <td bgcolor="#CCCCCC"><strong>Fiscal:</strong></td>
        <td><label><span class="Estilo15"><?php echo $registro_00->nombrefuncionario; ?></span></label></td>
      </tr>
    </table>
    <a name="vista"></a>
    <?php include "../funciones/0_cuadro_multas1.php"; ?>

    <p><?php $serie = "serie<>38 ";
        include "../funciones/0_sanciones_aplicadas.php"; ?>&nbsp;</p>
  </form>
  <p align="center"><a href="../ARCHIVOS/Sanciones.pdf" target="_blank">&lt; Haga click aqu&iacute; para Ver la hoja de Sanciones &gt;</a></p>
  <p>&nbsp; </p>
  <p>
    <?php include "../pie.php"; ?>
  </p>
  <p>&nbsp;</p>
</body>

</html>