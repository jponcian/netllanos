<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";
include_once __DIR__ . '/../frases.php';
$_SESSION['NOMBRE_MODULO'] = 'BIENES NACIONALES';

if ($_SESSION['VERIFICADO'] != "SI") {
  header("Location: index.php?errorusuario=val");
  exit();
}

//----------
include "0_respaldo_inv.php";

// Determinar el saludo según la hora del día
$hora = date('H');
$saludo = "";
if ($hora < 12) {
  $saludo = "¡Buenos Días!";
} elseif ($hora >= 12 && $hora < 18) {
  $saludo = "¡Buenas Tardes!";
} else {
  $saludo = "¡Buenas Noches!";
}

// Seleccionar una frase aleatoria
$frase_aleatoria = $frases_de_exito[array_rand($frases_de_exito)];

//----------
// Condición para mostrar el dashboard
$mostrarDashboard = ($_SESSION['ADMINISTRADOR'] == 1 || $_SESSION['DIVISION_USUARIO'] == 9);

if ($mostrarDashboard) {
  // Datos para dashboard
  $mysqli = $_SESSION['conexionsqli'];

  // 1) Resumen de bienes sin asignar por área
  $resumenSinAsignar = [];
  $sqlResumen = "SELECT a.id_area, d.descripcion as division, a.descripcion as area, COUNT(b.id_bien) as total
                 FROM bn_bienes b
                 JOIN bn_areas a ON b.id_area = a.id_area
                 JOIN z_jefes_detalle d ON a.division = d.division
                 WHERE (b.inf_ci_asignado IS NULL OR b.inf_ci_asignado = 0 OR b.inf_ci_asignado = '')
                   AND b.borrado = 0 
                   AND b.id_area NOT IN (91, 199)
                 GROUP BY a.id_area, d.descripcion, a.descripcion
                 ORDER BY d.descripcion, a.descripcion";
  if ($res = $mysqli->query($sqlResumen)) {
    while ($row = $res->fetch_assoc()) {
      $resumenSinAsignar[] = $row;
    }
  }

  // 2) Tabla: movimientos (reasignaciones) últimos 7 días
  $movBienes7 = [];
  $movBienesAviso = '';
  $fechaExpr = "COALESCE(STR_TO_DATE(r.fecha,'%Y-%m-%d'), STR_TO_DATE(r.fecha,'%d/%m/%Y'), STR_TO_DATE(r.fecha,'%Y/%m/%d'), STR_TO_DATE(r.fecha,'%d-%m-%Y'))";
  $sqlMov7 = "SELECT r.id_reasignacion, r.numero, r.fecha, z_jefes_actual.descripcion AS div_actual, z_jefes_destino.descripcion AS div_destino, COUNT(DISTINCT d.id_bien) AS items
              FROM bn_reasignaciones r
              JOIN bn_reasignaciones_detalle d ON d.id_reasignacion = r.id_reasignacion
              JOIN bn_areas bn_area_actual ON bn_area_actual.id_area = d.id_area_anterior
              JOIN bn_areas bn_area_destino ON bn_area_destino.id_area = d.id_area_destino
              JOIN z_jefes_detalle z_jefes_actual ON z_jefes_actual.division = bn_area_actual.division
              JOIN z_jefes_detalle z_jefes_destino ON z_jefes_destino.division = bn_area_destino.division
              WHERE d.borrado = 0 AND ($fechaExpr) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
              GROUP BY r.id_reasignacion, r.numero, r.fecha, z_jefes_actual.descripcion, z_jefes_destino.descripcion
              ORDER BY ($fechaExpr) DESC, r.id_reasignacion DESC
              LIMIT 20";
  if ($res = $mysqli->query($sqlMov7)) {
    while ($row = $res->fetch_assoc()) {
      $movBienes7[] = $row;
    }
  }
  if (empty($movBienes7)) {
    $movBienesAviso = 'No hay movimientos en los últimos 7 días; mostrando últimos 20 movimientos.';
    $sqlMovAll = "SELECT r.id_reasignacion, r.numero, r.fecha, z_jefes_actual.descripcion AS div_actual, z_jefes_destino.descripcion AS div_destino, COUNT(DISTINCT d.id_bien) AS items
                  FROM bn_reasignaciones r
                  JOIN bn_reasignaciones_detalle d ON d.id_reasignacion = r.id_reasignacion
                  JOIN bn_areas bn_area_actual ON bn_area_actual.id_area = d.id_area_anterior
                  JOIN bn_areas bn_area_destino ON bn_area_destino.id_area = d.id_area_destino
                  JOIN z_jefes_detalle z_jefes_actual ON z_jefes_actual.division = bn_area_actual.division
                  JOIN z_jefes_detalle z_jefes_destino ON z_jefes_destino.division = bn_area_destino.division
                  WHERE d.borrado = 0
                  GROUP BY r.id_reasignacion, r.numero, r.fecha, z_jefes_actual.descripcion, z_jefes_destino.descripcion
                  ORDER BY ($fechaExpr) DESC, r.id_reasignacion DESC
                  LIMIT 20";
    if ($res2 = $mysqli->query($sqlMovAll)) {
      while ($row = $res2->fetch_assoc()) {
        $movBienes7[] = $row;
      }
    }
  }

  // 3) Lista: últimos bienes cargados
  $bienesRecientes = [];
  $sqlBienRec = "SELECT id_bien, numero_bien, descripcion_bien, valor, fecha FROM bn_bienes WHERE borrado = 0 ORDER BY id_bien DESC LIMIT 20";
  if ($res = $mysqli->query($sqlBienRec)) {
    while ($row = $res->fetch_assoc()) {
      $bienesRecientes[] = $row;
    }
  }

  // 4) Reasignaciones pendientes por aprobar (agregado por interno + división)
  $reasignacionesPendientes = [];
  // Nueva consulta: agrupar por interno, id_division_actual, id_division_destino
  $sqlPend = "SELECT interno, id_division_actual, id_division_destino, division_actual, COUNT(id_bien) AS cantidad FROM vbienes_pendientes WHERE por_reasignar = 2 GROUP BY interno, id_division_actual, id_division_destino ORDER BY cantidad DESC LIMIT 20";
  if ($res = $mysqli->query($sqlPend)) {
    while ($row = $res->fetch_assoc()) {
      $reasignacionesPendientes[] = $row;
    }
  }
}
?>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="X-UA-Compatible" content="IE=9" />
  <title>Men&uacute; Principal</title>
  <?php include "../funciones/headNew.php"; ?>
  <style>
    .swal2-image.rounded-circle {
      border-radius: 50%;
      object-fit: cover;
      box-shadow: 0 0 15px 5px rgba(0, 123, 255, 0.5);
    }

    .card {
      border-radius: .75rem;
    }

    .card-header {
      font-weight: 600;
      display: flex;
      align-items: center;
    }

    .card-header .card-title {
      font-weight: 700;
      color: #007bff;
      position: relative;
    }

    .card-header .card-title::after {
      content: '';
      display: block;
      height: 3px;
      width: 140px;
      margin-top: 4px;
      border-radius: 2px;
      background: linear-gradient(90deg, #007bff, #0056b3);
    }

    .card-header i {
      margin-right: .65rem !important;
    }

    .product-list .product-img {
      width: 50px;
      height: 50px;
      background: #f0f2f5;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: .5rem;
    }

    .product-list .product-title {
      font-weight: 600;
    }

    .table td,
    .table th {
      vertical-align: middle;
    }

    .card-header .card-tools {
      margin-left: auto;
      display: flex;
      align-items: center;
    }
  </style>
</head>

<body style="background: transparent !important;">

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      Swal.fire({
        title: '<?php echo $saludo; ?>',
        html: '<?php echo ucwords(strtolower(addslashes($_SESSION['NOM_USUARIO']))); ?> <br><strong>¡Bienvenido al Módulo de Bienes Nacionales!</strong><br><br><i>"<?php echo addslashes($frase_aleatoria); ?>"</i>',
        imageUrl: '../imagenes/funcionarios/<?php echo $_SESSION['CEDULA_USUARIO']; ?>.png',
        imageWidth: 100,
        imageHeight: 100,
        imageAlt: 'Foto del usuario',
        customClass: { image: 'rounded-circle' },
        timer: 15000,
        timerProgressBar: true,
        allowOutsideClick: true,
        allowEscapeKey: false,
        didOpen: () => {
          const img = document.querySelector('.swal2-image');
          if (img) { img.onerror = function () { this.onerror = null; this.src = '../imagenes/funcionarios/default.png'; }; }

          // Efecto navideño: copos de nieve (sin confetti)
          function randomInRange(min, max) { return Math.random() * (max - min) + min; }

          // Snowfall: crear copos SVG blancos que caen suavemente
          window.__snowInterval = setInterval(() => {
            const s = document.createElement('div');
            s.style.position = 'fixed';
            s.style.left = Math.random() * 100 + '%';
            s.style.top = '-5%';
            const size = Math.floor(10 + Math.random() * 20);
            s.style.width = size + 'px';
            s.style.height = size + 'px';
            s.style.pointerEvents = 'none';
            s.style.zIndex = 10001;
            s.style.opacity = (0.8 + Math.random() * 0.2).toString();

            // Usar archivo SVG local para el copo (mejor caché y consistencia)
            // la ruta es relativa al documento público: /netlosllanos/assets/svg/flake.svg
            s.innerHTML = '<img src="/netlosllanos/assets/svg/flake.svg" style="width:100%;height:100%;display:block;" alt="copo">';

            document.body.appendChild(s);
            const fallDuration = 4000 + Math.random() * 4000;
            s.animate([
              { transform: 'translateY(0) rotate(0deg)', opacity: s.style.opacity },
              { transform: 'translateY(110vh) rotate(' + (Math.random() * 360) + 'deg)', opacity: '0.6' }
            ], { duration: fallDuration, easing: 'linear' });
            setTimeout(() => s.remove(), fallDuration + 50);
          }, 220);
        },
        didClose: () => {
          // Mostrar el dashboard y notificar al padre
          var root = document.getElementById('dashboard-root');
          if (root) root.style.display = '';
          if (window.parent && window.parent !== window) {
            window.parent.postMessage({ accion: 'mostrarMenuBienes', animar: true }, '*');
          }
          // Limpiar intervals de efectos
          try { clearInterval(window.__snowInterval); } catch (e) { /* silent */ }
        }
      });
    });
  </script>

  <?php if ($mostrarDashboard): ?>
    <div class="container-fluid py-3" id="dashboard-root" style="display:none;">
      <div class="row g-3 align-items-stretch">
        <?php if (!empty($reasignacionesPendientes)): ?>
          <div class="col-12 col-xl-6">
            <!-- Reasignaciones pendientes por aprobar (coordinador) - PRIMERO -->
            <div class="card h-100">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-clipboard-check"></i> Reasignaciones por Aprobar</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                      class="fas fa-minus"></i></button>
                </div>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-striped table-hover mb-0" id="tabla-reasignaciones">
                    <thead class="table-light">
                      <tr>
                        <th>División</th>
                        <th>Tipo</th>
                        <th style="width:120px; text-align:center;">Cantidad</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($reasignacionesPendientes as $rp) {
                        $divId = (int) $rp['id_division_destino'];
                        $divActualId = (int) $rp['id_division_actual'];
                        $interno = (int) $rp['interno'];
                        $tipo = $interno === 1 ? 'INTERNA' : 'EXTERNA';
                        $divName = htmlspecialchars($rp['division_actual']);
                        $cant = (int) $rp['cantidad'];
                        // data-interno, data-div-actual y data-div-dest para solicitar detalles
                        echo '<tr class="reasign-row" data-interno="' . $interno . '" data-div-actual="' . $divActualId . '" data-div-dest="' . $divId . '" style="cursor:pointer;">';
                        echo '<td>' . $divName . '</td>';
                        echo '<td>' . $tipo . '</td>';
                        echo '<td class="text-center"><span class="badge bg-warning">' . $cant . '</span></td>';
                        echo '</tr>';
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <?php if (!empty($resumenSinAsignar)): ?>
          <div class="col-12 col-xl-6">
            <div class="card h-100">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-question-circle"></i> Bienes Sin Asignar por Área</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Colapsar"><i
                      class="fas fa-minus"></i></button>
                </div>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-striped table-hover mb-0" id="tabla-sin-asignar">
                    <thead class="table-light">
                      <tr>
                        <th>División</th>
                        <th>Área</th>
                        <th style="width:100px; text-align:center;">Cantidad</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      foreach ($resumenSinAsignar as $r) {
                        $id_area_val = (int) $r['id_area'];
                        $area_name = htmlspecialchars($r['area']);
                        $division_name = htmlspecialchars($r['division']);
                        $total_val = (int) $r['total'];
                        echo '<tr class="area-row" data-area-id="' . $id_area_val . '" data-area-name="' . $area_name . '" title="Ver detalle de bienes para esta área" style="cursor:pointer;">';
                        echo '<td>' . $division_name . '</td>';
                        echo '<td>' . $area_name . '</td>';
                        echo '<td class="text-center"><span class="badge bg-warning">' . $total_val . '</span></td>';
                        echo '</tr>';
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <div class="row g-3 align-items-start mt-3">
        <div class="col-12 col-xl-8">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-exchange-alt"></i>Movimientos (últimos 7 días)</h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Colapsar"><i
                    class="fas fa-minus"></i></button>
              </div>
            </div>
            <div class="card-body p-0">
              <?php if (!empty($movBienesAviso)) { ?>
                <div class="px-3 pt-2 text-muted small"><?php echo htmlspecialchars($movBienesAviso); ?></div>
              <?php } ?>
              <div class="table-responsive">
                <table class="table table-striped table-hover mb-0" id="tabla-movimientos">
                  <thead class="table-light">
                    <tr>
                      <th style="width:100px;">Número</th>
                      <th>Fecha</th>
                      <th>Origen</th>
                      <th>Destino</th>
                      <th style="width:100px;">Cantidad</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($movBienes7)) { ?>
                      <tr>
                        <td colspan="5" class="text-center text-muted py-4">Sin movimientos recientes</td>
                      </tr>
                    <?php } else {
                      foreach ($movBienes7 as $m) { ?>
                        <tr class="mov-row" data-id="<?php echo (int) $m['id_reasignacion']; ?>"
                          title="Ver detalle del movimiento" style="cursor:pointer;">
                          <td>#<?php echo htmlspecialchars($m['numero'] ?: $m['id_reasignacion']); ?></td>
                          <td><?php echo htmlspecialchars(voltea_fecha($m['fecha'])); ?></td>
                          <td><?php echo htmlspecialchars($m['div_actual']); ?></td>
                          <td><?php echo htmlspecialchars($m['div_destino']); ?></td>
                          <td><?php echo (int) $m['items']; ?></td>
                        </tr>
                      <?php }
                    } ?>
                  </tbody>
                </table>
              </div>
              <div class="p-2 text-center" id="movimientos-toggle-wrapper" style="display:none;">
                <button class="btn btn-sm btn-outline-primary" id="movimientos-toggle">Mostrar más</button>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 col-xl-4">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-plus-square"></i>Últimos bienes cargados</h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Colapsar"><i
                    class="fas fa-minus"></i></button>
              </div>
            </div>
            <div class="card-body">
              <?php if (empty($bienesRecientes)) { ?>
                <div class="text-muted">No hay bienes recientes.</div>
              <?php } else {
                foreach ($bienesRecientes as $b) { ?>
                  <div class="d-flex align-items-center mb-3 product-list">
                    <div class="product-img mr-3"><i class="fas fa-cube"></i></div>
                    <div class="flex-grow-1">
                      <div class="product-title mb-1">Bien N° <?php echo htmlspecialchars($b['numero_bien']); ?></div>
                      <div class="small text-muted">"<?php echo htmlspecialchars($b['descripcion_bien']); ?>" · Valor:
                        <?php echo number_format((float) $b['valor'], 2, ',', '.'); ?>
                      </div>
                    </div>
                  </div>
                <?php }
              } ?>
              <div class="text-center mt-2" id="bienes-toggle-wrapper" style="display:none;">
                <button class="btn btn-sm btn-outline-primary" id="bienes-toggle">Mostrar más</button>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    <div class="modal fade" id="modalDetalleMovimiento" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title m-0"><i class="fas fa-exchange-alt mr-2"></i>Detalle de Movimiento</h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Cerrar"><span
                aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-body">
            <div class="text-center p-4 text-muted"><i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
              <div>Cargando detalle...</div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                class="fas fa-times mr-1"></i>Cerrar</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modalDetalleSinAsignar" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title m-0"><i class="fas fa-question-circle mr-2"></i>Bienes Sin Asignar</h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Cerrar"><span
                aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-body">
            <div class="text-center p-4 text-muted"><i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
              <div>Cargando detalle...</div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                class="fas fa-times mr-1"></i>Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <?php include "../funciones/footNew.php"; ?>
  <script>
    (function () {
      <?php if ($mostrarDashboard): ?>
        const $doc = $(document);
        // Mostrar más/menos para tablas
        function setupToggle(tableId, wrapperId, maxRows) {
          const $rows = $(tableId + ' tbody tr');
          if ($rows.length > maxRows) {
            $rows.each(function (i) { if (i >= maxRows) $(this).addClass('row-hidden'); });
            $(wrapperId).show();
          } else {
            $(wrapperId).hide();
          }
          $doc.on('click', wrapperId + ' button', function () {
            const $btn = $(this);
            const hidden = $(tableId + ' tbody tr.row-hidden').length > 0;
            if (hidden) {
              $(tableId + ' tbody tr').removeClass('row-hidden');
              $btn.text('Mostrar menos');
            } else {
              $rows.each(function (i) { if (i >= maxRows) $(this).addClass('row-hidden'); });
              $btn.text('Mostrar más');
            }
          });
        }
        function setupListToggle(wrapperId, listSelector, maxItems) {
          const $items = $(listSelector);
          if ($items.length > maxItems) {
            $items.each(function (i) { if (i >= maxItems) $(this).addClass('item-hidden'); });
            $(wrapperId).show();
          } else {
            $(wrapperId).hide();
          }
          $doc.on('click', wrapperId + ' button', function () {
            const $btn = $(this);
            const hidden = $(listSelector + '.item-hidden').length > 0;
            if (hidden) {
              $(listSelector).removeClass('item-hidden');
              $btn.text('Mostrar menos');
            } else {
              $items.each(function (i) { if (i >= maxItems) $(this).addClass('item-hidden'); });
              $btn.text('Mostrar más');
            }
          });
        }

        setupToggle('#tabla-movimientos', '#movimientos-toggle-wrapper', 5);
        setupListToggle('#bienes-toggle-wrapper', '.card-body .product-list', 5);

        // Clic en movimiento: abrir detalle
        $doc.on('click', 'tr.mov-row', function (e) {
          if ($(e.target).closest('a, button').length) return;
          const id = $(this).data('id');
          if (!id) return;
          const $modal = $('#modalDetalleMovimiento');
          const $body = $modal.find('.modal-body');
          $body.html('<div class="text-center p-4 text-muted"><i class="fas fa-spinner fa-spin fa-2x mb-2"></i><div>Cargando detalle...</div></div>');
          $modal.modal('show');

          $.get('ajax_detalle_movimiento.php', { reasignacion: id })
            .done(function (html) { $body.html(html); })
            .fail(function () { $body.html('<div class="alert alert-danger">No se pudo cargar el detalle del movimiento.</div>'); });
        });

        // Clic en área sin asignar: abrir detalle
        $doc.on('click', 'tr.area-row', function (e) {
          if ($(e.target).closest('a, button').length) return;
          const id = $(this).data('area-id');
          const name = $(this).data('area-name');
          if (!id) return;
          const $modal = $('#modalDetalleSinAsignar');
          const $body = $modal.find('.modal-body');
          $modal.find('.modal-title').html('<i class="fas fa-question-circle mr-2"></i> Bienes Sin Asignar en ' + name);
          $body.html('<div class="text-center p-4 text-muted"><i class="fas fa-spinner fa-spin fa-2x mb-2"></i><div>Cargando detalle...</div></div>');
          $modal.modal('show');
          $.get('ajax_detalle_sin_asignar.php', { area_id: id })
            .done(function (html) { $body.html(html); })
            .fail(function () { $body.html('<div class="alert alert-danger">No se pudo cargar el detalle.</div>'); });
        });

        // Funcionalidad para colapsar tarjetas
        $doc.on('click', '[data-card-widget="collapse"]', function (e) {
          e.preventDefault();
          const $card = $(this).closest('.card');
          const $body = $card.find('.card-body, .card-footer');
          $body.slideToggle('fast');
          $(this).find('i').toggleClass('fa-minus fa-plus');
        });

        const style = document.createElement('style');
        style.innerHTML = '.row-hidden{display:none;} .item-hidden{display:none !important;}';
        document.head.appendChild(style);
      <?php endif; ?>
    })();
  </script>
  <script>
    // Handler para abrir la pantalla de aprobación en el dashboard padre
    (function () {
      if (typeof jQuery === 'undefined') return;
      // Ahora la lista es una tabla y las filas tienen la clase .reasign-row
      $(document).on('click', '.reasign-row', function (e) {
        if ($(e.target).closest('a, button').length) return; // evitar clicks en botones/links internos
        e.preventDefault();
        var $el = $(this);
        var interno = $el.data('interno');
        var divActual = $el.data('div-actual');
        var divDest = $el.data('div-dest');
        if (typeof interno === 'undefined' || typeof divActual === 'undefined' || typeof divDest === 'undefined') return;

        var $modal = $('#modalDetalleMovimiento');
        var $body = $modal.find('.modal-body');
        var tipo = (parseInt(interno, 10) === 1) ? 'INTERNA' : 'EXTERNA';
        $modal.find('.modal-title').html('<i class="fas fa-clipboard-check mr-2"></i> Bienes para reasignación (' + tipo + ')');
        $body.html('<div class="text-center p-4 text-muted"><i class="fas fa-spinner fa-spin fa-2x mb-2"></i><div>Cargando detalle...</div></div>');
        $modal.modal('show');

        $.get('ajax_detalle_reasignacion.php', { interno: interno, div_actual: divActual, div_dest: divDest })
          .done(function (html) { $body.html(html); })
          .fail(function () { $body.html('<div class="alert alert-danger">No se pudo cargar el detalle de la reasignación.</div>'); });
      });
    })();
  </script>
</body>

</html>
<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";
include_once __DIR__ . '/../frases.php';
// include_once __DIR__ . '/../config.php';
$_SESSION['NOMBRE_MODULO'] = 'BIENES NACIONALES';

if ($_SESSION['VERIFICADO'] != "SI") {
  header("Location: index.php?errorusuario=val");
  exit();
}

//----------
include "0_respaldo_inv.php";

// Determinar el saludo según la hora del día
$hora = date('H');
$saludo = "";
if ($hora < 12) {
  $saludo = "¡Buenos Días!";
} elseif ($hora >= 12 && $hora < 18) {
  $saludo = "¡Buenas Tardes!";
} else {
  $saludo = "¡Buenas Noches!";
}

// Seleccionar una frase aleatoria
$frase_aleatoria = $frases_de_exito[array_rand($frases_de_exito)];
//----------
?>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="X-UA-Compatible" content="IE=9" />
  <title>Men&uacute; Principal</title>
  <?php
  include "../funciones/headNew.php";
  ?>
  <style>
    .swal2-image.rounded-circle {
      border-radius: 50%;
      object-fit: cover;
      box-shadow: 0 0 15px 5px rgba(0, 123, 255, 0.5);
    }
  </style>
</head>

<body style="background: transparent !important;">

  <script>
    // Mensaje de bienvenida espectacular
    document.addEventListener('DOMContentLoaded', function () {
      Swal.fire({
        title: '<?php echo $saludo; ?>',
        html: '<?php echo ucwords(strtolower(addslashes($_SESSION['NOM_USUARIO']))); ?> <br><strong>¡Bienvenido al Nuevo Módulo de Bienes Nacionales!</strong><br><br><i>"<?php echo addslashes($frase_aleatoria); ?>"</i>',
        imageUrl: '../imagenes/funcionarios/<?php echo $_SESSION['CEDULA_USUARIO']; ?>.png',
        imageWidth: 100,
        imageHeight: 100,
        imageAlt: 'Foto del usuario',
        customClass: {
          image: 'rounded-circle'
        },
        timer: 15000,
        timerProgressBar: true,
        allowOutsideClick: true,
        allowEscapeKey: false,
        didOpen: () => {
          // Fuegos artificiales
          const duration = 4 * 1000;
          const animationEnd = Date.now() + duration;
          const defaults = {
            startVelocity: 30,
            spread: 360,
            ticks: 60,
            zIndex: 0
          };

          function randomInRange(min, max) {
            return Math.random() * (max - min) + min;
          }

          const interval = setInterval(function () {
            const timeLeft = animationEnd - Date.now();

            if (timeLeft <= 0) {
              return clearInterval(interval);
            }

            const particleCount = 50 * (timeLeft / duration);
            // since particles fall down, start a bit higher than random
            confetti(Object.assign({}, defaults, {
              particleCount,
              origin: {
                x: randomInRange(0.1, 0.3),
                y: Math.random() - 0.2
              }
            }));
            confetti(Object.assign({}, defaults, {
              particleCount,
              origin: {
                x: randomInRange(0.7, 0.9),
                y: Math.random() - 0.2
              }
            }));
          }, 250);
        },
        didClose: () => {
          // Notificar al padre (index.php) que debe mostrar el menú de almacén con animación
          if (window.parent && window.parent !== window) {
            window.parent.postMessage({
              accion: 'mostrarMenuBienes',
              animar: true
            }, '*');
          }
        }
      });
    });
  </script>

  <?php
  include "../funciones/footNew.php";
  ?>