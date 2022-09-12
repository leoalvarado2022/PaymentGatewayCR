<?php
	namespace Gateways;

	class GatewayFactory{

		public static function create(string $gateway,array $parameters = []){
			return new $gateway($parameters);
		}
	}