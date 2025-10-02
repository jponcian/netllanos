$(document).ready(function(){

	//alert("Jquery done");
    $(document).ajaxStart(function(){
        $.blockUI({ 
            message:  '<h2><img src="images/ajax-loader0.gif" />&nbsp;... Cargando ...</h2> Por favor NO recargar la página',  
            css: {
                border: 'solid 2px', 
                padding: '1px', 
                backgroundColor: '#fff', 
                '-webkit-border-radius': '10px', 
                '-moz-border-radius': '10px', 
                opacity: .7, 
                color: '#EE1616'
            } 
        });
    });

	

    $.datepicker.regional['es'] = {
        closeText: 'Cerrar',
        currentText: 'Hoy',
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
        dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
        dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
        dateFormat: 'dd/mm/yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        changeMonth: true,
        changeYear: true,
        yearSuffix: ''
    };
    $.datepicker.setDefaults($.datepicker.regional['es']);

    $(".botonoptions").button({
        icons: {
            primary: 'ui-icon ui-icon-document'
        }
    })

	$('#finicio').datepicker();
	$('#ffin').datepicker();
    $(".boton").button();
    $('#reportes').hide();

    //******************* 1.1 INFORME MENSUAL ***************************************************
    $("#btninformegestion").on("click", function(){
        var mes = $("#cboMeses").val();
        var anno = $("#cboAnnos").val();
        var data = "mes=" + mes + "&anno=" + anno;
        $.ajax({
            url: "includes/informe_gestion.php",
            type: "POST",
            data: data,
            dataType:"json",
            success: function( result ) {
                setTimeout($.unblockUI, 3000);
                if (result.permitido == true)
                {
                    alertify.success(result.mensaje);
                } else {
                    alertify.error(result.mensaje);
                }
            }
        })
	});

	//******************* 1.2 RESULTADO MENSUAL ***************************************************
    $("#btnresultadomensual").on("click", function(){
        var mes = $("#cboMeses").val();
        var anno = $("#cboAnnos").val();
        var data = "mes=" + mes + "&anno=" + anno;
        $.ajax({
            url: "includes/resultado_mensual.php",
            type: "POST",
            data: data,
            dataType:"json",
            success: function( result ) {
                setTimeout($.unblockUI, 3000);
                if (result.permitido == true)
                {
                    alertify.success(result.mensaje);
                } else {
                    alertify.error(result.mensaje);
                }
            }
        })
    });

	//******************* 2.1 FISCALIZACIONES PUNTUALES ********************************************
    $("#btnpuntuales").on("click", function(){
        var mes = $("#cboMeses").val();
        var anno = $("#cboAnnos").val();
        var data = "mes=" + mes + "&anno=" + anno;
        $.ajax({
            url: "includes/fisc_puntuales.php",
            type: "POST",
            data: data,
            dataType:"json",
            success: function( result ) {
                setTimeout($.unblockUI, 3000);
                if (result.permitido == true)
                {
                    alertify.success(result.mensaje);
                } else {
                    alertify.error(result.mensaje);
                }
            }
        })
    });

	//******************* 2.3 OTROS PROGRAMAS ***************************************************
    $("#btnotrosprogramas").on("click", function(){
        var mes = $("#cboMeses").val();
        var anno = $("#cboAnnos").val();
        var data = "mes=" + mes + "&anno=" + anno;
        $.ajax({
            url: "includes/otros_programas.php",
            type: "POST",
            data: data,
            dataType:"json",
            success: function( result ) {
                setTimeout($.unblockUI, 3000);
                if (result.permitido == true)
                {
                    alertify.success(result.mensaje);
                } else {
                    alertify.error(result.mensaje);
                }
            }
        })
    });

    //******************* 2.2 OPERATIVOS ***************************************************
    $("#btnoperativos").on("click", function(){
        var mes = $("#cboMeses").val();
        var anno = $("#cboAnnos").val();
        var data = "mes=" + mes + "&anno=" + anno;
        $.ajax({
            url: "includes/operativos.php",
            type: "POST",
            data: data,
            dataType:"json",
            success: function( result ) {
                setTimeout($.unblockUI, 3000);
                if (result.permitido == true)
                {
                    alertify.success(result.mensaje);
                } else {
                    alertify.error(result.mensaje);
                }
            }
        })
    });

	//******************* 3.1 GENERAR SIGER FISCALIZACION - Fuerza Fiscal ***************************************************
    $("#btnsigerFF").on("click", function(){
        var mes = $("#cboMeses").val();
        var anno = $("#cboAnnos").val();
        var data = "mes=" + mes + "&anno=" + anno;
        $.ajax({
            url: "includes/sg_fuerza_fiscal.php",
            type: "POST",
            data: data,
            dataType:"json",
            success: function( result ) {
                if (result.permitido == true)
                {       
                    $.ajax({
                        url: "includes/sg_dist_fuerza_fiscal.php",
                        type: "POST",
                        data: data,
                        dataType:"json",
                        success: function( r3 ) {
                            setTimeout($.unblockUI, 3000);
                            if (r3.permitido == true)
                            {
                                alertify.success(r3.mensaje);
                            } else {
                                alertify.error(r3.mensaje);
                            }
                        }
                    })
                }
            }
        })
    });

    //******************* 3.2 GENERAR SIGER FISCALIZACION ***************************************************
    $("#btnsiger42").on("click", function(){
        var mes = $("#cboMeses").val();
        var anno = $("#cboAnnos").val();
        var data = "mes=" + mes + "&anno=" + anno;
        $.ajax({
            url: "includes/sg_cuadro_4_2.php",
            type: "POST",
            data: data,
            dataType:"json",
            success: function( m ) {
                $.ajax({
                    url: "includes/sg_cuadro_4_2_aa.php",
                    type: "POST",
                    data: data,
                    dataType:"json",
                    success: function( n ) {
                        $.ajax({
                            url: "includes/sg_cuadro_4_3.php",
                            type: "POST",
                            data: data,
                            dataType:"json",
                            success: function( o ) {
                                $.ajax({
                                    url: "includes/sg_cuadro_4_3_aa.php",
                                    type: "POST",
                                    data: data,
                                    dataType:"json",
                                    success: function( p ) {
                                        $.ajax({
                                            url: "includes/sg_cuadro_4_4.php",
                                            type: "POST",
                                            data: data,
                                            dataType:"json",
                                            success: function( q ) {
                                                $.ajax({
                                                    url: "includes/sg_cuadro_4_4_aa.php",
                                                    type: "POST",
                                                    data: data,
                                                    dataType:"json",
                                                    success: function( rs ) {
                                                        setTimeout($.unblockUI, 3000);
                                                        if (rs.permitido == true)
                                                        {
                                                            alertify.success(rs.mensaje);
                                                        } else {
                                                            alertify.error(rs.mensaje);
                                                        }
                                                    }
                                                })
                                            }
                                        })
                                    }
                                })
                            }
                        })
                    }
                })
            }
        })
    });

    //******************* 4.1 GENERAR PRACTICAR FISCALIZACION ***************************************************
    $("#btnpf").on("click", function(){
        var mes = $("#cboMeses").val();
        var anno = $("#cboAnnos").val();
        var data = "mes=" + mes + "&anno=" + anno;
        $.ajax({
            url: "includes/pf_actas_notificadas.php",
            type: "POST",
            data: data,
            dataType:"json",
            success: function( p1 ) {
                $.ajax({
                    url: "includes/pf_allanamientos.php",
                    type: "POST",
                    data: data,
                    dataType:"json",
                    success: function( q1) {
                        $.ajax({
                            url: "includes/pf_aceptacion_reparo.php",
                            type: "POST",
                            data: data,
                            dataType:"json",
                            success: function( s1 ) {
                                setTimeout($.unblockUI, 3000);
                                if (s1.permitido == true)
                                {
                                    alertify.success(s1.mensaje);
                                } else {
                                    alertify.error(s1.mensaje);
                                }
                            }
                        })
                    }
                })
            }
        })
    });

    //******************* 5.1 VERIFICAR DEBERES FORMALES ***************************************************
    $("#btnvdf").on("click", function(){
        var mes = $("#cboMeses").val();
        var anno = $("#cboAnnos").val();
        var data = "mes=" + mes + "&anno=" + anno;
        $.ajax({
            url: "includes/ris_notificadas.php",
            type: "POST",
            data: data,
            dataType:"json",
            success: function( t2 ) {
                $.ajax({
                    url: "includes/ris_pagadas.php",
                    type: "POST",
                    data: data,
                    dataType:"json",
                    success: function( u2 ) {
                        setTimeout($.unblockUI, 3000);
                        if (u2.permitido == true)
                        {
                            alertify.success(u2.mensaje);
                        } else {
                            alertify.error(u2.mensaje);
                        }
                    }
                })
            }
        })
    });

    //******************* 6.1 GENERAR CASOS EN PROCESO ***************************************************
    $("#btnproceso").on("click", function(){
        var mes = $("#cboMeses").val();
        var anno = $("#cboAnnos").val();
        var data = "mes=" + mes + "&anno=" + anno;
        $.ajax({
            url: "includes/casos_proceso.php",
            type: "POST",
            data: data,
            dataType:"json",
            success: function( resultc ) {
                setTimeout($.unblockUI, 3000);
                if (resultc.permitido == true)
                {
                    alertify.success(resultc.mensaje);
                } else {
                    alertify.error(resultc.mensaje);
                }
            }
        })
    });

    //******************* 6.2 GENERA FORMATO CASOS EN PROCESO ***************************************************
    $("#btnformatoproceso").on("click", function(){
        var mes = $("#cboMeses").val();
        var anno = $("#cboAnnos").val();
        var data = "mes=" + mes + "&anno=" + anno;
        $.ajax({
            url: "includes/formato_casos_proceso.php",
            type: "POST",
            data: data,
            dataType:"json",
            success: function( resultfc ) {
                setTimeout($.unblockUI, 3000);
                if (resultfc.permitido == true)
                {
                    alertify.success(resultfc.mensaje);
                } else {
                    alertify.error(resultfc.mensaje);
                }
            }
        })
    });

     //******************* 7.1. GENERA CONTROLES INTERNOS ********************************************************
    $("#btn_controles").on("click", function(){
        var mes = $("#cboMeses").val();
        var anno = $("#cboAnnos").val();
        var data = "mes=" + mes + "&anno=" + anno;
        $.ajax({
            url: "reportes/casos_proceso.excel.php",
            type: "POST",
            data: data,
            dataType:"json",
            success: function (resultcp) {
                $.ajax({
                    url: "reportes/rel_casos_proceso.excel.php",
                    type: "POST",
                    data: data,
                    dataType: "json",
                    success: function (resultig) {
                        //*********
                        $.ajax({
                            url: "reportes/inf_gestion.excel.php",
                            type: "POST",
                            data: data,
                            dataType: "json",
                            success: function (resultig) {
                                $.ajax({
                                    url: "reportes/operativos.excel.php",
                                    type: "POST",
                                    data: data,
                                    dataType: "json",
                                    success: function (resultop) {
                                        $.ajax({
                                            url: "reportes/otros_prog.excel.php",
                                            type: "POST",
                                            data: data,
                                            dataType: "json",
                                            success: function (resultprg) {
                                                $.ajax({
                                                    url: "reportes/practicar_fisc.excel.php",
                                                    type: "POST",
                                                    data: data,
                                                    dataType: "json",
                                                    success: function (resultpf) {
                                                        $.ajax({
                                                            url: "reportes/puntuales.excel.php",
                                                            type: "POST",
                                                            data: data,
                                                            dataType: "json",
                                                            success: function (resultfp) {
                                                                $.ajax({
                                                                    url: "reportes/result_mensual.excel.php",
                                                                    type: "POST",
                                                                    data: data,
                                                                    dataType: "json",
                                                                    success: function (resultrm) {
                                                                        $.ajax({
                                                                            url: "reportes/verificar_vdf.excel.php",
                                                                            type: "POST",
                                                                            data: data,
                                                                            dataType: "json",
                                                                            success: function (resultvdf) {
                                                                                $.ajax({
                                                                                    url: "reportes/siger.excel.php",
                                                                                    type: "POST",
                                                                                    data: data,
                                                                                    dataType: "json",
                                                                                    success: function (resultsig) {
                                                                                        $.ajax({
                                                                                            url: "reportes/controles_internos.excel.php",
                                                                                            type: "POST",
                                                                                            data: data,
                                                                                            dataType: "json",
                                                                                            success: function (resultci) {
                                                                                                setTimeout($.unblockUI, 3000);
                                                                                                if (resultci.permitido == true) {
                                                                                                    alertify.success(resultci.mensaje);
                                                                                                } else {
                                                                                                    alertify.error(resultci.mensaje);
                                                                                                }
                                                                                                $("#contenido__rpt").load('listar.php');
                                                                                                $('#reportes').show();
                                                                                            }
                                                                                        })
                                                                                    }
                                                                                })
                                                                            }
                                                                        })
                                                                    }
                                                                })
                                                            }
                                                        })
                                                    }
                                                })
                                            }
                                        })

                                    }
                                })
                            }
                        })
                        //************************
                    }
                })
            }
        })
    });
    //************************************************************************************************************************************
    
});