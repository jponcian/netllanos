<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";
$_SESSION['NOMBRE_MODULO'] = 'CAMBIO CONTRASE�A';

if ($_SESSION['VERIFICADO'] <> "SI") {
  header("Location: index.php?errorusuario=val");
  exit();
}

if ($_POST['CMDGUARDAR'] == "Guardar") {
  if ($_POST['OCLAVE1'] == "") {
    header("Location: menuprincipal.php?errorusuario=cv");
    exit();
  } else {
    // CONSULTA PARA ACTUALIZAR
    $consulta = "UPDATE z_empleados SET clave = '" . $_POST['OCLAVE1'] . "' WHERE (((cedula)=" . $_SESSION['CEDULA_USUARIO'] . "));";
    $tabla = mysql_query($consulta);
    //-----------------
    $_SESSION['VARIABLE'] = 'guardado';
    //-----------------
    header("Location: menuprincipal.php?errorusuario=si");
    exit();
  }
}
?>
<html>

<head>
  <title>Cambio de Clave</title>
  <style type="text/css">
    <!--
    .Estilomenun {
      font-family: Verdana, Arial, Helvetica, sans-serif;
      font-size: 12px;
      font-weight: bold;
    }

    body {
      background-color: #CCCCCC;
      background-image: url();
    }
    -->
  </style>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body style="background: transparent !important;">
  <p>
    <?php include "../titulo.php"; ?>
  </p>
  <table width=669 align=center border=0>
    <tbody>
      <tr>
        <td colSpan=5>
          <form name="form2" method="post" action="../">
            <div align="right"><input type="submit" class="boton" name="Submit2" value="Salir"></div>
          </form>
        </td>
      </tr>
      <tr>
        <td colSpan=5></td>
      </tr>
      <tr>
        <td colSpan=5>
          <FORM name="val" action="" method="post">
            <table width="60%" align=center border=0>
              <?php if ($_SESSION['VARIABLE'] <> 'guardado') { ?><tbody>
                  <tr>
                  <tr>
                    <td height=27><strong>Contrase&ntilde;a Nueva</strong></td>
                    <td><input name="OCLAVE1" type=password maxlength=25 size=30></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td colspan="2" class="Estilomenun"></td>
                  </tr>
                  <tr>
                    <td colSpan=2></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>
                      <DIV align=right>
                        <INPUT name="CMDGUARDAR" type=submit value="Guardar">

                      </DIV>
                    </td>
                  </tr>
                </tbody><?php } ?></table>
          </FORM>
        </td>
      </tr>
      <tr>
        <td colspan="5">
          <div align="center">
            <?php include "../msg_validacion.php"; ?>
          </div>
        </td>
      </tr>
      <tr>
        <td width=180>&nbsp;</td>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td colspan="5"><?php include "../pie.php"; ?></td>
      </tr>
    </tbody>
  </table>
  <p>&nbsp;</p>
</body>

</html>