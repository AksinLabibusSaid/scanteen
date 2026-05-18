<?php
require 'vendor/autoload.php';
use chillerlan\QRCode\QRCode;
$qr = new QRCode();
echo substr($qr->render('t1'), 0, 100);
