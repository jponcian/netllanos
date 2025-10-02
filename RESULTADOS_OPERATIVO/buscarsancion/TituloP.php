<html>

<head>
  <title></title>
  <style type="text/css">
    <!--
    .Estilomenun {
      font-family: Verdana, Arial, Helvetica, sans-serif;
      font-size: 12px;
      font-weight: bold;
    }

    .Estilo3 {
      color: #0000FF;
      font-weight: bold;
    }

    .Estilo4 {
      color: #FFFFFF
    }

    .Estilo6 {
      color: #FFFFFF;
      font-weight: bold;
    }
    -->
  </style>
</head>

<body style="background: transparent !important;">
  <table width=669 align=center border=0>
    <tbody>
      <tr>
        <td width="460">&nbsp;</td>
      </tr>
      <tr>
        <td><img src="imagenes/header1.jpg" width="947" height="52"></td>
      </tr>
      <tr>
        <td></td>
      </tr>
      <tr>
        <td><img src="imagenes/logo.JPeG" alt="logo" width="213" height="75"></td>
      </tr>
      <tr>
        <td>
          <p><strong>
              <Marquee scrolldelay="150">Calabozo,
                <?php

                setlocale(LC_ALL, 'sp_ES', 'sp', 'es');

                //echo strftime('%d de %B de %Y', strtotime(date("m/d/Y"))); 

                $mes = array(Enero, Febrero, Marzo, Abril, Mayo, Junio, Julio, Agosto, Septiembre, Octubre, Noviembre, Diciembre);

                echo date("d") . " de " . ($mes[(date("m") - 1)]) . " de " . date("Y");

                ?>
              </Marquee>
            </strong></p>
        </td>
      </tr>
      <td bgcolor="#FF0000">
        <div align="center" class="Estilo3 Estilo4">GERENCIA REGIONAL DE TRIBUTOS INTERNOS</div>
      </td>
      </tr>
      <tr>
        <td bgcolor="#FF0000">
          <div align="center"><span class="Estilo6">REGION LOS LLANOS</span></div>
        </td>
      </tr>
      <tr>
        <td>
          <p><strong>
              <?php
              //
              if ($_SESSION['VERIFICADO']) {
                session_start();
                include "../conexion.php";
                include "../funciones/auxiliar_php.php";

                $consulta = 'SELECT * FROM Empleados WHERE (((Empleados.Cedula)=' . $_SESSION['VAR_USUARIO'] . '));';
                $tabla = odbc_exec($_SESSION['conexion'], $consulta);
                if ($registro = odbc_fetch_object($tabla)) {
                  echo 'Usuario: ' . $registro->Nombres . ' ' . $registro->Apellidos;
                }
              }

              ?>
            </strong></p>
        </td>
      </tr>
    </tbody>
  </table>


</body>

</html>