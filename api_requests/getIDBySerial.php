<?php
require '../vendor/autoload.php';

use GuzzleHttp\Client as GuzzleClient;
use Google\Client as GoogleClient;
use Google\Service\Directory;

//get api key, snipe_url, google_admin_email, and google_customer_id
$api_key = file_get_contents("../user_variables/api_key.txt");
$api_key = str_replace(array("\r", "\n"), '', $api_key);
$snipe_url = file_get_contents("../user_variables/snipe_url.txt");
$snipe_url = str_replace(array("\r", "\n"), '', $snipe_url);
$google_admin_email = file_get_contents("../user_variables/google_admin_email.txt");
$google_admin_email = str_replace(array("\r", "\n"), '', $google_admin_email);
$google_customer_id = file_get_contents("../user_variables/google_customer_id.txt");
$google_customer_id = str_replace(array("\r", "\n"), '', $google_customer_id);

//assign variables gotten from request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	$source = $_GET['source']; //which php file sent us here, used to decide which api call to make
	$serial = $_GET['serial']; //serial number of asset
}

//first api call, a get request copied from Google's documentation
//Gets Google ID of asset
//optional, only if user checks the box to enable this call
//not needed for office.php
if (isset($_GET['GAdmin']) and $source != "office") {
	try {
		//create new connection to Google API
		$gclient = new GoogleClient();
		$gclient->setAuthConfig('../user_variables/google-auth.json');
		$gclient->addScope('https://www.googleapis.com/auth/admin.directory.device.chromeos');

		//impersonate an admin account(?) for proper permissions
		$gclient->setSubject($google_admin_email);

		//create directory object from client
		$service = new Directory($gclient);

		//create array specifying api call parameters
		$optParams = array(
			'projection' => 'BASIC',
			'query' => 'id:' . $serial,
			'maxResults' => 2
		);

		//make api call with the directory object
		$results = $service->chromeosdevices->listChromeosdevices($google_customer_id, $optParams); 

		//assign the Google Device ID to a variable
		if(!(empty($results->getChromeosdevices()))){
			$googleId = $results->getChromeosdevices()[0]->getDeviceId();
		} else {
			$googleId = -1;
		}

	} catch (Google_Service_Exception $e) {
		echo 'API Request Error: ' . $e->getMessage();
		$googleId = -1;
	} catch (Google_Exception $e) {
		echo 'General Error: ' . $e->getMessage();
		$googleId = -1;
	}
}

//Snipe api call, a get request copied from SnipeIT's documentation
//Gets Snipe ID
try {
	$client = new GuzzleClient();

	//utilizes $api_key and $snipe_url
	$response = $client->request('GET', $snipe_url.'/api/v1/hardware/byserial/'.$serial, [
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
			//route back to where request came from sending error code and og serial, further logic handled in handleAssetMessage.php

			//write this scan to the badscans.txt file for logging purposes
			file_put_contents("../logs/badscans.txt", $serial."\n", FILE_APPEND);

			//extra header information is optionally provided if the source is validate.php to keep previously checked boxes checked 
			header("Location: ../sites/" . $source . ".php?SnipeRequestStatus=-1&serial=". $serial . 
				(isset($_GET['GAdmin']) ? ("&GoogleRequestStatus") : "").
				((isset($_GET['status']))?("&status=".$_GET['status']):("")).
				((isset($_GET['location']))?("&location=".$_GET['location']):("")).
				((isset($_GET['remName']))?("&remName=on"):("")).
				((isset($_GET['retag']))?("&retag=on"):("")).
				((isset($_GET['checkin']))?("&checkin=on"):(""))
				);

			exit;

		} else { //if asset does exist in inventory
			
			//write to proper log
			file_put_contents("../logs/".$source."LOG.txt", $serial."\n", FILE_APPEND);

			//routes to the php file 
			$id = $assetJsonArray["rows"][0]["id"];
			$modelID = $assetJsonArray["rows"][0]["model"]["id"];
			$assetTag = $assetJsonArray["rows"][0]["asset_tag"];
			$currentStatus = $assetJsonArray["rows"][0]["status_label"]["id"];
		}
	}
//catch any internal/api/server errors
} catch (\GuzzleHttp\Exception\RequestException $e) {
	echo 'API Request Error: ' . $e->getMessage();
} catch (\Exception $e) {
	echo 'General Error: ' . $e->getMessage();
}
?>