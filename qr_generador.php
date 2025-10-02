<?php
require "lib/phpqrcode/qrlib.php";

QRcode::png($_GET['code']);

?>