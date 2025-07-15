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
$mysqli -> query('drop table if exists goodAssets;');

//create temporary table for asset and user exceptions
$sql = 'create table goodAssets (name varchar(255) not null) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';
if($mysqli -> query($sql) === FALSE) {
	echo "error creating the good assets table";
}

//prepare parameterized query
$sql = "INSERT IGNORE INTO goodAssets (name) VALUES (?)";
$prepSql = $mysqli->prepare($sql);
if (!$prepSql) {
	die("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
}

$prepSql->bind_param('s', $line);

//insert values from deprovisionLOG into goodAssets
$filename = '../logs/deprovisionLOG.txt';
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

//insert values from officeLOG into goodAssets
$filename = '../logs/officeLOG.txt';
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

//insert values from validateLOG into goodAssets
$filename = '../logs/validateLOG.txt';
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
	<link rel = "stylesheet" href = "../styles/plugStyle.css">
	<title>Inventory Health Report | SnipeTools</title>
</head>
<body>
	<div id = "instruction">
	<h1>Missing Assets</h1>
	<h4>This site shows all of the assets that have never been scanned into SnipeTools and aren't checked out</h4>
	<h4>Each section below can be expanded by clicking the dropdown arrow.</h4>
	<h4>After all of the known Chromebooks in the district have been scanned into SnipeTools,</h4>
	<h4>Connor will delete all other Chromebooks not assigned to students in inventory. Better not miss any :)</h4>
	<h5><b>*Please note: This report does not actually modify SnipeIT in any way, it just queries it</b></h5>
	<br>
	<br>
	<button id = "return" data-url ="../index.php">Return Home</button>
	<script src = "../scripts/buttons.js"></script>
	<br>
	<br>
	
	<?php
	//Dayton
	$sql = 'select assets.asset_tag, assets.serial, assets.name from assets inner join models on assets.model_id = models.id inner join categories on models.category_id = categories.id where categories.name = "Chromebook" and assets.deleted_at is null and (assets.assigned_to is null or assets.assigned_to = "")and rtd_location_id=5 and serial not in (select name from goodAssets);';
	getTagName($sql, $mysqli, $snipe_url, "Dayton");
	?>
	<br>

	<?php
	//Elderton
	$sql = 'select assets.asset_tag, assets.serial, assets.name from assets inner join models on assets.model_id = models.id inner join categories on models.category_id = categories.id where categories.name = "Chromebook" and assets.deleted_at is null and (assets.assigned_to is null or assets.assigned_to = "")and rtd_location_id=7 and serial not in (select name from goodAssets);';
	getTagName($sql, $mysqli, $snipe_url, "Elderton Elementary");
	?>
	<br>

	<?php
	//Shannock
	$sql = 'select assets.asset_tag, assets.serial, assets.name from assets inner join models on assets.model_id = models.id inner join categories on models.category_id = categories.id where categories.name = "Chromebook" and assets.deleted_at is null and (assets.assigned_to is null or assets.assigned_to = "")and rtd_location_id=9 and serial not in (select name from goodAssets);';
	getTagName($sql, $mysqli, $snipe_url, "Shannock Valley");
	?>
	<br>

	<?php
	//West Hills Primary
	$sql = 'select assets.asset_tag, assets.serial, assets.name from assets inner join models on assets.model_id = models.id inner join categories on models.category_id = categories.id where categories.name = "Chromebook" and assets.deleted_at is null and (assets.assigned_to is null or assets.assigned_to = "")and rtd_location_id=2 and serial not in (select name from goodAssets);';
	getTagName($sql, $mysqli, $snipe_url, "West Hills Primary");
	?>
	<br>
	
	<?php
	//West Hills Intermediate
	$sql = 'select assets.asset_tag, assets.serial, assets.name from assets inner join models on assets.model_id = models.id inner join categories on models.category_id = categories.id where categories.name = "Chromebook" and assets.deleted_at is null and (assets.assigned_to is null or assets.assigned_to = "")and rtd_location_id=4 and serial not in (select name from goodAssets);';
	getTagName($sql, $mysqli, $snipe_url, "West Hills Intermediate");
	?>
	<br>

	<?php
	//Armstrong
	$sql = 'select assets.asset_tag, assets.serial, assets.name from assets inner join models on assets.model_id = models.id inner join categories on models.category_id = categories.id where categories.name = "Chromebook" and assets.deleted_at is null and (assets.assigned_to is null or assets.assigned_to = "")and rtd_location_id=3 and serial not in (select name from goodAssets);';
	getTagName($sql, $mysqli, $snipe_url, "Armstrong");
	?>
	<br>

	<?php
	//Lenape
	$sql = 'select assets.asset_tag, assets.serial, assets.name from assets inner join models on assets.model_id = models.id inner join categories on models.category,id = categories.id where categories.name = "Chromebook" and assets.deleted_at is null and (assets.assigned_to is null or assets.assigned_to = "")and rtd_location_id=8 and serial not in (select name from goodAssets);';
	getTagName($sql, $mysqli, $snipe_url, "Lenape");
	?>
	<br>

	<?php
	//West Shamokin
	$sql = 'select assets.asset_tag, assets.serial, assets.name from assets inner join models on assets.model_id = models.id inner join categories on models.category_id = categories.id where categories.name = "Chromebook" and assets.deleted_at is null and (assets.assigned_to is null or assets.assigned_to = "")and rtd_location_id=6 and serial not in (select name from goodAssets);';
	getTagName($sql, $mysqli, $snipe_url, "West Shamokin");
	?>
	<br>

	<?php
	//Admin
	$sql = 'select assets.asset_tag, assets.serial, assets.name from assets inner join models on assets.model_id = models.id inner join categories on models.category_id = categories.id where categories.name = "Chromebook" and assets.deleted_at is null and (assets.assigned_to is null or assets.assigned_to = "")and rtd_location_id=1 and serial not in (select name from goodAssets);';
	getTagName($sql, $mysqli, $snipe_url, "Admin???");
	?>
	<br>

	<?php
	//M.I.A.
	$sql = 'select assets.asset_tag, assets.serial, assets.name from assets inner join models on assets.model_id = models.id inner join categories on models.category_id = categories.id where categories.name = "Chromebook" and assets.deleted_at is null and (assets.assigned_to is null or assets.assigned_to = "")and (rtd_location_id="" or rtd_location_id is null) and serial not in (select name from goodAssets);';
	getTagName($sql, $mysqli, $snipe_url, "No Location");
	?>
	<br>

	</div>
</body>

<?php
//drop goodAssets (it's recreated every time the page refreshes)
$sql = 'drop table goodAssets;';
if($mysqli -> query($sql) === false){
	echo "couldn't drop table";
}

?>
</html>