$(document).ready(function () {
  var tabla1 = $(".datatabla");
  if ($.fn.DataTable.isDataTable(tabla1)) {
    tabla1.DataTable().destroy();
  }

  var table = $(".datatabla").DataTable({
    ordering: false,
    pageLength: 10,
    lengthMenu: [
      [10, 25, 50, 1000],
      [10, 25, 50, 1000],
    ], // <-- Agregado aquí
    language: {
      search: "_INPUT_",
      searchPlaceholder: "Buscar...",
      decimal: "",
      emptyTable: "No hay datos disponibles",
      info: "Mostrando _START_ al _END_ de _TOTAL_ registros",
      infoEmpty: "Mostrando 0 al 0 de 0 entradas",
      infoFiltered: "(filtrado desde _MAX_ total registros)",
      infoPostFix: "",
      thousands: ",",
      lengthMenu: "Mostrar _MENU_ registros",
      loadingRecords: "Cargando...",
      processing: "",
      zeroRecords: "No se encontraron registros",
      paginate: {
        first: "Primero",
        last: "Ultimo",
        next: "Siguiente",
        previous: "Anterior",
      },
      aria: {
        sortAscending: ": activar el orden ascendente",
        sortDescending: ": activar el orden descendente",
      },
    },
    responsive: "true",
    order: "",
    dom: "Brtlp",
    buttons: {
      dom: {
        button: {
          className: "btn btn-success",
        },
      },
      buttons: [
        {
          extend: "excelHtml5",
          text: '<i class="fas fa-file-excel"></i> ',
          titleAttr: "Exportar a Excel",
          className: "btn btn-success",
        },
        {
          extend: "pdfHtml5",
          text: '<i class="fas fa-file-pdf"></i> ',
          titleAttr: "Exportar a PDF",
          className: "btn btn-danger",
        },
        {
          extend: "print",
          text: '<i class="fa fa-print"></i> ',
          titleAttr: "Imprimir",
          className: "btn btn-info",
        },
      ],
    },
  });
  //-------------
  $("#obuscar").on("keyup", function () {
    table.search(this.value).draw();
  });
});
