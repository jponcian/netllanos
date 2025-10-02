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
$objPHPExcel = $objReader->load("../formatos/fiscalizaciones_puntuales.xlsx");
// Indicamos que se pare en la hoja uno del libro
$objPHPExcel->setActiveSheetIndex(0);
//Escribimos en la hoja en la celda B1
$pagina = $objPHPExcel->getActiveSheet();

//Datos para generar los valores en el formato
$sql = "SELECT formato, titulo, periodo, celdas_editables, celda_inicial, celda_final, region, nom_region FROM formatos WHERE formato = 'fiscalizaciones_puntuales.xlsx'";
$tabla_formato = $conexion->query($sql);
$reg = $tabla_formato->fetch_object();
$lista = explode(',', $reg->celdas_editables);

//**************TITULO Y AÑO******************
$pagina->SetCellValue($reg->titulo, 'FECHA DESDE '.str_replace("/", "-", voltea_fecha($inicio)).' HASTA '.str_replace("/", "-", voltea_fecha($fin)));
$pagina->SetCellValue($reg->region, 'REGION: '.$reg->nom_region);


//FISCALES
$sqlFiscales = "SELECT COUNT(DISTINCT fiscal_actuantes) as fiscales FROM fiscalizaciones_puntuales WHERE periodo_inicio = '".$inicio."' AND periodo_fin = '".$fin."' AND tipo_operativo = 'NACIONAL'";
$tabla_f = $conexion->query($sqlFiscales);
$fiscal = $tabla_f->fetch_object();
$fiscales = $fiscal->fiscales; 
$pagina->SetCellValue('H2', $fiscales);

//FISCALES
$sqlFiscales = "SELECT COUNT(DISTINCT fiscal_actuantes) as fiscales FROM fiscalizaciones_puntuales WHERE periodo_inicio = '".$inicio."' AND periodo_fin = '".$fin."' AND tipo_operativo = 'REGIONAL'";
$tabla_f = $conexion->query($sqlFiscales);
$fiscal = $tabla_f->fetch_object();
$fiscales = $fiscal->fiscales; 
$pagina->SetCellValue('I2', $fiscales);

//Buscamos los NACIONALES
$datos = "SELECT tipo_operativo, programa, sector, num_prov, impuesto, emision_prov, notificacion_prov, sp_nombre, sp_rif, sp_sector_econ, fiscal_actuantes, sancionado, conforme, proceso, reparo, impuesto_omitido, intereses, multas, allanado_total, allanado_total FROM fiscalizaciones_puntuales WHERE periodo_inicio = '".$inicio."' AND periodo_fin = '".$fin."' AND tipo_operativo = 'NACIONAL'";
$tabla = $conexion->query($datos);
$x = $reg->celda_inicial;
$i = 1;

$cantidad = $tabla->num_rows;
if ($cantidad > 0)
{
    //echo "Nacional: ".$cantidad.'<br>';
    
    while ($data = $tabla->fetch_object())
    {
        $pagina->SetCellValue($lista[0].$x, $i); 
        $pagina->SetCellValue($lista[1].$x, $data->programa); 
        $pagina->SetCellValue($lista[2].$x, $data->sector); 
        $pagina->SetCellValue($lista[3].$x, $data->num_prov); 
        $pagina->SetCellValue($lista[4].$x, $data->impuesto); 
        $pagina->SetCellValue($lista[5].$x, voltea_fecha($data->emision_prov)); 
        $pagina->SetCellValue($lista[6].$x, voltea_fecha($data->notificacion_prov)); 
        $pagina->SetCellValue($lista[7].$x, $data->sp_nombre); 
        $pagina->SetCellValue($lista[8].$x, $data->sp_rif); 
        $pagina->SetCellValue($lista[9].$x, $data->sp_sector_econ); 
        $pagina->SetCellValue($lista[10].$x, $data->fiscal_actuantes);
        if ($data->sancionado == "x") { $pagina->SetCellValue($lista[11].$x, $data->sancionado); }
        if ($data->conforme == "x") { $pagina->SetCellValue($lista[12].$x, $data->conforme); } 
        if ($data->proceso == "x") { $pagina->SetCellValue($lista[13].$x, $data->proceso); }
        $pagina->SetCellValue($lista[14].$x, $data->reparo); 
        $pagina->SetCellValue($lista[15].$x, $data->impuesto_omitido); 
        $pagina->SetCellValue($lista[16].$x, $data->intereses); 
        $pagina->SetCellValue($lista[17].$x, $data->multas); 
        $pagina->SetCellValue($lista[18].$x, $data->allanado_total); 
        $pagina->SetCellValue($lista[19].$x, $data->allanado_total);
         
        $x++;
        $i++;

    }
}

//Buscamos los REGIONALES
$datos_r = "SELECT tipo_operativo, programa, sector, num_prov, impuesto, emision_prov, notificacion_prov, sp_nombre, sp_rif, sp_sector_econ, fiscal_actuantes, sancionado, conforme, proceso, reparo, impuesto_omitido, intereses, multas, allanado_total, allanado_total FROM fiscalizaciones_puntuales WHERE periodo_inicio = '".$inicio."' AND periodo_fin = '".$fin."' AND tipo_operativo = 'REGIONAL'";
$tabla_r = $conexion->query($datos_r);
$x = $reg->celda_inicial + 82;
$i = 1;

$cantidad = $tabla_r->num_rows;
if ($cantidad > 0)
{
    //echo "Regional: ".$cantidad.'<br>';
    
    while ($data = $tabla_r->fetch_object())
    {
        $pagina->SetCellValue($lista[0].$x, $i); 
        $pagina->SetCellValue($lista[1].$x, $data->programa); 
        $pagina->SetCellValue($lista[2].$x, $data->sector); 
        $pagina->SetCellValue($lista[3].$x, $data->num_prov); 
        $pagina->SetCellValue($lista[4].$x, $data->impuesto); 
        $pagina->SetCellValue($lista[5].$x, voltea_fecha($data->emision_prov)); 
        $pagina->SetCellValue($lista[6].$x, voltea_fecha($data->notificacion_prov)); 
        $pagina->SetCellValue($lista[7].$x, $data->sp_nombre); 
        $pagina->SetCellValue($lista[8].$x, $data->sp_rif); 
        $pagina->SetCellValue($lista[9].$x, $data->sp_sector_econ); 
        $pagina->SetCellValue($lista[10].$x, $data->fiscal_actuantes); 
        if ($data->sancionado == "x") { $pagina->SetCellValue($lista[11].$x, $data->sancionado); }
        if ($data->conforme == "x") { $pagina->SetCellValue($lista[12].$x, $data->conforme); } 
        if ($data->proceso == "x") { $pagina->SetCellValue($lista[13].$x, $data->proceso); }
        $pagina->SetCellValue($lista[14].$x, $data->reparo); 
        $pagina->SetCellValue($lista[15].$x, $data->impuesto_omitido); 
        $pagina->SetCellValue($lista[16].$x, $data->intereses); 
        $pagina->SetCellValue($lista[17].$x, $data->multas); 
        $pagina->SetCellValue($lista[18].$x, $data->allanado_total); 
        $pagina->SetCellValue($lista[19].$x, $data->allanado_total); 

        $x++;
        $i++;
    }
}

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
$objWriter->save("../formatos/generados/GEN_fiscalizaciones_puntuales.xlsx");

$mensaje = "Informe Generado Satisfactoriamente";
$permitido = true;

$info = array("permitido"=>$permitido,
				"mensaje"=>$mensaje);

echo json_encode($info);

exit;
?>