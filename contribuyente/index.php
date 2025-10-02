<?php

session_start();

$_SESSION['VAR_USUARIO']='-1';
$_SESSION['VAR_CLAVE']='-1';
$_SESSION['VARIABLE1']='-1';

?>
<style>
.logo img {

  max-width: 100%;
  height: auto;

}
.form desing {

  max-width: 100%;
  height: auto;

}
	
</style>
  <html>
<head>
<title>CONTRIBUYENTE</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/cabecera.css">
    <link rel="stylesheet" href="css/animate.min.css">
	<script language="javascript" type="text/javascript" src="../funciones/auxiliar_java.js"></script>
</head>

<body class="logo" style="background-color:#C0C0C0;" class="bodyIndex" >

 <form class="form" action="valida.php" method="post">
  <p> <img src="css/IMG/logo.jpg" width="200" height="75" border="0" usemap="#Map" /> </p>
   <h1 class="animate__animated animate__backInLeft">Logeate!</h1>
   <p><input type="text" placeholder="Ingrese Su Cédula Aqui" name="OUSUARIO"></p>
   <p><input type="password" placeholder="Ingrese Su Clave Aqui" name="OCLAVE"></p>
   <input type="submit" value="Entrar">
    <?php include "../msg_validacion.php";?>
   
   </form> 
      <style>  
.boton {
   padding: 15px;
   background-color: red;
   color: white;
}
</style>
<a href="../index.php" class="boton">INICIO</a>


 
</body>
</html>