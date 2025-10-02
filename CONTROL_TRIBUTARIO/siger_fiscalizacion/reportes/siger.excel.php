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
$objPHPExcel = $objReader->load("../formatos/siger_fiscalizacion.xlsx");
// Indicamos que se pare en la hoja uno del libro

//***************************************** INICIO HOJA 4.1 DEL FORMATO PRACTICAR FISCALIZACIONES ***************************************************
    $objPHPExcel->setActiveSheetIndex(0);
    //Escribimos en la hoja en la celda B1
    $pagina = $objPHPExcel->getActiveSheet();

    //Datos para generar los valores en el formato
    $sql = "SELECT formato, titulo, periodo, celdas_editables, celda_inicial, celda_final, region, nom_region FROM formatos WHERE formato = 'siger_fiscalizacion.xlsx'";
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
    $datos = "SELECT descripcion, sector, activo, reposo, vacacion, traslado, comision FROM sg_fuerza_fiscal WHERE periodo_inicio = '".$inicio."' AND periodo_fin = '".$fin."'";
    $tabla = $conexion->query($datos);
    $x = $reg->celda_inicial;
    $pagina->SetCellValue($lista[1].$x, 1);
    while ($data = $tabla->fetch_object())
    {
        if ($data->descripcion == "SUPERVISORES") 
        { 
       
            if ($data->sector == 1) 
            { 
                $sup_activos_sede += $data->activo; 
                $sup_reposo_sede += $data->reposo; 
                $sup_vacacion_sede += $data->vacacion; 
                $sup_traslado_sede += $data->traslado; 
                $sup_comision_sede += $data->comision; 
            }
            if ($data->sector == 2 or $data->sector == 3 or $data->sector == 5) 
            { 
                $sup_activos_sector += $data->activo; 
                $sup_reposo_sector += $data->reposo; 
                $sup_vacacion_sector += $data->vacacion; 
                $sup_traslado_sector += $data->traslado; 
                $sup_comision_sector += $data->comision; 
            }
            if ($data->sector == 4) 
            { 
                $sup_activos_unidad += $data->activo; 
                $sup_reposo_unidad += $data->reposo; 
                $sup_vacacion_unidad += $data->vacacion; 
                $sup_traslado_unidad += $data->traslado; 
                $sup_comision_unidad += $data->comision; 
            }
        
        }
        
        if ($data->descripcion == "FISCALES") 
        { 

            if ($data->sector == 1) 
            { 
                $fis_activos_sede += $data->activo; 
                $fis_reposo_sede += $data->reposo; 
                $fis_vacacion_sede += $data->vacacion; 
                $fis_traslado_sede += $data->traslado; 
                $fis_comision_sede += $data->comision; 
            }
            if ($data->sector == 2 or $data->sector == 3 or $data->sector == 5) 
            { 
                $fis_activos_sector += $data->activo; 
                $fis_reposo_sector += $data->reposo; 
                $fis_vacacion_sector += $data->vacacion; 
                $fis_traslado_sector += $data->traslado; 
                $fis_comision_sector += $data->comision; 
            }
            if ($data->sector == 4) 
            { 
                $fis_activos_unidad += $data->activo; 
                $fis_reposo_unidad += $data->reposo; 
                $fis_vacacion_unidad += $data->vacacion; 
                $fis_traslado_unidad += $data->traslado; 
                $fis_comision_unidad += $data->comision; 
            }
        }
    }

    // ************** supervisores ************************************
    if ($sup_activos_sede > 0) { $pagina->SetCellValue($lista[1].($x+1), $sup_activos_sede); }
    if ($sup_reposo_sede > 0) { $pagina->SetCellValue($lista[2].($x+1), $sup_reposo_sede); }
    if ($sup_vacacion_sede > 0) { $pagina->SetCellValue($lista[3].($x+1), $sup_vacacion_sede); }
    if ($sup_traslado_sede > 0) { $pagina->SetCellValue($lista[4].($x+1), $sup_traslado_sede); }
    if ($sup_comision_sede > 0) { $pagina->SetCellValue($lista[5].($x+1), $sup_comision_sede); }
    
    if ($sup_activos_sector > 0) { $pagina->SetCellValue($lista[6].($x+1), $sup_activos_sector); }
    if ($sup_reposo_sector > 0) { $pagina->SetCellValue($lista[7].($x+1), $sup_reposo_sector); }
    if ($sup_vacacion_sector > 0) { $pagina->SetCellValue($lista[8].($x+1), $sup_vacacion_sector); }
    if ($sup_traslado_sector > 0) { $pagina->SetCellValue($lista[9].($x+1), $sup_traslado_sector); }
    if ($sup_comision_sector > 0) { $pagina->SetCellValue($lista[10].($x+1), $sup_comision_sector); }
    
    if ($sup_activos_unidad > 0) { $pagina->SetCellValue($lista[11].($x+1), $sup_activos_unidad); }
    if ($sup_reposo_sede > 0) { $pagina->SetCellValue($lista[12].($x+1), $sup_reposo_sede); }
    if ($sup_vacacion_sede > 0) { $pagina->SetCellValue($lista[13].($x+1), $sup_vacacion_sede); }
    if ($sup_traslado_sede > 0) { $pagina->SetCellValue($lista[14].($x+1), $sup_traslado_sede); }
    if ($sup_comision_sede > 0) { $pagina->SetCellValue($lista[15].($x+1), $sup_comision_sede); }

    // ************** fiscales ************************************
    if ($fis_activos_sede > 0) { $pagina->SetCellValue($lista[1].($x+2), $fis_activos_sede); }
    if ($fis_reposo_sede > 0) { $pagina->SetCellValue($lista[2].($x+2), $fis_reposo_sede); }
    if ($fis_vacacion_sede > 0) { $pagina->SetCellValue($lista[3].($x+2), $fis_vacacion_sede); }
    if ($fis_traslado_sede > 0) { $pagina->SetCellValue($lista[4].($x+2), $fis_traslado_sede); }
    if ($fis_comision_sede > 0) { $pagina->SetCellValue($lista[5].($x+2), $fis_comision_sede); }
    
    if ($fis_activos_sector > 0) { $pagina->SetCellValue($lista[6].($x+2), $fis_activos_sector); }
    if ($fis_reposo_sector > 0) { $pagina->SetCellValue($lista[7].($x+2), $fis_reposo_sector); }
    if ($fis_vacacion_sector > 0) { $pagina->SetCellValue($lista[8].($x+2), $fis_vacacion_sector); }
    if ($fis_traslado_sector > 0) { $pagina->SetCellValue($lista[9].($x+2), $fis_traslado_sector); }
    if ($fis_comision_sector > 0) { $pagina->SetCellValue($lista[10].($x+2), $fis_comision_sector); }
    
    if ($fis_activos_unidad > 0) { $pagina->SetCellValue($lista[11].($x+2), $fis_activos_unidad); }
    if ($fis_reposo_sede > 0) { $pagina->SetCellValue($lista[12].($x+2), $fis_reposo_sede); }
    if ($fis_vacacion_sede > 0) { $pagina->SetCellValue($lista[13].($x+2), $fis_vacacion_sede); }
    if ($fis_traslado_sede > 0) { $pagina->SetCellValue($lista[14].($x+2), $fis_traslado_sede); }
    if ($fis_comision_sede > 0) { $pagina->SetCellValue($lista[15].($x+2), $fis_comision_sede); }
    

    //Buscamos los registros de la Base de datos
    $datos = "SELECT descripcion, fisc_integral, fisc_puntual, verificacion, otros FROM sg_dist_fuerza_fiscal WHERE periodo_inicio = '".$inicio."' AND periodo_fin = '".$fin."'";
    $tabla = $conexion->query($datos);
    $x = $reg->celda_inicial;
    $pagina->SetCellValue($lista[1].$x, 1);
    while ($data = $tabla->fetch_object())
    {
        if ($data->descripcion == "SUPERVISORES") 
        {
            $sup_fisc_integral += $data->fisc_integral;
            $sup_fisc_puntual += $data->fisc_puntual;
            $sup_verificacion += $data->verificacion;
            $sup_otros += $data->otros;
        }

        if ($data->descripcion == "FISCALES") 
        {
            $fis_fisc_integral += $data->fisc_integral;
            $fis_fisc_puntual += $data->fisc_puntual;
            $fis_verificacion += $data->verificacion;
            $fis_otros += $data->otros;            
        }
    }

    //***************************** supervisores *********************************************
    if ($sup_fisc_integral > 0) { $pagina->SetCellValue($lista[9].($x+12), $sup_fisc_integral); }
    if ($sup_fisc_puntual > 0) { $pagina->SetCellValue($lista[10].($x+12), $sup_fisc_puntual); }
    if ($sup_verificacion > 0) { $pagina->SetCellValue($lista[11].($x+12), $sup_verificacion); }
    if ($sup_otros > 0) { $pagina->SetCellValue($lista[12].($x+12), $sup_otros); }
    
    //***************************** fiscales *********************************************
    if ($fis_fisc_integral > 0) { $pagina->SetCellValue($lista[9].($x+13), $fis_fisc_integral); }
    if ($fis_fisc_puntual > 0) { $pagina->SetCellValue($lista[10].($x+13), $fis_fisc_puntual); }
    if ($fis_verificacion > 0) { $pagina->SetCellValue($lista[11].($x+13), $fis_verificacion); }
    if ($fis_otros > 0) { $pagina->SetCellValue($lista[12].($x+13), $fis_otros); }

//***************************************** FIN HOJA 4.1 DEL FORMATO PRACTICAR FISCALIZACIONES ******************************************************

//***************************************** INICIO HOJA 4.2 DEL FORMATO PRACTICAR FISCALIZACIONES ***************************************************
    $objPHPExcel->setActiveSheetIndex(1);
    $pagina = $objPHPExcel->getActiveSheet();

    //**************TITULO Y AÑO******************
    /*$pagina->SetCellValue($region[1], $reg->nom_region);
    $pagina->SetCellValue($titulo[1], $mes);
    $pagina->SetCellValue($periodo[1], $anno);*/
   
    //Buscamos los registros de la Base de datos
    $datos_2 = "SELECT sg_cuadro_4_2.programa, sg_cuadro_4_2.coordinadores, sg_cuadro_4_2.supervisores, sg_cuadro_4_2.fiscales, sg_cuadro_4_2.resguardo, sg_cuadro_4_2.emitidas, sg_cuadro_4_2.notificadas, sg_cuadro_4_2.anuladas, sg_cuadro_4_2.proceso_inicio, sg_cuadro_4_2.meta, sg_cuadro_4_2.conc_sancionado, sg_cuadro_4_2.conc_conforme, sg_cuadro_4_2.proc_final, lineas_siger.hoja2 FROM sg_cuadro_4_2 INNER JOIN lineas_siger ON lineas_siger.descripcion = sg_cuadro_4_2.programa WHERE sg_cuadro_4_2.periodo_inicio = '".$inicio."' AND sg_cuadro_4_2.periodo_fin = '".$fin."'";
    $tabla_2 = $conexion->query($datos_2);
    $x = $reg->celda_inicial;
    while ($data_2 = $tabla_2->fetch_object())
    {
        $i=$data_2->hoja2;

        $pagina->SetCellValue($lista[2].$i, $data_2->coordinadores);
        $pagina->SetCellValue($lista[3].$i, $data_2->supervisores);
        $pagina->SetCellValue($lista[4].$i, $data_2->fiscales);
        $pagina->SetCellValue($lista[5].$i, $data_2->resguardo);
        $pagina->SetCellValue($lista[6].$i, $data_2->emitidas);
        $pagina->SetCellValue($lista[7].$i, $data_2->notificadas);
        $pagina->SetCellValue($lista[8].$i, $data_2->anuladas);
        $pagina->SetCellValue($lista[9].$i, $data_2->proceso_inicio);
        $pagina->SetCellValue($lista[10].$i, $data_2->meta);
        $pagina->SetCellValue($lista[11].$i, $data_2->notificadas);
        $pagina->SetCellValue($lista[12].$i, $data_2->conc_sancionado);
        $pagina->SetCellValue($lista[13].$i, $data_2->conc_conforme);

    }
//***************************************** FIN HOJA 4.2 DEL FORMATO PRACTICAR FISCALIZACIONES ******************************************************

//***************************************** INICIO HOJA 4.3 DEL FORMATO PRACTICAR FISCALIZACIONES ***************************************************
    $objPHPExcel->setActiveSheetIndex(2);
    $pagina = $objPHPExcel->getActiveSheet();

    //**************TITULO Y AÑO******************
    /*$pagina->SetCellValue($region[2], $reg->nom_region);
    $pagina->SetCellValue($titulo[2], $mes);
    $pagina->SetCellValue($periodo[2], $anno);*/

    //Buscamos los registros de la Base de datos
    //Buscamos los registros de la Base de datos
    $datos_3 = "SELECT sg_cuadro_4_3.programa, sg_cuadro_4_3.reparo, sg_cuadro_4_3.impuesto, sg_cuadro_4_3.rebaja, sg_cuadro_4_3.intereses, sg_cuadro_4_3.multa_reparo, sg_cuadro_4_3.multa_vdf, lineas_siger.hoja3 FROM sg_cuadro_4_3 INNER JOIN lineas_siger ON lineas_siger.descripcion = sg_cuadro_4_3.programa WHERE sg_cuadro_4_3.periodo_inicio = '".$inicio."' AND sg_cuadro_4_3.periodo_fin = '".$fin."'";
    $tabla_3 = $conexion->query($datos_3);
    $x = $reg->celda_inicial;
    while ($data_3 = $tabla_3->fetch_object())
    {
        $i=$data_3->hoja3;

        $pagina->SetCellValue($lista[2].$i, $data_3->reparo);
        $pagina->SetCellValue($lista[3].$i, $data_3->impuesto);
        $pagina->SetCellValue($lista[4].$i, $data_3->rebaja);
        $pagina->SetCellValue($lista[5].$i, $data_3->intereses);
        $pagina->SetCellValue($lista[6].$i, $data_3->multa_reparo);
        $pagina->SetCellValue($lista[7].$i, $data_3->multa_vdf);

    }

//***************************************** FIN HOJA 4.3 DEL FORMATO PRACTICAR FISCALIZACIONES ******************************************************

//***************************************** INICIO HOJA 4.4 DEL FORMATO PRACTICAR FISCALIZACIONES ***************************************************
    $objPHPExcel->setActiveSheetIndex(3);
    $pagina = $objPHPExcel->getActiveSheet();

    //**************TITULO Y AÑO******************
    /*$pagina->SetCellValue($region[2], $reg->nom_region);
    $pagina->SetCellValue($titulo[2], $mes);
    $pagina->SetCellValue($periodo[2], $anno);*/

    //Buscamos los registros de la Base de datos
    $datos_4 = "SELECT sg_cuadro_4_4.programa, sg_cuadro_4_4.aa_cont, sg_cuadro_4_4.aa_actas, sg_cuadro_4_4.aa_importe, sg_cuadro_4_4.ap_cont, sg_cuadro_4_4.ap_actas, sg_cuadro_4_4.ap_pagado, sg_cuadro_4_4.ap_no_pagado, sg_cuadro_4_4.ana_cont, sg_cuadro_4_4.ana_actas, sg_cuadro_4_4.ana_no_pagado, lineas_siger.hoja4 FROM sg_cuadro_4_4 INNER JOIN lineas_siger ON lineas_siger.descripcion = sg_cuadro_4_4.programa WHERE sg_cuadro_4_4.periodo_inicio = '".$inicio."' AND sg_cuadro_4_4.periodo_fin = '".$fin."'";
    $tabla_4 = $conexion->query($datos_4);
    $x = $reg->celda_inicial;
    while ($data_4 = $tabla_4->fetch_object())
    {
        $i=$data_4->hoja4;

        $pagina->SetCellValue($lista[1].$i, 0);
        $pagina->SetCellValue($lista[2].$i, 0);
        $pagina->SetCellValue($lista[3].$i, $data_4->aa_cont);
        $pagina->SetCellValue($lista[4].$i, $data_4->aa_actas);
        $pagina->SetCellValue($lista[5].$i, $data_4->aa_importe);
        $pagina->SetCellValue($lista[6].$i, $data_4->ap_cont);
        $pagina->SetCellValue($lista[7].$i, $data_4->ap_actas);
        $pagina->SetCellValue($lista[8].$i, $data_4->ap_pagado);
        $pagina->SetCellValue($lista[9].$i, $data_4->ap_no_pagado);
        $pagina->SetCellValue($lista[10].$i, $data_4->ana_cont);
        $pagina->SetCellValue($lista[11].$i, $data_4->ana_actas);
        $pagina->SetCellValue($lista[12].$i, $data_4->ana_no_pagado);
        $pagina->SetCellValue($lista[13].$i, 0);
        $pagina->SetCellValue($lista[14].$i, 0);

    }

//***************************************** FIN HOJA 4.4 DEL FORMATO PRACTICAR FISCALIZACIONES ******************************************************

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
$objWriter->save("../formatos/generados/GEN_siger_fiscalizacion.xlsx");

$mensaje = "Informe Generado Satisfactoriamente";
$permitido = true;

$info = array("permitido"=>$permitido,
				"mensaje"=>$mensaje);

echo json_encode($info);

exit;
?>