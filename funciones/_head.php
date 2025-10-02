<!-- CSS: Bootstrap, FontAwesome, AdminLTE, Alertify, Select2, DataTables, Estilos propios -->
<link rel="stylesheet" href="/lib/bootstrap/css/bootstrap.min.css" />
<link rel="stylesheet" href="/lib/fontawesome/css/all.min.css" />
<link rel="stylesheet" href="/lib/overlayscrollbars.min.css" />
<link rel="stylesheet" href="../lib/AdminLTE/dist/css/adminlte.css" />
<link rel="stylesheet" href="../funciones/menu/theme.css" type="text/css">
<link rel="stylesheet" href="../funciones/alertify/css/alertify.css">
<link rel="stylesheet" href="../lib/select2/select2.min.css" />
<link rel="stylesheet" href="../lib/datatables.css" type="text/css" />
<link rel="stylesheet" href="/estilos/estilos.css" />

<!-- JS: jQuery, Bootstrap, AdminLTE, Alertify, Select2, DataTables, FontAwesome, SweetAlert2, jQuery UI, Otros -->
<script src="/lib/jquery/jquery.min.js"></script>
<script src="/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/lib/overlayscrollbars.browser.es6.min.js"></script>
<script src="../lib/AdminLTE/dist/js/adminlte.js"></script>
<script src="../funciones/menu/JSCookMenu.js"></script>
<script src="../funciones/menu/theme.js"></script>
<script src="../funciones/alertify/alertify.js"></script>
<script src="../lib/select2/select2.min.js"></script>
<script src="../lib/datatables.js"></script>
<script src="../lib/datatable.js"></script>
<script src="../lib/fontawesome/js/fontawesome.js"></script>
<script src="../lib/sweetalert2.all.min.js"></script>
<script src="../lib/jquery-ui/jquery-ui.min.js"></script>
<script src="../funciones/auxiliar_java.js"></script>
<script src='../funciones/scw_normal.js'></script>

<!-- Inicialización de plugins -->
<script language="javascript">
  //---- ICONO DE ESPERA
  $(document).ready(function() {
    $(".loader").fadeOut("");
    $('.select2').select2({});
    $(".datepicker").datepicker({
      dateFormat: "dd-mm-yy"
    });
  });
  //-----------
</script>