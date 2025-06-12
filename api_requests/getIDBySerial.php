<?php
require '../vendor/autoload.php';

use GuzzleHttp\Client;

//get api key and snipe_url
$api_key = file_get_contents("../api_key.txt");
$snipe_url = file_get_contents("../snipe_url.txt");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	$source = $_GET['source'];
	$serial = $_GET['serial'];
}
$client = new \GuzzleHttp\Client();

$response = $client->request('GET', $snipe_url.'/api/v1/hardware/byserial/'.$serial.'?deleted=false', [
	'headers' => [
		'Authorization' => 'Bearer '.$api_key,
		'accept' => 'application/json',
	],
]);

echo $response->getBody();

?>