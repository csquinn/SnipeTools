<?php
require '../vendor/autoload.php';

//get api key and snipe_url
$api_key = file_get_contents("../user_variables/api_key.txt");
$api_key = str_replace(array("\r", "\n"), '', $api_key);
$snipe_url = file_get_contents("../user_variables/snipe_url.txt");
$snipe_url = str_replace(array("\r", "\n"), '', $snipe_url);


use GuzzleHttp\Client;


//assign variables from request
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$id = $_GET['id'];
	$modelID = $_GET['modelID'];
	$serial = $_GET['serial'];
}

//Two requests are sent by officeAPI.php. A put request updates everything besides being checked in or checked out, and a post request checks the asset out

//Put request, Everything besides checkin
try {

	$client = new \GuzzleHttp\Client();

	//api request copied from snipeIT
	//important note: I did not have to list every single asset field in this request, just the ones I wanted to update. Anything not mentioned is not touched
	//rtd_location_id 15 = Office, status_id 2 = Ready to Deploy
	$response = $client->request('PUT', $snipe_url.'/api/v1/hardware/'.$id, [
		'body' =>'{"rtd_location_id":15,"asset_tag":"' . $serial .'","status_id":2,"model_id":' . $modelID . '}',
		'headers' => [
			'Authorization' => 'Bearer ' . $api_key,
			'accept' => 'application/json',
			'content-type' => 'application/json',
		],
	]);

//catch internal/api/server errors
} catch (\GuzzleHttp\Exception\RequestException $e) {
	echo 'API Request Error: ' . $e->getMessage();
} catch (\Exception $e) {
	echo 'General Error: ' . $e->getMessage();
}

//checkin
try {

	$client = new \GuzzleHttp\Client();

	//api request copied from snipeIT
	$response = $client->request('POST', $snipe_url.'/api/v1/hardware/'.$id.'/checkin', [
		'body' =>'{"status_id":2}',
		'headers' => [
			'Authorization' => 'Bearer ' . $api_key,
			'accept' => 'application/json',
			'content-type' => 'application/json',
		],
	]);
	
	//redirect back to office.php with a confirmation code so assetMessage and assetLink can be set
	header("Location: ../sites/office.php?SnipeRequestStatus=1&serial=". $serial);

//catch internal/api/server errors
} catch (\GuzzleHttp\Exception\RequestException $e) {
	echo 'API Request Error: ' . $e->getMessage();
} catch (\Exception $e) {
	echo 'General Error: ' . $e->getMessage();
}

?>