<?php


	add_action( 'woocommerce_after_checkout_billing_form', 'pre_checkout' );


	function pre_checkout(){
		add_assets();
		?>
			<div class="isa_hidden" id="zip_code_field">
				<label for="send_two_hour" class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
					<i class="fa" aria-hidden="true"></i>
					<span class="isa_hidden">
						¿Desea envío en 2 horas? 
						<input type="checkbox" name="vehicle" value="Bike" id="send_two_hour" class="zip_code_menu">
					</span>
				</label>
			</div>
		<?php
	}


	add_action( 'woocommerce_thankyou', 'custom_email_notification', 10, 1 );
	function custom_email_notification( $order_id ) {

		if ( ! $order_id ) return;
		$order = wc_get_order( $order_id );
		PaackApi::send_order(get_billing_info($order), 0);

	}
	function get_delivery_windows($meta_data){
		$windows = array();
		$utc_date = getUTCDate();
		if($meta_data->value == 'now'){
			$windows["start_time"] = formatDate($utc_date);
			$windows["end_time"] = formatDate(addHour($utc_date,1));
		}
		else{
			$date = $utc_date->setTime(getHours($meta_data->value),0);
			$windows["start_time"] = formatDate($date);
			$windows["end_time"] = formatDate(addHour($date,1));
		}
		return $windows;
	}

	function get_billing_info($order){

		$date = new DateTime();
		$date_format= $date->format("Y-m-d H:i:s");
		$date_format = str_replace(' ','T',$date_format).".000Z";
		
		
		$res= array(
			"store_id"=>get_option('store_id'),
			"name"=>$order->get_shipping_first_name() . " " . $order->get_shipping_last_name(),
			"email"=>$order->get_billing_email(),
			"phone"=>$order->get_billing_phone(),
			"description"=>$product_data["description"],
			//"company_store_id"=> "",
			"retailer_order_number"=> $order->get_order_number(),
			"sale_number"=> "",
			"delivery_address"=>array(
					"address"=>$order->get_shipping_address_1(),
					"city"=>$order->get_shipping_city(),
					"postal_code"=>$order->get_shipping_postcode(),
					"country"=>$order->get_shipping_country(),
				)
			);
			$packages=array();
			$meta_data = null;
			foreach ( $order->get_items() as $item_id => $order_item ) {

				$order_item_data = $order_item->get_data();
				$meta_data = $order_item_data["meta_data"];
					$product = $order_item->get_product();

					// Accessing to the WC_Product object protected data
					$product_data = $product->get_data();

					if(isset($order_item_data["meta_data"])&&count($order_item_data["meta_data"])>0){
						$meta_data = get_meta_data($order_item_data["meta_data"]);
						if($meta_data->value!= ''){
							array_push($packages, get_packages($product_data,$order_item_data));
						}
					}
			}	
			$res["packages"]=$packages;
			if($meta_data!=null){
				$res['delivery_window'] = get_delivery_windows($meta_data);
			}
			return $res;
	}
		function get_packages($product_data,$order_item_data){
			return array(
				"width"=>$product_data["width"],
				"weight"=>$product_data["weight"],
				"height"=>$product_data["height"],
				"length"=>$product_data["length"],
				"description"=>$product_data["description"],
				"units"=>$order_item_data["quantity"]
			);
		}
		function get_meta_data($meta_data){
			$meta_data_res=array();
		foreach($meta_data as $meta){
			if($meta->key=='Envio a 2 horas'){
				$meta_data_res = $meta;
			}
		}
		return $meta_data_res;
	}

	/**
	 * Add engraving text to order.
	 *
	 * @param WC_Order_Item_Product $item
	 * @param string                $cart_item_key
	 * @param array                 $values
	 * @param WC_Order              $order
	 */
	function add_data_order_thank( $item, $cart_item_key, $values, $order ) {
		if ( empty( $values['paack_two_hour'] ) ) {
			return;
		}

		$item->add_meta_data( __( 'Envio a 2 horas'), getHourSelected($values['paack_two_hour']) );
	}

	add_action( 'woocommerce_checkout_create_order_line_item', 'add_data_order_thank', 10, 4 );
?>
