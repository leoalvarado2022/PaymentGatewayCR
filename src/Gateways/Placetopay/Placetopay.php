<?php
	namespace Gateways\Gateways\Placetopay;

	use GuzzleHttp\Client;
	use GuzzleHttp\Exception\RequestException;
	use GuzzleHttp\Exception\BadResponseException;
	use Throwable;

	class Placetopay{
		private $login;
		private $secret_key;
		private $currency;
		private $url;
		protected $clientHttp;

		public function __construct($url) {
			$this->url = $url;
			$this->clientHttp = new Client([
	            'base_uri' => $this->url,
	            "verify" => false,
	            'headers' => [
	                'Content-Type' => 'application/json',
	            ],
	        ]);
		}

		public function __set($param,$value){
			$this->$param = $value;	
		}

		public function pay($params) {

			$json = [
	            "auth" => $params["auth"],
	            "buyer" => [
	                "name" => $params["name"],
	                "documentType" => $params["documentType"],
	                "document" => $params["document"],
	                "surname" => $params["surname"],
	                "email" => $params["email"],
	                "mobile" => $params["mobile"],
	            ],
	            "payment" => [
	                "reference" => $params["reference"],
	                "description" => $params["description"],
	                "amount" => [
	                    "taxes" => [
	                        "kind" => "iva",
	                        "amount" => $params["iva"],
	                        "base" => $params["iva_base"]
	                    ],
	                    "details"=> [
	                        "kind" => "subtotal",
	                        "amount" => $params["subtotal"],
	                    ],
	                    "currency" => $this->currency,
	                    "total" => $params["total"],
	                ]
	            ],
	            "skipResult" => false, //al colocarlo true no genera voucher
	            "expiration" => date("c",strtotime("+25 minutes")), //tiempo para realizar el pago
	            "returnUrl" => $params["returnUrl"],
	            "ipAddress" => $params["ipAddress"],
	            "userAgent" => $params["userAgent"]
	        ];

	        try {
	            $response = $this->clientHttp->post('/api/session', [
	                'body' => json_encode($json)
	            ]);
	        } catch (BadResponseException $exception) {
	            return json_decode($exception->getResponse()->getBody()->getContents());
	            
	        } catch (Throwable $exception) {
	            
	            throw new Exception("Error, Gateways\Gateways\PlaceToPay\Placetopay->pay".$exception);
	        }
	        return json_decode($response->getBody()->getContents());
		}

		public function getPayData($params) {
			$json = [
				"auth" => $params["auth"]
			];
			
			try {
	            $response = $this->clientHttp->post('/api/session/'.$params["reference"], [
	                'body' => json_encode($json)
	            ]);
	        } catch (BadResponseException $exception) {
	            return json_decode($exception->getResponse()->getBody()->getContents());
	        } catch (Throwable $exception) {
	            throw new Exception("Error, Gateways\Gateways\PlaceToPay\Placetopay->pay".$exception);
	        }
	        return json_decode($response->getBody()->getContents());
		}

		public function recurrentPay() {

		}

		public function makeAuth() {
			$login = $this->login;
	        $secretKey = $this->secret_key;
	        $seed = date('c');
	        if (function_exists('random_bytes')) {
	            $nonce = bin2hex(random_bytes(16));
	        } elseif (function_exists('openssl_random_pseudo_bytes')) {
	            $nonce = bin2hex(openssl_random_pseudo_bytes(16));
	        } else {
	            $nonce = mt_rand();
	        }

	        $nonceBase64 = base64_encode($nonce);
	        $tranKeyRequest = base64_encode(sha1($nonce . $seed . $secretKey, true));
	        
	        return [
	            "login" => $login,
	            "tranKey" => $tranKeyRequest,
	            "nonce" => $nonceBase64,
	            "seed" => $seed,
	        ];
		}

		public function tokenize($params) {

		}

		public function payWithToken($params){

		}
	}