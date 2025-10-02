<?php

$info = array();
$permitido = true;
$mensaje = "Es Verdadero";

$info = array("permitido" => $permitido,
				"mensaje" => $mensaje);

echo json_encode($info);

?>