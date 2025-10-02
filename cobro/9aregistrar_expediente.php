<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
  header("Location: index.php?errorusuario=val");
  exit();
}

$acceso = 101;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------
if ($_POST['OFECHA'] == '') {
  $_POST['OFECHA'] = date('d/m/Y');
}

if ($_POST['CMDGUARDAR'] == "Guardar") {
  if (
    $_POST['ORIF'] <> ""
    and $_POST['OSUPERVISOR'] > 1 and $_POST['OFISCAL'] > 1 and $_POST['OSEDE'] > 0
  ) {
    // CONSULTA DEL EXPEDIENTE SIGUIENTE
    $consulta_x = 'SELECT Max(numero)+1 AS Maximo FROM expedientes_cobro_siscontri WHERE sector=0' . $_POST['OSEDE'] . ' AND anno=' . date('Y') . ';';
    $tabla_x = mysql_query($consulta_x);
    $registro_x = mysql_fetch_array($tabla_x);
    //-------------
    if ($registro_x['Maximo'] > 0) {
      $Maximo = $registro_x['Maximo'];
    } else {
      $Maximo = 1;
    }
    // FIN

    // JEFE DIVISION, SECTOR O UNIDAD
    //		$consulta_x = "SELECT cedula FROM z_jefes_detalle WHERE id_sector=".$_POST['OSEDE']." AND (id_division=0 or id_division=8);";
    //		$tabla_x = mysql_query ( $consulta_x);
    //		$registro_x = mysql_fetch_object($tabla_x);
    //		$Jefe_Division = $registro_x->cedula;

    //VERIFICAMOS SI EXISTE UN EXPEDIENTE AL CONTRIBUYENTE EN ESE ESTATUS

    //    $consulta_existe = "SELECT Rif, Numero, Anno, Status FROM expedientes_cobro_siscontri WHERE Rif='" . strtoupper( $_POST[ 'ORIF' ] ) . "' and Anno=" . date( "Y" ) . " and Status=0";
    //    $tabla_existe = mysql_query( $consulta_existe );
    //    $cantidad = mysql_num_rows( $tabla_existe );
    $cantidad = 0;


    if ($cantidad < 1) {
      // GUARDADO DE LOS DATOS
      $consulta = "INSERT INTO expedientes_cobro_siscontri (fecha_registro, Numero, Anno, Rif, Coordinador, Funcionario, Usuario, Sector, Status) VALUES ('" . voltea_fecha($_POST['OFECHA']) . "', " . $Maximo . ", " . date("Y") . ", '" . strtoupper($_POST['ORIF']) . "', " . $_POST['OSUPERVISOR'] . ", " . $_POST['OFISCAL'] . ", " . $_SESSION['CEDULA_USUARIO'] . ", " . $_POST['OSEDE'] . ",0);";
      $tabla = mysql_query($consulta);
      // FIN
      echo "<script type=\"text/javascript\">alert('Expediente Creado bajo el Numero => " . $Maximo . "');</script>";
    } else {
      $valor = mysql_fetch_object($tabla_existe);
      echo "<script type=\"text/javascript\">alert('Existe un Expediente abierto para el contribuyente con el N�mero => " . $valor->Numero . " A�o => " . $valor->Anno . "');</script>";
    }
  } else {
    echo "<script type=\"text/javascript\">alert('���Existen Campos Vac�os!!!');</script>";
  }
}


?>
<html>

<head>
  <title>Crear Expediente Cobro Administrativo</title>
  <meta http-equiv="X-UA-Compatible" content="IE=9" />
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
  </script>
</head>

<body style="background: transparent !important;">
  <p>
    <?php include "../titulo.php"; ?>
  </p>
  </p>
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
          <td align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7"><u>Datos del Expediente - SISCONTRI - Cobranza</u></span></td>
        </tr>
        <td height="35" bgcolor="#CCCCCC">
          <div align="center"><strong>Dependencia</strong></div>
        </td>
        <td bgcolor="#FFFFFF">
          <div align="center">
            <label></label>
            <span class="Estilo1">
              <select name="OSEDE" size="1" onChange="this.form.submit()">
                <option value="-1">Seleccione</option>
                <?php
                if ($_SESSION['ADMINISTRADOR'] > 0 or $_SESSION['SEDE_USUARIO'] == 1) {
                  $consulta_x = 'SELECT * FROM z_sectores WHERE id_sector<=5;';
                  $tabla_x = mysql_query($consulta_x);
                  while ($registro_x = mysql_fetch_array($tabla_x)) {
                    echo '<option ';
                    if ($_POST['OSEDE'] == $registro_x['id_sector']) {
                      echo 'selected="selected" ';
                    }
                    echo ' value=' . $registro_x['id_sector'] . '>' . $registro_x['nombre'] . '</option>';
                  }
                } else {
                  $consulta_x = 'SELECT * FROM z_sectores WHERE id_sector=' . $_SESSION['SEDE_USUARIO'] . ';';
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
            </span>
          </div>
        </td>
        <td bgcolor="#CCCCCC" align="right"><strong>Numero:</strong></td>
        <td width="40"><label>
            <div align="center"><span class="Estilo15">
                <?php
                if ($_POST['OSEDE'] > 0) {
                  // CONSULTA DEL EXPEDIENTE SIGUIENTE
                  $consulta_x = 'SELECT Max(numero)+1 AS Maximo FROM expedientes_cobro_siscontri WHERE sector=0' . $_POST['OSEDE'] . ' AND anno=' . date('Y') . ';';
                  $tabla_x = mysql_query($consulta_x);
                  $registro_x = mysql_fetch_array($tabla_x);
                  //-------------
                  if ($registro_x['Maximo'] > 0) {
                    $Maximo = $registro_x['Maximo'];
                  } else {
                    $Maximo = 1;
                  }
                  // FIN
                  echo $Maximo;
                }
                ?>
              </span></div>
          </label></td>
        <td width="60" bgcolor="#CCCCCC" align="right"><strong> A&ntilde;o:</strong></td>
        <td width="50"><label>
            <div align="center"><span class="Estilo15"><?php echo date('Y'); ?></span></div>
          </label></td>
        <td bgcolor="#CCCCCC" align="right"><strong> Fecha:</strong></td>
        <td><label>
            <div align="center"><span class="Estilo15">
                <input type="text" onclick='javascript:scwShow(this,event);' name="OFECHA" value="<?php echo $_POST['OFECHA']; ?>" readonly>
              </span></div>
          </label></td>
      </table>
      <table width="50%" border="1" align="center">
        <tr>
          <td align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7"><u>Datos del Contribuyente o Sujeto Pasivo</u></span></td>
        </tr>
        <tr>
          <td width="15%" bgcolor="#CCCCCC"><strong>Rif: </strong></td>
          <td width="20%"><label>
              <input type="text" name="ORIF" id="ORIF" onKeyPress="return SoloRif(event)" size="12" maxlength="10" value="<?php echo $_POST['ORIF']; ?>">
              <input type="submit" class="boton" name="Submit" value="Buscar">
            </label></td>
          <td width="11%" bgcolor="#CCCCCC"><strong> Contribuyente:</strong></td>
          <td width="36%"><label><span class="Estilo15">
                <?php
                if ($_POST['ORIF'] <> "") {
                  list($contribuyente, $direccion) = funcion_contribuyente($_POST['ORIF']);
                  echo $contribuyente;
                }


                ?>
              </span></label></td>
        </tr>
        <tr>
          <td width="15%" bgcolor="#CCCCCC"><strong>Direcci&oacute;n:</strong></td>
          <td colspan="3"><label><span class="Estilo15"><?php echo $direccion;  ?> </span></label></td>
        </tr>
      </table>
      <table width="50%" border="1" align="center">
        <tr>
          <td align="center" bgcolor="#FF0000" colspan="9"><span class="Estilo7"><u>Datos de los Funcionarios</u></span></td>
        </tr>
        <tr>
          <td bgcolor="#CCCCCC"><strong>Coordinador:</strong></td>
          <td><label>
              <select name="OSUPERVISOR" size="1">
                <option value="-1">Seleccione</option>
                <?php
                //--------------------
                $consulta_x = "SELECT z_empleados.cedula, Apellidos, Nombres, modulo, z_accesos_roles.rol FROM z_empleados, z_empleados_roles, z_accesos_roles where z_empleados_roles.cedula = z_empleados.cedula AND z_empleados_roles.rol = z_accesos_roles.id AND z_empleados.cedula>1000000 AND z_accesos_roles.rol='Coordinador' and sector=" . $_POST['OSEDE'] . " AND modulo='COBRANZA';";
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
              <select name="OFISCAL" size="1">
                <option value="-1">Seleccione</option>
                <?php
                //--------------------
                $consulta_x = "SELECT z_empleados.cedula, Apellidos, Nombres, modulo, z_accesos_roles.rol FROM z_empleados, z_empleados_roles, z_accesos_roles where z_empleados_roles.cedula = z_empleados.cedula AND z_empleados_roles.rol = z_accesos_roles.id AND z_empleados.cedula>1000000 AND z_accesos_roles.rol='Funcionario' and sector=" . $_POST['OSEDE'] . " AND modulo='COBRANZA';";
                $tabla_x = mysql_query($consulta_x);
                while ($registro_x = mysql_fetch_object($tabla_x))
                //-------------
                {
                  echo '<option';
                  if ($_POST['OFISCAL'] == $registro_x->cedula) {
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
              ?> </label>
    </div>
  </form>
  <p>
    <?php include "../pie.php"; ?>
  </p>
  <p>&nbsp;</p>
</body>

</html>