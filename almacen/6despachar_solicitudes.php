<?php
session_start();
include "../conexion.php";
if ($_SESSION['VERIFICADO'] != "SI") {
  header("Location: index.php?errorusuario=val");
  exit();
}
$acceso = 160;
include "../validacion_usuario.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Solicitudes por Despachar</title>
  <?php include "../funciones/headNew.php"; ?>
  <link rel="stylesheet" href="css/custom-ui.css?v=1">
  <style>
    /* Estilo personalizado solo para el modal de despachar */
    #despacharModalContent {
      max-width: 720px !important;
      width: 52vw !important;
      min-width: 340px;
    }
  </style>
</head>

<body style="background: transparent !important;">
  <?php //include "menu.php"; ?>
  <div class="mx-auto d-block" style="width:85%;max-width:1200px;">

    <div class="card border-danger p-0 m-0">
      <div class="card-header bg-danger text-white text-center py-1" style="padding:0.5rem 1rem;">
        <p class="Estilo3" style="margin:0;font-size:1.15rem;">
          <i class="fa-solid fa-truck"></i> <strong>Solicitudes Aprobadas por Despachar</strong>
        </p>
      </div>
      <div class="card-body p-2">
        <form id="filtrosForm" method="post" autocomplete="off">
          <div class="row mb-2">
            <div class="col-md-6">
              <label class="fw-bold">División:</label>
              <select name="ODIVISION" id="ODIVISION" class="form-select form-select-sm">
                <?php
                if ($_SESSION['DIVISION_USUARIO'] == 9) {
                  echo '<option value="0">-------- TODAS --------</option>';
                  $consulta_x = 'SELECT division, descripcion FROM vista_alm_solicitud WHERE status=5 GROUP BY descripcion;';
                } else {
                  $consulta_x = 'SELECT division, descripcion FROM vista_alm_solicitud WHERE status=5 and division=' . $_SESSION['DIVISION_USUARIO'] . ' GROUP BY descripcion;';
                }
                $tabla_x = $_SESSION['conexionsqli']->query($consulta_x);
                while ($registro_x = $tabla_x->fetch_object()) {
                  echo '<option value="' . $registro_x->division . '">' . $registro_x->descripcion . '</option>';
                }
                ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="fw-bold">Fecha:</label>
              <select name="OMES" id="OMES" class="form-select form-select-sm">
                <option value="0">Todas</option>
                <?php
                $consulta_x = 'SELECT month(fecha) as mes, year(fecha) as anno FROM alm_solicitudes WHERE status=5 GROUP BY year(fecha), month(fecha) ORDER BY fecha DESC;';
                $tabla_x = $_SESSION['conexionsqli']->query($consulta_x);
                while ($registro_x = $tabla_x->fetch_object()) {
                  echo '<option';
                  if (isset($_POST['OMES']) && $_POST['OMES'] == $registro_x->mes . '-' . $registro_x->anno) {
                    echo ' selected="selected" ';
                  }
                  echo ' value="' . $registro_x->mes . '-' . $registro_x->anno . '">';
                  echo $_SESSION['meses_anno'][$registro_x->mes] . ' ' . $registro_x->anno;
                  echo '</option>';
                }
                ?>
              </select>
            </div>
          </div>
        </form>
        <div class="table-responsive">
          <table class="table table-bordered table-sm align-middle text-center mt-3">
            <thead class="table-danger">
              <tr>
                <th>#</th>
                <th>Fecha</th>
                <th>Solicitud</th>
                <th>Dependencia</th>
                <th>Funcionario</th>
                <th>Acción</th>
                <th>PDF</th>
              </tr>
            </thead>
            <tbody id="tabla-solicitudes-despachar">
              <!-- Las filas se cargan vía AJAX -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div id="despacharModalCustom">
    <div id="despacharModalContent">
      <div id="despacharModalHeader">
        <span id="despacharModalTitle">Despachar Solicitud</span>
        <button id="despacharModalClose" title="Cerrar">&times;</button>
      </div>
      <div id="despacharModalBody">
        <!-- Aquí se carga el formulario -->
      </div>
    </div>
  </div>

  <?php include "../funciones/footNew.php"; ?>

  <script>
    function cargarTablaDespachar() {
      // Botón para abrir modal de prueba estático
      document.getElementById('abrirModalPrueba') && document.getElementById('abrirModalPrueba').addEventListener('click', function () {
        document.getElementById('modalPrueba').style.display = 'flex';
      });
      const division = document.getElementById('ODIVISION').value;
      const fecha = document.getElementById('OMES').value;
      fetch('6z1despachar.php?division=' + division + '&fecha=' + fecha)
        .then(r => r.text())
        .then(html => {
          document.getElementById('tabla-solicitudes-despachar').innerHTML = html;
          document.querySelectorAll('.btn-despachar-modal').forEach(btn => {
            btn.onclick = function (e) {
              e.preventDefault();
              showDespacharModal(this.dataset.url);
            };
          });
        });
    }

    function showDespacharModal(url) {
      const modal = document.getElementById('despacharModalCustom');
      const body = document.getElementById('despacharModalBody');
      body.innerHTML = '<div class="text-center p-4"><div class="spinner-border text-danger"></div> Cargando...</div>';
      modal.style.display = 'flex';
      fetch(url)
        .then(r => r.text())
        .then(html => {
          body.innerHTML = html;
          // Asignar evento al botón Guardar después de cargar el modal
          const btnGuardar = body.querySelector('button.btn-danger');
          if (btnGuardar) {
            btnGuardar.onclick = guardarDespacho;
          }
        });
    }

    // Definir la función guardarDespacho en el archivo principal
    function guardarDespacho() {
      var form = document.getElementById('form-despachar');
      if (!form) {
        alert('No se encontró el formulario.');
        return;
      }
      // Validar que ninguna cantidad a despachar sea mayor a la aprobada
      var inputs = form.querySelectorAll('input[type="number"]');
      for (var i = 0; i < inputs.length; i++) {
        var input = inputs[i];
        var max = parseFloat(input.getAttribute('max'));
        var val = parseFloat(input.value);
        if (val > max) {
          alert('La cantidad a despachar no puede ser mayor a la aprobada.');
          input.focus();
          return;
        }
      }
      var solicitud = form.getAttribute('data-solicitud') || '';
      if (!solicitud) {
        var urlParams = new URLSearchParams(window.location.search);
        solicitud = urlParams.get('solicitud') || '';
      }
      if (!solicitud) {
        alert('No se pudo determinar el número de solicitud.');
        return;
      }
      var formData = new FormData(form);
      formData.set('CMDGUARDAR', 'Guardar');
      fetch('7despachar_solicitud_guardar.php?solicitud=' + encodeURIComponent(solicitud), {
        method: 'POST',
        body: formData
      })
        .then(r => r.text())
        .then(resp => {
          // Si la respuesta está vacía, se asume éxito
          if (!resp.trim()) {
            // Cerrar modal y recargar tabla en el mismo contexto
            var modal = document.getElementById('despacharModalCustom');
            if (modal) modal.style.display = 'none';
            cargarTablaDespachar();
          } else {
            form.parentNode.innerHTML = resp;
          }
        })
        .catch(function (error) {
          alert('Error al guardar: ' + error);
        });
    }
    document.getElementById('despacharModalClose').onclick = function () {
      document.getElementById('despacharModalCustom').style.display = 'none';
    };
    // Inicializar tabla al cargar
    window.addEventListener('DOMContentLoaded', cargarTablaDespachar);
    // Recargar tabla al cambiar filtros
    document.getElementById('ODIVISION').addEventListener('change', cargarTablaDespachar);
    document.getElementById('OMES').addEventListener('change', cargarTablaDespachar);
  </script>
</body>

</html>