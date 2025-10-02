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
</body>

</html>