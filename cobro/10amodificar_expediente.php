<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
  header("Location: index.php?errorusuario=val");
  exit();
}

$acceso = 102;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

if ($_POST['CMDGUARDAR'] == "Guardar") {
  $guardar = 0;
  if (
    $_POST['ORIF'] <> ""
    and $_POST['OSUPERVISOR'] > 1 and $_POST['OFUNCIONARIO'] > 1
  ) {
    // GUARDADO DE LOS DATOS
    $consulta = "UPDATE expedientes_cobro_siscontri SET fecha_registro='" . voltea_fecha($_POST['OFECHA']) . "', rif='" . $_POST['ORIF'] . "', Coordinador=" . $_POST['OSUPERVISOR'] . ", Funcionario=" . $_POST['OFUNCIONARIO'] . ", Usuario=" . $_SESSION['CEDULA_USUARIO'] . " WHERE sector=" . $_POST['OSEDE'] . " and Numero=" . $_POST['ONUMERO'] . " and Anno=" . $_POST['OANNO'];
    //echo $consulta;
    $tabla = mysql_query($consulta);
    // FIN
    $_POST['OSEDE'] = '-1';
    $_POST['OANNO'] = '-1';
    $_POST['ONUMERO'] = '-1';
    $_POST['ORIF'] = '';
    $_POST['OSUPERVISOR'] = '';
    $_POST['OFUNCIONARIO'] = '';
    $_POST['OFECHA'] = '';
    //--------------
    echo "<script type=\"text/javascript\">alert('Expediente Modificado Exitosamente!!!');</script>";
  } else {
    echo "<script type=\"text/javascript\">alert('���Existen Campos Vac�os!!!');</script>";
  }
}

//------ BUSCAR LA INFORMACION
if ($_POST['ONUMERO'] > 0 and $_POST['ONUMERO_ANTERIOR'] <> $_POST['ONUMERO']) {
  $consulta_x = 'SELECT numero, anno, rif, coordinador, funcionario, fecha_registro FROM vista_exp_cobro_siscontri WHERE sector=0' . $_POST['OSEDE'] . ' AND anno=' . $_POST['OANNO'] . ' AND numero=' . $_POST['ONUMERO'] . ';';
  $tabla_x = mysql_query($consulta_x);
  $registro_x = mysql_fetch_array($tabla_x);
  //---------
  $_POST['ORIF'] = $registro_x['rif'];
  $_POST['OSUPERVISOR'] = $registro_x['coordinador'];
  $_POST['OFUNCIONARIO'] = $registro_x['funcionario'];
  $_POST['OFECHA'] = voltea_fecha($registro_x['fecha_registro']);
  $_POST['OESPECIAL'] = $registro_x['Especial'];
  $_POST['OPORTADANUM'] = $registro_x['numero'];
  $_POST['OPORTADANNO'] = $registro_x['anno'];
}
?>
<html>
<title>Modificar Expediente Cobro Administrativo</title>
<meta http-equiv="X-UA-Compatible" content="IE=9" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
</script>
</head>


<body style="background: transparent !important;">
  <p>
    <?php include "../titulo.php"; ?>
  </p>
  <p>
  <div align="center">
    <p align="center">
      <?php
      include "menu.php";
      ?>
  </div>
  </p>
  <form name="form1" method="post" action="">
    <div align="center">
      <table width="50%" border="1" align="center">
        <tr>
          <td height="37" colspan="9" align="center" bgcolor="#FF0000"><span class="Estilo7"><u>Datos del Expediente - SISCONTRI - Cobranza</u></span></td>
        </tr>
        <td width="87" height="35" bgcolor="#CCCCCC">
          <div align="center"><strong>Dependencia</strong></div>
        </td>
        <td width="145" bgcolor="#FFFFFF">
          <div align="center">
            <label></label>
            <span class="Estilo1">
              <select name="OSEDE" size="1" onChange="this.form.submit()">
                <option value="-1">--> Seleccione <--< /option>
                    <?php
                    if ($_SESSION['ADMINISTRADOR'] > 0) {
                      $consulta_x = 'SELECT Sector, nombre FROM vista_exp_cobro_siscontri GROUP BY sector;';
                      $tabla_x = mysql_query($consulta_x);
                      while ($registro_x = mysql_fetch_array($tabla_x)) {
                        echo '<option ';
                        if ($_POST['OSEDE'] == $registro_x['sector']) {
                          echo 'selected="selected" ';
                        }
                        echo ' value=' . $registro_x['sector'] . '>' . $registro_x['nombre'] . '</option>';
                      }
                    } else {
                      $consulta_x = 'SELECT Sector, nombre FROM vista_exp_cobro_siscontri WHERE sector=' . $_SESSION['SEDE_USUARIO'] . ' GROUP BY sector;';
                      $tabla_x = mysql_query($consulta_x);
                      while ($registro_x = mysql_fetch_array($tabla_x)) {
                        echo '<option ';
                        if ($_POST['OSEDE'] == $registro_x['sector']) {
                          echo 'selected="selected" ';
                        }
                        echo ' value=' . $registro_x['sector'] . '>' . $registro_x['nombre'] . '</option>';
                      }
                    }
                    ?>
              </select>
            </span>
          </div>
        </td>
        <td width="98" bgcolor="#CCCCCC"><strong>A&ntilde;o:</strong></td>
        <td width="98"><label>
            <div align="center"><span class="Estilo1">
                <select name="OANNO" size="1" onChange="this.form.submit()">
                  <option value="-1">--> Seleccione <--< /option>
                      <?php
                      if ($_POST['OSEDE'] > 0) {
                        $consulta_x = 'SELECT anno FROM vista_exp_cobro_siscontri WHERE sector =0' . $_POST['OSEDE'] . ' GROUP BY anno ORDER BY anno DESC;';
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
          </label></td>
        <td width="97" bgcolor="#CCCCCC"><strong> Numero:</strong></td>
        <td width="97"><label>
            <div align="center"><span class="Estilo1">
                <select name="ONUMERO" size="1" onChange="this.form.submit()">
                  <option value="-1">--> Seleccione <--< /option>
                      <?php
                      if ($_POST['OANNO'] > 0) {
                        $consulta_x = 'SELECT numero FROM vista_exp_cobro_siscontri WHERE sector=' . $_SESSION['SEDE_USUARIO'] . ' AND anno=' . $_POST['OANNO'] . ' ORDER BY numero DESC;';
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
            <input type="hidden" name="ONUMERO_ANTERIOR" value="<?php echo $_POST['ONUMERO']; ?>"></td>
      </table>
      <p>
        <?php if ($_POST['ONUMERO'] > 0) { ?>
      </p>
      <p></p>
      <table width="60%" border="1" align="center">
        <tr>
          <td height="37" colspan="9" align="center" bgcolor="#FF0000"><span class="Estilo7"><u>Datos del Contribuyente o Sujeto Pasivo</u></span></td>
        <tr>
          <td width="15%" bgcolor="#CCCCCC"><strong>Rif: </strong></td>
          <td width="28%"><label>
              <input type="text" name="ORIF" id="ORIF" onKeyPress="return SoloRif(event)" size="12" maxlength="10" value="<?php echo $_POST['ORIF']; ?>">
              <input type="submit" class="boton" name="Submit" value="Buscar">
            </label></td>
          <td width="21%" bgcolor="#CCCCCC"><strong> Contribuyente:</strong></td>
          <td width="36%"><label><span class="Estilo15">
                <?php
                if ($_POST['ORIF'] <> "") {
                  // BUSQUEDA DEL CONTRIBUYENTE
                  $consulta_x = "SELECT * FROM vista_contribuyentes_direccion WHERE rif='" . $_POST['ORIF'] . "';";
                  $tabla_x = mysql_query($consulta_x);
                  $registro_x = mysql_fetch_object($tabla_x);
                  // FIN
                  echo $registro_x->contribuyente;
                  $_POST['OESPECIAL'] = $registro_x->Especial;
                }
                ?>
              </span></label></td>
          <td width="97" bgcolor="#CCCCCC"><strong> Fecha:</strong></td>
          <td width="56"><label>
              <div align="center"><span class="Estilo15">
                  <input type="text" onclick='javascript:scwShow(this,event);' name="OFECHA" value="<?php echo $_POST['OFECHA']; ?>" readonly>
                </span></div>
            </label></td>
        </tr>
        <tr>
          <td width="15%" bgcolor="#CCCCCC"><strong>Direcci&oacute;n:</strong></td>
          <td colspan="5"><label><span class="Estilo15"><?php echo $registro_x->direccion;  ?> </span></label></td>
        </tr>
      </table>
      <table width="60%" border="1" align="center">
        <tr>
          <td height="37" colspan="9" align="center" bgcolor="#FF0000"><span class="Estilo7"><u>Datos de los Funcionarios</u></span></td>
        </tr>
        <tr>
          <td bgcolor="#CCCCCC"><strong>Coordinador:</strong></td>
          <td><label>
              <select name="OSUPERVISOR" size="1">
                <option value="-1">Seleccione</option>
                <?php
                //--------------------
                /*
              $consulta_x = "SELECT cedula, Nombres, Apellidos FROM z_empleados WHERE ((Rol='S' or Rol='C') and id_origen=13) or ((Rol2='S' or Rol2='C') and id_origen2=13) or ((Rol3='S' or Rol3='C') and id_origen3=13) OR CEDULA=20908897 AND cedula>1000000 and sector=".$_POST['OSEDE'].";"; 
              $tabla_x = mysql_query ($consulta_x);
              while ($registro_x = mysql_fetch_object($tabla_x))
              */
                $consulta_x = "SELECT z_empleados.cedula, Apellidos, Nombres, modulo, z_accesos_roles.rol FROM z_empleados, z_empleados_roles, z_accesos_roles where z_empleados_roles.cedula = z_empleados.cedula AND z_empleados_roles.rol = z_accesos_roles.id AND z_empleados.cedula>1000000 AND z_accesos_roles.rol='Coordinador' and sector=" . $_SESSION['SEDE_USUARIO'] . " AND modulo='" . strtoupper($_SESSION['NOMBRE_MODULO']) . "';";
                $tabla_x = mysql_query($consulta_x);
                while ($registro_x = mysql_fetch_object($tabla_x))
                //-------------
                {
                  echo '<option';
                  if ($_POST['OSUPERVISOR'] == $registro_x->cedula) {
                    echo ' selected="selected" ';
                  }
                  echo ' value="';
                  echo $registro_x->cedula;
                  echo '">';
                  echo $registro_x->cedula . " - " . $registro_x->Nombres . " " . $registro_x->Apellidos;
                  echo '</option>';
                }
                ?>
              </select>
            </label></td>
        </tr>
        <tr>
          <td bgcolor="#CCCCCC"><strong>Funcionario:</strong></td>
          <td><label>
              <select name="OFUNCIONARIO" size="1">
                <option value="-1">Seleccione</option>
                <?php
                //--------------------
                /*
              $consulta_x = "SELECT cedula, Nombres, Apellidos FROM z_empleados WHERE ((Rol='F') and id_origen=13) or ((Rol2='F') and id_origen2=13) or ((Rol3='F') and id_origen3=13) AND cedula>1000000 and sector=".$_POST['OSEDE'].";"; 
              $tabla_x = mysql_query ($consulta_x);
              while ($registro_x = mysql_fetch_object($tabla_x))
              */
                $consulta_x = "SELECT z_empleados.cedula, Apellidos, Nombres, modulo, z_accesos_roles.rol FROM z_empleados, z_empleados_roles, z_accesos_roles where z_empleados_roles.cedula = z_empleados.cedula AND z_empleados_roles.rol = z_accesos_roles.id AND z_empleados.cedula>1000000 AND z_accesos_roles.rol='Funcionario' and sector=" . $_SESSION['SEDE_USUARIO'] . " AND modulo='" . strtoupper($_SESSION['NOMBRE_MODULO']) . "';";
                $tabla_x = mysql_query($consulta_x);
                while ($registro_x = mysql_fetch_object($tabla_x))
                //-------------
                {
                  echo '<option';
                  if ($_POST['OFUNCIONARIO'] == $registro_x->cedula) {
                    echo ' selected="selected" ';
                  }
                  echo ' value="';
                  echo $registro_x->cedula;
                  echo '">';
                  echo $registro_x->cedula . " - " . $registro_x->Nombres . " " . $registro_x->Apellidos;
                  echo '</option>';
                }
                ?>
              </select>
            </label></td>
        </tr>
      </table>
      </p>
      <label> <?php echo '<input type="submit" class="boton" name="CMDGUARDAR" value="Guardar">';
              ?>
      <?php
        }
      ?>
      </label>
    </div>
  </form>
  <div align="center">
    <?php
    if ($_POST['ONUMERO'] > 0) {
      //------------------
      echo '<form name="form2" method="post" action="formatos/portadaSIS.php?num=' . $_POST['ONUMERO'] . '&anno=' . $_POST['OANNO'] . '&sede=0' . $_POST['OSEDE'] . '" target="_blank">';
      echo '<input type="submit" class="boton" name="CMDPORTADA" value="Ver Hoja de Portada"></form>	';
      //------------------
      $_SESSION['SEDE'] = $_POST['OSEDE'];
      $_SESSION['ORIGEN'] = 13;
      $_SESSION['ANNO_PRO'] = $_POST['OANNO'];
      $_SESSION['NUM_PRO'] = $_POST['ONUMERO'];
      //---------------
      echo '<form name="form2" method="post" action="formatos/autorizacionSIS.php" target="_blank">';
      echo '<input type="submit" class="boton" name="CMDAUTORIZACION" value="Ver Autorizaci&oacute;n"></form>	';
      // FIN
    }
    ?>
  </div>
  <p>
    <?php include "../pie.php"; ?>
  </p>
  <p>&nbsp;</p>
</body>

</html>