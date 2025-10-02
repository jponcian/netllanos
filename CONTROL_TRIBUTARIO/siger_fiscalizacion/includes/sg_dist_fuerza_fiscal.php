<?php
//*****    SCRIPT PARA DIST DE FUERZA FISCAL    *****//
//                                                   //
//    Elaborado por Gustavo García para el SENIAT    //
//                                                   //
///////////////////////////////////////////////////////

//incluimos las funciones
include("conexion.php");
include("../funciones/funcionesphp.php");

//mysql_query("SET NAMES 'utf8'");

//Variables a utilizar
$info = array();
$mensaje = "Error al Generar el Informe";
$permitido = false;
$mes = $_POST['mes'];
$anno = $_POST['anno'];
$getFechas = fechas($mes, $anno);
$inicio = $getFechas[0];
$fin = $getFechas[1];

BorrarRegistros($conexion, 'sg_dist_fuerza_fiscal', $inicio, $fin);

//OBTENEMOS LA CANTIDAD DE FISCALES
$sql_cantidad = "SELECT count(DISTINCTROW ci_fiscal1) as cantidad FROM expedientes_fiscalizacion";
$tabla_cantidad = $con->query($sql_cantidad);
$reg_cantidad = $tabla_cantidad->fetch_object();
$fuerza_fiscal_total = $reg_cantidad->cantidad;
       
//VERIFICAMOS SI EXISTE REGISTRO PARA ESE PERIODO
$sql_existe = "SELECT sg_dist_fuerza_fiscal.descripcion FROM sg_dist_fuerza_fiscal WHERE sg_dist_fuerza_fiscal.periodo_inicio = '".$inicio."' AND sg_dist_fuerza_fiscal.periodo_fin = '".$fin."'";
$result = $conexion->query($sql_existe);
$cantidad = $result->num_rows;

if ($cantidad === 0)
{
	//FISCALES ACTUANTES
	$fisc_integral = 0;
	$fisc_puntual = 0;
	$verificacion = 0;
	$otros = 0;

	$sqlFiscales = "SELECT count(DISTINCTROW expedientes_fiscalizacion.ci_fiscal1) AS cantidad, a_tipo_programa.clasificacion FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa WHERE expedientes_fiscalizacion.fecha_emision BETWEEN '".$inicio."' AND '".$fin."' group by a_tipo_programa.clasificacion"; 
	$tablaFiscales = $con->query($sqlFiscales);
	while ($fiscal = $tablaFiscales->fetch_object())
	{
		if ($fiscal->clasificacion === 'FIN' or $fiscal->clasificacion === 'FIR')
		{
			$fisc_integral += $fiscal->cantidad;
		}

		if ($fiscal->clasificacion === 'FPN' or $fiscal->clasificacion === 'FPR')
		{
			$fisc_puntual += $fiscal->cantidad;
		}

		if ($fiscal->clasificacion === 'VN' or $fiscal->clasificacion === 'VR')
		{
			$verificacion += $fiscal->cantidad;
		}

		if ($fiscal->clasificacion === 'OP')
		{
			$otros += $fiscal->cantidad;
		}
	}

	if ($fisc_integral > $fuerza_fiscal_total) { $fisc_integral = $fuerza_fiscal_total; }
	if ($fisc_puntual > $fuerza_fiscal_total) { $fisc_puntual = $fuerza_fiscal_total; }
	if ($verificacion > $fuerza_fiscal_total) { $verificacion = $fuerza_fiscal_total; }
	if ($otros > $fuerza_fiscal_total) { $otros = $fuerza_fiscal_total; }

	$agregar = "INSERT INTO sg_dist_fuerza_fiscal (descripcion, fisc_integral, fisc_puntual, verificacion, otros, periodo_inicio, periodo_fin) VALUES ('FISCALES', ".$fisc_integral.", ".$fisc_puntual.", ".$verificacion.", ".$otros.", '".$inicio."','".$fin."')";
	$tablaAgregar = $conexion->query($agregar);


	//SUPERVISORES
	$fisc_integral_s = 0;
	$fisc_puntual_s = 0;
	$verificacion_s = 0;
	$otros_s = 0;

	$sqlSupervisores = "SELECT count(DISTINCTROW expedientes_fiscalizacion.ci_supervisor) AS cantidad, a_tipo_programa.clasificacion FROM expedientes_fiscalizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa WHERE expedientes_fiscalizacion.fecha_emision BETWEEN '".$inicio."' AND '".$fin."' group by a_tipo_programa.clasificacion";
	$tablaSupervisores = $con->query($sqlSupervisores);
	while ($super = $tablaSupervisores->fetch_object())
	{
		if ($super->clasificacion === 'FIN' or $super->clasificacion === 'FIR')
		{
			$fisc_integral_s += $super->cantidad;
		}

		if ($super->clasificacion === 'FPN' or $super->clasificacion === 'FPR')
		{
			$fisc_puntual_s += $super->cantidad;
		}

		if ($super->clasificacion === 'VN' or $super->clasificacion === 'VR')
		{
			$verificacion_s += $super->cantidad;
		}

		if ($super->clasificacion === 'OP')
		{
			$otros_s += $super->cantidad;
		}

	}

	if ($fisc_integral_s > $fuerza_fiscal_total) { $fisc_integral_s = $fuerza_fiscal_total; }
	if ($fisc_puntual_s > $fuerza_fiscal_total) { $fisc_puntual_s = $fuerza_fiscal_total; }
	if ($verificacion_s > $fuerza_fiscal_total) { $verificacion_s = $fuerza_fiscal_total; }
	if ($otros_s > $fuerza_fiscal_total) { $otros_s = $fuerza_fiscal_total; }

	$agregar_s = "INSERT INTO sg_dist_fuerza_fiscal (descripcion, fisc_integral, fisc_puntual, verificacion, otros, periodo_inicio, periodo_fin) VALUES ('SUPERVISORES', ".$fisc_integral_s.", ".$fisc_puntual_s.", ".$verificacion_s.", ".$otros_s.", '".$inicio."','".$fin."')";
	//echo $agregar.'<br>';
	$tablaAgregar_s = $conexion->query($agregar_s);

	if ($conexion->affected_rows){
		$mensaje = "Informe Generado Satisfactoriamente";
		$permitido = true;
	}

}

$info = array("permitido"=>$permitido,
				"mensaje"=>$mensaje);

echo json_encode($info);

?>