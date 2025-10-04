<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";
include_once __DIR__ . '/../frases.php';
// include_once __DIR__ . '/../config.php';
$_SESSION['NOMBRE_MODULO'] = 'SOL_APLICATIVOS';

$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$is_localhost = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']);
$base_url = $is_localhost ? $base : 'http://' . $_SERVER['HTTP_HOST'] . $base;

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

// Preparar datos para el dashboard de solicitudes
$solicitudesHistorial = [];
if (isset($_SESSION['conexionsqli']) && $_SESSION['conexionsqli'] instanceof mysqli) {
  $mysqli = $_SESSION['conexionsqli'];
  // Historial de solicitudes del usuario (últimas 20)
  $sqlHist = "SELECT id, fecha, status FROM sol_solicitudes WHERE cedula_solicitante = ? ORDER BY fecha DESC LIMIT 20";
  if ($stmt = $mysqli->prepare($sqlHist)) {
    $stmt->bind_param('i', $_SESSION['CEDULA_USUARIO']);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
      $solicitudesHistorial[] = $row;
    }
    $stmt->close();
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
    // Ruta base dinámica para imágenes (soporta subcarpeta o raíz)
    var rutaBase = '<?php echo $is_localhost ? '../imagenes/funcionarios/' : 'http://rellanif023/imagenes/funcionarios/'; ?>';
    // Mensaje de bienvenida espectacular
    document.addEventListener('DOMContentLoaded', function() {
      Swal.fire({
        title: '<?php echo $saludo; ?>',
        html: '<?php echo ucwords(strtolower(addslashes($_SESSION['NOM_USUARIO']))); ?> <br><strong>¡Bienvenido al Módulo de Solicitudes de Aplicativos!</strong><br><br><i>"<?php echo addslashes($frase_aleatoria); ?>"</i>',
        imageUrl: rutaBase + '<?php echo $_SESSION['CEDULA_USUARIO']; ?>.png',
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
            img.onerror = function() {
              this.onerror = null;
              this.src = rutaBase + 'default.png';
            };
          }

          function randomInRange(min, max) {
            return Math.random() * (max - min) + min;
          }

          const interval = setInterval(function() {
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
          // Notificar al padre (index.php) que debe mostrar el menú vacío con animación
          if (window.parent && window.parent !== window) {
            window.parent.postMessage({
              accion: 'mostrarMenuVacio',
              animar: true
            }, '*');
          }
          // Mostrar directamente el resumen de Sol. Aplicativos si aplica
          const tienePrivilegios = <?php echo $mostrarAlertaPriv ? 'true' : 'false'; ?>;
          const mostrarResumen = () => {
            const root = document.getElementById('sol-aplicativos-dashboard');
            if (root) root.style.display = '';
            // Inicializar toggles de Mostrar más/menos
            try {
              // Solicitudes: ocultar >5 por defecto (ya vienen con clase); mostrar botón si hay más
              (function() {
                const table = document.getElementById('tabla-solicitudes');
                const wrap = document.getElementById('solicitudes-toggle-wrapper');
                const btn = document.getElementById('solicitudes-toggle');
                if (!table || !wrap || !btn) return;
                const totalRows = table.querySelectorAll('tbody tr').length;
                if (totalRows > 5) {
                  wrap.style.display = '';
                }
                btn.addEventListener('click', function() {
                  const anyHidden = table.querySelector('tbody tr.row-hidden') !== null;
                  if (anyHidden) {
                    table.querySelectorAll('tbody tr.row-hidden').forEach(tr => tr.classList.remove('row-hidden'));
                    btn.textContent = 'Mostrar menos';
                  } else {
                    let count = 0;
                    table.querySelectorAll('tbody tr').forEach(tr => {
                      count++;
                      if (count > 5) tr.classList.add('row-hidden');
                      else tr.classList.remove('row-hidden');
                    });
                    btn.textContent = 'Mostrar más';
                  }
                });
              })();

              // Artículos: ocultar >5; toggle (formato tipo jefaturas)
              (function() {
                const list = document.getElementById('lista-articulos');
                const wrap = document.getElementById('articulos-toggle-wrapper');
                const btn = document.getElementById('articulos-toggle');
                if (!list || !wrap || !btn) return;
                const totalItems = list.querySelectorAll('.product-list').length;
                if (totalItems > 5) {
                  wrap.style.display = '';
                }
                btn.addEventListener('click', function() {
                  const anyHidden = list.querySelector('.product-list.item-hidden') !== null;
                  if (anyHidden) {
                    list.querySelectorAll('.product-list.item-hidden').forEach(li => li.classList.remove('item-hidden'));
                    btn.textContent = 'Mostrar menos';
                  } else {
                    let count = 0;
                    list.querySelectorAll('.product-list').forEach(li => {
                      count++;
                      if (count > 5) li.classList.add('item-hidden');
                      else li.classList.remove('item-hidden');
                    });
                    btn.textContent = 'Mostrar más';
                  }
                });
              })();
            } catch (e) {
              /* noop */ }
          };
          if (tienePrivilegios) {
            mostrarResumen();
          }
        }
      });
    });
  </script>
  <script>
    // Mostrar dashboard siempre
    document.addEventListener('DOMContentLoaded', function() {
      const dashboard = document.getElementById('sol-aplicativos-dashboard');
      if (dashboard) {
        dashboard.style.display = 'block';
      }
    });
  </script>

  <div class="container-fluid py-3" id="sol-aplicativos-dashboard">
    <div class="row mb-2">
      <div class="col-12">
        <h5 class="mb-0"><i class="fas fa-file-signature mr-2"></i>Solicitudes de Aplicativos</h5>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-12">
        <div class="card">
          <div class="card-header d-flex align-items-center justify-content-between">
            <span><i class="fas fa-history"></i> Historial de Solicitudes</span>
            <button class="btn btn-primary btn-sm" onclick="crearNuevaSolicitud()">Crear Nueva Solicitud</button>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-striped table-hover mb-0" id="tabla-solicitudes">
                <thead class="thead-light">
                  <tr>
                    <th style="width:110px;">Fecha</th>
                    <th>ID</th>
                    <th>Status</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($solicitudesHistorial as $sol): ?>
                    <tr>
                      <td><?php echo date('d/m/Y', strtotime($sol['fecha'])); ?></td>
                      <td><?php echo $sol['id']; ?></td>
                      <td><?php echo $sol['status']; ?></td>
                      <td>
                        <button class="btn btn-sm btn-info" onclick="verSolicitud(<?php echo $sol['id']; ?>)">Ver</button>
                        <button class="btn btn-sm btn-success" onclick="imprimirSolicitud(<?php echo $sol['id']; ?>)">PDF</button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php
  include "../funciones/footNew.php";
  ?>
  <script>
    function crearNuevaSolicitud() {
      // Redirigir a página de crear solicitud
      window.location.href = 'crear_solicitud.php';
    }

    function verSolicitud(id) {
      // Ver solicitud
      alert('Ver solicitud ' + id);
    }

    function imprimirSolicitud(id) {
      // Abrir PDF en nueva ventana
      window.open('generar_pdf.php?id=' + id, '_blank');
    }
  </script>
  </script>
</body>

</html>