 <!DOCTYPE html>
 <html lang="es">

 <head>
   <title>Heredar Propiedades entre Funcionarios</title>
 </head>

 <body style="background: transparent !important;">

   <div>
     <h1>Heredar</h1>
     <br>
     <?php
      include "conexion.php";
      $conexion = conexion();
      if (! $_POST) {
      ?>
       <form method="POST" action="actualizar.php">
         Cedula Principal
         <br>
         <?php
          // creamos la sentencia SQL y la ejecutamos
          $ssql = "SELECT modulo, descripcion, z_accesos_tipo.acceso FROM z_accesos_tipo, z_empleados_accesos WHERE cedula=10272740 and z_accesos_tipo.acceso=z_empleados_accesos.acceso ORDER BY modulo, descripcion";
          $result = $conexion->query($ssql);

          //Generamos el campo select
          echo '<select cedula="cedula">';
          while ($row = $result->fetch_array()) {
            echo '<option>' . $row["cedula"] . '</option>';
          }
          echo '</select>';
          ?>
         <br>
         Cedula Heredero<br>
         <input type="text" name="cedula"><br>
         <input type="submit" value="Actualizar">
       </form>
     <?php
      } else {
        // Recibimos los datos del formulario
        $cedula = $_POST["cedula"];
        $cedula = $_POST["cedula"];

        // Montamos la sentencia SQL
        $ssql = "update clientes set cedula='$cedula' Where cedula='$cedula'";


        // Ejecutamos la sentencia de actualización
        if ($conexion->query($ssql)) {
          echo '<p>Cliente actualizado con éxito</p>';
        } else {
          echo '<p>Hubo un error al actualizar : ' . $conexion->error . '</p>';
        }
      }
      $conexion->close();
      ?>
     <p>
       <a href="actualizar.php">Actualizar otro registro</a>
     </p>
     <p>
       <a href="seleccionar.php">Listar</a>
     </p>
   </div>

 </body>

 </html>