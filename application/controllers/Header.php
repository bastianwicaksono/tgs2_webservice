<?php 

	function get_web_page($url){
		$options=[
			CURL_CUSTOMREQUEST=>'GET',
			CURLOPT_RETURNTRANSFER=>1,
			CURLOPT_HTTPHEADER=>[
				'Access-Control-Allow-Methods: GET',
				'Access-Control-Allow-Methods: *',
				'Content-Type: application/json',
				'Accept: application/json'
			]
		];
		$ch =curl_init($url);
		curl_setopt_array($ch, $options);
		$content =  curl_exec($ch);
		curl_close($ch);
		return $content;
	}
	$url ='localhost/api/customers';
	$data1 = get_web_page($url);
	var_dump($data1);
?>