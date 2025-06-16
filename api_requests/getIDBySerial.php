<?php
require '../vendor/autoload.php';

use GuzzleHttp\Client;

//get api key, snipe_url, and google_customer_id
$api_key = file_get_contents("../api_key.txt");
$api_key = str_replace(array("\r", "\n"), '', $api_key);
$snipe_url = file_get_contents("../snipe_url.txt");
$snipe_url = str_replace(array("\r", "\n"), '', $snipe_url);
$google_customer_id = file_get_contents("../google_customer_id.txt");
$google_customer_id = str_replace(array("\r", "\n"), '', $google_customer_id);

//assign variables gotten from request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	$source = $_GET['source']; //which php file sent us here, used to decide which api call to make
	$serial = $_GET['serial']; //serial number of asset
	$callGoogle = $_GET['GAdmin'];
}

//first api call, a get request copied from Google's documentation
//Gets Google ID
//optional, only if user checks the box to enable this call
if ($callGoogle == "yes") {
	try {
		$client = new \GuzzleHttp\Client();

		//utilizes 
		$response = $client->request('GET', 'https://admin.googleapis.com/admin/directory/v1/customer/'.$google_customer_id.'/devices/chromeos?query=serialNumber:'.$serial.'&projection=BASIC', []);
		echo $response->getBody();
	} catch (\GuzzleHttp\Exception\RequestException $e) {
		echo 'API Request Error: ' . $e->getMessage();
	} catch (\Exception $e) {
		echo 'General Error: ' . $e->getMessage();
	}
}

//Snipe api call, a get request copied from SnipeIT's documentation
//Gets Snipe ID
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
			//route back to where request came from sending error code and og serial, further logic handled in handleMissingAsset.php 
			//header("Location: ../sites/" . $source . ".php?SnipeRequestStatus=-1&serial=". $serial);
			exit;

		} else { //if asset does exist in inventory
			//routes to the php file 
			$id = $assetJsonArray["rows"][0]["id"];
			$modelID = $assetJsonArray["rows"][0]["model"]["id"];
			//header("Location: " . $source . "API.php?id=" . $id . "&modelID=" . $modelID . "&serial=" . $serial);
			exit;
		}
	}
//catch any internal/api/server errors
} catch (\GuzzleHttp\Exception\RequestException $e) {
	echo 'API Request Error: ' . $e->getMessage();
} catch (\Exception $e) {
	echo 'General Error: ' . $e->getMessage();
}
?>