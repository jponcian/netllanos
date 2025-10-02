<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";
include_once __DIR__ . '/../frases.php';
// include_once __DIR__ . '/../config.php';

if ($_SESSION['VERIFICADO'] != "SI") {
  header("Location: index.php?errorusuario=val");
  exit();
}

$acceso = 166;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------

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
// Datos para dashboard (Almacén y Bienes)
$mysqli = $_SESSION['conexionsqli'];

// Rango para series diarias
$diasSerie = 14; // últimos 14 días
$diaKeys = [];
$diaLabels = [];
$meses_es = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
for ($i = $diasSerie - 1; $i >= 0; $i--) {
  $ts = strtotime("-{$i} day");
  $diaKeys[] = date('Y-m-d', $ts);
  $diaLabels[] = (int) date('d', $ts) . ' ' . $meses_es[(int) date('n', $ts) - 1];
}

// 1) Divisiones con más solicitudes (últimos 14 días): resumen por división
$divisionesResumen = ['labels' => [], 'data' => []];
$sqlDivRes = "SELECT s.division id, COALESCE(jd.descripcion, CONCAT('Div ', s.division)) nombre, COUNT(*) total
              FROM alm_solicitudes s
              LEFT JOIN z_jefes_detalle jd ON jd.division = s.division
              WHERE s.fecha >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
              GROUP BY s.division, jd.descripcion
              ORDER BY total DESC";
if ($res = $mysqli->query($sqlDivRes)) {
  while ($row = $res->fetch_assoc()) {
    $divisionesResumen['labels'][] = $row['nombre'];
    $divisionesResumen['data'][] = (int) $row['total'];
  }
}

// 2) Torta: artículos más solicitados (14 días)
$topArticulos = ['labels' => [], 'data' => []];
$sqlTopArt = "SELECT a.descripcion nom, SUM(sd.cant_solicitada) total
              FROM alm_solicitudes s
              JOIN alm_solicitudes_detalle sd ON s.id_solicitud = sd.id_solicitud
              JOIN alm_inventario a ON sd.id_articulo = a.id_articulo
              WHERE s.fecha >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
              GROUP BY a.descripcion
              ORDER BY total DESC
              LIMIT 10";
if ($res = $mysqli->query($sqlTopArt)) {
  while ($row = $res->fetch_assoc()) {
    $topArticulos['labels'][] = $row['nom'];
    $topArticulos['data'][] = (int) $row['total'];
  }
}

// 3) Tabla: solicitudes últimos 7 días
$solicitudes7 = [];
$sqlSol7 = "SELECT s.id_solicitud, s.solicitud, s.fecha, s.division, s.fecha_aprobacion, s.fecha_despacho, s.status,
                   COALESCE(jd.descripcion, CONCAT('Div ', s.division)) division_nombre
            FROM alm_solicitudes s
            LEFT JOIN z_jefes_detalle jd ON jd.division = s.division
            WHERE s.fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            ORDER BY s.fecha DESC, s.id_solicitud DESC
            LIMIT 20";
if ($res = $mysqli->query($sqlSol7)) {
  while ($row = $res->fetch_assoc()) {
    $solicitudes7[] = $row;
  }
}

// 4) Lista: últimos artículos creados en inventario
$articulosRecientes = [];
$sqlArtRec = "SELECT id_articulo, descripcion, unidad, precio
              FROM alm_inventario
              ORDER BY id_articulo DESC
              LIMIT 20";
if ($res = $mysqli->query($sqlArtRec)) {
  while ($row = $res->fetch_assoc()) {
    $articulosRecientes[] = $row;
  }
}

// Bienes
// 5) Tabla: movimientos (reasignaciones) últimos 7 días
$movBienes7 = [];
$movBienesAviso = '';
$movBienesSQLDebug = '';
$movBienesSQLDebugFallback = '';
// Soportar distintos formatos de fecha almacenados como texto o date/datetime
$fechaExpr = "COALESCE(STR_TO_DATE(r.fecha,'%Y-%m-%d'), STR_TO_DATE(r.fecha,'%d/%m/%Y'), STR_TO_DATE(r.fecha,'%Y/%m/%d'), STR_TO_DATE(r.fecha,'%d-%m-%Y'))";
// Basado en la consulta propuesta: unimos áreas y jefaturas para obtener nombres de divisiones, y contamos items por reasignación
$sqlMov7 = "SELECT r.id_reasignacion,
                   r.numero,
                   r.fecha,
                   z_jefes_actual.descripcion AS div_actual,
                   z_jefes_destino.descripcion AS div_destino,
                   COUNT(DISTINCT d.id_bien) AS items
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
$movBienesSQLDebug = $sqlMov7;
if ($res = $mysqli->query($sqlMov7)) {
  while ($row = $res->fetch_assoc()) {
    $movBienes7[] = $row;
  }
}
// Fallback: si no hay movimientos en los últimos 7 días, mostrar últimos 20 sin filtro y avisar
if (empty($movBienes7)) {
  $movBienesAviso = 'No hay movimientos en los últimos 7 días; mostrando últimos 20 movimientos.';
  $sqlMovAll = "SELECT r.id_reasignacion,
                       r.numero,
                       r.fecha,
                       z_jefes_actual.descripcion AS div_actual,
                       z_jefes_destino.descripcion AS div_destino,
                       COUNT(DISTINCT d.id_bien) AS items
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
  $movBienesSQLDebugFallback = $sqlMovAll;
  if ($res2 = $mysqli->query($sqlMovAll)) {
    while ($row = $res2->fetch_assoc()) {
      $movBienes7[] = $row;
    }
  }
}

// 6) Lista: últimos bienes cargados
$bienesRecientes = [];
$sqlBienRec = "SELECT id_bien, numero_bien, descripcion_bien, valor, fecha
               FROM bn_bienes
               WHERE borrado = 0
               ORDER BY id_bien DESC
               LIMIT 20";
if ($res = $mysqli->query($sqlBienRec)) {
  while ($row = $res->fetch_assoc()) {
    $bienesRecientes[] = $row;
  }
}
?>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="X-UA-Compatible" content="IE=9" />
  <?php
  include "../funciones/headNew.php";
  ?>
  <!-- Chart.js y DataLabels se cargan globalmente desde funciones/footNew.php (versión local) -->
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

    /* Resaltar títulos de tarjetas */
    .card-header .card-title {
      font-weight: 700;
      color: #dc3545;
      /* Rojo principal */
      position: relative;
    }

    .card-header .card-title::after {
      content: '';
      display: block;
      height: 3px;
      width: 140px;
      margin-top: 4px;
      border-radius: 2px;
      background: linear-gradient(90deg, #dc3545, #bd2130);
      /* degradado rojo */
    }

    /* Más separación entre iconos y títulos */
    .card-header i,
    h5 i {
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

    /* Resaltar títulos de secciones (h5) */
    h5.mb-0 {
      font-weight: 700;
      color: #dc3545;
      border-left: 4px solid #dc3545;
      padding-left: 10px;
      line-height: 1.2;
      display: inline-flex;
      align-items: center;
    }

    .table td,
    .table th {
      vertical-align: middle;
    }

    /* Fallback de colores para badges (compatibilidad BS4/BS5) */
    .badge.badge-primary {
      background-color: #007bff;
      color: #fff;
    }

    .badge.badge-success {
      background-color: #28a745;
      color: #fff;
    }

    .badge.badge-danger {
      background-color: #dc3545;
      color: #fff;
    }

    .badge.badge-warning {
      background-color: #ffc107;
      color: #212529;
    }

    .badge.badge-secondary {
      background-color: #6c757d;
      color: #fff;
    }

    /* Alinear los botones a la derecha siempre */
    .card-header .card-tools {
      margin-left: auto;
      display: flex;
      align-items: center;
    }

    /* Leyenda HTML del doughnut */
    #legendTopArt .legend-item {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: .92rem;
      padding: 2px 0;
      line-height: 1.2;
      word-break: break-word;
      white-space: normal;
    }

    #legendTopArt .legend-color {
      width: 12px;
      height: 12px;
      border-radius: 3px;
      flex: 0 0 12px;
    }
  </style>
</head>

<body style="background: transparent !important;">

  <script>
    // Mensaje de bienvenida: ocultar dashboard hasta cerrar
    document.addEventListener('DOMContentLoaded', function () {
      window.__dashboardGreetingClosed = false;
      Swal.fire({
        title: '<?php echo $saludo; ?>',
        html: '<?php echo ucwords(strtolower(addslashes($_SESSION['NOM_USUARIO']))); ?> <br><strong>¡Bienvenido al Dashboard De Administración!</strong><br><br><i>"<?php echo addslashes($frase_aleatoria); ?>"</i>',
        imageUrl: '/imagenes/funcionarios/<?php echo $_SESSION['CEDULA_USUARIO']; ?>.png',
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

          // Fallback a imagen por defecto si no existe la foto
          const img = document.querySelector('.swal2-image');
          if (img) {
            img.onerror = function () {
              this.onerror = null;
              this.src = '/imagenes/funcionarios/default.png';
            };
          }


          // Cierre robusto del modal (soporta BS4 y BS5)
          function hideDetalleModal() {
            var modalEl = document.getElementById('modalDetalleSolicitud');
            if (!modalEl) return;
            try {
              if (window.bootstrap && bootstrap.Modal) {
                var inst = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                inst.hide();
                return;
              }
            } catch (e) { /* noop */ }
            if (window.jQuery && $.fn && $.fn.modal) {
              $('#modalDetalleSolicitud').modal('hide');
            }
          }
          // Botón X (header) y botón Cerrar (footer)
          $(document).on('click', '#modalDetalleSolicitud .close, #modalDetalleSolicitud [data-dismiss="modal"], #modalDetalleSolicitud [data-bs-dismiss="modal"]', function (e) {
            e.preventDefault();
            hideDetalleModal();
          });
          // Tecla ESC
          $(document).on('keydown', function (e) {
            if (e.key === 'Escape' || e.keyCode === 27) {
              hideDetalleModal();
            }
          });
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
          // Mostrar dashboard y notificar inicialización
          var root = document.getElementById('dashboard-root');
          if (root) root.style.display = '';
          window.__dashboardGreetingClosed = true;
          try {
            if (typeof window.initCharts === 'function') {
              window.initCharts();
            } else {
              document.dispatchEvent(new CustomEvent('dashboard:init'));
            }
          } catch (e) { /* noop */ }
        }
      });
    });
  </script>

  <div class="container-fluid py-3" id="dashboard-root" style="display:none;">
    <!-- Sección Almacén -->
    <div class="row mb-2">
      <div class="col-12">
        <h5 class="mb-0"><i class="fas fa-warehouse mr-2"></i>Almacén</h5>
      </div>
    </div>

    <div class="row g-3 align-items-start">
      <div class="col-12 col-lg-7">
        <div class="card">
          <div class="card-header d-flex align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-chart-bar mr-2"></i>Divisiones con más solicitudes (últimos 14
              días)</h3>
            <div class="card-tools ml-auto">
              <button type="button" class="btn btn-tool js-card-collapse" title="Colapsar"><i
                  class="fas fa-minus"></i></button>
              <button type="button" class="btn btn-tool js-card-remove" title="Cerrar"><i
                  class="fas fa-times"></i></button>
            </div>
          </div>
          <div class="card-body" style="height: 340px;">
            <canvas id="chartDivisiones"></canvas>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-5">
        <div class="card">
          <div class="card-header d-flex align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-chart-pie mr-2"></i>Artículos más solicitados (14 días)</h3>
            <div class="card-tools ml-auto">
              <button type="button" class="btn btn-tool js-card-collapse" title="Colapsar"><i
                  class="fas fa-minus"></i></button>
              <button type="button" class="btn btn-tool js-card-remove" title="Cerrar"><i
                  class="fas fa-times"></i></button>
            </div>
          </div>
          <div class="card-body" style="height: 340px; display:flex; gap:12px;" id="card-articulos">
            <div style="flex:1 1 auto; position:relative; min-width:0;">
              <canvas id="chartTopArt"></canvas>
            </div>
            <div id="legendTopArt" style="flex:0 0 42%; overflow:auto; max-height:100%;"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3 mt-1 align-items-start">
      <div class="col-12 col-xl-8">
        <div class="card">
          <div class="card-header d-flex align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-list mr-2"></i>Solicitudes (últimos 7 días)</h3>
            <div class="card-tools ml-auto">
              <button type="button" class="btn btn-tool js-card-collapse" title="Colapsar"><i
                  class="fas fa-minus"></i></button>
              <button type="button" class="btn btn-tool js-card-remove" title="Cerrar"><i
                  class="fas fa-times"></i></button>
            </div>
          </div>
          <div class="card-body p-0">
            <?php if (!empty($movBienesAviso)) { ?>
              <div class="px-3 pt-2 text-muted small"><?php echo htmlspecialchars($movBienesAviso); ?></div>
            <?php } ?>
            <?php if (isset($_GET['debug_sql'])) { ?>
              <div class="px-3 pt-2">
                <details open>
                  <summary class="small text-danger">SQL Movimientos (debug)</summary>
                  <div class="small text-muted">Consulta principal:</div>
                  <pre class="small mb-2"
                    style="white-space:pre-wrap; word-break:break-all; max-height:180px; overflow:auto; background:#f8f9fa; padding:6px; border-radius:4px;"><?php echo htmlspecialchars($movBienesSQLDebug); ?></pre>
                  <?php if (!empty($movBienesSQLDebugFallback)) { ?>
                    <div class="small text-muted">Fallback aplicado (últimos 20 sin filtro):</div>
                    <pre class="small mb-2"
                      style="white-space:pre-wrap; word-break:break-all; max-height:180px; overflow:auto; background:#fff3cd; padding:6px; border-radius:4px;"><?php echo htmlspecialchars($movBienesSQLDebugFallback); ?></pre>
                  <?php } ?>
                  <div class="small text-muted">Registros resultantes: <?php echo (int) count($movBienes7); ?></div>
                </details>
              </div>
            <?php } ?>
            <div class="table-responsive">
              <table class="table table-striped table-hover mb-0" id="tabla-solicitudes">
                <thead class="table-light">
                  <tr>
                    <th style="width:100px;">Solicitud</th>
                    <th>Fecha</th>
                    <th>División</th>
                    <th>Fecha Aprobación</th>
                    <th>Fecha Despacho</th>
                    <th style="width:120px;">Estatus</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($solicitudes7)) { ?>
                    <tr>
                      <td colspan="6" class="text-center text-muted py-4">Sin registros en los últimos 7 días</td>
                    </tr>
                  <?php } else {
                    foreach ($solicitudes7 as $s) { ?>
                      <tr class="solicitud-row" data-id="<?php echo (int) $s['id_solicitud']; ?>"
                        title="Ver detalle de la solicitud">
                        <td>#<?php echo htmlspecialchars($s['solicitud'] ?: $s['id_solicitud']); ?></td>
                        <td><?php echo htmlspecialchars(voltea_fecha($s['fecha'])); ?></td>
                        <td><?php echo htmlspecialchars($s['division_nombre']); ?></td>
                        <td>
                          <?php echo $s['fecha_aprobacion'] ? htmlspecialchars(voltea_fecha($s['fecha_aprobacion'])) : '-'; ?>
                        </td>
                        <td><?php echo $s['fecha_despacho'] ? htmlspecialchars(voltea_fecha($s['fecha_despacho'])) : '-'; ?>
                        </td>
                        <td>
                          <?php
                          $status = (int) $s['status'];
                          $badge = 'secondary';
                          $txt = 'Desconocido';
                          if ($status === 0) {
                            $badge = 'warning';
                            $txt = 'Creada';
                          } else if ($status === 5) {
                            $badge = 'primary';
                            $txt = 'Aprobada';
                          } else if ($status === 10) {
                            $badge = 'success';
                            $txt = 'Despachada';
                          } else if ($status === 99) {
                            $badge = 'danger';
                            $txt = 'Anulada';
                          }
                          ?>
                          <span class="badge badge-<?php echo $badge; ?>"><?php echo $txt; ?></span>
                        </td>
                      </tr>
                    <?php }
                  } ?>
                </tbody>
              </table>
            </div>
            <div class="p-2 text-center" id="solicitudes-toggle-wrapper" style="display:none;">
              <button class="btn btn-sm btn-outline-primary" id="solicitudes-toggle">Mostrar más</button>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-xl-4">
        <div class="card">
          <div class="card-header d-flex align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-plus-square mr-2"></i>Últimos artículos creados</h3>
            <div class="card-tools ml-auto">
              <button type="button" class="btn btn-tool js-card-collapse" title="Colapsar"><i
                  class="fas fa-minus"></i></button>
              <button type="button" class="btn btn-tool js-card-remove" title="Cerrar"><i
                  class="fas fa-times"></i></button>
            </div>
          </div>
          <div class="card-body">
            <?php if (empty($articulosRecientes)) { ?>
              <div class="text-muted">No hay artículos recientes.</div>
            <?php } else {
              foreach ($articulosRecientes as $a) { ?>
                <div class="d-flex align-items-center mb-3 product-list">
                  <div class="product-img mr-3"><i class="fas fa-box"></i></div>
                  <div class="flex-grow-1">
                    <div class="product-title mb-1"><?php echo htmlspecialchars($a['descripcion']); ?></div>
                    <div class="small text-muted">Unidad: <?php echo htmlspecialchars($a['unidad']); ?> · Precio:
                      <?php echo number_format((float) $a['precio'], 2, ',', '.'); ?>
                    </div>
                  </div>
                </div>
              <?php }
            } ?>
            <div class="text-center mt-2" id="articulos-toggle-wrapper" style="display:none;">
              <button class="btn btn-sm btn-outline-primary" id="articulos-toggle">Mostrar más</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Sección Bienes -->
    <div class="row mt-4 mb-2">
      <div class="col-12">
        <h5 class="mb-0"><i class="fas fa-briefcase mr-2"></i>Bienes Nacionales</h5>
      </div>
    </div>

    <div class="row g-3 align-items-start">
      <div class="col-12 col-xl-8">
        <div class="card">
          <div class="card-header d-flex align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-exchange-alt mr-2"></i>Movimientos (últimos 7 días)</h3>
            <div class="card-tools ml-auto">
              <button type="button" class="btn btn-tool js-card-collapse" title="Colapsar"><i
                  class="fas fa-minus"></i></button>
              <button type="button" class="btn btn-tool js-card-remove" title="Cerrar"><i
                  class="fas fa-times"></i></button>
            </div>
          </div>
          <div class="card-body p-0">
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
                        title="Ver detalle del movimiento">
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
          <div class="card-header d-flex align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-plus-square mr-2"></i>Últimos bienes cargados</h3>
            <div class="card-tools ml-auto">
              <button type="button" class="btn btn-tool js-card-collapse" title="Colapsar"><i
                  class="fas fa-minus"></i></button>
              <button type="button" class="btn btn-tool js-card-remove" title="Cerrar"><i
                  class="fas fa-times"></i></button>
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

  <!-- Modal: Detalle de Solicitud -->
  <div class="modal fade" id="modalDetalleSolicitud" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title m-0"><i class="fas fa-clipboard-list mr-2"></i>Detalle de Solicitud</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="text-center p-4 text-muted">
            <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
            <div>Cargando detalle...</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal"><i
              class="fas fa-times mr-1"></i>Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal: Detalle de Movimiento -->
  <div class="modal fade" id="modalDetalleMovimiento" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title m-0"><i class="fas fa-exchange-alt mr-2"></i>Detalle de Movimiento</h5>
          <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="text-center p-4 text-muted">
            <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
            <div>Cargando detalle...</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal"><i
              class="fas fa-times mr-1"></i>Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Construcción de gráficos con Chart.js (diferida hasta cerrar el saludo)
    (function () {
      window.__chartsInitialized = false;
      window.initCharts = function () {
        if (window.__chartsInitialized) return;
        window.__chartsInitialized = true;
        const divResumen = <?php echo json_encode($divisionesResumen, JSON_UNESCAPED_UNICODE); ?>;
        const topArt = <?php echo json_encode($topArticulos, JSON_UNESCAPED_UNICODE); ?>;

        // Paleta de colores
        const palette = ['#007bff', '#28a745', '#fd7e14', '#6f42c1', '#20c997', '#17a2b8', '#dc3545'];
        const rgba = (hex, a) => {
          const ctx = document.createElement('canvas').getContext('2d');
          ctx.fillStyle = hex; const c = ctx.fillStyle; const m = c.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)/i);
          return m ? `rgba(${m[1]}, ${m[2]}, ${m[3]}, ${a})` : hex;
        };

        // Registrar plugin de datalabels si está disponible
        if (window.ChartDataLabels) {
          Chart.register(ChartDataLabels);
        }

        // Helpers para legibilidad de etiquetas
        function wrapText(text, maxLen) {
          if (text == null) return '';
          const s = String(text);
          if (s.length <= maxLen) return s;
          const words = s.split(' ');
          const lines = [];
          let line = '';
          for (const w of words) {
            if ((line + ' ' + w).trim().length <= maxLen) {
              line = (line ? line + ' ' : '') + w;
            } else {
              if (line) lines.push(line);
              // Si una palabra es muy larga, cortarla con guion suave
              if (w.length > maxLen) {
                let chunk = '';
                for (let i = 0; i < w.length; i += maxLen) {
                  lines.push(w.slice(i, i + maxLen));
                }
                line = '';
              } else {
                line = w;
              }
            }
          }
          if (line) lines.push(line);
          return lines;
        }

        // Quitar prefijo común "División de " (con o sin tilde), al inicio del texto
        function sanitizeDivisionLabel(s) {
          if (s == null) return '';
          const cleaned = String(s).replace(/^\s*(divisi[oó]n\s+de\s+)/i, '');
          return cleaned.trim();
        }

        // Title Case simple con excepciones comunes en español
        function toTitleCaseEs(str) {
          if (!str) return '';
          const lower = String(str).toLowerCase();
          const exceptions = ['de', 'del', 'la', 'las', 'el', 'los', 'y', 'o', 'u', 'en', 'para', 'por', 'a'];
          return lower.split(/\s+/).map((w, i) => {
            if (i > 0 && exceptions.includes(w)) return w;
            return w.charAt(0).toUpperCase() + w.slice(1);
          }).join(' ');
        }

        // Config: barras verticales (eje X categórico, eje Y numérico)
        const divLabels = Array.isArray(divResumen.labels) ? divResumen.labels : [];
        const sanitizedLabels = divLabels.map(sanitizeDivisionLabel).map(toTitleCaseEs);
        const countDivs = divLabels.length;
        const isHorizontal = false;

        // Ajustar altura del contenedor en función del número de divisiones
        const divCanvas = document.getElementById('chartDivisiones');
        if (divCanvas && divCanvas.parentElement) {
          const base = isHorizontal ? 60 : 340; // base para horizontal vs vertical
          const per = isHorizontal ? 26 : 0; // px por etiqueta adicional en horizontal
          const h = Math.min(700, Math.max(base, base + per * countDivs));
          divCanvas.parentElement.style.height = h + 'px';
        }

        // Gráfico de barras: División vs Cantidad de solicitudes (14 días)
        const catAxis = 'x';
        const valAxis = 'y';
        new Chart(divCanvas, {
          type: 'bar',
          data: {
            labels: sanitizedLabels,
            datasets: [{
              label: 'Solicitudes',
              data: divResumen.data,
              backgroundColor: divResumen.data.map((_, i) => rgba(palette[i % palette.length], 0.6)),
              borderColor: divResumen.data.map((_, i) => palette[i % palette.length]),
              borderWidth: 1
            }]
          },
          options: {
            indexAxis: isHorizontal ? 'y' : 'x',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: { display: false },
              tooltip: {
                enabled: true,
                callbacks: {
                  title: (items) => {
                    // Mostrar el label completo en el tooltip
                    const i = items && items.length ? items[0].dataIndex : -1;
                    return i >= 0 ? String(sanitizedLabels[i] ?? '') : '';
                  }
                }
              },
              datalabels: window.ChartDataLabels ? {
                anchor: isHorizontal ? 'end' : 'end',
                align: isHorizontal ? 'right' : 'top',
                color: '#111',
                font: { weight: '600', size: 11 },
                formatter: (v) => v
              } : undefined
            },
            scales: {
              [catAxis]: {
                ticks: {
                  autoSkip: false,
                  maxRotation: 0,
                  minRotation: 0,
                  font: { size: 11 },
                  callback: function (val, idx) {
                    const full = (this && this.getLabelForValue) ? this.getLabelForValue(val) : (sanitizedLabels[idx] ?? val);
                    // Envolver etiquetas: más ancho permitido si es horizontal
                    const maxLen = isHorizontal ? 24 : 14;
                    const wrapped = wrapText(full, maxLen);
                    return wrapped;
                  }
                }
              },
              [valAxis]: {
                beginAtZero: true,
                ticks: { precision: 0 }
              }
            }
          }
        });

        // Torta de artículos más solicitados (leyenda con cantidad y % en el gráfico)
        // Normalizar labels de artículos a Title Case para mejor lectura
        topArt.labels = (topArt.labels || []).map(toTitleCaseEs);
        const countArt = (topArt.labels || []).length;
        // Generar paleta para N segmentos
        function genColors(n) {
          const arr = [];
          for (let i = 0; i < n; i++) {
            const hue = Math.round(360 * i / Math.max(1, n));
            arr.push(`hsl(${hue}, 65%, 55%)`);
          }
          return arr;
        }
        const dynamicColors = genColors(countArt);
        const chartTopCtx = document.getElementById('chartTopArt');
        const chartTop = new Chart(chartTopCtx, {
          type: 'doughnut',
          data: {
            labels: topArt.labels,
            datasets: [{ data: topArt.data, backgroundColor: dynamicColors }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: { display: false },
              tooltip: {
                callbacks: {
                  label: (ctx) => {
                    const data = ctx.dataset.data || [];
                    const total = data.reduce((a, b) => a + (Number(b) || 0), 0) || 0;
                    const val = Number(ctx.raw) || 0;
                    const pct = total ? (val * 100 / total) : 0;
                    return ` ${ctx.label}: ${val} (${pct.toFixed(1)}%)`;
                  }
                }
              },
              datalabels: countArt > 12 ? { display: false } : {
                formatter: (value, ctx) => {
                  const data = ctx.chart.data.datasets[0]?.data || [];
                  const total = data.reduce((a, b) => a + (Number(b) || 0), 0) || 0;
                  if (!total) return '';
                  const pct = value * 100 / total;
                  return `${pct.toFixed(1)}%`;
                },
                color: '#343a40',
                font: { weight: '600', size: 12 },
                anchor: 'center',
                align: 'center'
              }
            }
          }
        });

        // Leyenda HTML a la derecha con wrap y scroll
        (function renderTopLegend() {
          const labels = chartTop.data.labels || [];
          const data = chartTop.data.datasets[0]?.data || [];
          const colors = chartTop.data.datasets[0]?.backgroundColor || [];
          const $legend = document.getElementById('legendTopArt');
          if (!$legend) return;
          const frag = document.createDocumentFragment();
          labels.forEach((lbl, i) => {
            const item = document.createElement('div');
            item.className = 'legend-item';
            const color = document.createElement('span');
            color.className = 'legend-color';
            color.style.backgroundColor = colors[i] || '#999';
            const text = document.createElement('span');
            text.textContent = `${lbl} (${data[i] ?? 0})`;
            item.appendChild(color);
            item.appendChild(text);
            frag.appendChild(item);
          });
          $legend.innerHTML = '';
          $legend.appendChild(frag);
        })();
      };

      // Si el saludo ya se cerró antes de registrar esta función, inicializar
      if (window.__dashboardGreetingClosed) {
        try { window.initCharts(); } catch (e) { /* noop */ }
      } else {
        document.addEventListener('dashboard:init', function () {
          try { window.initCharts(); } catch (e) { /* noop */ }
        }, { once: true });
      }
    })();
  </script>

  <?php
  include "../funciones/footNew.php";
  ?>
  <script>
    // UX: hacer filas clicables para ver detalle de la solicitud
    (function () {
      const $doc = $(document);
      // Estilo de cursor y hover
      const style = document.createElement('style');
      style.innerHTML = 'tr.solicitud-row{cursor:pointer;} tr.solicitud-row:hover{background:#f8f9fa;} .row-hidden{display:none;} .item-hidden{display:none !important;}';
      document.head.appendChild(style);

      // Mostrar más/menos: colapsar después de N filas
      const MAX_ROWS = 8;
      function setupShowMore() {
        const $rows = $('#tabla-solicitudes tbody tr');
        if ($rows.length > MAX_ROWS) {
          $rows.each(function (i) { if (i >= MAX_ROWS) $(this).addClass('row-hidden'); });
          $('#solicitudes-toggle-wrapper').show();
        } else {
          $('#solicitudes-toggle-wrapper').hide();
        }
      }
      // Inicializar al cargar
      setupShowMore();
      // Delegar botón
      $(document).on('click', '#solicitudes-toggle', function () {
        const $btn = $(this);
        const $hidden = $('#tabla-solicitudes tbody tr.row-hidden');
        if ($hidden.length) {
          $('#tabla-solicitudes tbody tr').removeClass('row-hidden');
          $btn.text('Mostrar menos');
        } else {
          // Reaplicar colapso
          const $rows = $('#tabla-solicitudes tbody tr');
          $rows.each(function (i) { if (i >= MAX_ROWS) $(this).addClass('row-hidden'); });
          $btn.text('Mostrar más');
          // Scroll a la tabla
          document.getElementById('tabla-solicitudes').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });

      // Mostrar más/menos: tabla Movimientos (últimos 7 días)
      const MAX_MOV_ROWS = 8;
      (function setupMovimientos() {
        const $rows = $('#tabla-movimientos tbody tr');
        if ($rows.length > MAX_MOV_ROWS) {
          $rows.each(function (i) { if (i >= MAX_MOV_ROWS) $(this).addClass('row-hidden'); });
          $('#movimientos-toggle-wrapper').show();
        }
        $(document).on('click', '#movimientos-toggle', function () {
          const $btn = $(this);
          const $rows = $('#tabla-movimientos tbody tr');
          const hidden = $rows.filter('.row-hidden').length > 0;
          if (hidden) {
            $rows.removeClass('row-hidden');
            $btn.text('Mostrar menos');
          } else {
            $rows.each(function (i) { if (i >= MAX_MOV_ROWS) $(this).addClass('row-hidden'); });
            $btn.text('Mostrar más');
          }
        });
      })();

      // Mostrar más/menos: lista Artículos
      const MAX_ART_ITEMS = 5;
      (function setupArticulos() {
        const init = () => {
          const $items = $('#articulos-toggle-wrapper').closest('.card-body').find('.product-list');
          if ($items.length > MAX_ART_ITEMS) {
            $items.each(function (i) { if (i >= MAX_ART_ITEMS) $(this).addClass('item-hidden'); });
            $('#articulos-toggle-wrapper').show();
          } else {
            $('#articulos-toggle-wrapper').hide();
          }
        };
        init();
        $(document).on('click', '#articulos-toggle', function () {
          const $btn = $(this);
          const $items = $('#articulos-toggle-wrapper').closest('.card-body').find('.product-list');
          const hidden = $items.filter('.item-hidden').length > 0;
          if (hidden) {
            $items.removeClass('item-hidden');
            $btn.text('Mostrar menos');
          } else {
            $items.each(function (i) { if (i >= MAX_ART_ITEMS) $(this).addClass('item-hidden'); });
            $btn.text('Mostrar más');
          }
        });
      })();

      // Mostrar más/menos: lista Bienes
      const MAX_BIEN_ITEMS = 5;
      (function setupBienes() {
        const init = () => {
          const $items = $('#bienes-toggle-wrapper').closest('.card-body').find('.product-list');
          if ($items.length > MAX_BIEN_ITEMS) {
            $items.each(function (i) { if (i >= MAX_BIEN_ITEMS) $(this).addClass('item-hidden'); });
            $('#bienes-toggle-wrapper').show();
          } else {
            $('#bienes-toggle-wrapper').hide();
          }
        };
        init();
        $(document).on('click', '#bienes-toggle', function () {
          const $btn = $(this);
          const $items = $('#bienes-toggle-wrapper').closest('.card-body').find('.product-list');
          const hidden = $items.filter('.item-hidden').length > 0;
          if (hidden) {
            $items.removeClass('item-hidden');
            $btn.text('Mostrar menos');
          } else {
            $items.each(function (i) { if (i >= MAX_BIEN_ITEMS) $(this).addClass('item-hidden'); });
            $btn.text('Mostrar más');
          }
        });
      })();

      $doc.on('click', 'tr.solicitud-row', function (e) {
        // Evitar conflicto si en el futuro hay enlaces/botones dentro
        if ($(e.target).closest('a, button, .btn, .custom-control, input, label').length) return;
        const id = $(this).data('id');
        if (!id) return;
        const $modal = $('#modalDetalleSolicitud');
        const $body = $modal.find('.modal-body');
        // Spinner inicial
        $body.html('<div class="text-center p-4 text-muted"><i class="fas fa-spinner fa-spin fa-2x mb-2"></i><div>Cargando detalle...</div></div>');
        $modal.modal('show');
        $.get('solicitud_detalle.php', { solicitud: id })
          .done(function (html) { $body.html(html); })
          .fail(function () { $body.html('<div class="alert alert-danger">No se pudo cargar el detalle. Intente nuevamente.</div>'); });
      });

      // Click en movimiento: abrir detalle
      $doc.on('click', 'tr.mov-row', function (e) {
        if ($(e.target).closest('a, button, .btn, .custom-control, input, label').length) return;
        const id = $(this).data('id');
        if (!id) return;
        const $modal = $('#modalDetalleMovimiento');
        const $body = $modal.find('.modal-body');
        $body.html('<div class="text-center p-4 text-muted"><i class="fas fa-spinner fa-spin fa-2x mb-2"></i><div>Cargando detalle...</div></div>');
        $modal.modal('show');
        $.get('movimiento_detalle.php', { reasignacion: id })
          .done(function (html) { $body.html(html); })
          .fail(function () { $body.html('<div class="alert alert-danger">No se pudo cargar el detalle del movimiento. Intente nuevamente.</div>'); });
      });
    })();

    // Control propio de colapsar/cerrar para evitar doble disparo con AdminLTE
    $(function () {
      // Si AdminLTE está presente, dejamos su comportamiento por defecto
      var adminLTEPresent = typeof $.fn.CardWidget !== 'undefined';

      if (!adminLTEPresent) {
        $(document).on('click', '.js-card-collapse', function (e) {
          e.preventDefault();
          var $btn = $(this);
          var $card = $btn.closest('.card');
          var $sections = $card.children('.card-body, .card-footer');
          var isCollapsed = $card.hasClass('collapsed-card');
          if (isCollapsed) {
            $sections.slideDown(150);
          } else {
            $sections.slideUp(150);
          }
          $card.toggleClass('collapsed-card');
          $btn.find('i').toggleClass('fa-minus fa-plus');
        });
        $(document).on('click', '.js-card-remove', function (e) {
          e.preventDefault();
          var $card = $(this).closest('.card');
          $card.slideUp(150, function () { $(this).remove(); });
        });
      }

      // Nota: ya no ocultamos columnas completas al colapsar, para evitar que el '-' actúe como 'X'.
    });

    // Cierre robusto para modales (BS4/BS5): Solicitud y Movimiento
    (function () {
      function hideModalById(id) {
        var modalEl = document.getElementById(id);
        if (!modalEl) return;
        try {
          if (window.bootstrap && bootstrap.Modal) {
            var inst = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            inst.hide();
            return;
          }
        } catch (e) { /* noop */ }
        if (window.jQuery && $.fn && $.fn.modal) {
          $(modalEl).modal('hide');
        }
      }

      // Click en X y botones con data-dismiss/data-bs-dismiss
      $(document).on('click', '#modalDetalleMovimiento .close, #modalDetalleMovimiento [data-dismiss="modal"], #modalDetalleMovimiento [data-bs-dismiss="modal"]', function (e) {
        e.preventDefault();
        hideModalById('modalDetalleMovimiento');
      });
      $(document).on('click', '#modalDetalleSolicitud .close, #modalDetalleSolicitud [data-dismiss="modal"], #modalDetalleSolicitud [data-bs-dismiss="modal"]', function (e) {
        e.preventDefault();
        hideModalById('modalDetalleSolicitud');
      });

      // Tecla ESC: cerrar cualquier modal abierto (ambos)
      $(document).on('keydown', function (e) {
        if (e.key === 'Escape' || e.keyCode === 27) {
          hideModalById('modalDetalleMovimiento');
          hideModalById('modalDetalleSolicitud');
        }
      });
    })();
  </script>
</body>

</html>