<?php

	include "../conexion.php";
//include "../auxiliar.php";


	$sector = $_POST['sector'];
	$sqldelete = $conexionsql->query("DELETE FROM ct_temp_salida_expediente WHERE sector=$sector");
	$sqldelete1 = $conexionsql->query("DELETE FROM ct_tmp_mod_salida_expediente WHERE sector=$sector");
	$sqldelete2 = $conexionsql->query("DELETE FROM ct_tmp_doc_destfacturas WHERE sector=$sector");
?>