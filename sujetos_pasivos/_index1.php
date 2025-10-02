<?php
session_start();
// ----------------
$_SESSION['CEDULA_USUARIO'] = '-1';
$_SESSION['VAR_CLAVE'] = '-1';
?>

<html>

<head>
  <title>Sujetos Pasivos Especiales</title>
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
  <table width=60% align=center border=0>
    <tbody>
      <tr>
        <td colSpan=5>
          <div align="right">
            <form name="form2" method="post" action="../">
              <input type="submit" class="boton" name="Submit2" value="HOME">
            </form>
          </div>
        </td>
      </tr>
      <tr>
        <td colSpan=5></td>
      </tr>
      <tr>
        <td colSpan=5>
          <FORM name="val" action="valida.php" method="post">
            <table width="53%" height="129" border=0 align=center>
              <tbody>
                <tr>
                <tr>
                  <td height=27><strong>Cedula</strong></td>
                  <td><input name="OUSUARIO" type=text maxlength=25 size=30></td>
                </tr>
                <tr>
                  <td><strong>Contrase&ntilde;a</strong></td>
                  <td><input name="OCLAVE" type=password maxlength=25 size=30></td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td colspan="2" class="Estilomenun">
                    <div align="center">
                      <?php include "../msg_validacion.php"; ?>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td colSpan=2></td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td>
                    <DIV align=right><INPUT name=Submit type=submit value=Entrar class="btn btn-danger">
                    </DIV>
                  </td>
                </tr>
              </tbody>
            </table>
          </FORM>
        </td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colSpan=2>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td width=180>&nbsp;</td>
        <td width=200>&nbsp;</td>
        <td colSpan=2>&nbsp;</td>
        <td width=80>&nbsp;</td>
      </tr>
      <tr>

        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colSpan=2>&nbsp;</td>
        <td class="Estilomenu">&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colSpan=2>&nbsp;</td>
        <td>
      </tr>
      <tr>
        <td colspan="5"><?php include "../pie.php"; ?></td>
      </tr>
    </tbody>
  </table>
  <p>&nbsp;</p>
</body>

</html>