<?php
session_start();
include "../conexion.php";

if ($_SESSION['VERIFICADO'] != "SI") {
    header("Location: index.php?errorusuario=val");
    exit();
}

$acceso = 161;
//------- VALIDACION ACCESO USUARIO
include "../validacion_usuario.php";
//-----------------------------------	

// PARA ELIMINAR EL TEMPORAL
$mysqli = $_SESSION['conexionsqli'];
$consultad = "DELETE FROM alm_ingresos_detalle_tmp WHERE usuario = " . $_SESSION['CEDULA_USUARIO'] . ";";
$mysqli->query($consultad);

header('Content-Type: text/html; charset=utf-8');
?>
<html>

<head>
    <title>Registrar Ingreso al Almacen</title>

</head>
<?php include "../funciones/mensajes.php"; ?>

<body style="background: transparent !important;">
    <div class="container py-4">
        <!-- Título y menú -->
        <div class="row mb-3">
            <div class="col-12 text-center">
                <?php include "menu.php"; ?>
            </div>
        </div>

        <div id="mainContent">
            <form name="form1" id="form1" method="post" class="needs-validation" novalidate>
                <?php
                if ($_SESSION['VERIFICADO'] == 'SI') {
                    $mysqli = $_SESSION['conexionsqli'];
                    $consulta = 'SELECT z_empleados.cedula, Apellidos as apellidos, Nombres as nombres, descripcion as division FROM z_empleados , z_jefes_detalle WHERE z_jefes_detalle.division = z_empleados.division AND z_empleados.cedula=0' . $_SESSION['CEDULA_USUARIO'] . ';';
                    $tabla = $mysqli->query($consulta);
                    if ($registro = $tabla->fetch_assoc()) {
                        $usuario = strtoupper($registro['nombres'] . ' ' . $registro['apellidos']);
                        $dependencia = strtoupper($registro['division']);
                    }
                }
                ?>

                <div class="row justify-content-center mb-2">
                    <div class="col-md-6">
                        <div class="card shadow border-0">
                            <div class="card-header bg-danger text-white text-center">
                                <h5 class="mb-0"><i class="fa fa-file-alt me-2"></i>Datos del Ingreso</h5>
                            </div>
                            <div class="card-body py-2">
                                <div class="mb-1 row align-items-center">
                                    <label class="col-sm-4 col-form-label small fw-bold">Nombres:</label>
                                    <div class="col-sm-8 d-flex align-items-center">
                                        <span class="form-control-plaintext small mb-0"><?php echo $usuario; ?></span>
                                    </div>
                                </div>
                                <div class="mb-1 row align-items-center">
                                    <label class="col-sm-4 col-form-label small fw-bold">Dependencia:</label>
                                    <div class="col-sm-8 d-flex align-items-center">
                                        <span
                                            class="form-control-plaintext small mb-0"><?php echo $dependencia; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="combo-container">
                    <!-- El combo de artículos se cargará aquí -->
                </div>

                <div class="row mb-4">
                    <div class="col-12 text-center d-flex justify-content-center align-items-center gap-3">
                        <button type="button" id="guardarBtn" class="btn btn-success btn-lg px-5 shadow me-2"
                            name="CMDGUARDAR" onClick="guardar()" disabled data-bs-toggle="tooltip"
                            title="Agregue artículos para habilitar Guardar">
                            <i class="fa fa-save me-2"></i>Guardar
                        </button>
                        <button class="btn btn-primary btn-lg rounded-circle ms-2" id="openCartBtn" type="button"
                            data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas" aria-controls="cartOffcanvas">
                            <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                            <span id="cartCount" class="badge bg-light text-dark">0</span>
                        </button>
                    </div>
                </div>
            </form>

            <footer class="text-center mt-4">
                <?php //include "../pie.php"; ?>
            </footer>
        </div>
    </div>
</body>

</html>

<script>
    function agregar() {
        var articulo = $('#OARTICULO').val();
        var precio = $('#OPRECIO').val();
        var cantidad = $('#OCANTIDAD').val();

        if (articulo === "" || articulo === null) {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Debe seleccionar un artículo.'
            });
            return false;
        }

        if (precio.trim() === '' || isNaN(precio) || parseFloat(precio) <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'El campo Precio es obligatorio, debe ser numérico y mayor que cero.'
            });
            $('#OPRECIO').focus();
            return false;
        }

        if (cantidad.trim() === '' || isNaN(cantidad) || parseInt(cantidad) <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'El campo Cantidad es obligatorio, debe ser un número entero y mayor que cero.'
            });
            $('#OCANTIDAD').focus();
            return false;
        }

        var parametros = $("#form1").serialize();
        $.ajax({
            type: 'POST',
            url: '10ingreso_agregar_articulo.php',
            dataType: "json",
            data: parametros,
            success: function (data) {
                if (data.tipo == "alerta") {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Atención',
                        text: data.msj
                    });
                } else {
                    Swal.fire({
                        toast: true,
                        position: 'bottom-end',
                        icon: 'success',
                        title: data.msj,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                }
                $('#tabla-container').load('10ingreso_tabla.php', function () {
                    updateCartCount();
                });
                $('#combo-container').load('10ingreso_combo.php');
            }
        });
        return false;
    }

    function guardar() {
        Swal.fire({
            title: '¿Desea Guardar el Ingreso?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                var parametros = $("#form1").serialize();
                $.ajax({
                    type: 'POST',
                    url: '10ingreso_guardar.php',
                    dataType: "json",
                    data: parametros,
                    success: function (data) {
                        if (data && data.tipo === "alerta") {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Atención',
                                text: data.msj
                            });
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Éxito',
                                text: data.msj,
                                confirmButtonText: 'Aceptar'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'menuprincipal.php';
                                }
                            });
                        }
                        $('#tabla-container').load('10ingreso_tabla.php', function () {
                            updateCartCount();
                        });
                    }
                });
            }
        });
        return false;
    }

    function eliminar(id) {
        var parametros = "id=" + id;
        $.ajax({
            url: "10ingreso_eliminar.php",
            type: "POST",
            data: parametros,
            success: function (r) {
                $('#tabla-container').load('10ingreso_tabla.php', function () {
                    updateCartCount();
                });
                Swal.fire({
                    toast: true,
                    position: 'bottom-end',
                    icon: 'success',
                    title: 'Registro Eliminado Correctamente',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            }
        });
        return false;
    }

    function updateCartCount() {
        var count = $('#tabla-container').find('.articulo-item').length;
        $('#cartCount').text(count);
        if (count > 0) {
            $('#guardarBtn').prop('disabled', false).attr('title', '').removeAttr('data-bs-original-title');
        } else {
            $('#guardarBtn').prop('disabled', true).attr('data-bs-original-title', 'Agregue artículos para habilitar Guardar');
        }
    }

    $(document).ready(function () {
        $('#combo-container').load('10ingreso_combo.php');
        $('#tabla-container').load('10ingreso_tabla.php', function () {
            updateCartCount();
        });

        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>

<!-- Offcanvas carrito -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas" aria-labelledby="cartOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="cartOffcanvasLabel"><i class="fas fa-box-open me-2"
                aria-hidden="true"></i>Artículos en el Ingreso</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div id="tabla-container">
            <!-- La tabla de artículos se cargará aquí -->
        </div>
    </div>
</div>