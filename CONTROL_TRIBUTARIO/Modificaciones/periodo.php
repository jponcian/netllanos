<?php error_reporting(0); ?>
<!DOCTYPE html>
<html lang="es">

<head>
  <title></title>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE-edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
</head>
<link rel="stylesheet" href="jquery_ui/css/jquery-ui-1.10.4.custom.min.css">
<link rel="stylesheet" href="themes/jquery.alerts.css">

<body style="background: transparent !important;">
  <div class="container">
    <h3 class="text-center">Modificar Periodo de una Sanción</h3>
    <div class="row justify-content-center border" style="width: 40%; position :relative !important; left: 31% !important; padding: 20px;">

      <form class="needs-validation" novalidate>
        <div class="form-row">
          <div class="form-row"><strong>
              <h4>Datos de la Providencia Administrativa<h4>
            </strong>
            <div class="col-md-12 mb-3">
              <label for="validationTooltip01">Sector:</label>
              <select class="form-select" aria-label="Default select example" id="cbosectorLiq">
                <option selected value="0">Seleccione el sector</option>
                <?php
                include "../conexion.php";

                $registros = "SELECT id_sector, nombre FROM z_sectores Where id_sector between 1 and 5";
                $resultregistros = $conexionsql->query($registros);
                while ($valor = $resultregistros->fetch_object()) {
                  echo $valor->nombre;
                ?><option value="<?php echo $valor->id_sector; ?>"><?php echo $valor->nombre; ?></option><?php
                                                                                                      }
                                                                                                        ?>
              </select>

            </div>

            <div class="col-md-12 mb-3">
              <label for="annoProvidenciaLiq">Año Providencia:</label>
              <input type="text" class="form-control" id="annoProvidenciaLiq" placeholder="Año Providencia" required>
              <div class="valid-tooltip">
                Ingrese año de la providencia administrativa
              </div>
            </div>

            <div class="col-md-12 mb-3">
              <label for="numeroProvidenciaLiq">Numero Providencia:</label>
              <input type="text" class="form-control" id="numeroProvidenciaLiq" placeholder="Numero Providencia" required>
              <div class="valid-tooltip">
                Ingrese numero de la providencia administrativa
              </div>
            </div>

            <div class="col-md-12 mb-3">
              <label for="sancion">Código de Sanción:</label>
              <input type="text" class="form-control" id="sancion" placeholder="Código de Sanción" required>
              <div class="valid-tooltip">
                Ingrese código de la sanción
              </div>
            </div>

            <button id="buscarLiq" class="btn btn-success" type="button">Buscar</button>
          </div>



        </div>

        <div class="form-row">
          <div class="form-row"><strong>
              <h4>Periodo a Modificar<h4>
            </strong>
            <div class="col-md-12 mb-3">
              <label for="fechaDesde">Fecha Desde</label>
              <input type="text" class="form-control" id="fechaDesde" required>
              <div class="valid-tooltip">
                Looks good!
              </div>
            </div>

            <div class="col-md-12 mb-3">
              <label for="fechaHasta">Fecha Hasta</label>
              <input type="text" class="form-control" id="fechaHasta" required>
              <div class="valid-tooltip">
                Looks good!
              </div>
            </div>
          </div>

        </div>

        <button id="btnGuardarLiq" class="btn btn-primary" type="button" disabled>Guardar</button>
        <div style="padding-top: 20px; padding-bottom: 20px;">

          <div id="Aviso" class="alert alert-success mx-auto text-center" role="alert"></div>

        </div>

    </div>
  </div>



</body>

<script type='text/JavaScript' src='../funciones/scw_normal.js'></script>
<!-------------------------------------------------------------->
<link rel="stylesheet" type="text/css" href="estilos/estilos.css" />

<script type="text/javascript" src="jquery/jquery.js"></script>
<script type="text/javascript" src="jquery_ui/js/jquery-ui-1.10.4.custom.js"></script>

<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="funciones/funciones.js"></script>

</html>