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

<style> 
body {background-color: #337ab7; color: white;} 
details {text-align: center; margin: auto;}
table {text-align: center; margin: auto;}
</style>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Inventory Health Report | SnipeTools</title>
</head>
<body>
	<div style="text-align: center;">
	<h1>Inventory Health Report</h1>
	<h4>This site queries SnipeIT and returns potential asset errors.</h4>
	<h4>Each section below can be expanded by clicking the dropdown arrow.</h4>
	<h4>This report is <b>NOT</b> exhaustive and inventory should be examined regularly in addition to this report</h4>
	<h5><b>*Please note: This report does not actually modify SnipeIT in any way, it just queries it</b></h5>
	<h5>Click the link below to specify exclusions from this report</h5>
	<h5>(Exclusions will not appear in results even if they are errors)</h5>
	<a style="padding:15px;" href="modifyExclusions.php">Create Exclusions from Report</a>
	<br>
	<br>
	<a style="padding:15px;" href="../index.php">Return Home</a>
	<br>
	<br>
	
	<?php
	//Assets without a serial number or a highly shortened serial
	$sql = 'select * from assets where (serial = "" or serial is null or serial = " " or length(serial) < 7) and deleted_at is null and asset_tag not in (select name from tempExclusions) and serial not in (select name from tempExclusions);';
	/*
	$result = $mysqli -> query($sql);
	echo "<details>";
	echo "<summary>Assets with Incorrect Serial Numbers (". $result->num_rows .")</summary>";
	// Associative array
	echo "<table border='1'>";
	echo "<tr><td>Asset Tag</td><td>Serial</td><td>Link</td></tr>";
	while($row = $result -> fetch_assoc()){
		echo "<tr><td>". $row['asset_tag'] ."</td><td>". $row['serial'] ."</td><td><a href='" . $snipe_url . "/hardware?page=1&size=20&search=" . $row['serial'] . "'>Link</a></td></tr>";
	}
	echo"</table>";
	echo "</details>";
	// Free result set
	$result -> free_result();
	*/
	getTagSerial($sql, $mysqli, $snipe_url, "Assets with Incorrect Serial Numbers");
	?>
	<br>

	<?php
	//All assets where test is in asset tag but not testing
	$sql = 'select * from assets where asset_tag like "%test%" and asset_tag not like "%testing%" and deleted_at is null and asset_tag not in (select name from tempExclusions) and serial not in (select name from tempExclusions);';
	$result = $mysqli -> query($sql);
	echo "<details>";
	echo "<summary>Assets Believed to be SnipeIT Tests that were Never Deleted (". $result->num_rows .")</summary>";
	// Associative array
	echo "<table border='1'>";
	echo "<tr><td>Asset Tag</td><td>Serial</td><td>Link</td></tr>";
	while($row = $result -> fetch_assoc()){
		echo "<tr><td>". $row['asset_tag'] ."</td><td>". $row['serial'] ."</td><td><a href='" . $snipe_url . "/hardware?page=1&size=20&search=" . $row['serial'] . "'>Link</a></td></tr>";
	}
	echo"</table>";
	echo "</details>";
	// Free result set
	$result -> free_result();
	?>
	<br>

	<?php
	//look for import error accounts
	$sql = 'select * from users where username like "%delete%" and deleted_at is null and username not in (select name from tempExclusions);';
	$result = $mysqli -> query($sql);
	echo "<details>";
	echo "<summary>Assets Assigned to Mistakenly Created Accounts During CSV Import Errors (". $result->num_rows .")</summary>";
	// Associative array
	echo "<table border='1'>";
	echo "<tr><td>Asset Tag</td><td>Username/99#</td><td>First Name</td><td>Last Name</td><td>Link</td></tr>";
	while($row = $result -> fetch_assoc()){
		echo "<tr><td>". $row['asset_tag'] ."</td><td>". $row['username'] ."</td><td>". $row['first_name'] ."</td><td>". $row['last_name'] ."</td><td><a href='" . $snipe_url . "/hardware?page=1&size=20&search=" . $row['serial'] . "'>Link</a></td></tr>";
	}
	echo"</table>";
	echo "</details>";
	// Free result set
	$result -> free_result();
	?>
	<br>

	<?php
	//Accounts with more than 1 asset assigned
	$sql = 'select assigned_to, username, first_name, last_name, count(assigned_to) as "num" from assets inner join users on assets.assigned_to = users.id where assets.deleted_at is null and asset_tag not in (select name from tempExclusions) and serial not in (select name from tempExclusions) and username not in (select name from tempExclusions) group by assigned_to having count(*) > 1;';
	$result = $mysqli -> query($sql);
	echo "<details>";
	echo "<summary>Users with More Than 1 Asset Assigned to Them (". $result->num_rows .")</summary>";
	// Associative array
	echo "<table border='1'>";
	echo "<tr><td>Amount of Assets</td><td>Username/99#</td><td>First Name</td><td>Last Name</td><td>Link</td></tr>";
	while($row = $result -> fetch_assoc()){
		echo "<tr><td>". $row['num'] ."</td><td>". $row['username'] ."</td><td>". $row['first_name'] ."</td><td>". $row['last_name'] ."</td><td><a href='" . $snipe_url . "/users/" . $row['assigned_to'] . "'>Link</a></td></tr>";
	}
	echo"</table>";
	echo "</details>";
	// Free result set
	$result -> free_result();
	?>
	<br>

	<?php
	//chromebooks with spaces in their asset tags and asset tag != serial
	$sql = 'select * from assets inner join models on assets.model_id = models.id inner join categories on models.category_id = categories.id where categories.name = "Chromebook" and assets.asset_tag not like "% %" and assets.asset_tag != assets.serial and assets.deleted_at is null and asset_tag not in (select name from tempExclusions) and serial not in (select name from tempExclusions);';
	$result = $mysqli -> query($sql);
	echo "<details>";
	echo "<summary>Chromebooks without a space in their Asset Tags (". $result->num_rows .")</summary>";
	// Associative array
	echo "<table border='1'>";
	echo "<tr><td>Asset Tag</td><td>Serial</td><td>Link</td></tr>";
	while($row = $result -> fetch_assoc()){
		echo "<tr><td>". $row['asset_tag'] ."</td><td>". $row['serial'] ."</td><td><a href='" . $snipe_url . "/hardware?page=1&size=20&search=" . $row['serial'] . "'>Link</a></td></tr>";
	}
	echo"</table>";
	echo "</details>";
	// Free result set
	$result -> free_result();
	?>
	<br>

	<?php
	//Assets with 7 character or less asset tags
	$sql = 'select * from assets where length(asset_tag) < 7 and deleted_at is null and asset_tag not in (select name from tempExclusions) and serial not in (select name from tempExclusions);';
	$result = $mysqli -> query($sql);
	echo "<details>";
	echo "<summary>Assets with 7 Character or Less Asset Tags (". $result->num_rows .")</summary>";
	// Associative array
	echo "<table border='1'>";
	echo "<tr><td>Asset Tag</td><td>Serial</td><td>Link</td></tr>";
	while($row = $result -> fetch_assoc()){
		echo "<tr><td>". $row['asset_tag'] ."</td><td>". $row['serial'] ."</td><td><a href='" . $snipe_url . "/hardware?page=1&size=20&search=" . $row['serial'] . "'>Link</a></td></tr>";
	}
	echo"</table>";
	echo "</details>";
	// Free result set
	$result -> free_result();
	?>
	<br>

	<?php
	//Assets with 8 character or less asset tags, may not all be errors
	$sql = 'select * from assets where length(asset_tag) < 8 and asset_tag not like "%TV%" and asset_tag not like "%TC%" and deleted_at is null and asset_tag not in (select name from tempExclusions) and serial not in (select name from tempExclusions);';
	$result = $mysqli -> query($sql);
	echo "<details>";
	echo "<summary>Assets with 8 Character or Less Asset Tags (may not all be errors) (". $result->num_rows .")</summary>";
	// Associative array
	echo "<table border='1'>";
	echo "<tr><td>Asset Tag</td><td>Serial</td><td>Link</td></tr>";
	while($row = $result -> fetch_assoc()){
		echo "<tr><td>". $row['asset_tag'] ."</td><td>". $row['serial'] ."</td><td><a href='" . $snipe_url . "/hardware?page=1&size=20&search=" . $row['serial'] . "'>Link</a></td></tr>";
	}
	echo"</table>";
	echo "</details>";
	// Free result set
	$result -> free_result();
	?>
	<br>
 
	<?php
	//assigned assets who's asset tags aren't serial
	$sql = 'select * from assets where assigned_to is not null and asset_tag != serial and asset_tag not in (select name from tempExclusions) and serial not in (select name from tempExclusions);';
	$result = $mysqli -> query($sql);
	echo "<details>";
	echo "<summary>Assets Checked Out to Users with their Asset Tag not Matching their Serial (". $result->num_rows .")</summary>";
	// Associative array
	echo "<table border='1'>";
	echo "<tr><td>Asset Tag</td><td>Serial</td><td>Link</td></tr>";
	while($row = $result -> fetch_assoc()){
		echo "<tr><td>". $row['asset_tag'] ."</td><td>". $row['serial'] ."</td><td><a href='" . $snipe_url . "/hardware?page=1&size=20&search=" . $row['serial'] . "'>Link</a></td></tr>";
	}
	echo"</table>";
	echo "</details>";
	// Free result set
	$result -> free_result();
	?>
	<br>

	<?php
	//look for weird student accounts
	$sql = 'select * from assets inner join users on assets.assigned_to = users.id where users.username like "%99%" and length(users.username) != 9 and assets.deleted_at is null and asset_tag not in (select name from tempExclusions) and serial not in (select name from tempExclusions) and username not in (select name from tempExclusions);';
	$result = $mysqli -> query($sql);
	echo "<details>";
	echo "<summary>Assets Checked Out to Students with Strange Accounts (". $result->num_rows .")</summary>";
	// Associative array
	echo "<table border='1'>";
	echo "<tr><td>Asset Tag</td><td>Username/99#</td><td>First Name</td><td>Last Name</td><td>Link</td></tr>";
	while($row = $result -> fetch_assoc()){
		echo "<tr><td>". $row['asset_tag'] ."</td><td>". $row['username'] ."</td><td>". $row['first_name'] ."</td><td>". $row['last_name'] ."</td><td><a href='" . $snipe_url . "/hardware?page=1&size=20&search=" . $row['serial'] . "'>Link</a></td></tr>";
	}
	echo"</table>";
	echo "</details>";
	// Free result set
	$result -> free_result();
	?>
	<br>

	<?php
	//assets with non-necessary fields set
	$sql = 'select * from assets where ((name != "" and name is not null)) and deleted_at is null and asset_tag not in (select name from tempExclusions) and serial not in (select name from tempExclusions);';
	$result = $mysqli -> query($sql);
	echo "<details>";
	echo "<summary>Assets with Non-Necessary Fields Set (mostly asset name, this is huge) (". $result->num_rows .")</summary>";
	// Associative array
	echo "<table border='1'>";
	echo "<tr><td>Asset Tag</td><td>Serial</td><td>Asset Name</td><td>Link</td></tr>";
	while($row = $result -> fetch_assoc()){
		echo "<tr><td>". $row['asset_tag'] ."</td><td>". $row['serial'] ."</td><td>". $row['name'] ."</td><td><a href='" . $snipe_url . "/hardware?page=1&size=20&search=" . $row['serial'] . "'>Link</a></td></tr>";
	}
	echo"</table>";
	echo "</details>";
	// Free result set
	$result -> free_result();
	?>
	<br>

	<?php
	//Ready to Deploy or Deprovisioned assets with improper locations
	$sql = 'select * from assets inner join locations on assets.rtd_location_id = locations.id where status_id != 4 and (rtd_location_id != 15 and rtd_location_id != 16) and assets.deleted_at is null and asset_tag not in (select name from tempExclusions) and serial not in (select name from tempExclusions);';
	$result = $mysqli -> query($sql);
	echo "<details>";
	echo "<summary>Ready to Deploy and Deprovisioned Assets with Improper Locations (". $result->num_rows .")</summary>";
	// Associative array
	echo "<table border='1'>";
	echo "<tr><td>Asset Tag</td><td>Serial</td><td>Location</td><td>Status</td><td>Link</td></tr>";
	while($row = $result -> fetch_assoc()){
		echo "<tr><td>". $row['asset_tag'] ."</td><td>". $row['serial'] ."</td><td>". $row['name'] ."</td><td>".(($row['status_id'] == 2)?("Ready to Deploy"):("Deprovisioned"))."</td><td><a href='" . $snipe_url . "/hardware?page=1&size=20&search=" . $row['serial'] . "'>Link</a></td></tr>";
	}
	echo"</table>";
	echo "</details>";
	// Free result set
	$result -> free_result();
	?>
	<br>

	<?php
	//Deployed Assets with improper locations
	$sql = 'select * from assets inner join locations on assets.rtd_location_id = locations.id where status_id = 4 and (rtd_location_id = 15 or rtd_location_id = 16) and assets.deleted_at is null and asset_tag not in (select name from tempExclusions) and serial not in (select name from tempExclusions);';
	$result = $mysqli -> query($sql);
	echo "<details>";
	echo "<summary>Deployed Assets with Improper Locations (". $result->num_rows .")</summary>";
	// Associative array
	echo "<table border='1'>";
	echo "<tr><td>Asset Tag</td><td>Serial</td><td>Location</td><td>Link</td></tr>";
	while($row = $result -> fetch_assoc()){
		echo "<tr><td>". $row['asset_tag'] ."</td><td>". $row['serial'] ."</td><td>". $row['name'] ."</td><td><a href='" . $snipe_url . "/hardware?page=1&size=20&search=" . $row['serial'] . "'>Link</a></td></tr>";
	}
	echo"</table>";
	echo "</details>";
	// Free result set
	$result -> free_result();
	?>
	<br>

	<?php
	//Assets with no location
	$sql = 'select * from assets where rtd_location_id = "" or rtd_location_id is null and deleted_at is null and asset_tag not in (select name from tempExclusions) and serial not in (select name from tempExclusions);';
	$result = $mysqli -> query($sql);
	echo "<details>";
	echo "<summary>Assets with No Location Set (". $result->num_rows .")</summary>";
	// Associative array
	echo "<table border='1'>";
	echo "<tr><td>Asset Tag</td><td>Serial</td><td>Location</td><td>Link</td></tr>";
	while($row = $result -> fetch_assoc()){
		echo "<tr><td>". $row['asset_tag'] ."</td><td>". $row['serial'] ."</td><td>". $row['rtd_location_id'] ."</td><td><a href='" . $snipe_url . "/hardware?page=1&size=20&search=" . $row['serial'] . "'>Link</a></td></tr>";
	}
	echo"</table>";
	echo "</details>";
	// Free result set
	$result -> free_result();
	?>
	<br>

	<?php
	//assets with no letters in the asset tag
	$sql = 'select * from assets where asset_tag not regexp "[a-zA-Z]" and deleted_at is null and asset_tag not in (select name from tempExclusions) and serial not in (select name from tempExclusions);';
	$result = $mysqli -> query($sql);
	echo "<details>";
	echo "<summary>Assets Without Letters in their Asset Tags (". $result->num_rows .")</summary>";
	// Associative array
	echo "<table border='1'>";
	echo "<tr><td>Asset Tag</td><td>Serial</td><td>Link</td></tr>";
	while($row = $result -> fetch_assoc()){
		echo "<tr><td>". $row['asset_tag'] ."</td><td>". $row['serial'] ."</td><td><a href='" . $snipe_url . "/hardware?page=1&size=20&search=" . $row['serial'] . "'>Link</a></td></tr>";
	}
	echo"</table>";
	echo "</details>";
	// Free result set
	$result -> free_result();
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