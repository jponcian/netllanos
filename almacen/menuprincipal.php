<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";
include_once __DIR__ . '/../frases.php';
// include_once __DIR__ . '/../config.php';
$_SESSION['NOMBRE_MODULO'] = 'ALMACEN';

if ($_SESSION['VERIFICADO'] != "SI") {
  header("Location: index.php?errorusuario=val");
  exit();
}

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
// Verificar privilegios especiales: admin o roles 19/20 en z_empleados_roles
$esAdmin = !empty($_SESSION['ADMINISTRADOR']) && intval($_SESSION['ADMINISTRADOR']) > 0;
$cedulaUsuario = isset($_SESSION['CEDULA_USUARIO']) ? intval($_SESSION['CEDULA_USUARIO']) : 0;
$tieneRolEspecial = false;
if ($cedulaUsuario > 0 && isset($_SESSION['conexionsqli']) && $_SESSION['conexionsqli'] instanceof mysqli) {
  if ($stmt = $_SESSION['conexionsqli']->prepare("SELECT COUNT(*) AS cnt FROM z_empleados_roles WHERE cedula = ? AND rol IN (19,20)")) {
    $stmt->bind_param('i', $cedulaUsuario);
    if ($stmt->execute()) {
      $stmt->bind_result($cnt);
      if ($stmt->fetch()) {
        $tieneRolEspecial = intval($cnt) > 0;
      }
    }
    $stmt->close();
  }
}
$mostrarAlertaPriv = $esAdmin || $tieneRolEspecial;
$mensajePriv = $esAdmin
  ? 'Tienes privilegios de Administrador en el sistema.'
  : ($tieneRolEspecial ? 'Posees roles especiales (19 o 20) asignados en el sistema.' : '');

// Si tiene privilegios, preparar datos de resumen de Almacén (similar a jefaturas)
$divisionesResumen = ['labels' => [], 'data' => []];
$topArticulos = ['labels' => [], 'data' => []];
$solicitudes7 = [];
$articulosRecientes = [];
if ($mostrarAlertaPriv && isset($_SESSION['conexionsqli']) && $_SESSION['conexionsqli'] instanceof mysqli) {
  $mysqli = $_SESSION['conexionsqli'];
  // 1) Divisiones con más solicitudes (últimos 14 días)
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
    $res->free();
  }

  // 2) Artículos más solicitados (14 días)
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
    $res->free();
  }

  // 3) Tabla: solicitudes últimos 7 días
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
    $res->free();
  }

  // 4) Lista: últimos artículos creados en inventario
  $sqlArtRec = "SELECT id_articulo, descripcion, unidad, precio
                FROM alm_inventario
                ORDER BY id_articulo DESC
                LIMIT 20";
  if ($res = $mysqli->query($sqlArtRec)) {
    while ($row = $res->fetch_assoc()) {
      $articulosRecientes[] = $row;
    }
    $res->free();
  }
}
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

    /* Estilos de tarjetas/resumen similares a jefaturas */
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
      color: #dc3545;
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
    }

    .card-header i,
    h5 i {
      margin-right: .65rem !important;
    }

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

    .badge.badge-info {
      background-color: #17a2b8;
      color: #fff;
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

    /* Helpers para ocultar extras y toggles */
    .row-hidden {
      display: none;
    }

    .item-hidden {
      display: none !important;
    }

    /* Lista de artículos (igual que jefaturas) */
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
  </style>
</head>

<body style="background: transparent !important;">

  <script>
    // Mensaje de bienvenida espectacular
    document.addEventListener('DOMContentLoaded', function () {
      Swal.fire({
        title: '<?php echo $saludo; ?>',
        html: '<?php echo ucwords(strtolower(addslashes($_SESSION['NOM_USUARIO']))); ?> <br><strong>¡Bienvenido al Nuevo Módulo de Almacén!</strong><br><br><i>"<?php echo addslashes($frase_aleatoria); ?>"</i>',
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
              accion: 'mostrarMenuAlmacen',
              animar: true
            }, '*');
          }
          // Mostrar directamente el resumen de Almacén si aplica
          const tienePrivilegios = <?php echo $mostrarAlertaPriv ? 'true' : 'false'; ?>;
          const mostrarResumen = () => {
            const root = document.getElementById('almacen-resumen-root');
            if (root) root.style.display = '';
            if (typeof window.initAlmacenCharts === 'function') {
              try { window.initAlmacenCharts(); } catch (e) { /* noop */ }
            }
            // Inicializar toggles de Mostrar más/menos
            try {
              // Solicitudes: ocultar >5 por defecto (ya vienen con clase); mostrar botón si hay más
              (function () {
                const table = document.getElementById('tabla-solicitudes');
                const wrap = document.getElementById('solicitudes-toggle-wrapper');
                const btn = document.getElementById('solicitudes-toggle');
                if (!table || !wrap || !btn) return;
                const totalRows = table.querySelectorAll('tbody tr').length;
                if (totalRows > 5) { wrap.style.display = ''; }
                btn.addEventListener('click', function () {
                  const anyHidden = table.querySelector('tbody tr.row-hidden') !== null;
                  if (anyHidden) {
                    table.querySelectorAll('tbody tr.row-hidden').forEach(tr => tr.classList.remove('row-hidden'));
                    btn.textContent = 'Mostrar menos';
                  } else {
                    let count = 0;
                    table.querySelectorAll('tbody tr').forEach(tr => {
                      count++;
                      if (count > 5) tr.classList.add('row-hidden'); else tr.classList.remove('row-hidden');
                    });
                    btn.textContent = 'Mostrar más';
                  }
                });
              })();

              // Artículos: ocultar >5; toggle (formato tipo jefaturas)
              (function () {
                const list = document.getElementById('lista-articulos');
                const wrap = document.getElementById('articulos-toggle-wrapper');
                const btn = document.getElementById('articulos-toggle');
                if (!list || !wrap || !btn) return;
                const totalItems = list.querySelectorAll('.product-list').length;
                if (totalItems > 5) { wrap.style.display = ''; }
                btn.addEventListener('click', function () {
                  const anyHidden = list.querySelector('.product-list.item-hidden') !== null;
                  if (anyHidden) {
                    list.querySelectorAll('.product-list.item-hidden').forEach(li => li.classList.remove('item-hidden'));
                    btn.textContent = 'Mostrar menos';
                  } else {
                    let count = 0;
                    list.querySelectorAll('.product-list').forEach(li => {
                      count++;
                      if (count > 5) li.classList.add('item-hidden'); else li.classList.remove('item-hidden');
                    });
                    btn.textContent = 'Mostrar más';
                  }
                });
              })();
            } catch (e) { /* noop */ }
          };
          if (tienePrivilegios) { mostrarResumen(); }
        }
      });
    });
  </script>

  <?php if ($mostrarAlertaPriv): ?>
    <div class="container-fluid py-3" id="almacen-resumen-root" style="display:none;">
      <div class="row mb-2">
        <div class="col-12">
          <h5 class="mb-0"><i class="fas fa-warehouse mr-2"></i>Almacén</h5>
        </div>
      </div>

      <div class="row g-3 align-items-start">
        <div class="col-12 col-lg-7">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <i class="fas fa-chart-bar"></i>
              <span class="card-title">Solicitudes por división (14 días)</span>
            </div>
            <div class="card-body" style="height: 340px;">
              <canvas id="chartDivisiones"></canvas>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-5">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <i class="fas fa-chart-pie"></i>
              <span class="card-title">Top 10 artículos solicitados (14 días)</span>
            </div>
            <div class="card-body" style="height: 340px; display:flex; gap:12px;">
              <canvas id="chartTopArt" style="min-width: 240px;"></canvas>
              <div id="legendTopArt" style="flex:1; overflow:auto; max-height:100%; padding:4px 0 0 6px;"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-3 mt-1 align-items-start">
        <div class="col-12 col-xl-8">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <i class="fas fa-clipboard-list"></i>
              <span class="card-title">Solicitudes recientes (7 días)</span>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-striped table-hover mb-0" id="tabla-solicitudes">
                  <thead class="thead-light">
                    <tr>
                      <th style="width:110px;">Fecha</th>
                      <th>N°</th>
                      <th>División</th>
                      <th style="width:130px;">Aprobación</th>
                      <th style="width:130px;">Despacho</th>
                      <th>Estatus</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (!empty($solicitudes7)): ?>
                      <?php $idx = 0;
                      foreach ($solicitudes7 as $r):
                        $idx++; ?>
                        <tr class="<?php echo ($idx > 5 ? 'row-hidden' : ''); ?>">
                          <td>
                            <?php $f = $r['fecha'];
                            echo htmlspecialchars((!empty($f) && $f != '0000-00-00') ? date('d/m/Y', strtotime(substr($f, 0, 10))) : ''); ?>
                          </td>
                          <td><?php echo htmlspecialchars($r['solicitud']); ?></td>
                          <td><?php echo htmlspecialchars($r['division_nombre']); ?></td>
                          <td>
                            <?php $f = $r['fecha_aprobacion'];
                            echo htmlspecialchars((!empty($f) && $f != '0000-00-00') ? date('d/m/Y', strtotime(substr($f, 0, 10))) : ''); ?>
                          </td>
                          <td>
                            <?php $f = $r['fecha_despacho'];
                            echo htmlspecialchars((!empty($f) && $f != '0000-00-00') ? date('d/m/Y', strtotime(substr($f, 0, 10))) : ''); ?>
                          </td>
                          <td>
                            <?php
                            $code = is_numeric($r['status']) ? (int) $r['status'] : null;
                            // Clases por código conocido; fallback si llega texto
                            if ($code !== null) {
                              switch ($code) {
                                case 0:
                                  $cls = 'warning';
                                  $label = status_almacen(0);
                                  break;      // Solicitada
                                case 5:
                                  $cls = 'success';
                                  $label = status_almacen(5);
                                  break;      // Aprobada
                                case 10:
                                  $cls = 'primary';
                                  $label = status_almacen(10);
                                  break;     // Despachada
                                case 99:
                                  $cls = 'danger';
                                  $label = status_almacen(99);
                                  break;     // Anulada
                                default:
                                  $cls = 'secondary';
                                  $label = (string) $r['status'];
                                  break;
                              }
                            } else {
                              // Fallback para valores textuales eventuales
                              $st = strtoupper(trim((string) $r['status']));
                              $map = [
                                'SOLICITADA' => ['warning', 'Solicitada'],
                                'PENDIENTE' => ['warning', 'Solicitada'],
                                'POR APROBAR' => ['warning', 'Solicitada'],
                                'APROBADA' => ['success', 'Aprobada'],
                                'DESPACHADA' => ['primary', 'Despachada'],
                                'ANULADA' => ['danger', 'Anulada'],
                              ];
                              $tmp = isset($map[$st]) ? $map[$st] : ['secondary', (string) $r['status']];
                              list($cls, $label) = $tmp;
                            }
                            ?>
                            <span class="badge badge-<?php echo $cls; ?>"><?php echo htmlspecialchars($label); ?></span>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="6" class="text-center text-muted py-4">Sin solicitudes recientes.</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="card-footer py-2 text-center" id="solicitudes-toggle-wrapper" style="display: none;">
              <button type="button" class="btn btn-sm btn-danger" id="solicitudes-toggle">Mostrar más</button>
            </div>
          </div>
        </div>
        <div class="col-12 col-xl-4">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <i class="fas fa-boxes"></i>
              <span class="card-title">Artículos recientes</span>
            </div>
            <div class="card-body">
              <?php if (!empty($articulosRecientes)): ?>
                <div id="lista-articulos">
                  <?php $i = 0;
                  foreach ($articulosRecientes as $a):
                    $i++; ?>
                    <div class="d-flex align-items-center mb-3 product-list <?php echo ($i > 5 ? 'item-hidden' : ''); ?>">
                      <div class="product-img mr-3"><i class="fas fa-box"></i></div>
                      <div class="flex-grow-1">
                        <div class="product-title mb-1"><?php echo htmlspecialchars($a['descripcion']); ?></div>
                        <div class="small text-muted">Unidad: <?php echo htmlspecialchars($a['unidad']); ?> · Precio:
                          <?php echo number_format((float) $a['precio'], 2, ',', '.'); ?>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
                <div class="mt-2 text-center" id="articulos-toggle-wrapper" style="display:none;">
                  <button type="button" class="btn btn-sm btn-danger" id="articulos-toggle">Mostrar más</button>
                </div>
              <?php else: ?>
                <div class="text-muted">Sin artículos registrados recientemente.</div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <?php
  include "../funciones/footNew.php";
  ?>
  <?php if ($mostrarAlertaPriv): ?>
    <script>
      // Inicialización de gráficos del resumen (solo si hay privilegios)
      (function () {
        const divResumen = <?php echo json_encode($divisionesResumen, JSON_UNESCAPED_UNICODE); ?>;
        const topArt = <?php echo json_encode($topArticulos, JSON_UNESCAPED_UNICODE); ?>;

        window.initAlmacenCharts = function () {
          const palette = ['#007bff', '#28a745', '#fd7e14', '#6f42c1', '#20c997', '#17a2b8', '#dc3545', '#343a40', '#ffc107', '#6610f2'];
          const rgba = (hex, a) => {
            const ctx = document.createElement('canvas').getContext('2d');
            ctx.fillStyle = hex; const c = ctx.fillStyle; const m = c.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)/i);
            return m ? `rgba(${m[1]}, ${m[2]}, ${m[3]}, ${a})` : hex;
          };

          if (window.ChartDataLabels) { Chart.register(ChartDataLabels); }

          // Helpers para legibilidad de etiquetas
          function toTitleCaseEs(str) {
            if (!str) return '';
            const min = ['de', 'la', 'las', 'y', 'o', 'del', 'los', 'en', 'para', 'por', 'con', 'a'];
            return String(str).toLowerCase().split(' ').map((w, i) => min.includes(w) && i > 0 ? w : w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
          }
          function sanitizeDivisionLabel(s) {
            if (!s) return '';
            return String(s).replace(/^Div(isi[oó]n)?\s+de\s+/i, '').replace(/^Div(isi[oó]n)?\s+/i, '');
          }

          // Gráfico de barras: Divisiones
          try {
            const divCanvas = document.getElementById('chartDivisiones');
            if (divCanvas) {
              const labels = (divResumen.labels || []).map(sanitizeDivisionLabel).map(toTitleCaseEs);
              const data = divResumen.data || [];
              const bgColors = (divResumen.labels || []).map((_, i) => rgba(palette[i % palette.length], 0.25));
              const borderColors = (divResumen.labels || []).map((_, i) => palette[i % palette.length]);
              new Chart(divCanvas, {
                type: 'bar',
                data: { labels, datasets: [{ label: 'Solicitudes', data, backgroundColor: bgColors, borderColor: borderColors, borderWidth: 1.5, borderRadius: 6 }] },
                options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  plugins: {
                    legend: { display: false },
                    tooltip: { mode: 'index', intersect: false },
                    datalabels: { anchor: 'end', align: 'top', color: '#495057', formatter: (v) => v > 0 ? v : '' }
                  },
                  scales: {
                    x: { ticks: { color: '#343a40' } },
                    y: { beginAtZero: true, ticks: { precision: 0, color: '#343a40' } }
                  }
                }
              });
            }
          } catch (e) { /* noop */ }

          // Doughnut: Top artículos
          try {
            const chartTopCtx = document.getElementById('chartTopArt');
            if (chartTopCtx) {
              const labels = (topArt.labels || []).map(toTitleCaseEs);
              const values = topArt.data || [];
              const colors = labels.map((_, i) => palette[i % palette.length]);
              const chart = new Chart(chartTopCtx, {
                type: 'doughnut',
                data: { labels, datasets: [{ data: values, backgroundColor: colors, borderWidth: 1, borderColor: '#fff' }] },
                options: {
                  cutout: '55%',
                  responsive: true,
                  plugins: {
                    legend: { display: false },
                    datalabels: {
                      formatter: (v, ctx) => { const sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0) || 1; const p = Math.round((v * 100) / sum); return p >= 8 ? p + '%' : ''; },
                      color: '#212529',
                    }
                  }
                }
              });
              // Leyenda HTML
              (function renderLegend() {
                const legend = document.getElementById('legendTopArt');
                if (!legend) return;
                legend.innerHTML = '';
                const frag = document.createDocumentFragment();
                labels.forEach((lab, i) => {
                  const item = document.createElement('div');
                  item.className = 'legend-item';
                  const color = document.createElement('span'); color.className = 'legend-color'; color.style.backgroundColor = colors[i];
                  const text = document.createElement('span'); text.textContent = `${lab} (${values[i] || 0})`;
                  item.appendChild(color); item.appendChild(text); frag.appendChild(item);
                });
                legend.appendChild(frag);
              })();
            }
          } catch (e) { /* noop */ }
        };
      })();
    </script>
  <?php endif; ?>
</body>

</html>