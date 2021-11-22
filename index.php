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

/*
$testGD = get_extension_funcs("gd"); // Grab function list 
if (!$testGD){ echo "GD not even installed.";
	phpinfo();
	 exit; }
echo"<pre>".print_r($testGD,true)."</pre>";
*/

$image = (new Output\Png)->output($obQrCode, 200);

/*
header('Content-Type: image/png');
echo $image;
*/
/*
echo "<pre>";
print_r($obQrCode);
echo "</pre>";
exit;
*/


?>


<img src="data:image/png;base64, <?=base64_encode($image)?>">
<br>
<strong>
	<?=$payloadQrCode?>
</strong>

