<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

if ($_SESSION['VERIFICADO'] != "SI") {
  header("Location: index.php?errorusuario=val");
  exit();
}

$acceso = 112;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------	
$f = 0;
if ($_POST['CMDGUARDAR'] == 'Guardar') {
  if ($_POST['OBIEN'] > 0) {
    //-------- 
    $consulta = "SELECT * FROM bn_bienes WHERE borrado=0 AND numero_bien = " . $_POST['OBIEN'] . " LIMIT 1";
  }
  //---------------
  $tabla = mysql_query($consulta);
  while ($registro = mysql_fetch_object($tabla)) {
    //PARA GUARDAR EL BIEN NACIONAL
    $consultax = "UPDATE bn_bienes SET serial='" . ($_POST['OSERIAL']) . "', modelo='" . ($_POST['OMODELO']) . "', marca='" . ($_POST['OMARCA']) . "', color='" . ($_POST['OCOLOR']) . "', fecha='" . voltea_fecha($_POST['OFECHA']) . "', factura='" . $_POST['OFACTURA'] . "', condicion='" . $_POST['OCONDICION'] . "', conservacion='" . $_POST['OESTADO'] . "', valor='" . $_POST['OVALOR'] . "', numero_bien='" . $_POST['ONUMERO'] . "', descripcion_bien='" . $_POST['ODESCRIPCION1'] . "', id_categoria='" . $_POST['OCATEGORIA'] . "', usuario=" . $_SESSION['CEDULA_USUARIO'] . " WHERE id_bien=" . $registro->id_bien . ";";
    //    echo $consultax;
    $tablax = mysql_query($consultax);
    $f++;
  }
  // MENSAJE DE GUARDADO
  //  echo "<script type=\"text/javascript\">alert('����BIENES ACTUALIZADOS EXITOSAMENTE!!!!');</script>";
}
?>
<html>

<head>
  <title>Modificar Bien Nacional</title>
  <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=Edge" />

</head>

<body style="background: transparent !important;">
  <p>
    <?php include "../titulo.php"; ?>
  </p>
  <p align="center">
    <?php
    include "menu.php";
    ?>
  </p>
  <form name="form1" method="post" action="#vista">
    <!--
  <table width="40%" border="1" align="center">
    <tr>
      <td height="40" align="center" bgcolor="#FF0000"colspan="8"><span class="Estilo7"><u>Bien Nacional</u></span></td>
    </tr>
  </table>
  <table width="40%" border="1" align="center">
    <tr>
      <td  bgcolor="#CCCCCC"><strong>N&deg; Bien:</strong></td>
      <td  bgcolor="#CCCCCC"><strong>Descripcion:</strong></td>
    </tr>
    <tr>
      <td ><label><span class="">
          <input type="text" name="OBIEN" size="10" maxlength="6" value="<?php //echo $_POST['OBIEN']; 
                                                                          ?>" >
          </span></label></td>
      <td ><label><span class="">
          <input type="text" name="ODESCRIPCION" size="50"  value="<?php //echo $_POST['ODESCRIPCION']; 
                                                                    ?>">
          </span></label></td>
    </tr>
  </table>
-->
    <!--
  <br>
  <p align="center">
    <button type="submit" class="btn btn-primary" name="CMDBUSCAR" id="CMDBUSCAR" value="Buscar"><i class="fa-solid fa-magnifying-glass"></i>Buscar</button>
  </p>
-->
    <?php
    if ($_POST['OBIEN'] > 0) {
      //-------- 
      $consulta = "SELECT * FROM bn_bienes WHERE borrado=0 AND numero_bien = " . $_POST['OBIEN'] . " LIMIT 1";
    }
    //      echo $consulta;
    $tabla = mysql_query($consulta);
    $registro = mysql_fetch_object($tabla);
    ?>
    <div class="modal-dialog" role="document" style="width: 40%">
      <div align="center">
        <h3 class="alert alert-danger" role="alert">Seleccione el Bien Nacional a Modificar</h3>
        <div class="form-row">
          <div class="form-group col-md-12">
            <!--          <label for="OCATEGORIA">Categoria</label>-->
            <select id="OBIEN" name="OBIEN" class="form-control select2" onChange="this.form.submit();">
              <?php
              $consulta_x = "SELECT * FROM bn_bienes WHERE borrado=0 ORDER BY numero_bien";
              $tabla_x = mysql_query($consulta_x);

              while ($registro_x = mysql_fetch_array($tabla_x)) {
                echo '<option ';
                if ($registro->numero_bien == $registro_x['numero_bien']) {
                  echo 'selected="selected" ';
                  $formato = $registro_x['formato'];
                }
                echo ' value=' . $registro_x['numero_bien'] . '>' . $registro_x['numero_bien'] . ' ' . $registro_x['descripcion_bien'] . '</option>';
              }
              ?>
            </select>
          </div>
        </div>
        <br>
        <br>
        <h3 class="alert alert-success" role="alert">Resultado para Modificar</h3>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="OCATEGORIA">Categoria</label>
            <select id="OCATEGORIA" name="OCATEGORIA" class="form-control select2" style="align-content: flex-end">
              <?php
              $consulta_x = 'SELECT * FROM bn_categorias ;';
              $tabla_x = mysql_query($consulta_x);

              while ($registro_x = mysql_fetch_array($tabla_x)) {
                echo '<option ';
                if ($registro->id_categoria == $registro_x['id_categoria']) {
                  echo 'selected="selected" ';
                  $formato = $registro_x['formato'];
                }
                echo ' value=' . $registro_x['id_categoria'] . '>' . $registro_x['codigo'] . ' ' . $registro_x['descripcion'] . '</option>';
              }
              ?>
            </select>
          </div>
          <div class="form-group col-md-6">
            <label for="ONUMERO">Numero Bien</label>
            <input type="text" class="form-control" id="ONUMERO" name="ONUMERO" value="<?php echo $registro->numero_bien; ?>">
          </div>
        </div>
        <div class="form-group">
          <label for="ODESCRIPCION1">Descripcion del Bien</label>
          <input type="text" class="form-control" id="ODESCRIPCION1" name="ODESCRIPCION1" size="70" value="<?php echo $registro->descripcion_bien; ?>">
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="OESTADO">Estado</label>
            <select id="OESTADO" name="OESTADO" class="form-control">
              <option value="0">--> Seleccione <--< /option>
                  <?php
                  $z = 1;
                  while ($z <= 4) {
                    echo '<option ';
                    if ($registro->conservacion == $z) {
                      echo 'selected="selected" ';
                    }
                    echo ' value=' . $z . '>' . $z . '</option>';
                    $z++;
                  }
                  ?>
            </select>
          </div>
          <div class="form-group col-md-4">
            <label for="OVALOR">Valor</label>
            <input type="text" class="form-control" id="OVALOR" name="OVALOR" style="align-content: right" value="<?php echo $registro->valor; ?>">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-4">
            <label for="OFACTURA">Factura</label>
            <input type="text" class="form-control" id="OFACTURA" name="OFACTURA" value="<?php echo $registro->factura; ?>">
          </div>
          <div class="form-group col-md-4">
            <label for="OFECHA">Fecha</label>
            <input type="text" class="form-control datepicker" style="align-content: center" id="OFECHA" name="OFECHA" value="<?php echo voltea_fecha($registro->fecha); ?>">
          </div>
          <div class="form-group col-md-4">
            <label for="OCONDICION">Condicion</label>
            <select id="OCONDICION" name="OCONDICION" class="form-control">
              <?php
              echo '<option ';
              if ($registro->condicion == 'COMPRA') {
                echo 'selected="selected" ';
              }
              echo ' value=COMPRA>COMPRA</option>';
              echo '<option ';
              if ($registro->condicion == 'DONACION') {
                echo 'selected="selected" ';
              }
              echo ' value=DONACION>DONACION</option>';
              ?>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-3">
            <label for="OSERIAL">Serial</label>
            <input type="text" class="form-control" id="OSERIAL" name="OSERIAL" value="<?php echo $registro->serial; ?>">
          </div>
          <div class="form-group col-md-3">
            <label for="OMARCA">Marca</label>
            <input type="text" class="form-control" id="OMARCA" name="OMARCA" value="<?php echo ($registro->marca); ?>">
          </div>
          <div class="form-group col-md-3">
            <label for="OMODELO">Modelo</label>
            <input type="text" class="form-control" id="OMODELO" name="OMODELO" value="<?php echo $registro->modelo; ?>">
          </div>
          <div class="form-group col-md-3">
            <label for="OCOLOR">Color</label>
            <input type="text" class="form-control" id="OCOLOR" name="OCOLOR" value="<?php echo ($registro->color); ?>">
          </div>
        </div>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>

        <button type="submit" class="btn btn-success" name="CMDGUARDAR" id="CMDGUARDAR" value="Guardar"><i class="fa-regular fa-floppy-disk"></i>Guardar</button>
      </div>
    </div>
  </form>
  <p>
    <?php include "../pie.php"; ?>
  </p>
  <p>&nbsp;</p>
</body>

</html>


<!--
	<script>
  $( function() {
    $( "#datepicker" ).datepicker();
  } );
  </script>-->
<script language="JavaScript">
  //-------------------------------------------- 
  <?php
  //if ($i==1) 	{	echo 'Swal.fire({ title: "Correcto!",  text: "Bien Nacional Reasignado!",  icon: "success" }); '; }
  //if ($f==1) 	{	echo 'Swal.fire({ title: "Bien Nacional Reasignado!",  icon: "success" }); cargar_tabla();'; }
  if ($f > 0) {
    echo 'Swal.fire({ title: "Bien Nacional Modificado!",  icon: "success" });';
  }
  //if ($g>0) 	{	echo 'Swal.fire({ title: "Bien Devuelto Exitosamente!", icon: "success" }); cargar_tabla();'; }
  ?>
</script>