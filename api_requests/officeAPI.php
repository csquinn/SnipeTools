<?php
require '../vendor/autoload.php';

//get api key and snipe_url
$api_key = file_get_contents("../api_key.txt");
$api_key = str_replace(array("\r", "\n"), '', $api_key);
$snipe_url = file_get_contents("../snipe_url.txt");
$snipe_url = str_replace(array("\r", "\n"), '', $snipe_url);


use GuzzleHttp\Client;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$id = $_GET['id'];
	$modelID = $_GET['modelID'];
	$serial = $_GET['serial'];
}

try {

	$client = new \GuzzleHttp\Client();

	$response = $client->request('PUT', $snipe_url.'/api/v1/hardware/'.$id, [
		'body' =>'{"last_checkout":"null","assigned_user":0,"assigned_location":null,"assigned_asset":null,"company_id":null,"serial":"null","warranty_months":null,"purchase_cost":null,"purchase_date":"null","requestable":false,"archived":false,"rtd_location_id":15,"name":"null","location_id":null,"image":"null","asset_tag":"' . $serial .'","status_id":2,"model_id":' . $modelID . '}',
		'headers' => [
			'Authorization' => 'Bearer ' . $api_key,
			'accept' => 'application/json',
			'content-type' => 'application/json',
		],
	]);

} catch (\GuzzleHttp\Exception\RequestException $e) {
	echo 'API Request Error: ' . $e->getMessage();
} catch (\Exception $e) {
	echo 'General Error: ' . $e->getMessage();
}

?>