<?php
	namespace Gateways;

	interface GatewayInterface{
		public function pay($params);
		public function createLink($params);
		public function reversePayment($params);
		public function validatePayment($params);
		public function tokenize($params);
		public function payWithToken($params);
	}