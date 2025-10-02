        $(document).ready(function() {
            //alert("Done");
            /*$.datepicker.regional['es'] = {
                closeText: 'Cerrar',
                prevText: '< Ant',
                nextText: 'Sig >',
                currentText: 'Hoy',
                monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
                dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
                dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
                weekHeader: 'Sm',
                dateFormat: 'dd-mm-yy',
                firstDay: 1,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: ''
            };
            $.datepicker.setDefaults($.datepicker.regional['es']);*/
            //$("#myModal").modal("show");
            //$('#modalForm1').trigger("reset");
            $("#myContent").on('click', '#sidebarCollapse', function() {
                alert("Sdebar");
                $("#sidebar").toggleClass('active');
                $(this).toggleClass('active');
            });
            $("#myContent").on('click', '#modal_close', function() {
                //alert("Click Cancelar");
                //window.open('','_parent','');
                //window.close();
                $("#myModal").modal("hide");
            });
            /*$("#myContent").on('click', '#btnLogin', function(e) {
                var parametros = $("#formLogin").serialize();
                alert(parametros);
                $.ajax({
                    url: "scripts/listar_login.php",
                    type: "POST",
                    dataType: "json",
                    data: parametros,
                    success: function(data) {
                        // alertify.alert(data.permitido);
                        if (data.permitido == true) {
                            window.open('declaracion.php', '_blank');
                            $("#myModal").modal("hide");
                            $("#msgAlert").hide();
                        } else {
                            $('#msgAlert').html(data.mensaje);
                            $("#msgAlert").show();
                        }
                    },
                });
            });*/
            $("#myContent").on('click', '#btnAccederSistema', function(e) {
                //alert("Click Entrar");
                /*$("#myModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });*/
            });
            //$("#msgAlert").hide();
            $("#myModal").on('hidden.bs.modal', function() {
                //$("#msgAlert").hide();
                //alert("Esta accion se ejecuta al cerrar el modal")
            });
        });