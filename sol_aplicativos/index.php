<?php
session_start();
// error_reporting(0);
include "../conexion.php";
//--------------------
$_SESSION['VAR_USUARIO'] = '-1';
$_SESSION['VAR_CLAVE'] = '-1';
?>

<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="../lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="../lib/bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../lib/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../plugins/animate.min.css">
    <script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>
</head>

<body style="background: transparent !important;">
    <div class="container d-flex justify-content-center align-items-center" style="min-height:100vh;">
        <div class="card shadow p-4" style="max-width: 400px; width: 100%; position: relative;">

            <form action="valida.php" method="post">
                <div class="text-center mb-3">
                    <img src="../imagenes/logo.jpg" height="75" class="img-fluid mb-2" alt="Logo" /><br>
                    <h1 class="animate__animated animate__backInLeft h4 mb-3">Inicio de Sesión</h1>
                </div><br>
                <div class="mb-3">
                    <input type="text" class="form-control" placeholder="Ingrese Su Cédula Aqui" name="OUSUARIO"
                        required autofocus>
                </div>
                <div class="d-grid mb-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>Entrar
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>