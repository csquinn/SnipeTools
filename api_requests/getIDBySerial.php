<?php
require '../vendor/autoload.php';

use GuzzleHttp\Client;

//get api key and snipe_url
$api_key = file_get_contents("../api_key.txt");
$api_key = str_replace(array("\r", "\n"), '', $api_key);
$snipe_url = file_get_contents("../snipe_url.txt");
$snipe_url = str_replace(array("\r", "\n"), '', $snipe_url);

//assign variables gotten from request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	$source = $_GET['source']; //which php file sent us here, used to decide which api call to make
	$serial = $_GET['serial']; //serial number of asset
}

//get request api call
try {
	$client = new \GuzzleHttp\Client();

	//utilizes $api_key and $snipe_url
	$response = $client->request('GET', $snipe_url.'/api/v1/hardware/byserial/'.$serial.'?	deleted=false', [
		'headers' => [
			'Authorization' => 'Bearer '.$api_key,
			'accept' => 'application/json',
		],
	]);

	//if asset is found or doesn't exist, basically if there's no internal/api/server errors
	if ($response->getStatusCode() == 200) {
		//convert json response into array
		$assetJsonArray = json_decode($response->getBody(), true);

		//if asset doesn't exist in inventory
		if(array_key_exists('status',$assetJsonArray)) {
			echo "Asset not found";
			//in SnipeTools, an id of -2 means the asset was not found in inventory
			$id = -2;
		} else {
			$id = $assetJsonArray["id"];
			echo $id;
		}
	}
} catch (\GuzzleHttp\Exception\RequestException $e) {
	echo 'API Request Error: ' . $e->getMessage();
} catch (\Exception $e) {
	echo 'General Error: ' . $e->getMessage();
}
?>