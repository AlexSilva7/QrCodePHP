<?php 
//composer require mpdf/qrcode
require __DIR__.'/vendor/autoload.php';

//require 'vendor/autoload.php';
//require "app/Pix/Payload.php";

use \App\Pix\Payload;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;

//INSTANCIA PRINCIPAL DO PAYLOAD PIX
$obPayload = (new Payload)->setPixKey('alexaraujo_rj@yahoo.com.br')
						->setDescription('CartolaFC')
						->setMerchantName('Alex Silva')
						  ->setMerchantCity('RIO DE JANEIRO')
						  ->setAmount(30.00)
						  ->setTxid('AlexSilva');

//CODIGO DE PAGAMENTO PIX
$payloadQrCode = $obPayload->getPayload();

//QR CODE
$obQrCode = new QrCode($payloadQrCode);


$image = (new Output\png)->output($obQrCode,400);

header('Content-Type: image/png');
echo $image;


/*
echo "<pre>";
print_r($obQrCode);
echo "</pre>";
exit;
*/


/*
<!--
<img src="data:image/png;base64, <?=base64_encode($image)?>">
<strong>
	<?=$payloadQrCode?>
</strong>
-->
*/