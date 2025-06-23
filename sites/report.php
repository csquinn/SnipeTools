<!DOCTYPE html>
<html lang="en">
<?php

//get mysql password
$db_password = file_get_contents("../user_variables/snipe_mysql_password.txt");
$db_password = str_replace(array("\r", "\n"), '', $db_password);

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

	$sql = 'select * from assets inner join users on assets.assigned_to = users.id where users.username like "%99%" and length(users.username) != 9 and assets.deleted_at is null;';

	$result = $mysqli -> query($sql);
	
	echo "<details>";
	echo "<summary>Assets signed out to students with strange accounts</summary>";
	// Associative array
	while($row = $result -> fetch_assoc()){
		echo $row['asset_tag']." ";
		echo $row['username']."<br>";
	}
	echo "</details>";

	// Free result set
	$result -> free_result();

	$mysqli -> close();
	
	?>
	</div>
</body>
</html>