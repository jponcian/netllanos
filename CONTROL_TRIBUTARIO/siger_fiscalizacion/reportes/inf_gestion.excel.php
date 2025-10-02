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
$objPHPExcel = $objReader->load("../formatos/informe_gestion.xlsx");
// Indicamos que se pare en la hoja uno del libro
$objPHPExcel->setActiveSheetIndex(0);
//Escribimos en la hoja en la celda B1
$pagina = $objPHPExcel->getActiveSheet();

//Datos para generar los valores en el formato
$sql = "SELECT formato, titulo, periodo, celdas_editables, celda_inicial, celda_final, region, nom_region FROM formatos WHERE formato = 'informe_gestion.xlsx'";
$tabla_formato = $conexion->query($sql);
$reg = $tabla_formato->fetch_object();
$lista = explode(',', $reg->celdas_editables);



//**************TITULO Y AÑO******************
$pagina->SetCellValue($reg->titulo, 'MES: '.nomMes($mes).' DE '.$anno);
$pagina->SetCellValue($reg->region, 'REGION: '.$reg->nom_region);
//        echo $reg->titulo.' --- '.'MES: '.nomMes($mes).' DE '.$anno.'<br>'; 
//        echo $reg->region.' --- '.'REGION: '.$reg->nom_region.'<br>'; 
$x = $reg->celda_inicial;
//        echo 'Celda Inicial: '.$x.'<br>'; 

//Buscamos los NACIONALES
$datos = "SELECT clasificacion, descripcion, fiscales, visitados, multados, clausurados, iniciadas, actas_notificadas, prod_potencial, prod_efectiva FROM informe_gestion WHERE periodo_inicio = '".$inicio."' AND periodo_fin = '".$fin."'";
$tabla = $conexion->query($datos);

$i = 1;

$cantidad = $tabla->num_rows;
if ($cantidad > 0)
{
    //echo "Nacional: ".$cantidad.'<br>';
    while ($data = $tabla->fetch_object())
    {
//        echo "Clasificacion: ".$data->clasificacion.'<br>';
        //**************** PROGRAMAS NACIONALES *****************************
        if ($data->clasificacion == "FISCALIZACION INTEGRAL NACIONAL")
        {
            $celdaY = $x;
        }

        if ($data->clasificacion == "FISCALIZACION PUNTUAL NACIONAL")
        {
            $celdaY = $x + 1;            
        }

        if ($data->clasificacion == "VERIFICACION NACIONAL")
        {
            $celdaY = $x + 2;
        }

        if ($data->clasificacion == "OTROS PROGRAMAS NACIONAL")
        {
            $celdaY = $x + 3;
        }



        //**************** PROGRAMAS ESPECIALES *****************************
        if ($data->clasificacion == "FISCALIZACION INTEGRAL ESPECIAL")
        {
            $celdaY = $x + 5;
        }

        if ($data->clasificacion == "FISCALIZACION PUNTUAL ESPECIAL")
        {
            $celdaY = $x + 6;
        }

        if ($data->clasificacion == "VERIFICACION ESPECIAL")
        {
            $celdaY = $x + 7;
        }

        if ($data->clasificacion == "OTROS PROGRAMAS ESPECIAL")
        {
            $celdaY = $x + 8;
        }



        //**************** PROGRAMAS REGIONALES *****************************
        if ($data->clasificacion == "FISCALIZACION INTEGRAL REGIONAL")
        {
            $celdaY = $x + 10;
        }

        if ($data->clasificacion == "FISCALIZACION PUNTUAL REGIONAL")
        {
            $celdaY = $x + 11;
        }

        if ($data->clasificacion == "VERIFICACION REGIONAL")
        {
            $celdaY = $x + 12;
        }

        if ($data->clasificacion == "OTROS PROGRAMAS")
        {
            $celdaY = $x + 13;
        }
//        echo "Celda a Imprimir: ".$celdaY.'<br>';

        $pagina->SetCellValue($lista[0].$celdaY, $data->fiscales); 
        $pagina->SetCellValue($lista[1].$celdaY, $data->visitados); 
        $pagina->SetCellValue($lista[2].$celdaY, $data->multados); 
        $pagina->SetCellValue($lista[3].$celdaY, $data->clausurados); 
        $pagina->SetCellValue($lista[4].$celdaY, $data->iniciadas); 
        $pagina->SetCellValue($lista[5].$celdaY, $data->actas_notificadas); 
        $pagina->SetCellValue($lista[6].$celdaY, $data->prod_potencial); 
        $pagina->SetCellValue($lista[7].$celdaY, $data->prod_efectiva); 

//        echo $lista[0].$celdaY.' --- '.$data->fiscales.'<br>'; 
//        echo $lista[1].$celdaY.' --- '.$data->visitados.'<br>'; 
//        echo $lista[2].$celdaY.' --- '.$data->multados.'<br>'; 
//        echo $lista[3].$celdaY.' --- '.$data->clausurados.'<br>'; 
//        echo $lista[4].$celdaY.' --- '.$data->iniciadas.'<br>'; 
//        echo $lista[5].$celdaY.' --- '.$data->actas_notificadas.'<br>'; 
//        echo $lista[6].$celdaY.' --- '.$data->prod_potencial.'<br>'; 
//        echo $lista[7].$celdaY.' --- '.$data->prod_efectiva.'<br>'; 

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
$objWriter->save("../formatos/generados/GEN_informe_gestion.xlsx");

$mensaje = "Informe Generado Satisfactoriamente";
$permitido = true;

$info = array("permitido"=>$permitido,
				"mensaje"=>$mensaje);

echo json_encode($info);

exit;
?>