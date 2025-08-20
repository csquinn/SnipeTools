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
    <title>Recent Asset Updates | SnipeTools</title>
    <link rel = "stylesheet" href = "../styles/reportStyle.css">
</head>
<body>
    <div id = "info">
        <h1>Recent Asset Updates</h1>
        <h4>This site queries SnipeIT and returns all recent inventory transactions</h4>
        <h4>This is done to make sure that inventory management best-practices are being followed</h4>
        <br>
        <br>
        <button data-url = "../index.php">Return Home</button>
        <script src = "../scripts/buttons.js"></script>
        <br>
        <br>

        <?php
    
            $sql = 'select * from assets where created_at > NOW() - INTERVAL 1 WEEK  or updated_at > NOW() - INTERVAL 1 WEEK';
            getTagSerial($sql, $mysqli, $snipe_url, "Assets updated within the last week");

            //Assets that need to be updated to Windows 11
            //but already have serial numbers properly set
            $sql = 'select * from assets where (created_at < NOW() - INTERVAL 1 WEEK and created_at > NOW() - INTERVAL 4 WEEK)  or (updated_at < NOW() - INTERVAL 1 WEEK and updated_at > NOW() - INTERVAL 4 WEEK)';
            getTagSerial($sql, $mysqli, $snipe_url, "Assets updated within the last month");
        ?>
