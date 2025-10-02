<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";
$_SESSION['NOMBRE_MODULO'] = 'INFORMATICA';

if ($_SESSION['VERIFICADO'] != "SI") {
  header("Location: index.php?errorusuario=val");
  exit();
}
//----------
?>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="X-UA-Compatible" content="IE=9" />
  <?php if ($_GET['opcion'] == 'no') {
    echo "<script type=\"text/javascript\">alert('�No Posee Acceso a esta opcion!');</script>";
  }
  if ($_GET['opcion'] == 'mant') {
    echo "<script type=\"text/javascript\">alert('�Opcion en Mantenimiento!');</script>";
  } ?>
  <title>Men&uacute; Principal</title>
  <link rel="stylesheet" type="text/css" href="../estilos/estilos.css" />
</head>

<body style="background-color:#C0C0C0;">
  <p>
    <?php include "../titulo.php"; ?>
  </p>
  <div align="center">
    <p align="center">
      <?php
      include "menu.php";
      ?>
  </div>
  <?php include "../logo_central.php"; ?>

  <?php include "../pie.php"; ?>

  <p>&nbsp;</p>
</body>

</html>