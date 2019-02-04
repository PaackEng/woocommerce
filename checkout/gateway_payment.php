<?php


function gateway_payment_paack() {
	if (!class_exists('WC_Payment_Gateway')) {
		return;
    }
    class WC_Gateway_Paack extends WC_Payment_Gateway {
        public function __construct() {
			
		}

		public function process_payment( $order_id ) {
			global $woocommerce;
 
			// we need it to get any order detailes
			$order = wc_get_order( $order_id );
 
	 		$json_string = json_encode($order);
	 		$file = 'order.json';
			file_put_contents($file, $json_string);
		}

    }




	function add_paack_gateway( $methods ) {
		//if (current_user_can('administrator') || WP_DEBUG) {
			$methods[] = 'WC_Gateway_Paack';
		//}
		
		return $methods;
	}
	
	add_filter('woocommerce_payment_gateways', 'add_paack_gateway' );
}
add_filter('plugins_loaded', 'gateway_payment_paack' );

?>