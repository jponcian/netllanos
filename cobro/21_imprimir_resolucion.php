<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
  header("Location: index.php?errorusuario=val");
  exit();
}

$acceso = 106;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

if ($_POST['ONUMERO'] > 0) {
  $_SESSION['ANNO_PRO'] = $_POST['OANNO'];
  $_SESSION['NUM_PRO'] = $_POST['ONUMERO'];
  $_SESSION['SEDE'] = $_POST['OSEDE'];
}

?>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="X-UA-Compatible" content="IE=9" />
  <title>Imprimir Resoluci&oacute;n</title>
  <!--<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>	</script>-->
  <!--<link rel="stylesheet" type="text/css" href="../estilos/estilos.css">-->
</head>

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
  -->
</style>

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
  <form name="form1" method="post" action="#vista">
    <table width="47%" border="1" align="center">
      <tr>
        <td height="35" align="center" bgcolor="#FF0000" colspan="6"><span class="Estilo7"><u>Selecci&oacute;n del Expediente - Emitir Resoluci&oacute;n </u></span></td>
      </tr>
      <tr>
        <td bgcolor="#CCCCCC">
          <div align="center"><strong>Dependencia:</strong></div>
        </td>
        <td bgcolor="#FFFFFF">
          <div align="center"><span class="Estilo1">
              <select name="OSEDE" id="OSEDE" size="1" onChange="cargar_combo(1,this.value);">
                <option value="-1">--> Seleccione <--< /option>
                    <?php
                    if ($_SESSION['ADMINISTRADOR'] > 0 or $_SESSION['SEDE_USUARIO'] == 1) {
                      $consulta_x = 'SELECT sector as id_sector, nombre FROM vista_exp_cobro where status>=6 GROUP BY sector;';
                      $tabla_x = mysql_query($consulta_x);
                      while ($registro_x = mysql_fetch_array($tabla_x)) {
                        echo '<option ';
                        if ($_POST['OSEDE'] == $registro_x['id_sector']) {
                          echo 'selected="selected" ';
                        }
                        echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
                      }
                    } else {
                      $consulta_x = 'SELECT sector as id_sector, nombre FROM vista_exp_cobro WHERE status>=6 and sector=' . $_SESSION['SEDE_USUARIO'] . ' GROUP BY sector;';
                      $tabla_x = mysql_query($consulta_x);
                      while ($registro_x = mysql_fetch_array($tabla_x)) {
                        echo '<option ';
                        if ($_POST['OSEDE'] == $registro_x['id_sector']) {
                          echo 'selected="selected" ';
                        }
                        echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
                      }
                    }
                    ?>
              </select>
            </span></div>
        </td>
        <td bgcolor="#CCCCCC">
          <div align="center"><strong>A&ntilde;o:</strong></div>
        </td>
        <td bgcolor="#FFFFFF">
          <div align="center"><span class="Estilo1">
              <select name="OANNO" id="OANNO" size="1" onChange="cargar_combo2(2,this.value);">
                <option value="-1">Seleccione</option>
                <?php
                if ($_POST['OSEDE'] > 0) {
                  $consulta_x = 'SELECT anno FROM vista_exp_cobro WHERE status>=6 and sector=0' . $_POST['OSEDE'] . ' GROUP BY anno ORDER BY anno DESC;';
                  $tabla_x = mysql_query($consulta_x);
                  while ($registro_x = mysql_fetch_array($tabla_x)) {
                    echo '<option ';
                    if ($_POST['OANNO'] == $registro_x['anno']) {
                      echo 'selected="selected" ';
                    }
                    echo ' value=' . $registro_x['anno'] . '>' . $registro_x['anno'] . '</option>';
                  }
                }
                ?>
              </select>
            </span></div>
        </td>
        <td bgcolor="#CCCCCC">
          <div align="center"><strong>Numero:</strong></div>
        </td>
        <td><label>
            <div align="center"><span class="Estilo1">
                <select name="ONUMERO" id="ONUMERO" size="1" onChange="this.form.submit()">
                  <option value="-1">Seleccione</option>
                  <?php
                  if ($_POST['OANNO'] > 0) {
                    $consulta_x = 'SELECT numero FROM vista_exp_cobro WHERE status>=6 and anno=' . $_POST['OANNO'] . ' AND sector=0' . $_POST['OSEDE'] . ' ORDER BY numero DESC;';
                    $tabla_x = mysql_query($consulta_x);
                    while ($registro_x = mysql_fetch_array($tabla_x)) {
                      echo '<option ';
                      if ($_POST['ONUMERO'] == $registro_x['numero']) {
                        echo 'selected="selected" ';
                      }
                      echo ' value=' . $registro_x['numero'] . '>' . $registro_x['numero'] . '</option>';
                    }
                  }
                  ?>
                </select>
              </span></div>
          </label></td>
      </tr>
      <tr>
        <td colspan="6" align="center">
          <p>
            <?php include "../msg_validacion.php"; ?>
          </p>
        </td>
      </tr>
    </table>
    <p></p>
    <?php
    if ($_POST['ONUMERO'] > 0) {
    ?>
      <table width="60%" border="1" align="center">
        <tr>
          <td height="36" colspan="8" align="center" bgcolor="#FF0000"><span class="Estilo7"><u>Datos del Expediente </u></span></td>
        </tr>
        <tr>
          <td bgcolor="#CCCCCC">
            <div align="center"><strong>A�o:</strong></div>
          </td>
          <td><label>
              <div align="center"><span class="Estilo15">
                  <?php
                  $consulta = "SELECT * FROM vista_exp_cobro WHERE anno=0" . $_SESSION['ANNO_PRO'] . " AND numero=0" . $_SESSION['NUM_PRO'] . " AND sector =0" . $_SESSION['SEDE'] . ";";
                  $tabla = mysql_query($consulta);
                  $registro = mysql_fetch_object($tabla);
                  //----------
                  echo $registro->anno;
                  $tipo = $registro->tipo;
                  ?>
                </span>
            </label>
            </div>
          </td>
          <td bgcolor="#CCCCCC">
            <div align="center"><strong>N&uacute;mero:</strong></div>
          </td>
          <td><label>
              <div align="center"><span class="Estilo15"><?php echo $registro->numero; ?></span>
            </label>
            </div>
          </td>
          <td bgcolor="#CCCCCC">
            <div align="center"><strong>Fecha:</strong></div>
          </td>
          <td><label>
              <div align="center"><span class="Estilo15"><?php echo voltea_fecha($registro->fecha_registro); ?></span>
            </label>
            </div>
          </td>
          <td bgcolor="#CCCCCC">
            <div align="center"><strong>Sector:</strong></div>
          </td>
          <td><label>
              <div align="center"><span class="Estilo15"><?php echo strtoupper($registro->nombre); ?></span>
            </label>
            </div>
          </td>
        </tr>
      </table>
      <table width="60%" border="1" align="center">
        <tr>
          <td bgcolor="#CCCCCC"><strong>Rif:</strong></td>
          <td><label>
              <div align="center"><span class="Estilo15"><?php echo formato_rif($registro->rif); ?></span></div>
            </label></td>
          <td bgcolor="#CCCCCC"><strong>Contribuyente:</strong></td>
          <td><label><span class="Estilo15"><?php echo $registro->contribuyente; ?></span></label></td>
        </tr>
        <tr>
          <td bgcolor="#CCCCCC"><strong>Cedula:</strong></td>
          <td><label>
              <div align="center"><span class="Estilo15"><?php echo formato_cedula($registro->coordinador); ?></span></div>
            </label></td>
          <td bgcolor="#CCCCCC"><strong>Supervisor:</strong></td>
          <td><label><span class="Estilo15"><?php echo $registro->nombrecoordinador; ?></span></label></td>
        </tr>
        <tr>
          <td bgcolor="#CCCCCC"><strong>Cedula:</strong></td>
          <td><label>
              <div align="center"><span class="Estilo15"><?php echo formato_cedula($registro->funcionario); ?></span></div>
            </label></td>
          <td bgcolor="#CCCCCC"><strong>Fiscal:</strong></td>
          <td><label><span class="Estilo15"><?php echo $registro->nombrefuncionario; ?></span></label></td>
        </tr>
      </table>
      <p><a name="vista"></a></p>
      <p>
        <?php
        $mostrarboton = 'NO';
        $serie = "1=1";
        include "../funciones/0_sanciones_aplicadas.php";
        ?>
      </p>
      <p> </p>
      <p> </p>
  </form>
  <table align="center">
    <tr>
      <td><a href="formatos/resolucion.php?tamano=LETTER" target="_blank"><i class="fa-regular fa-file-pdf"></i> Resolucion (Carta)</td>
      <td><a href="formatos/resolucion.php?tamano=oficio" target="_blank"><i class="fa-regular fa-file-pdf"></i> Resolucion (Oficio)</a></td>
    </tr>
  </table>
<?php
      //print_r($_SESSION);
    }
?>
<p>&nbsp;</p>
<?php include "../pie.php"; ?>
<p>&nbsp;</p>
</body>

</html>
<script language="JavaScript">
  //-------------------------------------------- 
  <?php
  //if ($i==1) 	{	echo 'Swal.fire({ title: "Correcto!",  text: "Bien Nacional Reasignado!",  icon: "success" }); '; }
  if ($g == 1) {
    echo 'Swal.fire({ title: "Movimiento Realizado!",  icon: "success" }); ';
  }
  if ($g > 1) {
    echo 'Swal.fire({ title: "Movimientos Realizados!",  icon: "success" }); ';
  }
  if ($f > 0) {
    echo 'Swal.fire({ title: "Bien Devuelto Exitosamente!", icon: "success" }); ';
  }
  ?>
  //-------------------------------------------- 
  function cargar_tabla2() {
    alert("si");
    $('#div2').load('0_bienes_reasignados.php?sede1=' + (document.form1.OSEDE.value) + '&div1=' + document.form1.ODIVISION.value + '&area1=' + document.form1.OAREA.value);
  }
  //-------------------------------------------- 
  function cargar_combo(tipo, val) {
    $.ajax({
      type: "POST",
      url: '21_combo.php?sede=' + document.form1.OSEDE.value, //---+document.form1.OSEDE.value
      data: 'tipo=' + tipo,
      success: function(resp) {
        $('#OANNO').html(resp);
      }
    });
    alertify.message("Por favor espere la carga de datos...");
  }
  //-------------------------------------------- 
  function cargar_combo2(tipo, val) {
    $.ajax({
      type: "POST",
      url: '21_combo.php?sede=' + document.form1.OSEDE.value + '&anno=' + document.form1.OANNO.value,
      data: 'tipo=' + tipo,
      success: function(resp) {
        $('#ONUMERO').html(resp);
      }
    });
    alertify.message("Por favor espere la carga de datos...");
  }
  //--------------------------------------------
</script>