<?php
 
// Camino a los include
//set_include_path(get_include_path() . PATH_SEPARATOR . '../Classes/');</p>
// PHPExcel
require_once '../PHPExcel/librerias/ExcelLibrary/PHPExcel.php';
// PHPExcel_IOFactory
include '../PHPExcel/librerias/ExcelLibrary/PHPExcel/IOFactory.php';
include("../includes/conexion.php");
include("../funciones/funcionesphp.php");

//******VARIABLES
$mes = $_POST['mes'];
$anno = $_POST['anno'];
$getFechas = fechas($mes, $anno);
$inicio = $getFechas[0];
$fin = $getFechas[1];
$info = array();
$mensaje = "Error al Generar el Informe";
$permitido = false;

// Creamos un objeto PHPExcel
$objPHPExcel = new PHPExcel();
// Leemos un archivo Excel 2007
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("../formatos/practicar_fiscalizaciones.xlsx");
// Indicamos que se pare en la hoja uno del libro

//***************************************** INICIO HOJA 1 DEL FORMATO PRACTICAR FISCALIZACIONES *****************************************************
    $objPHPExcel->setActiveSheetIndex(0);
    //Escribimos en la hoja en la celda B1
    $pagina = $objPHPExcel->getActiveSheet();

    //Datos para generar los valores en el formato
    $sql = "SELECT formato, titulo, periodo, celdas_editables, celda_inicial, celda_final, region, nom_region FROM formatos WHERE formato = 'practicar_fiscalizaciones.xlsx'";
    $tabla_formato = $conexion->query($sql);
    $reg = $tabla_formato->fetch_object();
    $lista = explode(',', $reg->celdas_editables);
    $titulo = explode(',', $reg->titulo);
    $periodo = explode(',', $reg->periodo);
    $region = explode(',', $reg->region);


    //**************TITULO Y AÑO******************
    $pagina->SetCellValue($region[0], $reg->nom_region);
    $pagina->SetCellValue($titulo[0], nomMes($mes));
    $pagina->SetCellValue($periodo[0], $anno);

    //Buscamos los registros de la Base de datos
    $datos = "SELECT rif, num_acta, tipo_programacion, tipo_impuesto, reparo_not, reparo_inf, conformidad, fisc_integral, fisc_interes_estrategico, fisc_interes_no_estrategico, emision, notificacion, med_cautelar_sol, med_cautelar_acordada, impuesto, multa, intereses, otras_multas
    FROM pf_actas_notificadas WHERE periodo_inicio = '".$inicio."' AND periodo_fin = '".$fin."'";
    $tabla = $conexion->query($datos);
    $x = $reg->celda_inicial + 2;
    while ($data = $tabla->fetch_object())
    {
        $i=$x;

        if ($i < $reg->celda_final)
        {

            $pagina->SetCellValue($lista[0].$i, $data->rif);
            $pagina->SetCellValue($lista[1].$i, $data->num_acta);
            $pagina->SetCellValue($lista[2].$i, $data->tipo_programacion);
            $pagina->SetCellValue($lista[3].$i, $data->tipo_impuesto);
            $pagina->SetCellValue($lista[4].$i, $data->reparo_not);
            $pagina->SetCellValue($lista[5].$i, $data->reparo_inf);
            $pagina->SetCellValue($lista[6].$i, $data->conformidad);
            $pagina->SetCellValue($lista[7].$i, $data->fisc_integral);
            $pagina->SetCellValue($lista[8].$i, $data->fisc_interes_estrategico);
            $pagina->SetCellValue($lista[9].$i, $data->fisc_interes_no_estrategico);
            $pagina->SetCellValue($lista[10].$i, voltea_fecha($data->emision));
            $pagina->SetCellValue($lista[11].$i, voltea_fecha($data->notificacion));
            $pagina->SetCellValue($lista[12].$i, $data->med_cautelar_sol);
            $pagina->SetCellValue($lista[13].$i, $data->med_cautelar_acordada);
            $pagina->SetCellValue($lista[14].$i, $data->impuesto);
            $pagina->SetCellValue($lista[15].$i, $data->multa);
            $pagina->SetCellValue($lista[16].$i, $data->intereses);
            $pagina->SetCellValue($lista[17].$i, $data->otras_multas);
            
            $x++;
        }
    }
//***************************************** FIN HOJA 1 DEL FORMATO PRACTICAR FISCALIZACIONES ********************************************************

//***************************************** INICIO HOJA 2 DEL FORMATO PRACTICAR FISCALIZACIONES *****************************************************
    $objPHPExcel->setActiveSheetIndex(1);
    $pagina = $objPHPExcel->getActiveSheet();

    //**************TITULO Y AÑO******************
    $pagina->SetCellValue($region[1], $reg->nom_region);
    $pagina->SetCellValue($titulo[1], nomMes($mes));
    $pagina->SetCellValue($periodo[1], $anno);

    //Buscamos los registros de la Base de datos
    $datos_2 = "SELECT rif, num_acta, tipo_impuesto, fisc_integral, fisc_interes_estrategico, fisc_interes_no_estrategico, actas_aceptada, actas_parcial_aceptada, actas_no_aceptada, emision, notificacion, pago, fecha_not_resolucion, med_cautelar_sol, med_cautelar_acordada, impuesto_determinado FROM pf_allanamientos WHERE periodo_inicio = '".$inicio."' AND periodo_fin = '".$fin."'";
    $tabla_2 = $conexion->query($datos_2);
    $x = $reg->celda_inicial;
    while ($data_2 = $tabla_2->fetch_object())
    {
        $i=$x;

        if ($i < $reg->celda_final)
        {

            $pagina->SetCellValue($lista[0].$i, $data_2->rif);
            $pagina->SetCellValue($lista[1].$i, $data_2->num_acta);
            $pagina->SetCellValue($lista[2].$i, $data_2->tipo_impuesto);
            $pagina->SetCellValue($lista[3].$i, $data_2->fisc_integral);
            $pagina->SetCellValue($lista[4].$i, $data_2->fisc_interes_estrategico);
            $pagina->SetCellValue($lista[5].$i, $data_2->fisc_interes_no_estrategico);
            $pagina->SetCellValue($lista[6].$i, $data_2->actas_aceptada);
            $pagina->SetCellValue($lista[7].$i, $data_2->actas_parcial_aceptada);
            $pagina->SetCellValue($lista[8].$i, $data_2->actas_no_aceptada);
            $pagina->SetCellValue($lista[9].$i, voltea_fecha($data_2->emision));
            $pagina->SetCellValue($lista[10].$i, voltea_fecha($data_2->notificacion));
            $pagina->SetCellValue($lista[11].$i, voltea_fecha($data_2->pago));
            if ($data_2->fecha_not_resolucion != "0000-00-00") { $pagina->SetCellValue($lista[12].$i, voltea_fecha($data_2->fecha_not_resolucion)); }
            $pagina->SetCellValue($lista[13].$i, $data_2->med_cautelar_sol);
            $pagina->SetCellValue($lista[14].$i, $data_2->med_cautelar_acordada);
            $pagina->SetCellValue($lista[15].$i, $data_2->impuesto_determinado);
            
            $x++;
        }
    }
//***************************************** FIN HOJA 2 DEL FORMATO PRACTICAR FISCALIZACIONES ********************************************************

//***************************************** INICIO HOJA 3 DEL FORMATO PRACTICAR FISCALIZACIONES *****************************************************
    $objPHPExcel->setActiveSheetIndex(2);
    $pagina = $objPHPExcel->getActiveSheet();

    //**************TITULO Y AÑO******************
    $pagina->SetCellValue($region[2], $reg->nom_region);
    $pagina->SetCellValue($titulo[2], nomMes($mes));
    $pagina->SetCellValue($periodo[2], $anno);

    //Buscamos los registros de la Base de datos
    $datos_3 = "SELECT rif, num_acta, notificacion, pago, fisc_integral, fisc_interes_estrategico, fisc_interes_no_estrategico, num_resolucion, emision_res, notificacion_res, pago_res, impuesto_determinado, multa_vdf, intereses, otras_multas FROM pf_aceptacion_reparo WHERE periodo_inicio = '".$inicio."' AND periodo_fin = '".$fin."'";
    $tabla_3 = $conexion->query($datos_3);
    $x = $reg->celda_inicial;
    while ($data_3 = $tabla_3->fetch_object())
    {
        $i=$x;

        if ($i < $reg->celda_final)
        {

            $pagina->SetCellValue($lista[0].$i, $data_3->rif);
            $pagina->SetCellValue($lista[1].$i, $data_3->num_acta);
            $pagina->SetCellValue($lista[2].$i, voltea_fecha($data_3->notificacion));
            $pagina->SetCellValue($lista[3].$i, voltea_fecha($data_3->pago));
            $pagina->SetCellValue($lista[4].$i, $data_3->fisc_integral);
            $pagina->SetCellValue($lista[5].$i, $data_3->fisc_interes_estrategico);
            $pagina->SetCellValue($lista[6].$i, $data_3->fisc_interes_no_estrategico);
            $pagina->SetCellValue($lista[7].$i, $data_3->num_resolucion);
            if ($data_3->emision_res != "0000-00-00") { $pagina->SetCellValue($lista[8].$i, voltea_fecha($data_3->emision_res)); }
            if ($data_3->notificacion_res != "0000-00-00") { $pagina->SetCellValue($lista[9].$i, voltea_fecha($data_3->notificacion_res)); }
            if ($data_3->pago_res != "0000-00-00") { $pagina->SetCellValue($lista[10].$i, voltea_fecha($data_3->pago_res)); }
            $pagina->SetCellValue($lista[11].$i, $data_3->impuesto_determinado);
            $pagina->SetCellValue($lista[12].$i, $data_3->multa_vdf);
            $pagina->SetCellValue($lista[13].$i, $data_3->intereses);
            $pagina->SetCellValue($lista[14].$i, $data_3->otras_multas);
            
            $x++;
        }
    }
//***************************************** FIN HOJA 3 DEL FORMATO PRACTICAR FISCALIZACIONES ********************************************************




/*
//$objPHPExcel->getActiveSheet()->SetCellValue('B2', 'Hola mundo');
// Color rojo al texto
$objPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
// Texto alineado a la derecha
$objPHPExcel->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
// Damos un borde a la celda
$objPHPExcel->getActiveSheet()->getStyle('B2')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('B2')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('B2')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
*/
//Guardamos el archivo en formato Excel 2007
//Si queremos trabajar con Excel 2003, basta cambiar el 'Excel2007' por 'Excel5' y el nombre del archivo de salida cambiar su formato por '.xls'
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("../formatos/generados/GEN_practicar_fiscalizaciones.xlsx");

$mensaje = "Informe Generado Satisfactoriamente";
$permitido = true;

$info = array("permitido"=>$permitido,
				"mensaje"=>$mensaje);

echo json_encode($info);

exit;
?>