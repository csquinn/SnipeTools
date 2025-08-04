<html lang="en">
<?php
//Creates messages based on the status of api requests
//creates a success message when the specified goal is completed (SnipeRequestStatus=1)
//creates an error message to display when an asset can't be found (SnipeRequestStatus=-1)
//creates other messages based off of GoogleRequestStatus
//should be included in the office.php, deprovision.php, and validate.php to reduce redundancy


//get snipe_url
$snipe_url = file_get_contents("../user_variables/snipe_url.txt");
$snipe_url = str_replace(array("\r", "\n"), '', $snipe_url);

//hopefully remove errors
$assetMessage1='';
$assetMessage2='';
$assetLink='';
$audioMessage='';


//set a pretty background and text color
echo "<style> body {background-color: #337ab7; color: white;} </style>";

if ($_SERVER['REQUEST_METHOD'] === 'GET' and isset($_GET['SnipeRequestStatus'])) {
	//SnipeRequestStatus is sent after each cycle of api calls. If it's -1, then the asset wasn't found (this status value is set in getIDBySerial.php)
	if($_GET['SnipeRequestStatus'] == -1) {
		//assetMessage is set to a failure message
		$assetMessage1 = "This asset couldn't be found :(";

		//create a link to search inventory for the missing asset
		//serial should always be set by getIDBySerial.php, but this is in an if statement in case it's not
		if(isset($_GET['serial'])) {
			$assetLink = '<a href="' . $snipe_url . '/hardware?page=1&size=20&search=' . $_GET['serial'] . '" target = "_blank" rel = "noopener noreferrer">Try searching for the asset on inventory</a>';
		}

		//set a pretty background color
		echo "<style> body {background-color: red; color: black;} </style>";
	
	} else if (isset($_GET['tagAvailability']) and $_GET['tagAvailability'] == -3) {
		//assetMessage is set to a failure on tag message
		$assetMessage1 = "This asset was not adjusted due to a bad asset tag :^(";

		//create a link to search inventory for the asset tag sent by user
		$assetLink = '<a href="' . $snipe_url . '/hardware?page=1&size=20&search=' . $_GET['newTag'] . '" target = "_blank" rel = "noopener noreferrer">Try searching for the asset tag on inventory</a>';

	} else if ($_GET['SnipeRequestStatus'] == 1) { //if SnipeRequestStatus is 1, then the asset was found and all desired actions were completed (this value is set in the xxxAPI.php)

		//create link to inventory for updated asset
		if(isset($_GET['serial'])) {
			$assetLink = '<a href="' . $snipe_url . '/hardware?page=1&size=20&search=' . $_GET['serial'] . '" target = "_blank" rel = "noopener noreferrer">Check this action on inventory</a>';
			$serial = $_GET['serial'];
		} else {
			$serial = "";
		}

		//assetMessage is set to a scucess message
		$assetMessage1 = "Successfully Updated Asset " . $serial;

		//set a pretty background color
		echo "<style> body {background-color: green; color: black;} </style>";

	}

	//set sound effect
	if ($_GET['SnipeRequestStatus'] >= 1){
		$audioMessage = "<audio src='../sfx/ding.mp3' autoplay='autoplay'></audio>";
	} else if ($_GET['SnipeRequestStatus'] <= -1){
		$audioMessage = "<audio src='../sfx/buzzer.mp3' autoplay='autoplay'></audio>";
	}
	
	//GoogleRequestStatus logic, follows similar guidelines to SnipeRequestStatus
	if($_SERVER['REQUEST_METHOD'] === 'GET' and isset($_GET['GoogleRequestStatus'])) {
		//no status=0 because that means no api call was sent to Google

		//if cb was found in Google Admin and was already deprovisioned (while attempting to be deprovisioned)
		if($_GET['GoogleRequestStatus'] == 3) {
			$assetMessage2 = "Asset was Already Deprovisioned in Google Admin";
		}

		//if cb was found in Google Admin and then deprovisioned successfully
		if($_GET['GoogleRequestStatus'] == 2) {
			$assetMessage2 = "Asset was Deprovisioned in Google Admin";
		}

		//if cb was found in Google Admin and is provisioned
		if($_GET['GoogleRequestStatus'] == 1) {
			$assetMessage2 = "Asset is Provisioned in Google Admin";
		}

		//if cb was found in Google Admin but is deprovisioned
		if($_GET['GoogleRequestStatus'] == -1) {
			$assetMessage2 = "Asset is <b>DEPROVISIONED</b> in Google Admin";
		}

		//if cb can't be found because of broad search or it doesn't exist
		if($_GET['GoogleRequestStatus'] == -2) {
			$assetMessage2 = "Asset couldn't be found on Google Admin";
		}

		
		//set background color to yellow if SnipeIT worked but Google Admin did not
		if($_GET['SnipeRequestStatus'] >= 1 && $_GET['GoogleRequestStatus'] <= -1){
			echo "<style> body {background-color: yellow; color: black;} </style>";
		}
		
		//set sound effect, same logic as yellow background
		if($_GET['SnipeRequestStatus'] >= 1 && $_GET['GoogleRequestStatus'] <= -1){
			$audioMessage = "<audio src='../sfx/buzzer.mp3' autoplay='autoplay'></audio>";
		}
	}
}
?>
