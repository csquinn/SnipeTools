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

?>

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Chromebook Status | SnipeTools</title>
	<link rel = "stylesheet" href = "../styles/reportStyle.css">
</head>
<body>
	<div id = "info">
		<h1>Chromebook Status</h1>
		<h4>This site queries SnipeIT and examines each individual Chromebook asset for errors.</h4>
		<h4>Each section below can be expanded by clicking the dropdown arrow.</h4>
		<h4>Although this report is designed to examine every Chromebook asset, Inventory should still be examined manually.</h4>
		<h5><b>*Please note: This report does not actually modify SnipeIT in any way, it just queries it</b></h5>
		
		<br>
		<br>
		<button data-url ="../index.php">Return Home</button>
		<br>
		<br>
		<script src = "../scripts/buttons.js"></script>

		<?php
			$daytonLocations = array (
				array("A104", 22),
				array("A126", 17),
				array("A110", 17),
				array("C107", 22),
				array("C106", 18),
				array("C115", 21),
				array("C114", 19)
			);
			getK4Errors($daytonLocations, $mysqli, $snipe_url, "Dayton", "DE");
		?>

</div>
</body>
</html>
