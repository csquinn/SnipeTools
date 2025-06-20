<?php
include 'getIDBySerial.php';

use GuzzleHttp\Client as GuzzleClient;
use Google\Client as GoogleClient;
use Google\Service\Directory;

//variable that keeps track of Google API requests. 2 is success, 3 is success and device was already deprovisioned, -2 is not found, 0 is no call made
$gSuccess = 0;

//optional Google Request to deprovision asset on Google Admin
if (isset($_GET['GAdmin']) and ($googleId != -1)) {
	try {
		//create new connection to Google API
		$gclient = new GoogleClient();
		$gclient->setAuthConfig('../user_variables/google-auth.json');
		$gclient->addScope('https://www.googleapis.com/auth/admin.directory.device.chromeos');

		//impersonate an admin account(?) for proper permissions
		$gclient->setSubject($google_admin_email);

		//create directory object from client
		$service = new Directory($gclient);

		$requestBody = new Google_Service_Directory_ChromeOsDeviceAction();
		$requestBody->setAction('deprovision');
		$requestBody->setDeprovisionReason('retiring_device');

		//make api call with the directory object to deprovision object
		$service->chromeosdevices->action($google_customer_id, $googleId, $requestBody); 	
	
		//powerwash chromebook
		$command = new Google_Service_Directory_DirectoryChromeosdevicesIssueCommandRequest();
		$command->setCommandType('REMOTE_POWERWASH');
		$command->setPayload('');
		$response = $service->customer_devices_chromeos->issueCommand($google_customer_id, $googleId, $command);

		//assume success on the call, failure is indicated in catch statements
		$gSuccess = 2;
		
	} catch (Google_Service_Exception $e) {
		$error = $e->getErrors()[0];

		//if the error was caused because the device is already deprovisioned. This is okay, as all that matters is the device was deprovisioned in some way
		if (isset($error['domain']) && $error['domain'] === 'global' &&isset($error['reason']) && $error['reason'] === 'conditionNotMet' && isset($error['message']) && $error['message'] === 'Illegal device state transition.') {
			$gSuccess = 3;
		} else {
			echo 'API Request Error: ' . $e->getMessage();
			$gSuccess = -2;
		}
	} catch (Google_Exception $e) {
		echo 'General Error: ' . $e->getMessage();
		$gSuccess = -2;
	}
//if Google Device ID wasn't found on getIDBySerial for whatever reason
} else if(isset($_GET['GAdmin'])) {
	$gSuccess = -2;
}


//Two requests are sent by deprovisionAPI.php to SnipeIT. A post request checks the asset in from any users, and a put request updates everything else as deprovisioned

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

//catch internal/api/server errors
} catch (\GuzzleHttp\Exception\RequestException $e) {
	echo 'API Request Error: ' . $e->getMessage();
} catch (\Exception $e) {
	echo 'General Error: ' . $e->getMessage();
}


//Put request, Everything besides checkin
try {

	$client = new GuzzleClient();

	//api request copied from snipeIT
	//important note: I did not have to list every single asset field in this request, just the ones I wanted to update. Anything not mentioned is not touched
	//rtd_location_id 16 = Storage, status_id 6 = Deprovisioned
	$response = $client->request('PUT', $snipe_url.'/api/v1/hardware/'.$id, [
		'body' =>'{"rtd_location_id":16,"asset_tag":"' . $serial .'","status_id":6,"model_id":' . $modelID . '}',
		'headers' => [
			'Authorization' => 'Bearer ' . $api_key,
			'accept' => 'application/json',
			'content-type' => 'application/json',
		],
	]);

	//redirect back to deprovision.php with a request statuses so that handleAssetMessages.php can display right info
	header("Location: ../sites/deprovision.php?SnipeRequestStatus=1". (($gSuccess == 0) ? '' : "&GoogleRequestStatus=".$gSuccess) ."&serial=". $serial);

//catch internal/api/server errors
} catch (\GuzzleHttp\Exception\RequestException $e) {
	echo 'API Request Error: ' . $e->getMessage();
} catch (\Exception $e) {
	echo 'General Error: ' . $e->getMessage();
}


?>