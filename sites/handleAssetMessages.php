<?php
//Creates messages based on the status of api requests
//creates a success message when the specified goal is completed (SnipeRequestStatus=1)
//creates an error message to display when an asset can't be found (SnipeRequestStatus=-1)
//should be included in the office.php, deprovision.php, and validate.php to reduce redundancy


//get snipe_url
$snipe_url = file_get_contents("../snipe_url.txt");
$snipe_url = str_replace(array("\r", "\n"), '', $snipe_url);

//hopefully remove errors
$assetMessage='';
$assetLink='';

if ($_SERVER['REQUEST_METHOD'] === 'GET' and isset($_GET['SnipeRequestStatus'])) {
	//RequestStatus is sent after each cycle of api calls. If it's -1, then the asset wasn't found (this status value is set in getIDBySerial.php)
	if($_GET['SnipeRequestStatus'] == -1) {
		//assetMessage is set to a failure message. If an asset is found, then assetMessage is set to a success message in office.php, deprovision.php, or validate.php
		$assetMessage = "This asset couldn't be found :(";

		//create a link to search inventory for the missing asset
		//serial should always be set by getIDBySerial.php, but this is in an if statement in case it's not
		if(isset($_GET['serial'])) {
			$assetLink = '<a href="' . $snipe_url . '/hardware?page=1&size=20&search=' . $_GET['serial'] . '">Try searching for the asset on inventory</a>';
		}
	} else if ($_GET['SnipeRequestStatus'] == 1) {
		$assetMessage = "Successfully Updated Asset " . $_GET['serial'];
		if(isset($_GET['serial'])) {
			$assetLink = '<a href="' . $snipe_url . '/hardware?page=1&size=20&search=' . $_GET['serial'] . '">Check this action on inventory</a>';
		}

	}
}
