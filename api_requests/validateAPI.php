<?php
include 'getIDBySerial.php';

use GuzzleHttp\Client as GuzzleClient;
use Google\Client as GoogleClient;
use Google\Service\Directory;

//variable that keeps track of Google API requests. 1 is success, -1 is found but deprovisioned, -2 is not found, 0 is no call made
$gSuccess = 0;

//optional Google Request to ensure that a device exists in google admin
if (isset($_GET['GAdmin'])) {
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
	
		//assume success on the call, then check it
		$gSuccess = 1;


		//if search is too ambiguous and returns multiple or if it returns none
		if(count($results->getChromeosdevices()) != 1) {
			$gSuccess = -2;
		}

		//if cb is found but is deprovisioned
		 else if($results->getChromeosdevices()[0]->getStatus() != "ACTIVE") {
			$gSuccess = -1;
		}
		
	} catch (Google_Service_Exception $e) {
		echo 'API Request Error: ' . $e->getMessage();
		$gSuccess = -2;
	} catch (Google_Exception $e) {
		echo 'General Error: ' . $e->getMessage();
		$gSuccess = -2;
	}
}

//assign all of the variables that were set by user
if (isset($_GET['status'])){
	$status=$_GET['status'];
}
if (isset($_GET['location'])){
	$location=$_GET['location'];
}
if (isset($_GET['remName'])){
	$remName=$_GET['remName'];
}
if (isset($_GET['retag'])){
	$retag=$_GET['retag'];
}
if (isset($_GET['checkin'])){
	$checkin=$_GET['checkin'];
}
if (isset($_GET['newTag'])){
	$newTag = $_GET['newTag'];
}

//Two requests are sent by validateAPI.php to SnipeIT. A put request updates everything besides being checked in or checked out, and a post request checks the asset out

//Put request, Everything besides checkin
try {

	$client = new GuzzleClient();

	//api request copied from snipeIT
	//important note: I did not have to list every single asset field in this request, just the ones I wanted to update. Anything not mentioned is not touched
	$response = $client->request('PUT', $snipe_url.'/api/v1/hardware/'.$id, [
		'body' =>'{'
		.((isset($newTag) and $newTag != '')?('"asset_tag": "'.$newTag.'"'):((isset($retag) and $retag=="on")?('"asset_tag": "'.$serial.'"'):('"asset_tag": "'.$assetTag.'"')))	//asset_tag
		.((isset($status) and $status != "lai")?(', "status_id": '.$status):(', "status_id": '.$currentStatus))	//status_id
		.', "model_id": '.$modelID	//model_id
		.((isset($location) and $location != "lai")?(', "rtd_location_id": '.$location):(''))	//rtd_location_id
		.((isset($remName) and $remName=="on")?(', "name": null'):(''))	//name
		.'}',
		'headers' => [
			'Authorization' => 'Bearer ' . $api_key,
			'accept' => 'application/json',
			'content-type' => 'application/json',
		],
	]);

	//set assetTag to new assetTag
	$tagResponse = $client -> request('PUT', $snipe_url.'/api/v1/hardware/'.$id, [
		'body' =>'{'
		.((isset($newTag) and $newTag != '')?('"asset_tag": "'.$newTag.'"'):('"asset_tag": "'.$assetTag.'"'))	//asset_tag
		.'}',
		'headers' => [
			'Authorization' => 'Bearer ' . $api_key,
			'accept' => 'application/json',
			'content-type' => 'application/json',
		],
	]);

	//you'll use $response->getBody(), then need to explore it somehow
	//if asset tag is already in use, then do not replace asset tag
	if ($tagResponse -> getStatusCode() == 200){
		//convert JSON response to an array
		$tagJsonArray = json_decode($tagResponse->getBody(), true);
	
		//if problem exists
		if(array_key_exists('status', $tagResponse)){
			//find out how to execute the asset message in here
			if (isset($tagJsonArray['status']) && $tagJsonArray['status'] === 'error'){
				
				header("Location: ../sites/" . $source . ".php?SnipeRequestStatus=-3&serial=". $serial .
				(isset($_GET['newTag']) ? ("&newTag=".$_GET['newTag']) : ""). 
				(isset($_GET['GAdmin']) ? ("&GoogleRequestStatus") : "").
				((isset($_GET['status']))?("&status=".$_GET['status']):("")).
				((isset($_GET['location']))?("&location=".$_GET['location']):("")).
				((isset($_GET['remName']))?("&remName=on"):("")).
				((isset($_GET['retag']))?("&retag=on"):("")).
				((isset($_GET['checkin']))?("&checkin=on"):(""))
				);

			}
			exit;
		}
	}
//catch internal/api/server errors
} catch (\GuzzleHttp\Exception\RequestException $e) {
	echo 'API Request Error: ' . $e->getMessage();
} catch (\Exception $e) {
	echo 'General Error: ' . $e->getMessage();
}


//checkin
if(isset($checkin) and $checkin=="on"){
	try {

		$client = new \GuzzleHttp\Client();
	
		//api request copied from snipeIT
		$response = $client->request('POST', $snipe_url.'/api/v1/hardware/'.$id.'/checkin', [
			'body' =>'{"status_id":'. ((isset($status))?($status):($currentStatus)) .'}',
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
}
//redirect back to validate.php with a request statuses so that handleAssetMessages.php can display right info
header("Location: ../sites/validate.php?SnipeRequestStatus=1". (($gSuccess == 0) ? '' : "&GoogleRequestStatus=".$gSuccess) ."&serial=". $serial.
	((isset($status))?("&status=".$status):("")).
	((isset($location))?("&location=".$location):("")).
	((isset($remName))?("&remName=on"):("")).
	((isset($retag))?("&retag=on"):("")).
	((isset($checkin))?("&checkin=on"):(""))
	);
?>