<!DOCTYPE html>
<html lang="en">
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

?>

<style> 
body {background-color: #337ab7; color: white;} 
details {text-align: center; margin: auto;}
table {text-align: center; margin: auto;}
a:link{color:white;}
a:visited{color:white;}
a:hover{color:white;}
a:focus{color:white;}
a:active{color:white;}
</style>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Inventory Health Report | SnipeTools</title>
</head>
<body>
	<div style="text-align: center;">
	<h1>Inventory Health Report</h1>
	<h4>This site queries SnipeIT and returns assets that are believed to be errors.</h4>
	<h4>Each different section can be expanded below</h4>
	<h5><b>*Please note: This report does not actually modify SnipeIT in any way</b></h5>
	<br>
	
	<?php
	//All assets where test is in asset tag but not testing
	$sql = 'select * from assets where asset_tag like "%test%" and asset_tag not like "%testing%" and deleted_at is null;';
	$result = $mysqli -> query($sql);
	echo "<details>";
	echo "<summary>Assets believed to be SnipeIT tests that were never deleted</summary>";
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
	$sql = 'select * from users where username like "%delete%" and deleted_at is null;';
	$result = $mysqli -> query($sql);
	echo "<details>";
	echo "<summary>Assets assigned to mistakenly created accounts during CSV import errors</summary>";
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
	$sql = 'select assigned_to, username, first_name, last_name, count(assigned_to) as "num" from assets inner join users on assets.assigned_to = users.id where assets.deleted_at is null group by assigned_to having count(*) > 1;';
	$result = $mysqli -> query($sql);
	echo "<details>";
	echo "<summary>Users with more than 1 asset assigned to them</summary>";
	// Associative array
	echo "<table border='1'>";
	echo "<tr><td>Amount of assets</td><td>Username/99#</td><td>First Name</td><td>Last Name</td><td>Link</td></tr>";
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
	//look for weird student accounts
	$sql = 'select * from assets inner join users on assets.assigned_to = users.id where users.username like "%99%" and length(users.username) != 9 and assets.deleted_at is null;';
	$result = $mysqli -> query($sql);
	echo "<details>";
	echo "<summary>Assets signed out to students with strange accounts</summary>";
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
	</div>
</body>
</html>