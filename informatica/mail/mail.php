<?php
require_once('class.phpmailer.php');
$mail = new PHPMailer();
//indico a la clase que use SMTP
$mail­>IsSMTP();
//permite modo debug para ver mensajes de las cosas que van ocurriendo
//$mail­>SMTPDebug = 1;
//Debo de hacer autenticación SMTP
$mail­>SMTPAuth = true;
$mail­>SMTPSecure = "ssl";
//indico el servidor de Gmail para SMTP
$mail­>Host = "smtp.gmail.com";
//indico el puerto que usa Gmail
$mail­>Port = 465;
//indico un usuario / clave de un usuario de gmail
$mail­>Username = "jponciang";
$mail­>Password = "j16912337";
$mail­>SetFrom('jponciang@gmail.com', 'Javier Alejandro Ponciano');
$mail­>AddReplyTo("jponciang@gmail.com","Javier Alejandro Ponciano");
$mail­>Subject = "Envío de email usando SMTP de Gmail";
$mail­>MsgHTML("Hola que tal, esto es el cuerpo del mensaje!");
//indico destinatario
$address = "jponcian@seniat.gob.ve";
$mail­>AddAddress($address, "Javier Ponciano");
if(!$mail­>Send()) {
echo "Error al enviar: " . $mail­>ErrorInfo;
} else {
echo "Mensaje enviado!";
}
?>