<?php
/**
* Plugin Name: Paack Plugin
* Plugin URI:
* Description: Plugin para consultar y generar envios.
* Version: 1.0.0
* Author: Wiljac Aular
* Author URI:
* License: GPL2
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once 'admin/Util.php';
require_once 'admin/menu.php';

require_once 'checkout/checkout.php';
add_action( 'wp_ajax_is_zip_code', 'isZipCodeAjax' );
add_action( 'wp_ajax_nopriv_is_zip_code', 'isZipCodeAjax' );

function paack_field_custom_send(){
	echo "<input type='hidden' name='paack_two_hour' value ='0' id='paack-two-hour'>";
}

add_action( 'woocommerce_after_order_notes', 'paack_field_custom_send' );

function paack_bottom_filter(){
	$store_id = get_option('store_id');
	$is_store_valid = get_option('is_store_valid');
	$zip_codes = get_option('zip_codes');

	if($is_store_valid == 1 && $zip_codes != ''){
		add_assets();
		echo paack_html();
	}
}
add_action('woocommerce_checkout_before_order_review','paack_bottom_filter'); // LUL

function paack_html(){
	?>
		<div class="isa_success">
			<a id="paack_delivery_slot_link" href="#test-form" class="wp-paack-pop">
				Envio en 2 horas
			</a>
		</div>
		<div id="delivery_slot_info" class="isa_success isa_hidden text-center"></div>
    <div id="test-form" class="mfp-hide white-popup-block">
			<h2>Ingresa tu código postal.</h2>
			<hr/>
			<p><?php esc_html(get_option("text_popup"));?></p>
			<div id="consult-zip-code">
				<fieldset style="border:0;">
					<label for="name">Name</label>
					<input id="zip_code" name="zip_code" type="text" style="width:250px;" placeholder="Codigo Postal" required="">
					<button type="button" id="button-zip-code">Consultar</button>
				</fieldset>
			</div>
			<div class="isa_hidden no-padding" id="message_zip_code">
				<i class="fa fa-info-circle"></i>
				<span></span>
			</div>
			<table class="isa_hidden" id="table_options"></table>
			<button type="button" class="isa_hidden right" id="button_zip_code">
				 Agregar
			</button>
		</div>
	<?php
}

function add_assets(){
	    register_assets();
	    wp_enqueue_style('style-magnific');
	    wp_enqueue_style('style-paack');
	    wp_enqueue_script('script-magnific');
	    wp_enqueue_script('script-paack');
	    wp_localize_script ('script-paack', 'paack', array ('ajax_url' => admin_url ('admin-ajax.php')));

}

function register_assets(){
    wp_register_style('style-magnific',("/wp-content/plugins/paack-plugin/assets/css/magnific-popup.css"), false);
    wp_register_style('style-paack',("/wp-content/plugins/paack-plugin/assets/css/paack.css"), false);
    wp_register_script('script-magnific', ("/wp-content/plugins/paack-plugin/assets/js/jquery.magnific-popup.min.js"), false);
    wp_register_script('script-paack', ("/wp-content/plugins/paack-plugin/assets/js/paack-script.js"), false);
}

function isZipCodeAjax(){

	$zip_codes = get_option('zip_codes');
	$is = false;
	$message = get_option('paack_message_zip_code_error');
	if($zip_codes!=null && $zip_codes != ''){
		$zip_codes_permited = explode(',',$zip_codes);
		$zipCode = $_POST['zip_code'];
		$is =in_array($zipCode,$zip_codes_permited);
		if($is){
			$message = get_option('paack_message_zip_code_success');
		}
	}
	$res = array("availability"=> $is,"message" => $message);
	wp_send_json($res);
	wp_die();
}

function save_paack_two_hour_field( $cart_item_data, $product_id ) {
    if( isset( $_REQUEST['paack-two-hour'] ) ) {
		$cart_item_data[ 'paack_two_hour' ] = $_REQUEST['paack-two-hour'];
        /* below statement make sure every add to cart action as unique line item */
        $cart_item_data['unique_key'] = md5( microtime().rand() );
	}
    return $cart_item_data;
}

// add_action( 'woocommerce_add_cart_item_data', 'save_paack_two_hour_field', 10, 2 );


function render_meta_on_cart_and_checkout( $cart_data, $cart_item = null ) {
	add_assets();

    $custom_items = array();
    if( !empty( $cart_data ) ) {
        $custom_items = $cart_data;
    }
    if( isset( $cart_item['paack_two_hour'] ) ) {
		if($cart_item['paack_two_hour']!='0'){
			$custom_items[] = array( "name" => 'Envio a 2 horas', "value" => '&nbsp;'.getHourSelected($cart_item['paack_two_hour']) );
		}
	}
    return $custom_items;
}
// add_filter( 'woocommerce_get_item_data', 'render_meta_on_cart_and_checkout', 10, 2 );
