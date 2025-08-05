<!DOCTYPE html>
<html lang="en">
<?php
	include 'reportFunctions.php'
?>
<?php

//get mysql password
$db_password = file_get_contents("../user_variables/snipe_mysql_password.txt");
$db_password = str_replace(array("\r", "\n"), '', $db_password);

//get snipe_url
$snipe_url = file_get_contents("../user_variables/snipe_url.txt");
$snipe_url = str_replace(array("\r", "\n"), '', $snipe_url);

//Create mysqli connection
$mysqli = new mysqli("localhost","snipe_user",$db_password,"snipeit");

// Check connection
if ($mysqli -> connect_errno) {
  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
  exit();
}

//delete old table if it still exists
$mysqli -> query('drop table if exists tempExclusions;');

//create temporary table for asset and user exceptions
$sql = 'create table tempExclusions (name varchar(255) not null) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';
if($mysqli -> query($sql) === FALSE) {
	echo "error creating the exclusions table";
}

//prepare parameterized query
$sql = "INSERT INTO tempExclusions (name) VALUES (?)";
$prepSql = $mysqli->prepare($sql);
if (!$prepSql) {
	die("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
}

$prepSql->bind_param('s', $line);

//insert values from txt file into tempExclusions
$filename = '../exclusions.txt';
$handle = fopen($filename, 'r');
if (!$handle) {
	die("Cannot open file: $filename");
}

// Read file line by line
while (($line = fgets($handle)) !== false) {
	$line = trim($line);      // Remove line breaks and spaces
	if(!($line == "null" or $line == " " or $line == "" or $line == null)){
		$prepSql->execute();
		if ($prepSql->errno) {
			error_log("Insert error on line: $line — " . $prepSql->error);
		}
	}
}
?>

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Inventory Health Report | SnipeTools</title>
	<link rel = "stylesheet" href = "../styles/reportStyle.css">
</head>
<body>
	<div id = "info">
		<h1>Inventory Health Report</h1>
		<h4>This site queries SnipeIT and returns many common asset errors.</h4>
		<h4>Each section below can be expanded by clicking the dropdown arrow.</h4>
		<h4>This report is <b>NOT</b> exhaustive and inventory should be examined regularly in addition to this report</h4>
		<h5><b>*Please note: This report does not actually modify SnipeIT in any way, it just queries it</b></h5>
		<h5>Click the link below to specify exclusions from this report</h5>
		<h5>(Exclusions will not appear in results even if they are errors)</h5>
		<button data-url="modifyExclusions.php">Create Exclusions from Report</button>
		<br>
		<br>
		<button data-url ="../index.php">Return Home</button>
		<br>
		<br>
		<script src = "../scripts/buttons.js"></script>
	
		<?php
			//Assets without a serial number or a highly shortened serial
			$sql = 'select * from assets where (serial = "" or serial is null or serial = " " or length(serial) < 7) and deleted_at is null and asset_tag not in (select name from tempExclusions) and serial not in (select name from tempExclusions);';
			getTagSerial($sql, $mysqli, $snipe_url, "Assets with Incorrect Serial Numbers");
		?>
		<br>

		<?php
			//All assets where test is in asset tag but not testing
			$sql = 'select * from assets where asset_tag like "%test%" and asset_tag not like "%testing%" and deleted_at is null and asset_tag not in (select name from tempExclusions) and serial not in (select name from tempExclusions);';
			getTagSerial($sql, $mysqli, $snipe_url, "Assets Believed to be SnipeIT Tests that were Never Deleted");
		?>
		<br>

		<?php
			//look for import error accounts
			$sql = 'select * from users where username like "%delete%" and deleted_at is null and username not in (select name from tempExclusions);';
			getTagUserAsset($sql, $mysqli, $snipe_url, "Assets Assigned to Mistakenly Created Accounts During CSV Import Errors");
		?>
		<br>

		<?php
			//Accounts with more than 1 asset assigned
			$sql = 'select assigned_to, username, first_name, last_name, count(assigned_to) as "num" from assets inner join users on assets.assigned_to = users.id where assets.deleted_at is null and asset_tag not in (select name from tempExclusions) and serial not in (select name from tempExclusions) and username not in (select name from tempExclusions) group by assigned_to having count(*) > 1;';
			getTagUser($sql, $mysqli, $snipe_url, "Users with More Than 1 Asset Assigned to Them");
		?>
		<br>

		<?php
			//chromebooks with spaces in their asset tags and asset tag != serial
			$sql = 'select * from assets inner join models on assets.model_id = models.id inner join categories on models.category_id = categories.id where categories.name = "Chromebook" and assets.asset_tag not like "% %" and assets.asset_tag != assets.serial and assets.deleted_at is null and asset_tag not in (select name from tempExclusions) and serial not in (select name from tempExclusions);';
			getTagSerial($sql, $mysqli, $snipe_url, "Chromebooks without a space in their Asset Tags");
		?>
		<br>

		<?php
			//Assets with 7 character or less asset tags
			$sql = 'select * from assets where length(asset_tag) < 6 and asset_tag not like "%TV%" and deleted_at is null and asset_tag not in (select name from tempExclusions) and serial not in (select name from tempExclusions);';
			getTagSerial($sql, $mysqli, $snipe_url, "Assets with 5 Character or Less Asset Tags");
			?>
		<br>

		<?php
			//Assets with 8 character or less asset tags, may not all be errors
			$sql = 'select * from assets where length(asset_tag) < 9 and asset_tag not like "%TV%" and asset_tag not like "%TC%" and deleted_at is null and asset_tag not in (select name from tempExclusions) and serial not in (select name from tempExclusions);';
			getTagSerial($sql, $mysqli, $snipe_url, "Assets with 8 Character or Less Asset Tags (may not all be errors)");
		?>
		<br>
	
		<?php
			//assigned assets who's asset tags aren't serial
			$sql = 'select * from assets where assigned_to is not null and asset_tag != serial and asset_tag not in (select name from tempExclusions) and serial not in (select name from tempExclusions);';
			getTagSerial($sql, $mysqli, $snipe_url, "Assets Checked Out to Users with their Asset Tag not Matching their Serial");
		?>
		<br>

		<?php
			//look for weird student accounts
			$sql = 'select * from assets inner join users on assets.assigned_to = users.id where users.username like "%99%" and length(users.username) != 9 and assets.deleted_at is null and asset_tag not in (select name from tempExclusions) and serial not in (select name from tempExclusions) and username not in (select name from tempExclusions);';
			getTagUserAsset($sql, $mysqli, $snipe_url, "Assets Checked Out to Students with Strange Accounts");
		?>
		<br>

		<?php
			//assets assigned to students that may have already graduated
			$sql = 'select * from assets inner join users on assets.assigned_to = users.id where users.username like "%99%" and substring(users.username, 3, 2) <= DATE_FORMAT(now(), "%y") and assets.deleted_at is null and asset_tag not in (select name from tempExclusions) and serial not in (select name from tempExclusions) and username not in (select name from tempExclusions);';
			getTagUserAsset($sql, $mysqli, $snipe_url, "Assets Belived to be Checked Out to Graduated Students");
		?>
		<br>

		<?php
			//assets with non-necessary fields set
			$sql = 'select * from assets where ((name != "" and name is not null)) and deleted_at is null and asset_tag not in (select name from tempExclusions) and serial not in (select name from tempExclusions);';
			getTagName($sql, $mysqli, $snipe_url, "Assets with Non-Necessary Fields Set (mostly asset name, this is huge)");
		?>
		<br>

		<?php
			//Ready to Deploy or Deprovisioned assets with improper locations
			$sql = 'select * from assets inner join locations on assets.rtd_location_id = locations.id where status_id != 4 and (rtd_location_id != 15 and rtd_location_id != 16) and assets.deleted_at is null and asset_tag not in (select name from tempExclusions) and serial not in (select name from tempExclusions);';
			getTagStatus($sql, $mysqli, $snipe_url, "Ready to Deploy and Deprovisioned Assets with Improper Locations");
		?>
		<br>

		<?php
			//Deployed Assets with improper locations
			$sql = 'select * from assets inner join locations on assets.rtd_location_id = locations.id where status_id = 4 and (rtd_location_id = 15 or rtd_location_id = 16) and assets.deleted_at is null and asset_tag not in (select name from tempExclusions) and serial not in (select name from tempExclusions);';
			getTagLocation($sql, $mysqli, $snipe_url, "Deployed Assets with Improper Locations");
		?>
		<br>

		<?php
			//Assets with no location
			$sql = 'select * from assets left join locations on assets.rtd_location_id = locations.id where rtd_location_id = "" or rtd_location_id is null and assets.deleted_at is null and asset_tag not in (select name from tempExclusions) and serial not in (select name from tempExclusions);';
			getTagLocation($sql, $mysqli, $snipe_url, "Assets with No Location Set");
		?>
		<br>

		<?php
			//assets with no letters in the asset tag
			$sql = 'select * from assets where asset_tag not regexp "[a-zA-Z]" and deleted_at is null and asset_tag not in (select name from tempExclusions) and serial not in (select name from tempExclusions);';
			getTagSerial($sql, $mysqli, $snipe_url, "Assets Without Letters in their Asset Tags");
		?>
		<br>

	</div>
</body>

<?php
	//drop tempExclusions (it's recreated every time the page refreshes)
	$sql = 'drop table tempExclusions;';
	if($mysqli -> query($sql) === false){
		echo "couldn't drop table";
	}

?>
</html>
