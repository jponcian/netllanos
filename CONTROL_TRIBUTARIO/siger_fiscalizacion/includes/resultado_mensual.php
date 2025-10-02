<?php
//*****SCRIPT PARA GENERAR EL INFORME MENSUAL********//
//                                                   //
//    Elaborado por Gustavo García para el SENIAT    //
//                                                   //
///////////////////////////////////////////////////////

//incluimos las funciones
include("conexion.php");
include("../funciones/funcionesphp.php");
include("../funciones/func.contador.php");

//mysql_query("SET NAMES 'utf8'");

//Variables a utilizar
$info = array();
$mensaje = "Error al Generar el Informe";
$permitido = false;
$mes = $_POST['mes'];
$anno = $_POST['anno'];
$getFechas = fechas($mes, $anno);
$inicio = $getFechas[0];
$fin = $getFechas[1];
$año = date("Y");

BorrarRegistros($conexion, 'resultado_mensual', $inicio, $fin);

//RECORREMOS LOS TIPOS DE PROGRAMAS

$sqltipo = "SELECT operativos.programa FROM operativos GROUP BY operativos.programa ORDER BY operativos.programa ASC";
//echo $sqltipo.'<br>';
$programa = "";
$tabla_tipo = $conexion->query($sqltipo);
while ($reg = $tabla_tipo->fetch_object())
{
    //*********TIPO DE PROGRAMA**************
    $programa = $reg->programa;
    //echo "Programa: ".$reg->programa."<br>";
    //*********TRIBUTOS**********************
    $sql = "SELECT DISTINCTROW(impuesto) as tributo FROM operativos WHERE programa = '".$reg->programa."' AND periodo_inicio BETWEEN '".$inicio."' AND '".$fin."'";
    $tributos = tipo_tributo($conexion, $sql);
    //echo "Tributos: ".$tributos."<br>";
    //*********FECHA APLICACION**********************
    $sql = "SELECT MAX(notificacion_prov) as fecha FROM operativos WHERE periodo_inicio BETWEEN '".$inicio."' AND '".$fin."' AND programa = '".$reg->programa."'";
    $fecha_aplicacion = fecha_aplicacion($conexion, $sql);
    //echo "Tributos: ".$tributos."<br>";
    //*********TIPO OPERATIVO****************
    $tipos = contar_tipos($reg->programa);
    //echo "Tipos: VDF: ".$tipos[0]." FI: ".$tipos[1]." PUNTUAL: ".$tipos[2].'<br>';
    //*********SECTOR ECONOMICO**************
    $sql = "SELECT DISTINCTROW(sp_sector_econ) as sector FROM operativos WHERE programa = '".$reg->programa."' AND periodo_inicio BETWEEN '".$inicio."' AND '".$fin."'";
    $sector_econ = sector_comercial($conexion, $sql);
    //echo "Sector: ".$sector_econ."<br>";
    //*********SANCIONADOS*******************
    $sql = "SELECT Count(sancionado) AS cantidad FROM operativos WHERE programa = '".$reg->programa."' and sancionado = 'x' AND periodo_inicio BETWEEN '".$inicio."' AND '".$fin."'";
    $sancionados = contar($conexion, $sql);
    //echo "Sancionados: ".$sancionados.'<br>';
    //*********CLAUSURADOS*******************
    $sql = "SELECT Count(clausurado) AS cantidad FROM operativos WHERE programa = '".$reg->programa."' and clausurado = 'x' AND periodo_inicio BETWEEN '".$inicio."' AND '".$fin."'";
    $clausurados = contar($conexion, $sql);
    //echo "clausurados: ".$clausurados.'<br>';
    //*********CONFORMES*********************
    $sql = "SELECT Count(conforme) AS cantidad FROM operativos WHERE programa = '".$reg->programa."' and conforme = 'x' AND periodo_inicio BETWEEN '".$inicio."' AND '".$fin."'";
    $conformes = contar($conexion, $sql);
    //echo "conformes: ".$conformes.'<br>';
    //*********PROCESO***********************
    $sql = "SELECT Count(proceso) AS cantidad FROM operativos WHERE programa = '".$reg->programa."' and proceso = 'x' AND periodo_inicio BETWEEN '".$inicio."' AND '".$fin."'";
    $proceso = contar($conexion, $sql);
    //echo "proceso: ".$proceso.'<br>';
    //*********NOTIFICADOS*******************
    $notificados = $sancionados + $conformes;
    //echo "notificados: ".$notificados.'<br>';
    //*********PRODUCCION********************
    $sql = "SELECT SUM(produccion_potencial) AS cantidad FROM operativos WHERE programa = '".$reg->programa."' AND periodo_inicio BETWEEN '".$inicio."' AND '".$fin."'";
    $produccion = contar($conexion, $sql);
    //echo "produccion: ".$produccion.'<br>';
    //*********ALLANAMIENTO******************
    $sql = "SELECT SUM(produccion_efectiva) AS cantidad FROM operativos WHERE programa = '".$reg->programa."' AND periodo_inicio BETWEEN '".$inicio."' AND '".$fin."'";
    $produccion_efectiva = contar($conexion, $sql);
    //echo "produccion_efectiva: ".$produccion_efectiva.'<br>';
	

	//AGREGAMOS EL REGISTRO
	if ($programa != "")
	{
		$insert = "INSERT INTO resultado_mensual (programa, tributos, fecha_aplicacion, fisc_int, fisc_punt, vdf, sector_econ, notificados, sancionados, clausurados, conformes, proceso, produccion_potencial, allanamientos, periodo_inicio, periodo_fin) VALUES ('".$programa."', '".$tributos."', '".$fecha_aplicacion."', '".$tipos[1]."', '".$tipos[2]."', '".$tipos[0]."', '".$sector_econ."', ".$notificados.", ".$sancionados.", ".$clausurados.", ".$conformes.", ".$proceso.", ".$produccion.", ".$produccion_efectiva.", '".$inicio."', '".$fin."')";
		//echo $insert.'<br>';
		$result = $conexion->query($insert);

        if ($conexion->affected_rows){
            $mensaje = "Informe Generado Satisfactoriamente";
            $permitido = true;
        }
	}
}

$info = array("permitido"=>$permitido,
				"mensaje"=>$mensaje);

echo json_encode($info);

?>