(function () {
  // Mejora visual y sincronización del carrito lateral
  $(document).ready(function () {
    // Añadir icono al título del offcanvas si falta
    var $title = $("#cartOffcanvasLabel");
    if ($title.length && $title.find("i.fa-shopping-cart").length === 0) {
      $title.prepend(
        '<i class="fa fa-shopping-cart me-1" aria-hidden="true"></i> '
      );
    }

    // Añadir bloque de acciones en el offcanvas si no existe
    var $body = $("#cartOffcanvas .offcanvas-body");
    if ($body.length && $("#cartCountFooter").length === 0) {
      var $actions = $(
        '<div class="cart-actions mt-3">' +
          '<div class="text-start small text-muted">Total ítems: <span id="cartCountFooter">0</span></div>' +
          '<div class="text-end">' +
          '<button class="btn btn-sm btn-secondary" type="button" data-bs-dismiss="offcanvas">Cerrar</button>' +
          '<button class="btn btn-sm btn-success ms-2" type="button" id="cartFooterGuardarBtn">Guardar</button>' +
          "</div>" +
          "</div>"
      );
      $body.append($actions);

      // Botón guardar en el pie reutiliza la función guardar() si existe
      $("#cartFooterGuardarBtn").on("click", function () {
        if (typeof guardar === "function") {
          guardar();
        } else {
          // fallback: submit del form si no hay la función
          $("form#form1").submit();
        }
      });
    }

    // Sincronizar contador del pie con el badge superior
    function syncFooter() {
      var badge = $("#cartCount");
      var count = 0;
      if (badge.length) {
        var parsed = parseInt(badge.text(), 10);
        if (!isNaN(parsed)) count = parsed;
      }
      // si badge no existe o no tiene número, contar elementos dentro del offcanvas
      if (count === 0) {
        var c = $("#div1-offcanvas").find("table tbody tr").length;
        if (c === 0) c = $("#div1-offcanvas").find(".list-group-item").length;
        count = c;
      }
      $("#cartCountFooter").text(count);
    }

    // Inicial
    syncFooter();

    // Observador para detectar cambios en el contenido del carrito y sincronizar
    var target = document.getElementById("div1-offcanvas");
    if (target) {
      try {
        var mo = new MutationObserver(function () {
          syncFooter();
        });
        mo.observe(target, { childList: true, subtree: true });
      } catch (e) {}
    }

    // También reaccionar a eventos AJAX globales
    $(document).ajaxSuccess(function () {
      syncFooter();
    });

    // Cuando se abra el offcanvas, asegurar que el contador esté actualizado
    $("#openCartBtn").on("click", function () {
      setTimeout(syncFooter, 120); // pequeño retardo para que el contenido cargado pueda aparecer
    });
  });
})();
