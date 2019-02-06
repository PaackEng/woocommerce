<?php
class PaackApi{
	const API_HOST_TEST = 'http://localhost:3000';
	const API_HOST_PROD = 'http://api.paack.co';
	const API_PATH = '/api/public/v1';
	const API_KEY = 'cf25912e9c9f167e91f18106e4ef63d63d3159c0';

	public static function get($url, $test){
		$path = self::API_HOST_TEST;
		// if($test != 1){
		// 	$path = self::API_HOST_PROD;
		// }
		$request = wp_remote_get( $path . self::API_PATH . $url . "?api=" . self::API_KEY);
        $body=  wp_remote_retrieve_body($request);
        return $body;
	}

	public static function check_store($stores_id, $test){
		$res = array("error"=>1);
		$response = json_decode(self::get('/stores/'.$stores_id, $test),true);

		if(isset($response['data'])){
			$res['error'] = 0;
			$res['data'] = $response['data'];
		}
		return $res;
	}

	public static function send_order($order_json,$test){
		$order_json["api"] = self::API_KEY;
		$url = self::API_HOST_TEST;
		// if($test ==1){
		// 	$url = self::API_HOST_PROD;
		// }
		$response = wp_remote_post(
			$url . self::API_PATH . '/orders',
			array(
				'headers'   => array('Content-Type' => 'application/json; charset=utf-8'),
				'body' => json_encode($order_json),
				'timeout' => 5,
				'method' => 'POST')
			);

		$body= wp_remote_retrieve_body($response);
		return $body;
	}

}

?>
