<?php

session_start();
if (phpversion() == "4.1.10") {
  //session_register("VAR_USUARIO");
  //session_register("VAR_CLAVE");
  //session_register("VERIFICADO");
  //session_register("SEDE_USUARIO");
  //session_register("ADMINISTRADOR");
  //session_register("VALOR_UT_ACTUAL");
  //session_register("VALOR_UT_PRIMITIVA");

  //session_register("JEFE");
  //session_register("COORDINADOR");
  //session_register("FISCAL");
  //session_register("PROVIDENCIA");
}
?>

<html>

<head>
  <title>Validacion de Usuarios</title>
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
            <table width="60%" align=center border=0>
              <tbody>
                <tr>
                <tr>
                  <td height=27><strong>Usuario</strong></td>
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
                    <DIV align=right><INPUT name=Submit type=submit value=Entrar class="boton">
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