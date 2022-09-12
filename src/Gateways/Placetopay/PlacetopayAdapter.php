<?php
	namespace Gateways\Gateways\Placetopay;

	use Gateways\GatewayInterface;

	class PlacetopayAdapter implements GatewayInterface{
		protected $api;
		public function __construct($parameters){

			extract($parameters);
			$url = ($enviroment == "production") ? 
				"https://checkout.placetopay.com" : 
					"https://checkout-test.placetopay.com";

			if(!isset($parameters["login"])) {
				throw new \Exception("Error, falta el parametro login");
			}

			if(!isset($parameters["secret_key"])) {
				throw new \Exception("Error, falta el parametro secret_key");
			}

			if(!isset($parameters["currency"])) {
				throw new \Exception("Error, falta el parametro currency");
			}

			if(!isset($parameters["enviroment"])) {
				throw new \Exception("Error, falta el parametro enviroment");
			}

			$this->api = new Placetopay($url);
			$this->api->login = $login;
			$this->api->secret_key = $secret_key;
			$this->api->currency = $currency;
		}

		public function pay($_params){
			
		}
		public function createLink($_params) {
			$_params["auth"] = $this->api->makeAuth();
			return $this->api->pay($_params);
		}
		public function reversePayment($_params){
			$_params["auth"] = $this->api->makeAuth();
		}
		public function validatePayment($_params){
			$_params["auth"] = $this->api->makeAuth();
			return $this->api->getPayData($_params);
		}
	}