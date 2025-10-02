<?php
mysql_query("SET NAMES 'latin1'");

$fuente_cabecera = 8;
$alto_cabecera = 4;
$a=35 ; 	
$b=100 ; 
$c=80 ;

//----------------------------
//if ($comprobante == '31' and $_SESSION['DIVISION']==9 and $_SESSION['DIVISION2']==100) // solo jefe region
if ($comprobante == '31' and $_SESSION['DIVISION']==9 and ($_SESSION['DIVISION2']>=100))
	{
	$consulta_x = "SELECT * FROM vista_jefes WHERE division=1";
	$tabla_x = mysql_query($consulta_x);
	$registro_x = mysql_fetch_object($tabla_x);
	}
else
	{
	$consulta_x = "SELECT * FROM vista_jefes WHERE division=".$_SESSION['DIVISION']."";
	$tabla_x = mysql_query($consulta_x);
	$registro_x = mysql_fetch_object($tabla_x);
	}
//----------------------------
$this->SetY(-37.8);

$this->SetFont('Arial','B',$fuente_cabecera);
$this->Cell(0,$alto_cabecera,'Responsable Patrimonial Primario',1,0,'L');
$this->Ln($alto_cabecera);

$this->Cell($a,$alto_cabecera,'Cedula de Identidad',1,0,'L');
$this->Cell($b,$alto_cabecera,'Apellidos y Nombres',1,0,'L');
$this->Cell($c,$alto_cabecera,'Cargo',1,0,'L');	

$y=$this->GetY();
$this->Cell(0,$alto_cabecera*3+2+2,'Firma y Sello',1,0,'L');	
$this->SetY($y);

$this->Ln($alto_cabecera);

$this->SetFont('Arial','B',$fuente_cabecera);
$this->Cell($a,$alto_cabecera+2,'V-'.formato_cedula($registro_x->cedula),1,0,'L');
$this->Cell($b,$alto_cabecera+2,$registro_x->jefe,1,0,'L');
$this->Cell($c,$alto_cabecera+2,$registro_x->cargo,1,0,'L');	
$this->Ln($alto_cabecera+2);

if ($comprobante == '21' or $comprobante == '31')
	{
	// USUARIO QUE HIZO LA REASIGNACION
	list ($nombres) = funcion_funcionario($funcionario);
	//-----------------------------
	$this->SetFont('Arial','B',$fuente_cabecera); 
	$this->Cell($a+$b+$c,$alto_cabecera+2,'Preparado por:         '.$nombres.'         C.I. V-'.formato_cedula($funcionario).'                 Firma:',1,0,'L');
	}
else
	{
	//----------------------------
	$consulta_x = "SELECT * FROM z_empleados WHERE (division=9 and cedula=12991310);";
	$tabla_x = mysql_query($consulta_x);
	$registro_x = mysql_fetch_object($tabla_x);
	//----------------------------
	
	$this->SetFont('Arial','B',$fuente_cabecera); 
	$this->Cell($a+$b+$c,$alto_cabecera+2,'Preparado por:         '.$registro_x->Nombres.' '.$registro_x->Apellidos.'         C.I. V-'.formato_cedula($registro_x->cedula).'         COORDINADOR BIENES NACIONALES '.strtoupper(utf8_decode(buscar_region())),1,0,'L');
	}
?>