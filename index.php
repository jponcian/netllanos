<?php
require_once 'funciones/run_bcv_update.php';
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
?>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>GRTI LOS LLANOS * APP</title>
  <link rel="stylesheet" href="plugins/bootstrap/css/bootstrap.min.css" />
  <link rel="stylesheet" href="plugins/fontawesome/css/all.min.css" />
  <link rel="stylesheet" href="plugins/overlayscrollbars.min.css" />
  <link rel="stylesheet" href="plugins/AdminLTE/dist/css/adminlte.css" />
  <link rel="stylesheet" href="estilos/estilos.css" />
  <script src="plugins/sweetalert2/sweetalert2.all.min.js"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
  <!-- <link rel="stylesheet" href="../../plugins/AdminLTE/dist/css/adminlte.css" /> -->
  <?php
  /*
  date_default_timezone_set('America/Caracas'); // Set the timezone
  $hour = (int)date('H');
  $minute = (int)date('i');
  $time_in_minutes = $hour * 60 + $minute;

  $backgroundImage = '';

  if ($time_in_minutes >= (5 * 60 + 30) && $time_in_minutes < (7 * 60)) {
      $backgroundImage = 'amanecer.png';
  } elseif ($time_in_minutes >= (7 * 60) && $time_in_minutes < (12 * 60)) {
      $backgroundImage = 'normal.png';
  } elseif ($time_in_minutes >= (12 * 60) && $time_in_minutes < (17 * 60 + 30)) {
      $backgroundImage = 'mediodia.png';
  } elseif ($time_in_minutes >= (17 * 60 + 30) && $time_in_minutes < (19 * 60)) {
      $backgroundImage = 'atardecer.png';
  } else {
      $backgroundImage = 'noche led.png';
  }
  */

  $images = [
    'amanecer.png',
    'normal.png',
    'mediodia.png',
    'atardecer.png',
    'noche led.png',
    'noche ambar.png',
    'nublado.png'
  ];
  $backgroundImage = $images[array_rand($images)];
  ?>
  <style>
    html,
    body {
      min-height: 100vh !important;
      background-image: url("imagenes/<?php echo $backgroundImage; ?>") !important;
      background-size: auto !important;
      background-position: center !important;
      background-repeat: no-repeat !important;
      background-attachment: fixed !important;
      background-color: transparent !important;
    }

    .sidebar-transition {
      transition: transform 0.3s ease-in-out;
    }
  </style>
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary sidebar-collapse">
  <div class="app-wrapper">
    <nav class="app-header navbar navbar-expand navbar-dark bg-danger">
      <div class="container-fluid">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
              <i class="fas fa-bars"></i>
            </a>
          </li>
          <!-- Botón Administración -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown"
              aria-expanded="false">
              Administración
            </a>
            <ul class="dropdown-menu" aria-labelledby="adminDropdown">
              <li><a class="dropdown-item" href="#" onclick="cargarEnDashboard('almacen/'); return false;"><i
                    class="fas fa-boxes me-2"></i>Almacén</a></li>
              <li><a class="dropdown-item" href="#" onclick="cargarEnDashboard('bienes/'); return false;"><i
                    class="fas fa-landmark me-2"></i>Bienes Nacionales</a></li>
              <li><a class="dropdown-item" href="#" onclick="cargarEnDashboard('informatica/'); return false;"><i
                    class="fas fa-desktop me-2"></i>CITINF</a></li>
              <li><a class="dropdown-item" href="#" onclick="cargarEnDashboard('jefaturas/'); return false;"><i
                    class="fa-solid fa-user-tie me-2"></i>Jefatura</a></li>
            </ul>
          </li>
          <!-- Botón Sujetos Pasivos Especiales -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="sujetosDropdown" role="button" data-bs-toggle="dropdown"
              aria-expanded="false">
              S.P.E.
            </a>
            <ul class="dropdown-menu" aria-labelledby="sujetosDropdown">
              <li><a class="dropdown-item" href="#" onclick="cargarEnDashboard('../SUJETOS_PASIVOS'); return false;"><i
                    class="fas fa-users-cog me-2"></i>Gestión</a></li>
              <li><a class="dropdown-item" href="#" onclick="cargarEnDashboard('../AJUSTE_UT'); return false;"><i
                    class="fas fa-balance-scale me-2"></i>Ajuste U.T.</a></li>
              <li><a class="dropdown-item" href="#" onclick="cargarEnDashboard('../FRACCIONAMIENTO'); return false;"><i
                    class="fas fa-divide me-2"></i>Fraccionamiento</a></li>
              <li><a class="dropdown-item" href="#" onclick="cargarEnDashboard('../LIQUIDACION'); return false;"><i
                    class="fas fa-file-invoice-dollar me-2"></i>Liquidación</a></li>
              <li><a class="dropdown-item" href="#" onclick="cargarEnDashboard('../notificacion'); return false;"><i
                    class="fas fa-bell me-2"></i>Notificación</a></li>
              <li><a class="dropdown-item" href="#" onclick="cargarEnDashboard('../COBRO'); return false;"><i
                    class="fas fa-hand-holding-usd me-2"></i>Cobro</a></li>
            </ul>
          </li>
          <!-- Botón Sumario Administrativos -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="sumarioDropdown" role="button" data-bs-toggle="dropdown"
              aria-expanded="false">
              Sumario
            </a>
            <ul class="dropdown-menu" aria-labelledby="sumarioDropdown">
              <li><a class="dropdown-item" href="#" onclick="cargarEnDashboard('../SUMARIO'); return false;"><i
                    class="fas fa-file-alt me-2"></i>Gestión</a></li>
              <li><a class="dropdown-item" href="#" onclick="cargarEnDashboard('../LIQUIDACION'); return false;"><i
                    class="fas fa-file-invoice-dollar me-2"></i>Liquidación</a></li>
            </ul>
          </li>
          <!-- Botón Fiscalización -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="fiscalDropdown" role="button" data-bs-toggle="dropdown"
              aria-expanded="false">
              Fiscalización
            </a>
            <ul class="dropdown-menu" aria-labelledby="fiscalDropdown">
              <li><a class="dropdown-item" href="#" onclick="cargarEnDashboard('../FISCALIZACION'); return false;"><i
                    class="fas fa-tasks me-2"></i>Gestión</a></li>
              <li><a class="dropdown-item" href="#" onclick="cargarEnDashboard('../LIQUIDACION'); return false;"><i
                    class="fas fa-file-invoice-dollar me-2"></i>Liquidación</a></li>
              <li><a class="dropdown-item" href="#" onclick="cargarEnDashboard('../euro'); return false;"><i
                    class="fas fa-euro-sign me-2"></i>Actualización del Euro</a></li>
            </ul>
          </li>
          <!-- Botón Recaudación -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="recaudacionDropdown" role="button"
              data-bs-toggle="dropdown" aria-expanded="false">
              Recaudación
            </a>
            <ul class="dropdown-menu" aria-labelledby="recaudacionDropdown">
              <li><a class="dropdown-item" href="#" onclick="cargarEnDashboard('../RIF'); return false;"><i
                    class="fas fa-id-card me-2"></i>Rif</a></li>
              <li><a class="dropdown-item" href="#" onclick="cargarEnDashboard('../SUCESIONES'); return false;"><i
                    class="fas fa-users me-2"></i>Sucesiones</a></li>
              <li><a class="dropdown-item" href="#" onclick="cargarEnDashboard('../LIQUIDACION'); return false;"><i
                    class="fas fa-file-invoice-dollar me-2"></i>Liquidacion</a></li>
              <li><a class="dropdown-item" href="#" onclick="cargarEnDashboard('../COBRO'); return false;"><i
                    class="fas fa-hand-holding-usd me-2"></i>Cobro</a></li>
              <li><a class="dropdown-item" href="#" onclick="cargarEnDashboard('../AJUSTE_UT'); return false;"><i
                    class="fas fa-balance-scale me-2"></i>Ajuste U.T.</a></li>
              <li><a class="dropdown-item" href="#" onclick="cargarEnDashboard('../FRACCIONAMIENTO'); return false;"><i
                    class="fas fa-divide me-2"></i>Fraccionamiento</a></li>
              <li><a class="dropdown-item" href="#" onclick="cargarEnDashboard('../TIMBRE'); return false;"><i
                    class="fas fa-stamp me-2"></i>Timbre Fiscal</a></li>
            </ul>
          </li>
          <!-- Botón Jurídico Tributario -->
          <li class="nav-item">
            <a class="nav-link" href="#" onclick="cargarEnDashboard('juridico/index.php'); return false;">
              <i class="fas fa-gavel me-2"></i>Jurídico
            </a>
          </li>
          <!-- Botón Tramitación -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="tramiteDropdown" role="button" data-bs-toggle="dropdown"
              aria-expanded="false">
              Tramitación
            </a>
            <ul class="dropdown-menu" aria-labelledby="tramiteDropdown">
              <li><a class="dropdown-item" href="#" onclick="cargarEnDashboard('notificacion/'); return false;"><i
                    class="fas fa-bell me-2"></i>Notificaciones</a></li>
            </ul>
          </li>
          <!-- Botón Control y Seguimientos a Expedientes -->
          <li class="nav-item">
            <a class="nav-link" href="#" onclick="cargarEnDashboard('EstatusContrib/'); return false;">
              <i class="fas fa-folder-open me-2"></i>Control
            </a>
          </li>
          <!-- Botón Otras (último) -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="otrasDropdown" role="button" data-bs-toggle="dropdown"
              aria-expanded="false">
              Otras
            </a>
            <ul class="dropdown-menu" aria-labelledby="otrasDropdown">
              <li><a class="dropdown-item" href="utilidades/menuprincipal.php"><i
                    class="fas fa-tools me-2"></i>Utilidades</a></li>
              <li><a class="dropdown-item" href="http://sai.seniat.gob.ve/"><i
                    class="fas fa-plane me-2"></i>Viaticos</a></li>
              <li><a class="dropdown-item" href="contribuyente/consultaavanzada/index.php"><i
                    class="fas fa-users me-2"></i>Cons.Contribuyentes</a></li>
              <li><a class="dropdown-item" href="funcionarios/index.php"><i
                    class="fas fa-user-tie me-2"></i>Cons.Funcionarios</a></li>
              <li><a class="dropdown-item" href="moneda/moneda.php"><i class="fas fa-euro-sign me-2"></i>Hist.Euro</a>
              </li>
            </ul>
          </li>
        </ul>
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="#" data-lte-toggle="fullscreen">
              <i data-lte-icon="maximize" class="fas fa-expand"></i>
              <i data-lte-icon="minimize" class="fas fa-compress" style="display: none"></i>
            </a>
          </li>
          <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
              <img src="<?php echo $base; ?>/imagenes/aniversario.jpeg" class="user-image rounded-circle shadow"
                alt="User Image" />
              <span class="d-none d-md-inline" id="nombreUsuario"></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
              <li class="user-header text-bg-danger">
                <img src="<?php echo $base; ?>/imagenes/aniversario.jpeg" class="rounded-circle shadow"
                  alt="User Image" />
                <p id="nombreUsuario2"></p>
              </li>
              <li class="user-footer" id="opciones" style="display: none">
                <a href="#" class="btn btn-default btn-flat">Contraseña</a>
                <a href="index.php" class="btn btn-default btn-flat float-end">Salir</a>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
    <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
      <div class="sidebar-brand">
        <a href="index.php" class="brand-link">
          <!-- <img
              src="<?php echo $base; ?>/imagenes/logo.jpeg"
              alt="AdminLTE Logo"
              class="brand-image opacity-75 shadow"
            /> -->
          <span class="brand-text fw-light"><strong>NetLosLlanos</strong></span>
        </a>
      </div>
      <div class="sidebar-wrapper p-2" id="menuVacioSidebar" style="display:block;">
        <?php include 'menu_vacio.php'; ?>
      </div>
      <div class="sidebar-wrapper p-2" id="menuAlmacenSidebar" style="display:none;">
        <?php include 'menu_almacen.php'; ?>
      </div>
      <div class="sidebar-wrapper p-2" id="menuBienesSidebar" style="display:none;">
        <?php include 'menu_bienes.php'; ?>
      </div>
    </aside>
    <script>
      // Escuchar mensajes del iframe para alternar el menú lateral
      window.addEventListener("message", function (event) {
        if (!event.data || typeof event.data !== "object") return;
        var menuVacio = document.getElementById("menuVacioSidebar");
        var menuAlmacen = document.getElementById("menuAlmacenSidebar");
        var menuBienes = document.getElementById("menuBienesSidebar");
        if (!menuVacio || !menuAlmacen || !menuBienes) return;

        if (event.data.accion === "mostrarMenuAlmacen") {
          menuVacio.style.display = "none";
          menuAlmacen.style.display = "block";
          menuBienes.style.display = "none";

          // Abrir el menú lateral con animación si se solicita
          if (event.data.animar) {
            const body = document.body;
            if (body.classList.contains('sidebar-collapse')) {
              // Añadir clase para la transición
              const sidebar = document.querySelector('.app-sidebar');
              sidebar.classList.add('sidebar-transition');

              // Forzar la apertura del menú
              body.classList.remove('sidebar-collapse');
              body.classList.add('sidebar-open');

              // Quitar la clase de transición después de la animación
              setTimeout(() => {
                sidebar.classList.remove('sidebar-transition');
              }, 300); // La duración debe coincidir con la de la transición en CSS
            }
          }
        } else if (event.data.accion === "mostrarMenuBienes") {
          menuVacio.style.display = "none";
          menuAlmacen.style.display = "none";
          menuBienes.style.display = "block";

          // Abrir el menú lateral con animación si se solicita
          if (event.data.animar) {
            const body = document.body;
            if (body.classList.contains('sidebar-collapse')) {
              // Añadir clase para la transición
              const sidebar = document.querySelector('.app-sidebar');
              sidebar.classList.add('sidebar-transition');

              // Forzar la apertura del menú
              body.classList.remove('sidebar-collapse');
              body.classList.add('sidebar-open');

              // Quitar la clase de transición después de la animación
              setTimeout(() => {
                sidebar.classList.remove('sidebar-transition');
              }, 300); // La duración debe coincidir con la de la transición en CSS
            }
          }
        } else if (event.data.accion === "mostrarMenuVacio") {
          menuVacio.style.display = "block";
          menuAlmacen.style.display = "none";
          menuBienes.style.display = "none";
        }
      });
    </script>
    <main class="app-main">
      <div class="app-content-header"></div>
      <div class="app-content">
        <iframe id="dashboardFrame" src="" style="
              width: 100%;
              border: none;
              display: none;
              background: rgba(255, 255, 255, 0.5);
            "></iframe>
      </div>
    </main>
    <footer class="app-footer"
      style="padding:10px; background:#f8f9fa; color:#333; font-size:1.1em; display:flex; align-items:center; justify-content:space-between; gap:10px;">

      <img src="imagenes/banner-left.png" alt="Banner izquierdo"
        style="max-height:48px; height:auto; width:auto; display:block; object-fit:contain;" />

      <div style="flex:1; text-align:center;">
        <?php
        // Mostrar la tasa del euro más reciente
        $db_host = 'localhost';
        $db_name = 'losllanos';
        $db_user = 'root';
        $db_pass = '';
        $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
        try {
          $conn = new PDO($dsn, $db_user, $db_pass);
          $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          $stmt = $conn->prepare("SELECT valor, FechaAplicacion FROM a_moneda_cambio WHERE moneda = 'EUR' ORDER BY FechaAplicacion DESC LIMIT 1");
          $stmt->execute();
          $tasa = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
          $tasa = null;
        }
        if ($tasa) {
          echo '<b>Tasa Euro BCV actual:</b> ' . number_format($tasa['valor'], 2, ',', '.') . ' Bs. (' . date('d/m/Y', strtotime($tasa['FechaAplicacion'])) . ')';
        } else {
          echo '<b>No disponible la tasa del Euro.</b>';
        }
        ?><br><strong style="font-size: 0.6em;">
          Copyright &copy; 2025 GRTI LOS LLANOS * APP | Departamento de Informática
        </strong>
      </div>

      <img src="imagenes/banner-right.png" alt="Banner derecho"
        style="max-height:48px; height:auto; width:auto; display:block; object-fit:contain;" />
    </footer>
  </div>

  <!-- Scripts al final del body -->
  <script src="plugins/jquery/jquery.min.js"></script>
  <script src="plugins/overlayscrollbars.browser.es6.min.js"></script>
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="plugins/AdminLTE/dist/js/adminlte.js"></script>
  <script>
    // Altura adaptativa del iframe para NO generar scroll en el index (padre)
    function updateDashboardFrameHeight() {
      var frame = document.getElementById("dashboardFrame");
      if (!frame) return;
      var footer = document.querySelector(".app-footer");
      var footerH = footer ? footer.offsetHeight : 0;

      // Distancia desde el top de la ventana hasta el borde superior del iframe
      var top = frame.getBoundingClientRect().top;
      // Altura disponible exacta sin provocar scroll: viewport - top del iframe - alto del footer
      var available = window.innerHeight - top - footerH;
      frame.style.height = Math.max(available, 200) + "px";
    }
    // Ruta base dinámica para imágenes (soporta subcarpeta o raíz)
    var rutaBase = '<?php echo $base; ?>';
    function activarUsuario(texto, imagen) {
      // Cambia el texto de los labels
      var label = document.getElementById("nombreUsuario");
      if (label) {
        label.textContent = texto;
      }
      var label2 = document.getElementById("nombreUsuario2");
      if (label2) {
        label2.textContent = texto;
      }
      // Cambia todas las imágenes de usuario y agrega fallback
      var userImgs = document.querySelectorAll(
        "img.user-image, .user-header img"
      );
      userImgs.forEach(function (img) {
        img.src = imagen;
        img.onerror = function () {
          var fallback = rutaBase + '/imagenes/funcionarios/default.png';
          if (!this.src.endsWith('default.png')) {
            this.src = fallback;
          }
        };
      });
      // Muestra el elemento opciones
      var opciones = document.getElementById("opciones");
      if (opciones) {
        opciones.style.display = "block";
      }
    }
    function cargarEnDashboard(url) {
      var frame = document.getElementById("dashboardFrame");
      if (frame) {
        frame.src = url;
        frame.style.display = "block";
        // Ajustar altura al mostrar contenido nuevo
        updateDashboardFrameHeight();
        // Reajustar también cuando el contenido del iframe cargue
        frame.onload = function () { updateDashboardFrameHeight(); };
      }
      // Cierra el sidebar
      if (window.AdminLTE && AdminLTE.Sidebar) {
        AdminLTE.Sidebar.collapse();
      } else {
        document.body.classList.add("sidebar-collapse");
      }
    }

    document.addEventListener("DOMContentLoaded", function () {
      // Evitar scroll vertical en el documento padre
      document.documentElement.style.overflowY = "hidden";
      document.body.style.overflowY = "hidden";

      updateDashboardFrameHeight();
      const sidebarWrapper = document.querySelector(".sidebar-wrapper");
      if (
        sidebarWrapper &&
        OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined
      ) {
        OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
          scrollbars: {
            theme: "os-theme-light",
            autoHide: "leave",
            clickScroll: true,
          },
        });
      }
    });
    window.addEventListener("load", function () {
      // Reforzar ocultar scroll del padre al finalizar carga
      document.documentElement.style.overflowY = "hidden";
      document.body.style.overflowY = "hidden";
      updateDashboardFrameHeight();
      if (window.AdminLTE && AdminLTE.Sidebar) {
        AdminLTE.Sidebar.collapse();
      } else {
        document.body.classList.add("sidebar-collapse");
      }
    });

    // Recalcular cuando cambia el tamaño de la ventana
    window.addEventListener("resize", updateDashboardFrameHeight);

    function alertainformatica() {
      Swal.fire({
        icon: "info",
        title: "Atención",
        text: "Comuniquese con el Departamento de Informática",
        confirmButtonText: "Aceptar",
        customClass: {
          confirmButton: "btn btn-success",
        },
        buttonsStyling: false,
      });
    }
  </script>

  <!--Add the following script at the bottom of the web page (before </body></html>)-->
  <!-- <script type="text/javascript">function add_chatinline() { var hccid = 15885734; var nt = document.createElement("script"); nt.async = true; nt.src = "https://mylivechat.com/chatinline.aspx?hccid=" + hccid; var ct = document.getElementsByTagName("script")[0]; ct.parentNode.insertBefore(nt, ct); }
    add_chatinline();</script> -->

</body>

</html>