<?php

function tipo_tributo($conn, $sql)
{
    $tributo = null;
    $tabla_tipo = $conn->query($sql);
    while ($reg = $tabla_tipo->fetch_object())
    {
        $pos = strpos($tributo, $reg->tributo);
        if ($pos === false) 
        {
            $tributo = $tributo."/".$reg->tributo;
        } else{
            $tributo = $tributo;
        }
    }

    if (substr($tributo, 0, 1) == "/") 
	{
		$tributo = substr($tributo, 1);
	}

    if (substr($tributo, -1) == "/") 
	{
		$tributo = substr($tributo, 0,-1);
	}

    if ($tributo == "ISLR/IVA") { $tributo = "IVA/ISLR"; }

    return $tributo;    
}

function sector_comercial($conn, $sql)
{
    $sector = null;
    $tabla_tipo = $conn->query($sql);
    while ($reg = $tabla_tipo->fetch_object())
    {
        $pos = strpos($sector, $reg->sector);
        if ($pos === false) 
        {
            $sector = $sector.", ".$reg->sector;
        } else{
            $sector = $sector;
        }
    }

    if (substr($sector, 0, 2) == ", ") 
	{
		$sector = substr($sector, 1);
	}

    if (substr($sector, -2) == ", ") 
	{
		$sector = substr($sector, 0,-2);
	}

    return $sector;    
}

function fecha_aplicacion($conn, $sql)
{
    $tabla_tipo = $conn->query($sql);
    $rows = $tabla_tipo->num_rows;
    if ($rows > 0)
    {
        $reg = $tabla_tipo->fetch_object();
        $fecha = $reg->fecha;
        if ($fecha == null) { $fecha = "0000-00-00"; }
    } else {
        $fecha = "0000-00-00";        
    }
    return $fecha;
}

function contar($conn, $sql)
{
    $tabla_tipo = $conn->query($sql);
    $rows = $tabla_tipo->num_rows;
    if ($rows > 0)
    {
        $reg = $tabla_tipo->fetch_object();
        $contar = $reg->cantidad;
        if ($contar == null) { $contar = 0; }
    } else {
        $contar = 0;        
    }
    return $contar;
}

function contar_tipos($tipo)
{
    $info = array();
    $tipo = substr($tipo, 0, 3);
    if ($tipo == "Ver")
    {
        $vdf = "x";
        $fisc_int = "";
        $fisc_p = "";
    }
    if ($tipo == "FPN" or $tipo == "FPR")
    {
        $vdf = "";
        $fisc_int = "";
        $fisc_p = "x";
    }
    if ($tipo == "Fis" or $tipo == "Imp")
    {
        $vdf = "";
        $fisc_int = "x";
        $fisc_p = "";
    }
    if ($tipo == "Otr" or $tipo == "Com")
    {
        $vdf = "";
        $fisc_int = "";
        $fisc_p = "";                
    }
    $info = array($vdf, $fisc_int, $fisc_p);
    return $info;

}
?>