<?php

$fechadada = $_POST['fecha'];
$fecha = new DateTime($fechadada);

$fecha->modify('first day of this month');
$info = array();
echo $fecha->format('d/m/Y').'<br>'; // imprime por ejemplo: 01/12/2012
$inicio = $fecha->format('d/m/Y');

//$fecha = new DateTime();
$fecha->modify('last day of this month');
echo $fecha->format('d/m/Y').'<br>'; // imprime por ejemplo: 31/12/2012
$fin = $fecha->format('d/m/Y');

$info = array("inicio"=>$inicio,
				"fin"=>$fin);

echo json_encode($info);


?>