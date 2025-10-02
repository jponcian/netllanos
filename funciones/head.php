<!-- CSS PRINCIPALES -->
<link rel="stylesheet" href="../funciones/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="../estilos/estilos.css" />
<link rel="stylesheet" href="../funciones/menu/theme.css" type="text/css">
<link rel="stylesheet" href="../funciones/alertify/css/alertify.css">
<!-- <link rel="stylesheet" href="../funciones/alertify/css/themes/bootstrap.css"> -->
<link rel="stylesheet" href="../lib/select2/select2.min.css" />
<link rel="stylesheet" href="../lib/datatables.css" type="text/css" />
<link rel="stylesheet" href="../lib/fontawesome/css/all.css" type="text/css">
<link rel="stylesheet" href="../lib/sweetalert2.min.css" type="text/css">
<link rel="stylesheet" href="../lib/jquery-ui/jquery-ui.min.css" type="text/css">

<!-- JS PRINCIPALES (ORDEN CORRECTO) -->
<script src="../lib/jquery/jquery-3.7.1.min.js"></script>
<script src="../funciones/bootstrap/js/bootstrap.min.js"></script>
<script src="../lib/jquery-ui/jquery-ui.js"></script>
<script src="../lib/select2/select2.min.js"></script>
<script src="../lib/datatables.js"></script>
<script src="../lib/datatable.js"></script>
<script src="../lib/fontawesome/js/fontawesome.js"></script>
<script src="../lib/sweetalert2.all.min.js"></script>
<script src="../funciones/auxiliar_java.js"></script>
<script src="../funciones/menu/JSCookMenu.js"></script>
<script src="../funciones/menu/theme.js"></script>
<script src="../funciones/alertify/alertify.js"></script>
<script src="../funciones/scw_normal.js"></script>

<script>
  //---- ICONO DE ESPERA
  $(document).ready(function() {
    $(".loader").fadeOut("");
    $('.select2').select2({});
    $(".datepicker").datepicker({
      dateFormat: "dd-mm-yy"
    });
  });
</script>