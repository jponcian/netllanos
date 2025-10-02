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
$objPHPExcel = $objReader->load("../formatos/resultado_mensual.xlsx");
// Indicamos que se pare en la hoja uno del libro
$objPHPExcel->setActiveSheetIndex(0);
//Escribimos en la hoja en la celda B1
$pagina = $objPHPExcel->getActiveSheet();

//Datos para generar los valores en el formato
$sql = "SELECT formato, titulo, periodo, celdas_editables, celda_inicial, celda_final, region, nom_region FROM formatos WHERE formato = 'resultado_mensual.xlsx'";
$tabla_formato = $conexion->query($sql);
$reg = $tabla_formato->fetch_object();
$lista = explode(',', $reg->celdas_editables);



//**************TITULO Y AÑO******************
$pagina->SetCellValue($reg->titulo, 'DEL 01 AL '.substr(voltea_fecha($fin), 0, 2).' DE '.nomMes($mes).' DE '.$anno);
//$pagina->SetCellValue($reg->region, 'REGION: '.$reg->nom_region);
//        echo $reg->titulo.' --- '.'MES: '.nomMes($mes).' DE '.$anno.'<br>'; 
//        echo $reg->region.' --- '.'REGION: '.$reg->nom_region.'<br>'; 
$x = $reg->celda_inicial;
//echo 'Celda Inicial: '.$x.'<br>'; 

$datos = "SELECT programa, tributos, fecha_aplicacion, fisc_int, fisc_punt, vdf, sector_econ, notificados, sancionados, clausurados, conformes, proceso, produccion_potencial, allanamientos FROM resultado_mensual WHERE periodo_inicio = '".$inicio."' AND periodo_fin = '".$fin."'";
$tabla = $conexion->query($datos);

$cantidad = $tabla->num_rows;
if ($cantidad > 0)
{
    //echo "Nacional: ".$cantidad.'<br>';
    while ($data = $tabla->fetch_object())
    {

        $i=$x;

        if ($i > $reg->celda_final) { $pagina->insertNewRowBefore($x, 1); }

        $pagina->SetCellValue($lista[0].$x, $reg->nom_region); 
        $pagina->SetCellValue($lista[1].$x, $data->programa); 
        $pagina->SetCellValue($lista[2].$x, $data->tributos); 
        if ($data->fecha_aplicacion != "0000-00-00") { $pagina->SetCellValue($lista[3].$x, voltea_fecha($data->fecha_aplicacion)); }
        if ($data->fisc_int == "x") { $pagina->SetCellValue($lista[4].$x, $data->fisc_int); }
        if ($data->fisc_punt == "x") { $pagina->SetCellValue($lista[5].$x, $data->fisc_punt); } 
        if ($data->vdf == "x") { $pagina->SetCellValue($lista[6].$x, $data->vdf); } 
        $pagina->SetCellValue($lista[7].$x, $data->sector_econ); 
        $pagina->SetCellValue($lista[8].$x, $data->notificados); 
        $pagina->SetCellValue($lista[9].$x, $data->sancionados); 
        $pagina->SetCellValue($lista[10].$x, $data->clausurados); 
        $pagina->SetCellValue($lista[11].$x, $data->conformes); 
        $pagina->SetCellValue($lista[12].$x, $data->proceso); 
        $pagina->SetCellValue($lista[13].$x, $data->produccion_potencial); 
        $pagina->SetCellValue($lista[14].$x, $data->allanamientos); 

        $x++;

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
$objWriter->save("../formatos/generados/GEN_resultado_mensual.xlsx");

$mensaje = "Informe Generado Satisfactoriamente";
$permitido = true;

$info = array("permitido"=>$permitido,
				"mensaje"=>$mensaje);

echo json_encode($info);

exit;
?>