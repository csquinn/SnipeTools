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
    <meta charset = "UTF-8">
    <meta name = "viewport" content = "width=device-width, initial-scale=1.0">
    <title>Windows 11 Status | SnipeTools</title>
    <link rel = "stylesheet" href = "../styles/reportStyle.css">
</head>
<body>
    <div id = "info">
        <h1>Windows 11 Update Assist</h1>
        <h4>This site queries SnipeIT and returns computers with serial numbers set to "VOID"</h4>
        <h4>This was done so that the TC import could be completed without the need of Windows 11</h4>
        <h4>These computers may still need Windows 11 imaged to them. The assets then need their serial numbers properly set in inventory</h4>
        <h5>Please do <b>NOT</b> set the serial numbers until the computer is imaged to Windows 11</h5>
        <h5>Note that this list is not all of the machines needing Windows 11. Reference the google doc for a complete list</h5>
        <br>
        <br>
        <button data-url = "../index.php">Return Home</button>
        <script src = "../scripts/buttons.js"></script>
        <br>
        <br>

        <?php
            //Assets that need to be updated to Windows 11
            //then properly have serial numbers set in inventory
            $sql = 'select * from assets where categories.name = "Computers" and serial = "VOID"';
            getTagSerial($sql, $mysqli, $snipe_url, "Assets needing Windows 11");
        ?>
