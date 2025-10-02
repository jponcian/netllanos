<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";
$_SESSION['NOMBRE_MODULO'] = 'CONTRIBUYENTE';

if ($_SESSION['VERIFICADO'] != "SI") {
  header("Location: index.php?errorusuario=val");
  exit();
}
//----------
?>
<html>

<head>
  <title>Men&uacute; Principal</title>
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
    -->
  </style>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="X-UA-Compatible" content="IE=9" />
  <?php if ($_GET['opcion'] == 'no') {
    echo "<script type=\"text/javascript\">alert('�No Posee Acceso a esta opci\u00D3n!');</script>";
  }
  if ($_GET['opcion'] == 'mant') {
    echo "<script type=\"text/javascript\">alert('�Opci�n en Mantenimiento!');</script>";
  } ?>
</head>

<body style="background: transparent !important;">
  <p>
    <?php include "../titulo.php"; ?>
  </p>
  <div align="center">
    <p align="center">
      <?php
      include "menu.php";
      ?>
  </div>
  <p align="center">&nbsp;</p>
  <p align="center">&nbsp;</p>

  <?php include "../pie.php"; ?>


  <p>&nbsp;</p>
</body>

</html>