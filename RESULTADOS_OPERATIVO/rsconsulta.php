<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Documento sin título</title>
  <script language="javascript" script type="text/javascript" src="datetimepicker_css.js"></script>
  <script type="text/javascript" src="jquery/jquery.js"></script>
  <script type="text/javascript" src="jquery_ui/js/jquery-ui-1.10.4.custom.js"></script>
  <link rel="stylesheet" href="jquery_ui/css/jquery-ui-1.10.4.custom.min.css">

  <script type="text/javascript">
    $(document).ready(function() {
      //alert("Done");
      $('#ConsultaI').datepicker();
      $('#ConsultaF').datepicker();
    });
  </script>
  <script language="JavaScript">
    function pregunta() {
      if (confirm('¿Estas seguro de enviar este formulario?')) {
        document.ListadoAnexo2.submit();
      }
    }

    function Valida(formulario) {
      if (document.ListadoAnexo2.ConsultaI.value != "" && document.ListadoAnexo2.ConsultaF.value != "") {
        return true
      } else {
        alert("Existen datos vacios, por favor verifique");
        return false
      }
    }
  </script>
</head>

<body style="background: transparent !important;">
  <form method="get" name="ListadoAnexo2" action="rptanexo2.php" target="ReporteListado" onSubmit="return Valida(this);">
    <p></p>
    <table width="489" border="1" cellpadding="0" cellspacing="0" bordercolor="#8B0000" bgcolor="#E6E6FA" align="center" style="font:Arial, Helvetica, sans-serif; font-size:12px; font-weight: bold;">
      <tr>
        <td bgcolor="#FF0000" colspan="2" align="center" style="font-weight: bold; font-size: 16px; color: #FFF;" height="60">Consulta Resultado de Operativos</td>
      </tr>
      <tr>
        <td width="338" height="40">Ingrese fecha inicio del periodo a Consultar:</td>
        <td width="145" height="40" align="center"><input name="ConsultaI" id="ConsultaI" readonly="readonly" type="text" size="10" maxlength="10" />
        </td>
      </tr>
      <tr>
        <td height="40">Ingrese fecha final del periodo a Consultar:</td>
        <td height="40" align="center"><input name="ConsultaF" id="ConsultaF" readonly="readonly" type="text" size="10" maxlength="10" />
        </td>
      </tr>
    </table>
    <table width="489" border="1" cellpadding="0" cellspacing="0" bordercolor="#8B0000" bgcolor="#E6E6FA" align="center" style="font:Arial, Helvetica, sans-serif; font-size:12px; font-weight: bold;">
      <tr>
        <td align="center" bgcolor="#FF0000" height="40">
          <input onmouseover=this.style.cursor="hand" type="submit" name="enviar" value="Cargar Consulta">
        </td>
      </tr>
    </table>
  </form>
</body>

</html>