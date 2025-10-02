<?php
 
// Camino a los include
//set_include_path(get_include_path() . PATH_SEPARATOR . '../Classes/');</p>
// PHPExcel
require_once '../PHPExcel/librerias/ExcelLibrary/PHPExcel.php';
// PHPExcel_IOFactory
include '../PHPExcel/librerias/ExcelLibrary/PHPExcel/IOFactory.php';
include("../includes/conexion.php");
include("../funciones/funcionesphp.php");
set_time_limit(0);

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
$objPHPExcel = $objReader->load("../formatos/controles_internos.xlsx");
// Indicamos que se pare en la hoja uno del libro


//***************************************** PROVIDENCIAS *********************************************************************************

    //***************************************** PROVIDENCIAS EMITIDAS ********************************************************************
        $objPHPExcel->setActiveSheetIndex(0);
        //Escribimos en la hoja en la celda B1
        $pagina = $objPHPExcel->getActiveSheet();
        //----FILA DE TITULOS----------------------------------------------------------------------------------------------------------------
        $pagina->SetCellValue('A1', 'PROVIDENCIAS EMITIDAS DESDE '.voltea_fecha($inicio).' AL '.voltea_fecha($fin));
        //-----------FIN TITULOS-------------------------------------------------------------------------------------------------------------

        $emitidas = "SELECT expedientes_fiscalizacion.anno, expedientes_fiscalizacion.numero, z_sectores.nombre, expedientes_fiscalizacion.rif, contribuyentes.contribuyente, expedientes_fiscalizacion.fecha_emision, CONCAT_WS(' ',Fiscal.Apellidos,Fiscal.Nombres) AS fiscal, CONCAT_WS(' ',Supervisor.Apellidos,Supervisor.Nombres) AS supervisor, a_tipo_programa.descripcion FROM a_tipo_programa INNER JOIN expedientes_fiscalizacion ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN z_empleados AS Fiscal ON Fiscal.cedula =  expedientes_fiscalizacion.ci_fiscal1 INNER JOIN z_empleados AS Supervisor ON Supervisor.cedula = expedientes_fiscalizacion.ci_supervisor INNER JOIN contribuyentes ON contribuyentes.rif = expedientes_fiscalizacion.rif INNER JOIN z_sectores ON z_sectores.id_sector = expedientes_fiscalizacion.sector WHERE a_tipo_programa.descripcion IS NOT null AND expedientes_fiscalizacion.fecha_emision BETWEEN '".$inicio."' AND '".$fin."'";
        $tabla_emitidas = $con->query($emitidas);
        $i = 3;
        while ($reg_emitidas = $tabla_emitidas->fetch_object())
        {
            $pagina->SetCellValue('A'.$i, $i - 2);
            $pagina->SetCellValue('B'.$i, $reg_emitidas->descripcion);
            $pagina->SetCellValue('C'.$i, $reg_emitidas->anno);
            $pagina->SetCellValue('D'.$i, $reg_emitidas->numero);
            $pagina->SetCellValue('E'.$i, $reg_emitidas->nombre);
            $pagina->SetCellValue('F'.$i, $reg_emitidas->rif);
            $pagina->SetCellValue('G'.$i, $reg_emitidas->contribuyente);
            $pagina->SetCellValue('H'.$i, voltea_fecha($reg_emitidas->fecha_emision));
            $pagina->SetCellValue('I'.$i, $reg_emitidas->fiscal);
            $pagina->SetCellValue('J'.$i, $reg_emitidas->supervisor);

            $i++;
        }

        //*****************RESUMEN*****************************
        $resumen_emi = "SELECT a_tipo_programa.descripcion as programa, COUNT(expedientes_fiscalizacion.anno) AS cantidad FROM a_tipo_programa INNER JOIN expedientes_fiscalizacion ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN z_empleados AS Fiscal ON Fiscal.cedula = expedientes_fiscalizacion.ci_fiscal1 INNER JOIN z_empleados AS Supervisor ON Supervisor.cedula = expedientes_fiscalizacion.ci_supervisor INNER JOIN contribuyentes ON contribuyentes.rif = expedientes_fiscalizacion.rif INNER JOIN z_sectores ON z_sectores.id_sector = expedientes_fiscalizacion.sector WHERE a_tipo_programa.descripcion IS NOT null AND expedientes_fiscalizacion.fecha_emision BETWEEN '".$inicio."' AND '".$fin."' GROUP BY a_tipo_programa.id_programa";
        $tabla_resumen_emi = $con->query($resumen_emi);
        $i++;
        $total = 0;
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('B'.$i, 'PROGRAMA');
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('C'.$i, 'CANTIDAD');
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        
        $i++;

        while ($reg_resumen_emi = $tabla_resumen_emi->fetch_object())
        {
            $pagina->SetCellValue('B'.$i, $reg_resumen_emi->programa);
            $pagina->SetCellValue('C'.$i, $reg_resumen_emi->cantidad);
            $total += $reg_resumen_emi->cantidad;
            $i++;

        }

        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('B'.$i, 'TOTAL');
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('C'.$i, $total);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        //***************FIN RESUMEN***************************

    //************************************************************************************************************************************

    //***************************************** PROVIDENCIAS NOTIFICADAS *****************************************************************
        $objPHPExcel->setActiveSheetIndex(1);
        //Escribimos en la hoja en la celda B1
        $pagina = $objPHPExcel->getActiveSheet();
        //----FILA DE TITULOS----------------------------------------------------------------------------------------------------------------
        $pagina->SetCellValue('A1', 'PROVIDENCIAS NOTIFICADAS DESDE '.voltea_fecha($inicio).' AL '.voltea_fecha($fin));
        //-----------FIN TITULOS-------------------------------------------------------------------------------------------------------------

        $notificadas = "SELECT expedientes_fiscalizacion.anno, expedientes_fiscalizacion.numero, z_sectores.nombre, expedientes_fiscalizacion.rif, contribuyentes.contribuyente, expedientes_fiscalizacion.fecha_emision, expedientes_fiscalizacion.fecha_notificacion, CONCAT_WS(' ',Fiscal.Apellidos,Fiscal.Nombres) AS fiscal, CONCAT_WS(' ',Supervisor.Apellidos,Supervisor.Nombres) AS supervisor, a_tipo_programa.descripcion FROM a_tipo_programa INNER JOIN expedientes_fiscalizacion ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN z_empleados AS Fiscal ON Fiscal.cedula =  expedientes_fiscalizacion.ci_fiscal1 INNER JOIN z_empleados AS Supervisor ON Supervisor.cedula = expedientes_fiscalizacion.ci_supervisor INNER JOIN contribuyentes ON contribuyentes.rif = expedientes_fiscalizacion.rif INNER JOIN z_sectores ON z_sectores.id_sector = expedientes_fiscalizacion.sector WHERE a_tipo_programa.descripcion IS NOT null AND expedientes_fiscalizacion.fecha_notificacion BETWEEN '".$inicio."' AND '".$fin."'";
        $tabla_notificadas = $con->query($notificadas);
        $i = 3;
        while ($reg_notificadas = $tabla_notificadas->fetch_object())
        {
            $pagina->SetCellValue('A'.$i, $i - 2);
            $pagina->SetCellValue('B'.$i, $reg_notificadas->descripcion);
            $pagina->SetCellValue('C'.$i, $reg_notificadas->anno);
            $pagina->SetCellValue('D'.$i, $reg_notificadas->numero);
            $pagina->SetCellValue('E'.$i, $reg_notificadas->nombre);
            $pagina->SetCellValue('F'.$i, $reg_notificadas->rif);
            $pagina->SetCellValue('G'.$i, $reg_notificadas->contribuyente);
            $pagina->SetCellValue('H'.$i, voltea_fecha($reg_notificadas->fecha_emision));
            $pagina->SetCellValue('I'.$i, voltea_fecha($reg_notificadas->fecha_notificacion));
            $pagina->SetCellValue('J'.$i, $reg_notificadas->fiscal);
            $pagina->SetCellValue('K'.$i, $reg_notificadas->supervisor);

            $i++;
        }

        //*****************RESUMEN*****************************
        $resumen_not = "SELECT a_tipo_programa.descripcion as programa, COUNT(expedientes_fiscalizacion.anno) AS cantidad FROM a_tipo_programa INNER JOIN expedientes_fiscalizacion ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN z_empleados AS Fiscal ON Fiscal.cedula =  expedientes_fiscalizacion.ci_fiscal1 INNER JOIN z_empleados AS Supervisor ON Supervisor.cedula = expedientes_fiscalizacion.ci_supervisor INNER JOIN contribuyentes ON contribuyentes.rif = expedientes_fiscalizacion.rif INNER JOIN z_sectores ON z_sectores.id_sector = expedientes_fiscalizacion.sector WHERE a_tipo_programa.descripcion IS NOT null AND expedientes_fiscalizacion.fecha_notificacion BETWEEN '".$inicio."' AND '".$fin."' GROUP BY a_tipo_programa.id_programa";
        $tabla_resumen_not = $con->query($resumen_not);
        $i++;
        $total = 0;
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('B'.$i, 'PROGRAMA');
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('C'.$i, 'CANTIDAD');
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        
        $i++;

        while ($reg_resumen_not = $tabla_resumen_not->fetch_object())
        {
            $pagina->SetCellValue('B'.$i, $reg_resumen_not->programa);
            $pagina->SetCellValue('C'.$i, $reg_resumen_not->cantidad);
            $total += $reg_resumen_not->cantidad;
            $i++;

        }

        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('B'.$i, 'TOTAL');
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('C'.$i, $total);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        //***************FIN RESUMEN***************************
    //************************************************************************************************************************************

    //***************************************** PROVIDENCIAS ANULADAS ********************************************************************
        $objPHPExcel->setActiveSheetIndex(2);
        //Escribimos en la hoja en la celda B1
        $pagina = $objPHPExcel->getActiveSheet();
        //----FILA DE TITULOS----------------------------------------------------------------------------------------------------------------
        $pagina->SetCellValue('A1', 'PROVIDENCIAS ANULADAS DESDE '.voltea_fecha($inicio).' AL '.voltea_fecha($fin));
        //-----------FIN TITULOS-------------------------------------------------------------------------------------------------------------

        $notificadas = "SELECT expedientes_fiscalizacion.anno, expedientes_fiscalizacion.numero, z_sectores.nombre, expedientes_fiscalizacion.rif, contribuyentes.contribuyente, expedientes_fiscalizacion.fecha_emision, expedientes_fiscalizacion.fecha_notificacion, expedientes_fiscalizacion.fecha_anulacion, CONCAT_WS(' ',Fiscal.Apellidos,Fiscal.Nombres) AS fiscal, CONCAT_WS(' ',Supervisor.Apellidos,Supervisor.Nombres) AS supervisor, a_tipo_programa.descripcion FROM a_tipo_programa INNER JOIN expedientes_fiscalizacion ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN z_empleados AS Fiscal ON Fiscal.cedula =  expedientes_fiscalizacion.ci_fiscal1 INNER JOIN z_empleados AS Supervisor ON Supervisor.cedula = expedientes_fiscalizacion.ci_supervisor INNER JOIN contribuyentes ON contribuyentes.rif = expedientes_fiscalizacion.rif INNER JOIN z_sectores ON z_sectores.id_sector = expedientes_fiscalizacion.sector WHERE a_tipo_programa.descripcion IS NOT null AND expedientes_fiscalizacion.fecha_anulacion BETWEEN '".$inicio."' AND '".$fin."'";
        $tabla_notificadas = $con->query($notificadas);
        $i = 3;
        while ($reg_notificadas = $tabla_notificadas->fetch_object())
        {
            $pagina->SetCellValue('A'.$i, $i - 2);
            $pagina->SetCellValue('B'.$i, $reg_notificadas->descripcion);
            $pagina->SetCellValue('C'.$i, $reg_notificadas->anno);
            $pagina->SetCellValue('D'.$i, $reg_notificadas->numero);
            $pagina->SetCellValue('E'.$i, $reg_notificadas->nombre);
            $pagina->SetCellValue('F'.$i, $reg_notificadas->rif);
            $pagina->SetCellValue('G'.$i, $reg_notificadas->contribuyente);
            $pagina->SetCellValue('H'.$i, voltea_fecha($reg_notificadas->fecha_emision));
            $pagina->SetCellValue('I'.$i, voltea_fecha($reg_notificadas->fecha_notificacion));
            $pagina->SetCellValue('J'.$i, voltea_fecha($reg_notificadas->fecha_anulacion));
            $pagina->SetCellValue('K'.$i, $reg_notificadas->fiscal);
            $pagina->SetCellValue('L'.$i, $reg_notificadas->supervisor);

            $i++;
        }

        //*****************RESUMEN*****************************
        $resumen_not = "SELECT a_tipo_programa.descripcion as programa, COUNT(expedientes_fiscalizacion.anno) AS cantidad FROM a_tipo_programa INNER JOIN expedientes_fiscalizacion ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN z_empleados AS Fiscal ON Fiscal.cedula =  expedientes_fiscalizacion.ci_fiscal1 INNER JOIN z_empleados AS Supervisor ON Supervisor.cedula = expedientes_fiscalizacion.ci_supervisor INNER JOIN contribuyentes ON contribuyentes.rif = expedientes_fiscalizacion.rif INNER JOIN z_sectores ON z_sectores.id_sector = expedientes_fiscalizacion.sector WHERE a_tipo_programa.descripcion IS NOT null AND expedientes_fiscalizacion.fecha_anulacion BETWEEN '".$inicio."' AND '".$fin."' GROUP BY a_tipo_programa.id_programa";
        $tabla_resumen_not = $con->query($resumen_not);
        $i++;
        $total = 0;
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('B'.$i, 'PROGRAMA');
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('C'.$i, 'CANTIDAD');
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        
        $i++;

        while ($reg_resumen_not = $tabla_resumen_not->fetch_object())
        {
            $pagina->SetCellValue('B'.$i, $reg_resumen_not->programa);
            $pagina->SetCellValue('C'.$i, $reg_resumen_not->cantidad);
            $total += $reg_resumen_not->cantidad;
            $i++;

        }

        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('B'.$i, 'TOTAL');
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('C'.$i, $total);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        //***************FIN RESUMEN***************************

    //************************************************************************************************************************************

//****************************************************************************************************************************************




//***************************************** CASOS CONCLUIDOS *****************************************************************************

    //***************************************** CONCLUIDOS TOTALES AÑO ACTUAL ************************************************************
        $objPHPExcel->setActiveSheetIndex(3);
        //Escribimos en la hoja en la celda B1
        $pagina = $objPHPExcel->getActiveSheet();
        //----FILA DE TITULOS----------------------------------------------------------------------------------------------------------------
        $pagina->SetCellValue('A1', 'PROVIDENCIAS CONCLUIDAS AÑO ACTUAL DESDE '.voltea_fecha($inicio).' AL '.voltea_fecha($fin));
        //-----------FIN TITULOS-------------------------------------------------------------------------------------------------------------

        $aa_conc = "SELECT a_tipo_programa.descripcion, ct_salida_expediente.Anno_Providencia, ct_salida_expediente.NroAutorizacion, ct_salida_expediente.sector, z_sectores.nombre, expedientes_fiscalizacion.rif, contribuyentes.contribuyente, expedientes_fiscalizacion.fecha_emision, expedientes_fiscalizacion.fecha_notificacion, ct_salida_expediente.FechaEmision AS fecha_conclusion, CONCAT_WS(' ',Fiscal.Apellidos,Fiscal.Nombres) AS fiscal, CONCAT_WS(' ',Supervisor.Apellidos,Supervisor.Nombres) as supervisor FROM ct_salida_expediente INNER JOIN z_sectores ON z_sectores.id_sector = ct_salida_expediente.sector INNER JOIN expedientes_fiscalizacion ON expedientes_fiscalizacion.sector = ct_salida_expediente.sector AND expedientes_fiscalizacion.anno = ct_salida_expediente.Anno_Providencia AND expedientes_fiscalizacion.numero = ct_salida_expediente.NroAutorizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN contribuyentes ON contribuyentes.rif = expedientes_fiscalizacion.rif INNER JOIN z_empleados AS Fiscal ON Fiscal.cedula = expedientes_fiscalizacion.ci_fiscal1 INNER JOIN z_empleados AS Supervisor ON Supervisor.cedula = expedientes_fiscalizacion.ci_supervisor WHERE ct_salida_expediente.FechaEmision BETWEEN '".$inicio."' AND '".$fin."' AND year(expedientes_fiscalizacion.fecha_notificacion) = year(date(now()))";
        $tabla_aa_conc = $con->query($aa_conc);
        $i = 3;
        while ($reg_aa_conc = $tabla_aa_conc->fetch_object())
        {
            $pagina->SetCellValue('A'.$i, $i - 2);
            $pagina->SetCellValue('B'.$i, $reg_aa_conc->descripcion);
            $pagina->SetCellValue('C'.$i, $reg_aa_conc->Anno_Providencia);
            $pagina->SetCellValue('D'.$i, $reg_aa_conc->NroAutorizacion);
            $pagina->SetCellValue('E'.$i, $reg_aa_conc->nombre);
            $pagina->SetCellValue('F'.$i, $reg_aa_conc->rif);
            $pagina->SetCellValue('G'.$i, $reg_aa_conc->contribuyente);
            $pagina->SetCellValue('H'.$i, voltea_fecha($reg_aa_conc->fecha_emision));
            $pagina->SetCellValue('I'.$i, voltea_fecha($reg_aa_conc->fecha_notificacion));
            $pagina->SetCellValue('J'.$i, voltea_fecha($reg_aa_conc->fecha_conclusion));
            $pagina->SetCellValue('K'.$i, $reg_aa_conc->fiscal);
            $pagina->SetCellValue('L'.$i, $reg_aa_conc->supervisor);

            $i++;
        }

        //*****************RESUMEN*****************************
        $resumen_aa_conc = "SELECT a_tipo_programa.descripcion, count(expedientes_fiscalizacion.anno) as cantidad FROM ct_salida_expediente INNER JOIN z_sectores ON z_sectores.id_sector = ct_salida_expediente.sector INNER JOIN expedientes_fiscalizacion ON expedientes_fiscalizacion.sector = ct_salida_expediente.sector AND expedientes_fiscalizacion.anno = ct_salida_expediente.Anno_Providencia AND expedientes_fiscalizacion.numero = ct_salida_expediente.NroAutorizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN contribuyentes ON contribuyentes.rif = expedientes_fiscalizacion.rif INNER JOIN z_empleados AS Fiscal ON Fiscal.cedula = expedientes_fiscalizacion.ci_fiscal1 INNER JOIN z_empleados AS Supervisor ON Supervisor.cedula = expedientes_fiscalizacion.ci_supervisor WHERE ct_salida_expediente.FechaEmision BETWEEN '".$inicio."' AND '".$fin."' AND year(expedientes_fiscalizacion.fecha_notificacion) = year(date(now())) GROUP BY a_tipo_programa.id_programa";
        $tabla_resumen_aa_conc = $con->query($resumen_aa_conc);
        $i++;
        $total = 0;
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('B'.$i, 'PROGRAMA');
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('C'.$i, 'CANTIDAD');
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        
        $i++;

        while ($reg_resumen_aa_conc = $tabla_resumen_aa_conc->fetch_object())
        {
            $pagina->SetCellValue('B'.$i, $reg_resumen_aa_conc->descripcion);
            $pagina->SetCellValue('C'.$i, $reg_resumen_aa_conc->cantidad);
            $total += $reg_resumen_aa_conc->cantidad;
            $i++;

        }

        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('B'.$i, 'TOTAL');
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('C'.$i, $total);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        //***************FIN RESUMEN***************************

    //************************************************************************************************************************************
    
//******************************************* CORREGIR ESTO ****************************************************************************

    //***************************************** CONCLUIDOS SANCIONADOS AÑO ACTUAL ********************************************************
        //-------------- BORRAMOS EL TEMPORAL -------------------------------
        $delete = "DELETE FROM temp_sanc_actual";
        $tabla_delete = $conexion->query($delete);
        //-------------------------------------------------------------------

        $objPHPExcel->setActiveSheetIndex(4);
        //Escribimos en la hoja en la celda B1
        $pagina = $objPHPExcel->getActiveSheet();
        //----FILA DE TITULOS----------------------------------------------------------------------------------------------------------------
        $pagina->SetCellValue('A1', 'PROVIDENCIAS CONCLUIDAS SANCIONADOS AÑO ACTUAL DESDE '.voltea_fecha($inicio).' AL '.voltea_fecha($fin));
        //-----------FIN TITULOS-------------------------------------------------------------------------------------------------------------

        $aa_sanc = "SELECT a_tipo_programa.descripcion, ct_salida_expediente.Anno_Providencia, ct_salida_expediente.NroAutorizacion, ct_salida_expediente.sector, z_sectores.nombre, expedientes_fiscalizacion.rif, contribuyentes.contribuyente, expedientes_fiscalizacion.fecha_emision, expedientes_fiscalizacion.fecha_notificacion, ct_salida_expediente.FechaEmision AS fecha_conclusion, CONCAT_WS(' ',Fiscal.Apellidos,Fiscal.Nombres) AS fiscal, CONCAT_WS(' ',Supervisor.Apellidos,Supervisor.Nombres) as supervisor FROM ct_salida_expediente INNER JOIN z_sectores ON z_sectores.id_sector = ct_salida_expediente.sector INNER JOIN expedientes_fiscalizacion ON expedientes_fiscalizacion.sector = ct_salida_expediente.sector AND expedientes_fiscalizacion.anno = ct_salida_expediente.Anno_Providencia AND expedientes_fiscalizacion.numero = ct_salida_expediente.NroAutorizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN contribuyentes ON contribuyentes.rif = expedientes_fiscalizacion.rif INNER JOIN z_empleados AS Fiscal ON Fiscal.cedula = expedientes_fiscalizacion.ci_fiscal1 INNER JOIN z_empleados AS Supervisor ON Supervisor.cedula = expedientes_fiscalizacion.ci_supervisor WHERE ct_salida_expediente.FechaEmision BETWEEN '".$inicio."' AND '".$fin."' AND year(expedientes_fiscalizacion.fecha_notificacion) = year(date(now()))"; 
        $tabla_aa_sanc = $con->query($aa_sanc);
        $i = 3;
        while ($reg_aa_sanc = $tabla_aa_sanc->fetch_object())
        {       
            //Buscamos si es sancionado
            //Por vdf
            $multas_vdf = mustas_vdf($con, $reg_aa_sanc->sector, $reg_aa_sanc->Anno_Providencia, $reg_aa_sanc->NroAutorizacion);
            //Por Fiscalizaciones
            $produccion = prod_actas($con, $reg_aa_sanc->sector, $reg_aa_sanc->Anno_Providencia, $reg_aa_sanc->NroAutorizacion);

            if ($multas_vdf > $produccion[2])
            {
                $multas_vdf = $multas_vdf - $produccion[2];
            } else {
                $multas_vdf = 0;
            }

            $pagina->SetCellValue('A'.$i, $i - 2);
            $pagina->SetCellValue('B'.$i, $reg_aa_sanc->descripcion);
            $pagina->SetCellValue('C'.$i, $reg_aa_sanc->Anno_Providencia);
            $pagina->SetCellValue('D'.$i, $reg_aa_sanc->NroAutorizacion);
            $pagina->SetCellValue('E'.$i, $reg_aa_sanc->nombre);
            $pagina->SetCellValue('F'.$i, $reg_aa_sanc->rif);
            $pagina->SetCellValue('G'.$i, $reg_aa_sanc->contribuyente);
            $pagina->SetCellValue('H'.$i, voltea_fecha($reg_aa_sanc->fecha_emision));
            $pagina->SetCellValue('I'.$i, voltea_fecha($reg_aa_sanc->fecha_notificacion));
            $pagina->SetCellValue('J'.$i, voltea_fecha($reg_aa_sanc->fecha_conclusion));
            $pagina->SetCellValue('K'.$i, $reg_aa_sanc->fiscal);
            $pagina->SetCellValue('L'.$i, $reg_aa_sanc->supervisor);
            $pagina->SetCellValue('M'.$i, $produccion[0]);
            $pagina->SetCellValue('N'.$i, $produccion[1]);
            $pagina->SetCellValue('O'.$i, $produccion[3]);
            $pagina->SetCellValue('P'.$i, $produccion[2]);
            $pagina->SetCellValue('Q'.$i, $multas_vdf);
            $pagina->SetCellValue('R'.$i, ($multas_vdf + $produccion[1] + $produccion[2] + $produccion[3]));

            $insert = "INSERT INTO temp_sanc_actual (anno, numero, sector, programa, reparo, impuesto, intereses, multa_rep, multa_vdf) VALUES (".$reg_aa_sanc->Anno_Providencia.", ".$reg_aa_sanc->NroAutorizacion.", ".$reg_aa_sanc->sector.", '".$reg_aa_sanc->descripcion."', ".$produccion[0].", ".$produccion[1].", ".$produccion[3].", ".$produccion[2].", ".$multas_vdf.")";
            $tabla_insert = $conexion->query($insert);

            $i++;
        }

        //*****************RESUMEN*****************************
        $resumen_aa_conc_sanc = "SELECT programa, count(anno) as cantidad, sum(reparo) as reparo, sum(impuesto) as impuesto, sum(intereses) as intereses, sum(multa_rep) as multas_rep, sum(multa_vdf) as multas_vdf FROM temp_sanc_actual GROUP BY programa";
        $tabla_resumen_aa_conc_sanc = $conexion->query($resumen_aa_conc_sanc);
        $i++;
        $total = 0;
        $objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('K'.$i, 'PROGRAMA');
        $objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('L'.$i, 'CANTIDAD');
        $objPHPExcel->getActiveSheet()->getStyle('M'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('M'.$i, 'REPARO');
        $objPHPExcel->getActiveSheet()->getStyle('N'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('N'.$i, 'IMPUESTO');
        $objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('O'.$i, 'INTERESES');
        $objPHPExcel->getActiveSheet()->getStyle('P'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('P'.$i, 'MULTAS REPARO');
        $objPHPExcel->getActiveSheet()->getStyle('Q'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('Q'.$i, 'MULTAS VDF');
        $objPHPExcel->getActiveSheet()->getStyle('R'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('R'.$i, 'TOTAL PRODUCCION');

        $objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('M'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('N'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('P'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('Q'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('R'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');

        $objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('M'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('M'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('N'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('N'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('P'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('P'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('Q'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('Q'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('R'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('R'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        
        $i++;

        while ($reg_resumen_aa_conc_sanc = $tabla_resumen_aa_conc_sanc->fetch_object())
        {
            $pagina->SetCellValue('K'.$i, $reg_resumen_aa_conc_sanc->programa);
            $pagina->SetCellValue('L'.$i, $reg_resumen_aa_conc_sanc->cantidad);
            $pagina->SetCellValue('M'.$i, $reg_resumen_aa_conc_sanc->reparo);
            $pagina->SetCellValue('N'.$i, $reg_resumen_aa_conc_sanc->impuesto);
            $pagina->SetCellValue('O'.$i, $reg_resumen_aa_conc_sanc->intereses);
            $pagina->SetCellValue('P'.$i, $reg_resumen_aa_conc_sanc->multas_rep);
            $pagina->SetCellValue('Q'.$i, $reg_resumen_aa_conc_sanc->multas_vdf);
            $produccion = $reg_resumen_aa_conc_sanc->impuesto + $reg_resumen_aa_conc_sanc->intereses + $reg_resumen_aa_conc_sanc->multas_rep + $reg_resumen_aa_conc_sanc->multas_vdf;
            $pagina->SetCellValue('R'.$i, $produccion);
            $cantidad += $reg_resumen_aa_conc_sanc->cantidad;
            $reparo += $reg_resumen_aa_conc_sanc->reparo;
            $impuesto += $reg_resumen_aa_conc_sanc->impuesto;
            $intereses += $reg_resumen_aa_conc_sanc->intereses;
            $multasrep += $reg_resumen_aa_conc_sanc->multas_rep;
            $multasvdf += $reg_resumen_aa_conc_sanc->multas_vdf;
            $totalproduccion += $produccion;
            $i++;

        }

        $objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('K'.$i, 'TOTAL');
        $objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('L'.$i, $cantidad);
        $objPHPExcel->getActiveSheet()->getStyle('M'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('M'.$i, $reparo);
        $objPHPExcel->getActiveSheet()->getStyle('N'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('N'.$i, $impuesto);
        $objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('O'.$i, $intereses);
        $objPHPExcel->getActiveSheet()->getStyle('P'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('P'.$i, $multasrep);
        $objPHPExcel->getActiveSheet()->getStyle('Q'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('Q'.$i, $multasvdf);
        $objPHPExcel->getActiveSheet()->getStyle('R'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('R'.$i, $totalproduccion);

        $objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        $objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        $objPHPExcel->getActiveSheet()->getStyle('M'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('M'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        $objPHPExcel->getActiveSheet()->getStyle('N'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('N'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        $objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        $objPHPExcel->getActiveSheet()->getStyle('P'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('P'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        $objPHPExcel->getActiveSheet()->getStyle('Q'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('Q'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        $objPHPExcel->getActiveSheet()->getStyle('R'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('R'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        //***************FIN RESUMEN***************************

    //************************************************************************************************************************************

    //***************************************** CONCLUIDOS TOTALES AÑOS ANTERIORES *******************************************************
        $objPHPExcel->setActiveSheetIndex(5);
        //Escribimos en la hoja en la celda B1
        $pagina = $objPHPExcel->getActiveSheet();
        //----FILA DE TITULOS----------------------------------------------------------------------------------------------------------------
        $pagina->SetCellValue('A1', 'PROVIDENCIAS CONCLUIDAS AÑOS ANTERIORES DESDE '.voltea_fecha($inicio).' AL '.voltea_fecha($fin));
        //-----------FIN TITULOS-------------------------------------------------------------------------------------------------------------
        BorrarTemporal($conexion);

        $aa_conc = "SELECT a_tipo_programa.descripcion, ct_salida_expediente.Anno_Providencia, ct_salida_expediente.NroAutorizacion, ct_salida_expediente.sector, z_sectores.nombre, expedientes_fiscalizacion.rif, contribuyentes.contribuyente, expedientes_fiscalizacion.fecha_emision, expedientes_fiscalizacion.fecha_notificacion, ct_salida_expediente.FechaEmision AS fecha_conclusion, CONCAT_WS(' ',Fiscal.Apellidos,Fiscal.Nombres) AS fiscal, CONCAT_WS(' ',Supervisor.Apellidos,Supervisor.Nombres) as supervisor FROM ct_salida_expediente INNER JOIN z_sectores ON z_sectores.id_sector = ct_salida_expediente.sector INNER JOIN expedientes_fiscalizacion ON expedientes_fiscalizacion.sector = ct_salida_expediente.sector AND expedientes_fiscalizacion.anno = ct_salida_expediente.Anno_Providencia AND expedientes_fiscalizacion.numero = ct_salida_expediente.NroAutorizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN contribuyentes ON contribuyentes.rif = expedientes_fiscalizacion.rif INNER JOIN z_empleados AS Fiscal ON Fiscal.cedula = expedientes_fiscalizacion.ci_fiscal1 INNER JOIN z_empleados AS Supervisor ON Supervisor.cedula = expedientes_fiscalizacion.ci_supervisor WHERE ct_salida_expediente.FechaEmision BETWEEN '".$inicio."' AND '".$fin."' AND year(expedientes_fiscalizacion.fecha_notificacion) < year(date(now()))";
        $tabla_aa_conc = $con->query($aa_conc);
        $i = 3;
        while ($reg_aa_conc = $tabla_aa_conc->fetch_object())
        {
            //------- VERIFICAMOS SI EXISTE EN CASOS EN PROCESO --------------------------------
            $sql_cp = "SELECT anno, numero, sector FROM casos_en_proceso WHERE sector = ".$reg_aa_conc->sector." AND anno = ".$reg_aa_conc->Anno_Providencia." AND numero = ".$reg_aa_conc->NroAutorizacion;
            $tabla_cp = $conexion->query($sql_cp);
            $existe = $tabla_cp->num_rows;

            if ($existe > 0)
            {       
                $pagina->SetCellValue('A'.$i, $i - 2);
                $pagina->SetCellValue('B'.$i, $reg_aa_conc->descripcion);
                $pagina->SetCellValue('C'.$i, $reg_aa_conc->Anno_Providencia);
                $pagina->SetCellValue('D'.$i, $reg_aa_conc->NroAutorizacion);
                $pagina->SetCellValue('E'.$i, $reg_aa_conc->nombre);
                $pagina->SetCellValue('F'.$i, $reg_aa_conc->rif);
                $pagina->SetCellValue('G'.$i, $reg_aa_conc->contribuyente);
                $pagina->SetCellValue('H'.$i, voltea_fecha($reg_aa_conc->fecha_emision));
                $pagina->SetCellValue('I'.$i, voltea_fecha($reg_aa_conc->fecha_notificacion));
                $pagina->SetCellValue('J'.$i, voltea_fecha($reg_aa_conc->fecha_conclusion));
                $pagina->SetCellValue('K'.$i, $reg_aa_conc->fiscal);
                $pagina->SetCellValue('L'.$i, $reg_aa_conc->supervisor);

                $insert = "INSERT INTO temp_sanc_actual (anno, numero, sector, programa, reparo, impuesto, intereses, multa_rep, multa_vdf) VALUES (".$reg_aa_conc->Anno_Providencia.", ".$reg_aa_conc->NroAutorizacion.", ".$reg_aa_conc->sector.", '".$reg_aa_conc->descripcion."', 0, 0, 0, 0, 0)";
                $tabla_insert = $conexion->query($insert);

                $i++;
            }
        }

        //*****************RESUMEN*****************************
        $resumen_aa_conc = "SELECT programa, count(anno) as cantidad FROM temp_sanc_actual GROUP BY programa";
        $tabla_resumen_aa_conc = $conexion->query($resumen_aa_conc);
        $i++;
        $total = 0;
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('B'.$i, 'PROGRAMA');
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('C'.$i, 'CANTIDAD');
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        
        $i++;

        while ($reg_resumen_aa_conc = $tabla_resumen_aa_conc->fetch_object())
        {
            $pagina->SetCellValue('B'.$i, $reg_resumen_aa_conc->programa);
            $pagina->SetCellValue('C'.$i, $reg_resumen_aa_conc->cantidad);
            $total += $reg_resumen_aa_conc->cantidad;
            $i++;

        }

        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('B'.$i, 'TOTAL');
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('C'.$i, $total);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        //***************FIN RESUMEN***************************

    //************************************************************************************************************************************

    //***************************************** CONCLUIDOS SANCIONADOS AÑOS ANTERIORES ***************************************************
        BorrarTemporal($conexion);

        $objPHPExcel->setActiveSheetIndex(6);
        //Escribimos en la hoja en la celda B1
        $pagina = $objPHPExcel->getActiveSheet();
        //----FILA DE TITULOS----------------------------------------------------------------------------------------------------------------
        $pagina->SetCellValue('A1', 'PROVIDENCIAS CONCLUIDAS SANCIONADOS AÑOS ANTERIORES DESDE '.voltea_fecha($inicio).' AL '.voltea_fecha($fin));
        //-----------FIN TITULOS-------------------------------------------------------------------------------------------------------------

        $aa_sanc = "SELECT a_tipo_programa.descripcion, ct_salida_expediente.Anno_Providencia, ct_salida_expediente.NroAutorizacion, ct_salida_expediente.sector, z_sectores.nombre, expedientes_fiscalizacion.rif, contribuyentes.contribuyente, expedientes_fiscalizacion.fecha_emision, expedientes_fiscalizacion.fecha_notificacion, ct_salida_expediente.FechaEmision AS fecha_conclusion, CONCAT_WS(' ',Fiscal.Apellidos,Fiscal.Nombres) AS fiscal, CONCAT_WS(' ',Supervisor.Apellidos,Supervisor.Nombres) as supervisor FROM ct_salida_expediente INNER JOIN z_sectores ON z_sectores.id_sector = ct_salida_expediente.sector INNER JOIN expedientes_fiscalizacion ON expedientes_fiscalizacion.sector = ct_salida_expediente.sector AND expedientes_fiscalizacion.anno = ct_salida_expediente.Anno_Providencia AND expedientes_fiscalizacion.numero = ct_salida_expediente.NroAutorizacion INNER JOIN a_tipo_programa ON a_tipo_programa.id_programa = expedientes_fiscalizacion.programa INNER JOIN contribuyentes ON contribuyentes.rif = expedientes_fiscalizacion.rif INNER JOIN z_empleados AS Fiscal ON Fiscal.cedula = expedientes_fiscalizacion.ci_fiscal1 INNER JOIN z_empleados AS Supervisor ON Supervisor.cedula = expedientes_fiscalizacion.ci_supervisor WHERE ct_salida_expediente.FechaEmision BETWEEN '".$inicio."' AND '".$fin."' AND year(expedientes_fiscalizacion.fecha_notificacion) < year(date(now()))";
        $tabla_aa_sanc = $con->query($aa_sanc);
        $i = 3;
        while ($reg_aa_sanc = $tabla_aa_sanc->fetch_object())
        {
            //------- VERIFICAMOS EXISTE EN CASOS EN PROCESO --------------------------------
            $sql_cp = "SELECT anno, sector, numero FROM casos_en_proceso WHERE sector = ".$reg_aa_sanc->sector." AND anno = ".$reg_aa_sanc->Anno_Providencia." AND numero = ".$reg_aa_sanc->NroAutorizacion;
            $tabla_cp = $conexion->query($sql_cp);
            $existe = $tabla_cp->num_rows;
            //echo "Existe --- : ".$existe.'<br>';
            if ($existe > 0)
            {       
                //Buscamos si es sancionado
                //Por vdf
                $multas_vdf = mustas_vdf($con, $reg_aa_sanc->sector, $reg_aa_sanc->Anno_Providencia, $reg_aa_sanc->NroAutorizacion);
                //Por Fiscalizaciones
                $produccion = prod_actas($con, $reg_aa_sanc->sector, $reg_aa_sanc->Anno_Providencia, $reg_aa_sanc->NroAutorizacion);

                if ($multas_vdf > $produccion[2])
                {
                    $multas_vdf = $multas_vdf - $produccion[2];
                } else {
                    $multas_vdf = 0;
                }

                $pagina->SetCellValue('A'.$i, $i - 2);
                $pagina->SetCellValue('B'.$i, $reg_aa_sanc->descripcion);
                $pagina->SetCellValue('C'.$i, $reg_aa_sanc->Anno_Providencia);
                $pagina->SetCellValue('D'.$i, $reg_aa_sanc->NroAutorizacion);
                $pagina->SetCellValue('E'.$i, $reg_aa_sanc->nombre);
                $pagina->SetCellValue('F'.$i, $reg_aa_sanc->rif);
                $pagina->SetCellValue('G'.$i, $reg_aa_sanc->contribuyente);
                $pagina->SetCellValue('H'.$i, voltea_fecha($reg_aa_sanc->fecha_emision));
                $pagina->SetCellValue('I'.$i, voltea_fecha($reg_aa_sanc->fecha_notificacion));
                $pagina->SetCellValue('J'.$i, voltea_fecha($reg_aa_sanc->fecha_conclusion));
                $pagina->SetCellValue('K'.$i, $reg_aa_sanc->fiscal);
                $pagina->SetCellValue('L'.$i, $reg_aa_sanc->supervisor);
                $pagina->SetCellValue('M'.$i, $produccion[0]);
                $pagina->SetCellValue('N'.$i, $produccion[1]);
                $pagina->SetCellValue('O'.$i, $produccion[3]);
                $pagina->SetCellValue('P'.$i, $produccion[2]);
                $pagina->SetCellValue('Q'.$i, $multas_vdf);
                $pagina->SetCellValue('R'.$i, ($multas_vdf + $produccion[1] + $produccion[2] + $produccion[3]));
                //echo $produccion[0].' -- '.$produccion[1].' -- '.$produccion[3].' -- '.$produccion[2].'<br>';

                $insert = "INSERT INTO temp_sanc_actual (anno, numero, sector, programa, reparo, impuesto, intereses, multa_rep, multa_vdf) VALUES (".$reg_aa_sanc->Anno_Providencia.", ".$reg_aa_sanc->NroAutorizacion.", ".$reg_aa_sanc->sector.", '".$reg_aa_sanc->descripcion."', ".$produccion[0].", ".$produccion[1].", ".$produccion[3].", ".$produccion[2].", ".$multas_vdf.")";
                $tabla_insert = $conexion->query($insert);

                $i++;
            }
        }

        //*****************RESUMEN*****************************
        $cant = 0;
        $reparo = 0;
        $impuesto = 0;
        $intereses = 0;
        $multasrep = 0;
        $multasvdf = 0;
        $totalproduccion = 0;

        $resumen_aa_conc_sanc = "SELECT programa, count(anno) as cantidad, sum(reparo) as reparo, sum(impuesto) as impuesto, sum(intereses) as intereses, sum(multa_rep) as multas_rep, sum(multa_vdf) as multas_vdf FROM temp_sanc_actual GROUP BY programa";
        $tabla_resumen_aa_conc_sanc = $conexion->query($resumen_aa_conc_sanc);
        $i++;
        $total = 0;
        $objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('K'.$i, 'PROGRAMA');
        $objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('L'.$i, 'CANTIDAD');
        $objPHPExcel->getActiveSheet()->getStyle('M'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('M'.$i, 'REPARO');
        $objPHPExcel->getActiveSheet()->getStyle('N'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('N'.$i, 'IMPUESTO');
        $objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('O'.$i, 'INTERESES');
        $objPHPExcel->getActiveSheet()->getStyle('P'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('P'.$i, 'MULTAS REPARO');
        $objPHPExcel->getActiveSheet()->getStyle('Q'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('Q'.$i, 'MULTAS VDF');
        $objPHPExcel->getActiveSheet()->getStyle('R'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('R'.$i, 'TOTAL PRODUCCION');

        $objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('M'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('N'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('P'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('Q'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
        $objPHPExcel->getActiveSheet()->getStyle('R'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');

        $objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('M'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('M'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('N'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('N'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('P'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('P'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('Q'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('Q'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('R'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('R'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        
        $i++;

        while ($reg_resumen_aa_conc_sanc = $tabla_resumen_aa_conc_sanc->fetch_object())
        {
            $pagina->SetCellValue('K'.$i, $reg_resumen_aa_conc_sanc->programa);
            $pagina->SetCellValue('L'.$i, $reg_resumen_aa_conc_sanc->cantidad);
            $pagina->SetCellValue('M'.$i, $reg_resumen_aa_conc_sanc->reparo);
            $pagina->SetCellValue('N'.$i, $reg_resumen_aa_conc_sanc->impuesto);
            $pagina->SetCellValue('O'.$i, $reg_resumen_aa_conc_sanc->intereses);
            $pagina->SetCellValue('P'.$i, $reg_resumen_aa_conc_sanc->multas_rep);
            $pagina->SetCellValue('Q'.$i, $reg_resumen_aa_conc_sanc->multas_vdf);
            $produccion = $reg_resumen_aa_conc_sanc->impuesto + $reg_resumen_aa_conc_sanc->intereses + $reg_resumen_aa_conc_sanc->multas_rep + $reg_resumen_aa_conc_sanc->multas_vdf;
            $pagina->SetCellValue('R'.$i, $produccion);
            $cant += $reg_resumen_aa_conc_sanc->cantidad;
            $reparo += $reg_resumen_aa_conc_sanc->reparo;
            $impuesto += $reg_resumen_aa_conc_sanc->impuesto;
            $intereses += $reg_resumen_aa_conc_sanc->intereses;
            $multasrep += $reg_resumen_aa_conc_sanc->multas_rep;
            $multasvdf += $reg_resumen_aa_conc_sanc->multas_vdf;
            $totalproduccion += $produccion;
            $i++;

        }

        $objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('K'.$i, 'TOTAL');
        $objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('L'.$i, $cant);
        $objPHPExcel->getActiveSheet()->getStyle('M'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('M'.$i, $reparo);
        $objPHPExcel->getActiveSheet()->getStyle('N'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('N'.$i, $impuesto);
        $objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('O'.$i, $intereses);
        $objPHPExcel->getActiveSheet()->getStyle('P'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('P'.$i, $multasrep);
        $objPHPExcel->getActiveSheet()->getStyle('Q'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('Q'.$i, $multasvdf);
        $objPHPExcel->getActiveSheet()->getStyle('R'.$i)->getFont()->setBold(true);
        $pagina->SetCellValue('R'.$i, $totalproduccion);

        $objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        $objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        $objPHPExcel->getActiveSheet()->getStyle('M'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('M'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        $objPHPExcel->getActiveSheet()->getStyle('N'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('N'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        $objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        $objPHPExcel->getActiveSheet()->getStyle('P'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('P'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        $objPHPExcel->getActiveSheet()->getStyle('Q'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('Q'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        $objPHPExcel->getActiveSheet()->getStyle('R'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('R'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        //***************FIN RESUMEN***************************

    //************************************************************************************************************************************

//****************************************************************************************************************************************


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
$objWriter->save("../formatos/generados/GEN_controles_internos.xlsx");

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