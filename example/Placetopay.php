<?php

	require __DIR__."/../vendor/autoload.php";

	use Gateways\GatewayFactory;
	use Gateways\Gateways\Placetopay\PlacetopayAdapter;
	
	$parameters = [
		"login"=>"80c024908a9c7fb90373115179eb8247",
		"secret_key"=>"NaW3O22Ue60i38z6",
		"currency"=>"CRC",
		"enviroment"=>"development"
	];
	$Placetopay = GatewayFactory::create(PlaceToPayAdapter::class,$parameters);


	$referenceCallback = $_GET["reference"];
	//este proceso se debe hacer en un archivo separado, la que coloquemos en nuestro returnUrl
	//a manera de ejemplo se hace aquí
	if(!isset($referenceCallback) && empty($referenceCallback)) {
		$reference = uniqid();
		$params = [
			"name" => "Leonardo",
			"documentType" => "CRCPF",
			"document" => "123456789",
			"surname" => "Leonardo",
			"email" => "softvenca@gmail.com",
			"mobile" => "+50712345678",
			"reference" => $reference,
			"description" => "Test de Pago",
			"subtotal" => "100",
			"iva_base" => "13",
			"iva" => "13",
			"total" => "13",
			"returnUrl" => "https://localhost/PaymentGateways/example/Placetopay.php?reference=$reference",
			"ipAddress" => "186.91.219.232",
			"userAgent" => $_SERVER["HTTP_USER_AGENT"]
		];

		$res = $Placetopay->createLink($params);
		
		if($res->status->status == "OK") {
			$link = $res->processUrl;

			//guardamos esta requestId para luego consultar este pago
			unset($_COOKIE['requestId']);
			setcookie('requestId', $res->requestId, time()+(60*30), "/"); 

			//el requestId debe guardarse junto con la referencia en una base de datos para luego consultar por la referencia y obtengamos el requestId para consultar los datos del pago

			echo "
				<h3>Hacer click en el link de pago para procesar la transaccion</h3>
			<a href='{$link}'>{$link}</a>";
		}else {
			echo "Error, ".$res->message;
		}
	}else {

		//esto no se debe hacer con una cookie, el proceso ideal es guardar el requestId en una base de datos y luego traer el requestId según la referencia

		$params = [
			"reference" => $_COOKIE['requestId']
		];
		$res = $Placetopay->validatePayment($params);
		if($res->status->status != "FAILED"){
			// var_dump($res);
			if($res->status->status == "APPROVED") { //comprobamos que el estatus de la transaccion es aprobado

				//si deseamos hacer una comparacion mas y saber que la referencia del callback es la que estamos buscando
				if($res->payment[0]->reference == $referenceCallback){
					echo $res->status->message;
					echo "<hr />";	
				}

			}

		}else{
			echo "Error, ".$res->status->message;
		}
	}