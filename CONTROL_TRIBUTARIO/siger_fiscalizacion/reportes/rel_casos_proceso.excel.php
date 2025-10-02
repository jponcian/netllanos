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
$objPHPExcel = $objReader->load("../formatos/rel_casos_proceso.xlsx");
// Indicamos que se pare en la hoja uno del libro


//***************************************** AÑO ACTUAL *********************************************************************************

    //***************************************** DETALLES ********************************************************************
        $objPHPExcel->setActiveSheetIndex(0);
        //Escribimos en la hoja en la celda B1
        $pagina = $objPHPExcel->getActiveSheet();
        //----FILA DE TITULOS----------------------------------------------------------------------------------------------------------------
        $pagina->SetCellValue('A1', 'RELACION DE CASOS EN PROCESO AÑO ACTUAL AL '.voltea_fecha($fin));
        //-----------FIN TITULOS-------------------------------------------------------------------------------------------------------------

        $actual = "SELECT anno, numero, descripcion, rif, contribuyente, ci_fiscal, fiscal, ci_supervisor, supervisor, emision, notificacion, nombre_sector, tipo, clasificacion, proceso_auditoria, nivel_supervisor, lapso_allanamiento, otras_causas, desc_formato, formato_cp FROM casos_en_proceso WHERE borrado = 0 AND year(notificacion) = year(date(now()))";
        $tabla_actual = $conexion->query($actual);
        $i = 4;
        while ($reg_actual = $tabla_actual->fetch_object())
        {
            $pagina->SetCellValue('A'.$i, $i - 3);
            $pagina->SetCellValue('B'.$i, $reg_actual->anno);
            $pagina->SetCellValue('C'.$i, $reg_actual->numero);
            $pagina->SetCellValue('D'.$i, $reg_actual->descripcion);
            $pagina->SetCellValue('E'.$i, $reg_actual->rif);
            $pagina->SetCellValue('F'.$i, $reg_actual->contribuyente);
            $pagina->SetCellValue('G'.$i, $reg_actual->ci_fiscal);
            $pagina->SetCellValue('H'.$i, $reg_actual->fiscal);
            $pagina->SetCellValue('I'.$i, $reg_actual->ci_supervisor);
            $pagina->SetCellValue('J'.$i, $reg_actual->supervisor);
            $pagina->SetCellValue('K'.$i, voltea_fecha($reg_actual->emision));
            $pagina->SetCellValue('L'.$i, voltea_fecha($reg_actual->notificacion));
            $pagina->SetCellValue('M'.$i, $reg_actual->nombre_sector);
            $pagina->SetCellValue('N'.$i, $reg_actual->tipo);
            $pagina->SetCellValue('O'.$i, $reg_actual->clasificacion);
            $pagina->SetCellValue('P'.$i, $reg_actual->proceso_auditoria);
            $pagina->SetCellValue('Q'.$i, $reg_actual->nivel_supervisor);
            $pagina->SetCellValue('R'.$i, $reg_actual->lapso_allanamiento);
            $pagina->SetCellValue('S'.$i, $reg_actual->otras_causas);
            $pagina->SetCellValue('T'.$i, $reg_actual->desc_formato);
            $pagina->SetCellValue('U'.$i, $reg_actual->formato_cp);

            $i++;
        }

        //*****************RESUMEN*****************************
        $resumen_actual = "SELECT casos_en_proceso.descripcion, COUNT(casos_en_proceso.anno) as cantidad FROM casos_en_proceso WHERE borrado = 0 AND year(notificacion) = year(date(now())) GROUP BY casos_en_proceso.descripcion";
        $tabla_resumen_actual = $conexion->query($resumen_actual);
        $i++;
        $total = 0;
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('A'.$i, 'PROGRAMA');
        $objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('E'.$i, 'CANTIDAD');
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        
        $i++;

        while ($reg_resumen_actual = $tabla_resumen_actual->fetch_object())
        {
            $pagina->SetCellValue('A'.$i, $reg_resumen_actual->descripcion);
            $pagina->SetCellValue('E'.$i, $reg_resumen_actual->cantidad);
            $total += $reg_resumen_actual->cantidad;
            $i++;

        }

        $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('A'.$i, 'TOTAL');
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('E'.$i, $total);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        $objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        //***************FIN RESUMEN***************************

    //************************************************************************************************************************************


//***************************************** AÑOS ANTERIORES ******************************************************************************

    //***************************************** DETALLES ********************************************************************
        $objPHPExcel->setActiveSheetIndex(1);
        //Escribimos en la hoja en la celda B1
        $pagina = $objPHPExcel->getActiveSheet();
        //----FILA DE TITULOS----------------------------------------------------------------------------------------------------------------
        $pagina->SetCellValue('A1', 'RELACION DE CASOS EN PROCESO AÑOS ANTERIORES AL '.voltea_fecha($fin));
        //-----------FIN TITULOS-------------------------------------------------------------------------------------------------------------

        $actual = "SELECT anno, numero, descripcion, rif, contribuyente, ci_fiscal, fiscal, ci_supervisor, supervisor, emision, notificacion, nombre_sector, tipo, clasificacion, proceso_auditoria, nivel_supervisor, lapso_allanamiento, otras_causas, desc_formato, formato_cp FROM casos_en_proceso WHERE borrado = 0 AND year(notificacion) < year(date(now()))";
        $tabla_actual = $conexion->query($actual);
        $i = 4;
        while ($reg_actual = $tabla_actual->fetch_object())
        {
            $pagina->SetCellValue('A'.$i, $i - 3);
            $pagina->SetCellValue('B'.$i, $reg_actual->anno);
            $pagina->SetCellValue('C'.$i, $reg_actual->numero);
            $pagina->SetCellValue('D'.$i, $reg_actual->descripcion);
            $pagina->SetCellValue('E'.$i, $reg_actual->rif);
            $pagina->SetCellValue('F'.$i, $reg_actual->contribuyente);
            $pagina->SetCellValue('G'.$i, $reg_actual->ci_fiscal);
            $pagina->SetCellValue('H'.$i, $reg_actual->fiscal);
            $pagina->SetCellValue('I'.$i, $reg_actual->ci_supervisor);
            $pagina->SetCellValue('J'.$i, $reg_actual->supervisor);
            $pagina->SetCellValue('K'.$i, voltea_fecha($reg_actual->emision));
            $pagina->SetCellValue('L'.$i, voltea_fecha($reg_actual->notificacion));
            $pagina->SetCellValue('M'.$i, $reg_actual->nombre_sector);
            $pagina->SetCellValue('N'.$i, $reg_actual->tipo);
            $pagina->SetCellValue('O'.$i, $reg_actual->clasificacion);
            $pagina->SetCellValue('P'.$i, $reg_actual->proceso_auditoria);
            $pagina->SetCellValue('Q'.$i, $reg_actual->nivel_supervisor);
            $pagina->SetCellValue('R'.$i, $reg_actual->lapso_allanamiento);
            $pagina->SetCellValue('S'.$i, $reg_actual->otras_causas);
            $pagina->SetCellValue('T'.$i, $reg_actual->desc_formato);
            $pagina->SetCellValue('U'.$i, $reg_actual->formato_cp);

            $i++;
        }

        //*****************RESUMEN*****************************
        $resumen_actual = "SELECT casos_en_proceso.desc_formato, COUNT(casos_en_proceso.anno) as cantidad FROM casos_en_proceso WHERE borrado = 0 AND year(notificacion) < year(date(now())) GROUP BY casos_en_proceso.desc_formato";
        $tabla_resumen_actual = $conexion->query($resumen_actual);
        $i++;
        $total = 0;
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('A'.$i, 'PROGRAMA');
        $objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('E'.$i, 'CANTIDAD');
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        
        $i++;

        while ($reg_resumen_actual = $tabla_resumen_actual->fetch_object())
        {
            $pagina->SetCellValue('A'.$i, $reg_resumen_actual->desc_formato);
            $pagina->SetCellValue('E'.$i, $reg_resumen_actual->cantidad);
            $total += $reg_resumen_actual->cantidad;
            $i++;

        }

        $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('A'.$i, 'TOTAL');
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('E'.$i, $total);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        $objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        //***************FIN RESUMEN***************************

    //************************************************************************************************************************************

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
$objWriter->save("../formatos/generados/GEN_rel_casos_proceso.xlsx");

$mensaje = "Informe Generado Satisfactoriamente";
$permitido = true;

$info = array("permitido"=>$permitido,
				"mensaje"=>$mensaje);

echo json_encode($info);

exit;


function mustas_vdf($con, $sector, $anno, $numero)
{
    $sql = "SELECT vista_ct_multas.Multas FROM vista_ct_multas WHERE vista_ct_multas.sector = ".$sector." AND vista_ct_multas.anno_expediente = ".$anno." AND vista_ct_multas.num_expediente = ".$numero;
    $tabla = $con->query($sql);
	$reg = $tabla->fetch_object();
	if ($reg->Multas>0)
	{
		$multas = round($reg->Multas, 2);
	} else {
		$multas = 0;
	}
	return $multas;
}


function prod_actas($con, $sector, $anno, $numero)
{
    $produccion = array();
    $sql = "SELECT vista_actas_siger.reparo, vista_actas_siger.impuesto_omitido, vista_actas_siger.multa_actual, vista_actas_siger.interes FROM vista_actas_siger WHERE vista_actas_siger.sector = ".$sector." AND vista_actas_siger.anno_prov = ".$anno." AND vista_actas_siger.num_prov = ".$numero;
    $tabla = $con->query($sql);
	$reg = $tabla->fetch_object();
	if ($reg->reparo>0)
	{
		$produccion = array(round($reg->reparo, 2), round($reg->impuesto_omitido, 2), round($reg->multa_actual, 2), round($reg->interes, 2));
	} else {
		$produccion = array(0, 0, 0, 0);
	}
	return $produccion;
}

function BorrarTemporal($conexion)
{
    //-------------- BORRAMOS EL TEMPORAL -------------------------------
    $delete = "DELETE FROM temp_sanc_actual";
    $tabla_delete = $conexion->query($delete);
    //-------------------------------------------------------------------
}
?>