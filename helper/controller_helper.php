<?php
/**
 * Controller helper
 * Dipanggil dari file Loader.php untuk persiapan file config/controller.php
 */

if ( ! function_exists('get_request_query')) {
	/**
	 * mendapatkan data dari $_GET
	 * @param  object 	$request 	object request dari slim
	 * @param  string 	$key     	key value yang ingin dicari
	 * @return mixed          		array atau string
	 */
	function get_request_query($request, $key = '') {
		$get = $request->getQueryParams();
		if ( ! empty($key)) {
			return ( ! isset($get[$key]) ? '' : $get[$key]);
		} else return $get;
	}
}

if ( ! function_exists('get_request_body')) {
	/**
	 * mendapatkan nilai dari $_POST / $_PUT
	 * @param  object 	$request 	object request dari slim
	 * @param  string 	$key     	key value yang ingin dicari
	 * @return mixed          		array atau string
	 */
	function get_request_body($request, $key = '') {
		$postput = $request->getParsedBody();
		if ( ! empty($key)) {
			return ( ! isset($postput[$key]) ? '' : $postput[$key]);
		} else return $postput;
	}
}

if ( ! function_exists('redirect')) {
	/**
	 * redirect url dengan slim
	 * @param  object 	object respon dari slim
	 * @param  string 	$url url tujuan
	 * @return object     
	 */
	function redirect($res, $url) {
		return $res->withStatus(302)->withHeader('Location', $url);
	}
}

if ( ! function_exists('exit_app')) {
	/**
	 * sama dengan halt di slim 2
	 * @param  object 	$response 	object respon dari slim
	 * @param  string 	$status   	status keluar
	 * @param  string 	$message 		pesan keluar
	 * @return object   
	 */
	function exit_app($response, $status = 404, $message = '') {
		$ctr = \Lib\Loader::get_instance();
		if ($status == 404) {
			return $response->withStatus(404)->withHeader('Content-Type', 'text/html')->write($this->load_view('404', array('request' => $request->getUri()->getPath()), false));
		} else {
			return $response->withStatus(500)->withHeader('Content-Type', 'text/html')->write($ctr->view('500', array('message' => $message), false));
		}
	}
}

