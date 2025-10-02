<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Reporte de Resoluciones VDF Emitidas</title>
  <script src="jquery/jquery.js" type="text/javascript"></script>
  <script language="javascript" script type="text/javascript" src="../datetimepicker_css.js"></script>
  <script language="javascript" type="text/javascript">
    $(document).ready(function() {

      $("#cargar").click(function() {
        var inicio = $("#inicio").val();
        var fin = $("#fin").val();
        if (inicio != "" && fin != "") {
          var datos = $('#form1').serialize();
          $("#cargando").css("display", "inline");
          $("#detalle").load("reporte.php", datos, function() {
            $("#cargando").css("display", "none");
          });
        } else {
          alert("Existen datos requeridos vacios, por favor verifique...!!!");
        }
      });

    });
  </script>

  <style type="text/css">
    body,
    td,
    th {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 12px;
    }

    #form1 fieldset {
      background-color: #C7CFE2;
      font-weight: bold;
      font-size: 12px;
    }

    #form1 label {
      width: 120px;
      float: left;
      text-align: left;
    }

    #form1 input[type=text] {
      border: 1px solid #00C;
    }

    #form1 select {
      border: 1px solid #00C;
    }

    #form1 input.btn {
      padding: 3px;
      color: #FFFFFF;
      background-color: #990000;
      border: 1px solid #000000;
      border-radius: 5px;
      cursor: pointer;
      position: relative;
      bottom: 2px;
      right: 2px;
    }

    .btn {
      background-color: #666;
      /*shadow color*/
      color: inherit;
    }

    #detalle {
      overflow: auto;
      max-height: 420px;
      min-height: 100px;
      width: auto;
    }
  </style>
</head>

<body style="background: transparent !important;">
  <form id="form1" name="form1" method="post" action="">
    <div id="inputs">
      <fieldset id="cabecera">
        <legend><strong>Resoluciones VDF Emitidas</strong></legend>
        <label for="anno">Año Providencia: </label>
        <select name="anno" id="anno">
          <?php
          $vaño = date("Y");
          $i = $vaño - 5;
          while ($i <= $vaño) {
            echo '<option';
            if ($_POST["anno"] == $i) {
              echo ' selected="selected"';
            }
            printf(">%s</option>", $i);
            $i++;
          }
          ?>
        </select><br />
        <label for="sector">Sector/Unidad: </label>
        <select name="sector" id="sector">
          <option value="LLANOS">SEDE</option>
          <option value="SFA">SAN FERNANDO DE APURE</option>
          <option value="SJM">SAN JUAN DE LOS MORROS</option>
          <option value="VLP">VALLE DE LA PASCUA</option>
        </select><br />
        <label for="inicio">Fecha Inicio: </label>
        <input type="text" name="inicio" id="inicio" size="12" maxlength="10" readonly="readonly" /><a href="javascript:NewCssCal('inicio','YYYYMMDD')"><img src="images/calendario.png" width="16" height="16" border="0" alt="Click para selecionar..."></a><br />
        <label for="fin">Fecha Fin: </label>
        <input type="text" name="fin" id="fin" size="12" maxlength="10" readonly="readonly" /><a href="javascript:NewCssCal('fin','YYYYMMDD')"><img src="images/calendario.png" width="16" height="16" border="0" alt="Click para selecionar..."></a><br />
        <input name="cargar" id="cargar" class="btn" type="button" value="Cargar" />
      </fieldset>
    </div>
    <fieldset>
      <legend><strong>Detalles Resoluciones Emitidas</strong></legend>
      <div id="detalles" align="center">
        <div id="cargando" style="display:none; color: green;" align="center"><img src="images/290.gif" width="64" height="11" /></div>
        <div id="detalle"></div>
      </div>
    </fieldset>
  </form>
</body>

</html>