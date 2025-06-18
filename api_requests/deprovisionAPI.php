<?php
require '../vendor/autoload.php';

use GuzzleHttp\Client as GuzzleClient;
use Google\Client as GoogleClient;
use Google\Service\Directory;

//get api key and snipe_url
$api_key = file_get_contents("../user_variables/api_key.txt");
$api_key = str_replace(array("\r", "\n"), '', $api_key);
$snipe_url = file_get_contents("../user_variables/snipe_url.txt");
$snipe_url = str_replace(array("\r", "\n"), '', $snipe_url);
$google_admin_email = file_get_contents("../user_variables/google_admin_email.txt");
$google_admin_email = str_replace(array("\r", "\n"), '', $google_admin_email);
$google_customer_id = file_get_contents("../user_variables/google_customer_id.txt");
$google_customer_id = str_replace(array("\r", "\n"), '', $google_customer_id);


//assign variables from request
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$id = $_GET['id'];
	$modelID = $_GET['modelID'];
	$serial = $_GET['serial'];
}

//variable that keeps track of Google API requests. 2 is success, 3 is success and device was already deprovisioned, -2 is not found, 0 is no call made
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

		$requestBody = new Google_Service_Directory_ChromeOsDeviceAction();
		$requestBody->setAction('deprovision');
		$requestBody->setDeprovisionReason('retiring_device');

		//make api call with the directory object to deprovision object
		$service->chromeosdevices->action($google_customer_id, $_GET['googleId'], $requestBody); 	
	
		//powerwash chromebook
		$requestBody = new Google_Service_Directory_ChromeOsDeviceAction();
		$requestBody->setCommandType('REMOTE_POWERWASH');
		$service->chromeosdevices->issueCommand($google_customer_id, $_GET['googleId'], $requestBody);

		//assume success on the call, failure is indicated in catch statements
		$gSuccess = 2;
		
	} catch (Google_Service_Exception $e) {
		$error = $e->getErrors()[0];
		//if the error was caused because the device is already deprovisioned. This is okay, as all that matters is the device was deprovisioned in some way
		if (isset($error['domain']) && $error['domain'] === 'global' &&isset($error['reason']) && $error['reason'] === 'conditionNotMet' && isset($error['message']) && $error['message'] === 'Illegal device state transition.') {

			//powerwash chromebook
			$requestBody = new Google_Service_Directory_ChromeOsDeviceAction();
			$requestBody->setCommandType('REMOTE_POWERWASH');
			$service->chromeosdevices->issueCommand($google_customer_id, $_GET['googleId'], $requestBody);

			$gSuccess = 3;
		} else {
			echo 'API Request Error: ' . $e->getMessage();
			$gSuccess = -2;
		}
	} catch (Google_Exception $e) {
		echo 'General Error: ' . $e->getMessage();
		$gSuccess = -2;
	}
}


//Two requests are sent by officeAPI.php to SnipeIT. A put request updates everything besides being checked in or checked out, and a post request checks the asset out

//Put request, Everything besides checkin
try {

	$client = new GuzzleClient();

	//api request copied from snipeIT
	//important note: I did not have to list every single asset field in this request, just the ones I wanted to update. Anything not mentioned is not touched
	//rtd_location_id 15 = Office, status_id 2 = Ready to Deploy
//	$response = $client->request('PUT', $snipe_url.'/api/v1/hardware/'.$id, [
//		'body' =>'{"rtd_location_id":15,"asset_tag":"' . $serial .'","status_id":2,"model_id":' . $modelID . //'}',
//		'headers' => [
//			'Authorization' => 'Bearer ' . $api_key,
//			'accept' => 'application/json',
//			'content-type' => 'application/json',
//		],
//	]);

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
//	$response = $client->request('POST', $snipe_url.'/api/v1/hardware/'.$id.'/checkin', [
//		'body' =>'{"status_id":2}',
//		'headers' => [
//			'Authorization' => 'Bearer ' . $api_key,
//			'accept' => 'application/json',
//			'content-type' => 'application/json',
//		],
//	]);
	


	//redirect back to office.php with a request statuses so that handleAssetMessages.php can display right info
	//header("Location: ../sites/office.php?SnipeRequestStatus=1". (($gSuccess == 0) ? '' : "&GoogleRequestStatus=".$gSuccess) ."&serial=". $serial);

//catch internal/api/server errors
} catch (\GuzzleHttp\Exception\RequestException $e) {
	echo 'API Request Error: ' . $e->getMessage();
} catch (\Exception $e) {
	echo 'General Error: ' . $e->getMessage();
}

?>