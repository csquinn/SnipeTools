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
		'body' =>'{"rtd_location_id":15,"asset_tag":"' . $serial .'","status_id":2,"model_id":' . $modelID . '}',
		'headers' => [
			'Authorization' => 'Bearer ' . $api_key,
			'accept' => 'application/json',
			'content-type' => 'application/json',
		],
	]);
	echo $response->getBody();
} catch (\GuzzleHttp\Exception\RequestException $e) {
	echo 'API Request Error: ' . $e->getMessage();
} catch (\Exception $e) {
	echo 'General Error: ' . $e->getMessage();
}

echo "trying checkin now";

try {

	$client = new \GuzzleHttp\Client();

	$response = $client->request('POST', $snipe_url.'/api/v1/hardware/'.$id.'/checkin', [
		'body' =>'{"status_id":2}',
		'headers' => [
			'Authorization' => 'Bearer ' . $api_key,
			'accept' => 'application/json',
			'content-type' => 'application/json',
		],
	]);
	echo $response->getBody();
} catch (\GuzzleHttp\Exception\RequestException $e) {
	echo 'API Request Error: ' . $e->getMessage();
} catch (\Exception $e) {
	echo 'General Error: ' . $e->getMessage();
}

?>