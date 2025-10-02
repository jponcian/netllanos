<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <title>Documento sin título</title>
</head>

<body style="background: transparent !important;">
  <?php

  function compararFechas($primera, $segunda)
  {
    $valoresPrimera = explode("/", $primera);
    $valoresSegunda = explode("/", $segunda);
    $diaPrimera    = $valoresPrimera[0];
    $mesPrimera  = $valoresPrimera[1];
    $anyoPrimera   = $valoresPrimera[2];
    $diaSegunda   = $valoresSegunda[0];
    $mesSegunda = $valoresSegunda[1];
    $anyoSegunda  = $valoresSegunda[2];
    $diasPrimeraJuliano = gregoriantojd($mesPrimera, $diaPrimera, $anyoPrimera);
    $diasSegundaJuliano = gregoriantojd($mesSegunda, $diaSegunda, $anyoSegunda);
    if (!checkdate($mesPrimera, $diaPrimera, $anyoPrimera)) {
      // "La fecha ".$primera." no es válida";
      return 0;
    } elseif (!checkdate($mesSegunda, $diaSegunda, $anyoSegunda)) {
      // "La fecha ".$segunda." no es válida";
      return 0;
    } else {
      return  $diasPrimeraJuliano - $diasSegundaJuliano;
    }
  }
  $primera = date("d/m/Y");
  $segunda = "01/01/2017";

  $resultado = compararFechas($primera, $segunda);

  if ($resultado < 0) {
    //echo "Sistema cerrado";
    header('Location: fiscalizacion_mantenimiento.php');
  } else {
    //echo "Sistema abierto";
    header('Location: fiscalizacion_original.php');
  }

  ?>
</body>

</html>