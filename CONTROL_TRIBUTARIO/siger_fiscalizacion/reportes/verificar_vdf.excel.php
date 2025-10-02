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
$objPHPExcel = $objReader->load("../formatos/verificar_vdf.xlsx");
// Indicamos que se pare en la hoja uno del libro

//***************************************** INICIO HOJA 1 DEL FORMATO PRACTICAR FISCALIZACIONES *****************************************************
    $objPHPExcel->setActiveSheetIndex(0);
    //Escribimos en la hoja en la celda B1
    $pagina = $objPHPExcel->getActiveSheet();

    //Datos para generar los valores en el formato
    $sql = "SELECT formato, titulo, periodo, celdas_editables, celda_inicial, celda_final, region, nom_region FROM formatos WHERE formato = 'verificar_vdf.xlsx'";
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
    $datos = "SELECT rif, num_providencia, tipo_programa, tipo_impuesto, conformidad, num_resolucion, tipo_contribuyente, emision, notificacion, multa, comiso, clausura, suspension, pp_multa, pp_valor_bienes FROM ris_notificadas WHERE periodo_inicio = '".$inicio."' AND periodo_fin = '".$fin."'";
    $tabla = $conexion->query($datos);
    $x = $reg->celda_inicial;
    while ($data = $tabla->fetch_object())
    {
        $i=$x;

        if ($i < $reg->celda_final)
        {

            $pagina->SetCellValue($lista[0].$i, $data->rif);
            $pagina->SetCellValue($lista[1].$i, $data->num_providencia);
            $pagina->SetCellValue($lista[2].$i, $data->tipo_programa);
            $pagina->SetCellValue($lista[3].$i, $data->tipo_impuesto);
            $pagina->SetCellValue($lista[4].$i, $data->conformidad);
            $pagina->SetCellValue($lista[5].$i, $data->num_resolucion);
            $pagina->SetCellValue($lista[6].$i, $data->tipo_contribuyente);
            $pagina->SetCellValue($lista[7].$i, voltea_fecha($data->emision));
            $pagina->SetCellValue($lista[8].$i, voltea_fecha($data->notificacion));
            $pagina->SetCellValue($lista[9].$i, $data->multa);
            $pagina->SetCellValue($lista[10].$i, $data->comiso);
            $pagina->SetCellValue($lista[11].$i, $data->clausura);
            $pagina->SetCellValue($lista[12].$i, $data->suspension);
            $pagina->SetCellValue($lista[13].$i, $data->pp_multa);
            $pagina->SetCellValue($lista[14].$i, $data->pp_valor_bienes);
            
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
    $datos_2 = "SELECT rif, num_providencia, tipo_programa, tipo_impuesto, num_resolucion, tipo_contribuyente, emision, notificacion, pago, monto FROM ris_pagadas WHERE periodo_inicio = '".$inicio."' AND periodo_fin = '".$fin."'";
    $tabla_2 = $conexion->query($datos_2);
    $x = $reg->celda_inicial;
    while ($data_2 = $tabla_2->fetch_object())
    {
        $i=$x;

        if ($i < $reg->celda_final)
        {

            $pagina->SetCellValue($lista[0].$i, $data_2->rif);
            $pagina->SetCellValue($lista[1].$i, $data_2->num_providencia);
            $pagina->SetCellValue($lista[2].$i, $data_2->tipo_programa);
            $pagina->SetCellValue($lista[3].$i, $data_2->tipo_impuesto);
            $pagina->SetCellValue($lista[4].$i, $data_2->num_resolucion);
            $pagina->SetCellValue($lista[5].$i, $data_2->tipo_contribuyente);
            $pagina->SetCellValue($lista[6].$i, voltea_fecha($data_2->emision));
            $pagina->SetCellValue($lista[7].$i, voltea_fecha($data_2->notificacion));
            $pagina->SetCellValue($lista[8].$i, voltea_fecha($data_2->pago));
            $pagina->SetCellValue($lista[9].$i, $data_2->monto);
            
            $x++;
        }
    }
//***************************************** FIN HOJA 2 DEL FORMATO PRACTICAR FISCALIZACIONES ********************************************************

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
$objWriter->save("../formatos/generados/GEN_verificar_vdf.xlsx");

$mensaje = "Informe Generado Satisfactoriamente";
$permitido = true;

$info = array("permitido"=>$permitido,
				"mensaje"=>$mensaje);

echo json_encode($info);

exit;
?>