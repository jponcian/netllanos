<?php
$path="formatos/generados"; 
$directorio=dir($path);
//echo "Descargas ".$path.":<br><br>";
while ($archivo = $directorio->read())
{
    if (strlen($archivo) > 3)
    {
        echo "<a href=".$path."/".$archivo.">".$archivo."</a>"."<br>";
    }
}	
$directorio->close();
?>