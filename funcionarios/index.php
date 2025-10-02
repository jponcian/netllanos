<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="icon" href="favicon.ico">
  <title>CONSULTAS FUNCIONARIOS</title>
  <!-- Bootstrap core CSS -->
  <link href="dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom styles for this template -->
  <link href="assets/css/sticky-footer-navbar.css" rel="stylesheet">
  <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />


</head>

<body style="background: transparent !important;">

  <header>
    <!-- Fixed navbar -->
    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav mr-auto">

          <li class="nav-item active">
            <a class="nav-link" href="../index.php">Volver al Inicio <span class="sr-only">(current)</span></a>
          </li>

        </ul>

      </div>
    </nav>
  </header>

  <!-- Begin page content -->

  <div class="container">
    <h4 class="mt-5">PERFIL DEL FUNCIONARIO</h4>
    <hr>

    <div class="row">
      <div class="col-12 col-md-12">
        <!-- Contenido -->

        <ul class="list-group">
          <li class="list-group-item">
            <form method="GET">
              <div class="form-row align-items-center">
                <div class="col-auto">
                  <label class="sr-only" for="inlineFormInput">Cedula</label>
                  <input name="z_empleados" type="text" class="form-control mb-2" id="inlineFormInput" placeholder="Ingrese Cedula" minlength="7" maxlength="8" required />
                  <input name="buscar" type="hidden" class="form-control mb-2" id="inlineFormInput" value="v">
                </div>

                <div class="col-auto">
                  <button type="submit" class="btn btn-primary mb-2">Buscar Ahora</button>
                </div>
              </div>
            </form>
          </li>

        </ul>
        <br>
        <?php

        include('conexion.php');
        $conn = new mysqli($servidor, $usuario, $password, $nombreBD);
        if ($conn->connect_error) {
          die("la conexión ha fallado: " . $conn->connect_error);
        }

        if (isset($_GET["z_empleados"])) {
          $pbu = $_GET["z_empleados"];
        }

        if (isset($_GET["buscar"])) {
          $sqln = mysqli_query($conn, "SELECT
z_empleados.cedula,
z_empleados.Apellidos,
z_empleados.Nombres,
z_sectores.nombre,
z_empleados.Cargo,
z_accesos_tipo.modulo,
z_accesos_tipo.descripcion

FROM
z_empleados
INNER JOIN z_empleados_accesos ON z_empleados.cedula = z_empleados_accesos.cedula
INNER JOIN z_empleados_roles ON z_empleados.cedula = z_empleados_roles.cedula
INNER JOIN z_accesos_tipo ON z_empleados_accesos.acceso = z_accesos_tipo.acceso
INNER JOIN z_sectores ON z_empleados.sector = z_sectores.id_sector WHERE z_empleados.cedula LIKE '%$pbu%' ")  or die(mysqli_error());
        }

        ?>



        <table class="table">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Cedula</th>
              <th scope="col">Apellidos</th>
              <th scope="col">Nombres</th>
              <th scope="col">Sector</th>
              <th scope="col">Cargo</th>
              <th scope="col">Modulo</th>
              <th scope="col">Descripcion</th>
            </tr>
          </thead>
          <?php
          if (isset($_GET["buscar"])) {
            $n = 0;
            while ($dato = mysqli_fetch_array($sqln)) {
              $n++;


              echo "<tbody>";
              echo "<tr>";
              echo "<th scope='row'>" . $n . "</th>";
              echo "<td>" . $dato['cedula'] . "</td>";
              echo "<td>" . $dato['Apellidos'] . "</td>";
              echo "<td>" . $dato['Nombres'] . "</td>";
              echo "<td>" . $dato['nombre'] . "</td>";
              echo "<td>" . $dato['Cargo'] . "</td>";
              echo "<td>" . $dato['modulo'] . "</td>";
              echo "<td>" . $dato['descripcion'] . "</td>";
              echo "</tr>";
              echo "  </tbody>";
            }
          }

          ?>

        </table>
        <p></p>


        <p></p>


        <!-- Fin Contenido -->
      </div>
    </div><!-- Fin row -->
  </div><!-- Fin container -->
  <CENTER>
    <footer class="footer">
      <div class="container">
        <span class="text-muted">
          <p>Informatica / GRTI LOS LLANOS</a></p>
        </span>
      </div>
    </footer>
  </CENTER>
  <!-- Bootstrap core JavaScript
    ================================================== -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script>
    window.jQuery || document.write('<script src="assets/js/vendor/jquery-slim.min.js"><\/script>')
  </script>
  <script src="assets/js/vendor/popper.min.js"></script>
  <script src="dist/js/bootstrap.min.js"></script>
</body>

</html>