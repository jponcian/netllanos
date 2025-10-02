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
$objPHPExcel = $objReader->load("../formatos/otros_programas.xlsx");
// Indicamos que se pare en la hoja uno del libro
$objPHPExcel->setActiveSheetIndex(0);
//Escribimos en la hoja en la celda B1
$pagina = $objPHPExcel->getActiveSheet();

//Datos para generar los valores en el formato
$sql = "SELECT formato, titulo, periodo, celdas_editables, celda_inicial, celda_final, region, nom_region FROM formatos WHERE formato = 'otros_programas.xlsx'";
$tabla_formato = $conexion->query($sql);
$reg = $tabla_formato->fetch_object();
$lista = explode(',', $reg->celdas_editables);

//**************TITULO Y AÑO******************
$pagina->SetCellValue($reg->region, $reg->nom_region);
$pagina->SetCellValue($reg->titulo, nomMes($mes));
$pagina->SetCellValue($reg->periodo, $anno);

//Buscamos los registros de la Base de datos
$datos = "SELECT rif, tipo_solicitud, numero_documento, emision, notificacion FROM programa_control_fiscal WHERE periodo_inicio = '".$inicio."' AND periodo_fin = '".$fin."'";
$tabla = $conexion->query($datos);
$x = $reg->celda_inicial;
while ($data = $tabla->fetch_object())
{
    $i=$x;

    if ($i < $reg->celda_final)
    {

        $pagina->SetCellValue($lista[0].$i, $data->rif);
        $pagina->SetCellValue($lista[1].$i, $data->tipo_solicitud);
        $pagina->SetCellValue($lista[2].$i, $data->numero_documento);
        $pagina->SetCellValue($lista[3].$i, voltea_fecha($data->emision));
        $pagina->SetCellValue($lista[4].$i, voltea_fecha($data->notificacion));
        
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
$objWriter->save("../formatos/generados/GEN_otros_programas.xlsx");

$mensaje = "Informe Generado Satisfactoriamente";
$permitido = true;

$info = array("permitido"=>$permitido,
				"mensaje"=>$mensaje);

echo json_encode($info);

exit;
?>