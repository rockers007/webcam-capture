<?php
$url = 'https://api.shuftipro.com/';
$client_id  = '8723dead44cc9a0001c0c1e2d6047725be6b4a4cc1c08e71c4ef50d2a391144e';
$secret_key = '1IGaMdCaHzbYtGKnlsJ3bwglVLaNObO1';

if($_GET['back']){
	//ref-41765
	
		$url = 'https://api.shuftipro.com/status';
		$status_request = [
		  "reference" => $_GET['back'],
		];

		$auth = $client_id.":".$secret_key; // remove this in case of Access Token
		$headers = ['Content-Type: application/json'];
		// if using Access Token then add it into headers as mentioned below otherwise remove access token
		// array_push($headers, 'Authorization : Bearer ' . $access_token);
		$post_data = json_encode($status_request);

		//Calling Shufti Pro request API using curl
		$response = send_curl($url, $post_data, $headers, $auth); // remove $auth in case of Access Token
		echo "<pre>";
			print_r($response);
		//Get Shufti Pro API Response
		$response_data    = $response['body'];
		//Get Shufti Pro Signature
		$exploded = explode("\n", $response['headers']);
		// Get Signature Key from Hearders
		$sp_signature = null;
		foreach ($exploded as $key => $value) {
		  if (strpos($value, 'signature: ') !== false || strpos($value, 'Signature: ') !== false) {
		    $sp_signature=trim(explode(':', $exploded[$key])[1]);
		    break;
		  }
		}
		//Calculating signature for verification
		// calculated signature functionality cannot be implement in case of access token
		$calculate_signature  = hash('sha256',$response_data.$secret_key);

		if($sp_signature == $calculate_signature){

		  echo "Response : $response_data";
		}else{
		  echo "Invalid signature :  $response_data";
		}
	die;
}

$verification_request = [
    "reference"         => "ref-".rand(4,444).rand(4,444),
    "callback_url"      => "https://rockerstech.com/kyc.php?back=yes",
    "redirect_url"      => "https://rockerstech.com/kyc.php?back=yes",
    "email"             => "rockersinfo@gmail.com",
    "country"           => "IN",
    "language"          => "EN",
    "verification_mode" => "any",
];
$verification_request['face'] = [
    "proof" => ""
];
$verification_request['document'] =[
    'proof' => '', 
    'additional_proof' => '',
    'name' => '',
    'dob'             => '',
    'age'             => '',
    'document_number' => '',
    'expiry_date'     => '',
    'issue_date'      => '',
    'allow_offline'      => '1', 
    'allow_online'     => '1',
    'supported_types' => ['id_card','passport'],
    "gender"          =>  ""
];
$auth = $client_id.":".$secret_key;
$headers = ['Content-Type: application/json'];
$post_data = json_encode($verification_request);
$response = send_curl($url, $post_data, $headers, $auth);
echo "<pre>";
			print_r($response);
function send_curl($url, $post_data, $headers, $auth){
    $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERPWD, $auth);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $html_response = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = substr($html_response, 0, $header_size);
        $body = substr($html_response, $header_size);
        curl_close($ch);
        return json_decode($body,true);
}
echo $response['verification_url'];