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
$objPHPExcel = $objReader->load("../formatos/formato_casos_proceso.xlsx");
// Indicamos que se pare en la hoja uno del libro
$objPHPExcel->setActiveSheetIndex(0);
//Escribimos en la hoja en la celda B1
$pagina = $objPHPExcel->getActiveSheet();

//Datos para generar los valores en el formato
$sql = "SELECT formato, titulo, periodo, celdas_editables, celda_inicial, celda_final, region, nom_region FROM formatos WHERE formato = 'formato_casos_proceso.xlsx'";
$tabla_formato = $conexion->query($sql);
$reg = $tabla_formato->fetch_object();
$lista = explode(',', $reg->celdas_editables);

//**************TITULO Y AÑO******************
$pagina->SetCellValue($reg->region, 'REGION: '.$reg->nom_region);
$pagina->SetCellValue($reg->titulo, 'AL '.str_replace("/", "-", voltea_fecha($fin)));

//Array con los ultimos 4 años
$listaAños = array($anno-3, $anno-2, $anno-1, $anno);

//Buscamos los registros de la Base de datos
$datos = "SELECT anno, programa, proceso_auditoria, nivel_supervisor, lapso_allanamiento, otras_causas FROM formato_casos_proceso WHERE formato_casos_proceso.periodo_inicio = '".$inicio."' AND formato_casos_proceso.periodo_fin = '".$fin."'"; //echo $datos.'<br>';
$tabla = $conexion->query($datos);
$x = $reg->celda_inicial;
$vAnno = 0;
while ($data = $tabla->fetch_object())
{
    //echo $data->anno.'<br>';
    if ($data->anno <= $listaAños[0])
    {
        $celdaX = array($lista[0], $lista[1], $lista[2], $lista[3]); 
    }

    if ($data->anno == $listaAños[1])
    {
        $celdaX = array($lista[4], $lista[5], $lista[6], $lista[7]); 
    }

    if ($data->anno == $listaAños[2])
    {
        $celdaX = array($lista[8], $lista[9], $lista[10], $lista[11]); 
    }
    
    if ($data->anno == $listaAños[3])
    {
        $celdaX = array($lista[12], $lista[13], $lista[14], $lista[15]); 
    }

    if ($data->programa == "FISCALIZACION INTEGRAL O GENERAL") 
    {
        $celdaY = $x;
    }

    if ($data->programa == "FISCALIZACION EN MATERIA DE PRECIOS DE TRANSFERENCIA") 
    { 
        $celdaY = $x + 1;
    }

    if ($data->programa == "FISCALIZACION PUNTUAL") 
    { 
        $celdaY = $x + 2;
    }
        
    if ($data->programa == "VERIFICACION") 
    { 
        $celdaY = $x + 3;
    }

    if ($data->programa == "OTROS PROGRAMAS") 
    { 
        $celdaY = $x + 4;
    }
    $pagina->SetCellValue($celdaX[0].$celdaY, $data->proceso_auditoria); 
    $pagina->SetCellValue($celdaX[1].$celdaY, $data->nivel_supervisor);
    $pagina->SetCellValue($celdaX[2].$celdaY, $data->lapso_allanamiento);
    $pagina->SetCellValue($celdaX[3].$celdaY, $data->otras_causas);

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
$objWriter->save("../formatos/generados/GEN_formato_casos_proceso.xlsx");

$mensaje = "Informe Generado Satisfactoriamente";
$permitido = true;

$info = array("permitido"=>$permitido,
				"mensaje"=>$mensaje);

echo json_encode($info);

exit;

?>