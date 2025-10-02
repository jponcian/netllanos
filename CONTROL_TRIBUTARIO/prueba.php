<?php
$con = mysql_connect("localhost", "root", "");
mysql_select_db('losllanos_prueba', $con);

$SQL = "SELECT Anno, Numero, sector FROM vista_ct_salida_archivo WHERE Anno=2016 and sector=2";

$valor = registros($SQL);
echo count(registros($SQL));
$n = 4;
for($i=0; $i<$n; $i++)
      {
      //saco el valor de cada elemento
	  echo $valor[Anno].' - '.$valor[Numero];
	  echo "<br>";
      }
	  
foreach($valor as $posicion=>$jugador)
	{
		if (!is_numeric($posicion))
		{
			echo "El " . $posicion . " es " . $jugador;
			echo "<br>";
		}
	}


function registros($sql)
{
	$row = array();
	$tabla = mysql_query($sql);
	$cantidad = mysql_num_rows($tabla);
	$row = mysql_fetch_array($tabla);
	
	return $row;
}

?>
