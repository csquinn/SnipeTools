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


// Read students.txt file line by line to get array of all students
$filename = '../student.csv';
$handle = fopen($filename, 'r');
$Daytonstudents= [];
$Eldertonstudents= [];
$Shannockstudents= [];
$Lenapestudents= [];
$Primarystudents= [];
$Intermediatestudents= [];
$Armstrongstudents= [];
$WSstudents= [];
$Cyberstudents= [];
$Otherstudents= [];
if (!$handle) {
	die("Cannot open file: $filename");
}

while (($line = fgets($handle)) !== false) {
	$line = trim($line);      // Remove line breaks and spaces
	if(!($line == "null" or $line == " " or $line == "" or $line == null)){
		$temp = explode(',', $line);
		if(substr($temp[4], 0, 2) == "99"){
			$location = 0;
			switch ($temp[0]) { //get locations of students from roster and match the id of snipe database
    				case "005": //Dayton
    				    $location = 5;
					$Daytonstudents[] = array($temp[3],$temp[1], $temp[4], (($temp[5] == "KG")?(0):((int)$temp[5])), $location);//last name, first name, 99#, grade, location
   				    break 1;
   				 case "013": //Elderton
      				  $location = 7;
					$Eldertonstudents[] = array($temp[3],$temp[1], $temp[4], (($temp[5] == "KG")?(0):((int)$temp[5])), $location);//last name, first name, 99#, grade, location
      				  break 1;
   				 case "026": //Shannock
       				  $location = 9;
					$Shannockstudents[] = array($temp[3],$temp[1], $temp[4], (($temp[5] == "KG")?(0):((int)$temp[5])), $location);//last name, first name, 99#, grade, location
       				  break 1;
				case "016": //Lenape
					$location = 8;
					$Lenapestudents[] = array($temp[3],$temp[1], $temp[4], (($temp[5] == "KG")?(0):((int)$temp[5])), $location);//last name, first name, 99#, grade, location
					break 1;
				case "028": //Primary
					$location = 2;
					$Primarystudents[] = array($temp[3],$temp[1], $temp[4], (($temp[5] == "KG")?(0):((int)$temp[5])), $location);//last name, first name, 99#, grade, location
					break 1;
				case "022": //Intermediate
					$location = 4;
					$Intermediatestudents[] = array($temp[3],$temp[1], $temp[4], (($temp[5] == "KG")?(0):((int)$temp[5])), $location);//last name, first name, 99#, grade, location
					break 1;
				case "032": //Armstrong
					$location = 3;
					$Armstrongstudents[] = array($temp[3],$temp[1], $temp[4], (($temp[5] == "KG")?(0):((int)$temp[5])), $location);//last name, first name, 99#, grade, location
					break 1;
				case "027": //WS
					$location = 6;
					$WSstudents[] = array($temp[3],$temp[1], $temp[4], (($temp[5] == "KG")?(0):((int)$temp[5])), $location);//last name, first name, 99#, grade, location
					break 1;
				case "040": //Cyber
					$location = 12;
					$Cyberstudents[] = array($temp[3],$temp[1], $temp[4], (($temp[5] == "KG")?(0):((int)$temp[5])), $location);//last name, first name, 99#, grade, location
					break 1;
				case "700": //also cyber I guess
					$location = 12;
					$Cyberstudents[] = array($temp[3],$temp[1], $temp[4], (($temp[5] == "KG")?(0):((int)$temp[5])), $location);//last name, first name, 99#, grade, location
					break 1;
				default:	//sets location to admin, will be used to mark as error
					$location = 1;
					$Otherstudents[] = array($temp[3],$temp[1], $temp[4], (($temp[5] == "KG")?(0):((int)$temp[5])), $location);//last name, first name, 99#, grade, location
					break 1;
			}
			
	}
}
}

//sort by grade level then alphabetically for every list
//sorting by location isn't necessary but I don't want to remove it right now
$locationCol = array_column($Daytonstudents, 4);
$grade  = array_column($Daytonstudents, 3);
$lastNames = array_column($Daytonstudents, 0); 
array_multisort($locationCol, SORT_ASC, $grade, SORT_ASC, $lastNames, SORT_ASC, $Daytonstudents);

$locationCol = array_column($Eldertonstudents, 4);
$grade  = array_column($Eldertonstudents, 3);
$lastNames = array_column($Eldertonstudents, 0); 
array_multisort($locationCol, SORT_ASC, $grade, SORT_ASC, $lastNames, SORT_ASC, $Eldertonstudents);

$locationCol = array_column($Shannockstudents, 4);
$grade  = array_column($Shannockstudents, 3);
$lastNames = array_column($Shannockstudents, 0); 
array_multisort($locationCol, SORT_ASC, $grade, SORT_ASC, $lastNames, SORT_ASC, $Shannockstudents);

$locationCol = array_column($Lenapestudents, 4);
$grade  = array_column($Lenapestudents, 3);
$lastNames = array_column($Lenapestudents, 0); 
array_multisort($locationCol, SORT_ASC, $grade, SORT_ASC, $lastNames, SORT_ASC, $Lenapestudents);

$locationCol = array_column($Primarystudents, 4);
$grade  = array_column($Primarystudents, 3);
$lastNames = array_column($Primarystudents, 0); 
array_multisort($locationCol, SORT_ASC, $grade, SORT_ASC, $lastNames, SORT_ASC, $Primarystudents);

$locationCol = array_column($Intermediatestudents, 4);
$grade  = array_column($Intermediatestudents, 3);
$lastNames = array_column($Intermediatestudents, 0); 
array_multisort($locationCol, SORT_ASC, $grade, SORT_ASC, $lastNames, SORT_ASC, $Intermediatestudents);

$locationCol = array_column($Armstrongstudents, 4);
$grade  = array_column($Armstrongstudents, 3);
$lastNames = array_column($Armstrongstudents, 0); 
array_multisort($locationCol, SORT_ASC, $grade, SORT_ASC, $lastNames, SORT_ASC, $Armstrongstudents);

$locationCol = array_column($WSstudents, 4);
$grade  = array_column($WSstudents, 3);
$lastNames = array_column($WSstudents, 0); 
array_multisort($locationCol, SORT_ASC, $grade, SORT_ASC, $lastNames, SORT_ASC, $WSstudents);

$locationCol = array_column($Cyberstudents, 4);
$grade  = array_column($Cyberstudents, 3);
$lastNames = array_column($Cyberstudents, 0); 
array_multisort($locationCol, SORT_ASC, $grade, SORT_ASC, $lastNames, SORT_ASC, $Cyberstudents);

$locationCol = array_column($Otherstudents, 4);
$grade  = array_column($Otherstudents, 3);
$lastNames = array_column($Otherstudents, 0); 
array_multisort($locationCol, SORT_ASC, $grade, SORT_ASC, $lastNames, SORT_ASC, $Otherstudents);
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
				array("C114", 19),
				array("LNR", 61)
			);
			getK4Errors($daytonLocations, $mysqli, $snipe_url, "Dayton K-4", "DE", 5);
			echo"<br>";

			get512Errors($Daytonstudents, $mysqli, $snipe_url, "Dayton 5-6");
			echo"<br>";

			$eldertonLocations = array (
				array("129", 13),
				array("104", 13),
				array("106", 22),
				array("105", 19),
				array("108", 17),
				array("109", 22),
				array("112", 15),
				array("114", 15),
				array("LNR", 10)
			);
			getK4Errors($eldertonLocations, $mysqli, $snipe_url, "Elderton K-4", "EE", 7);
			echo"<br>";

			get512Errors($Eldertonstudents, $mysqli, $snipe_url, "Elderton 5-6");
			echo"<br>";

			$shannockLocations = array (
				array("119", 15),
				array("120", 16),
				array("128", 18),
				array("130", 16),
				array("200", 27),
				array("225", 25),
				array("201", 22),
				array("224", 22),
				array("206", 21),
				array("218", 21),
				array("LNR", 13)
			);
			getK4Errors($shannockLocations, $mysqli, $snipe_url, "Shannock Valley K-4", "SV", 9);
			echo"<br>";
	
			get512Errors($Shannockstudents, $mysqli, $snipe_url, "Shannock Valley 5-6");
			echo"<br>";

			$lenapeLocations = array(
				array("0101", 18),
				array("0103", 18),
				array("0104", 18),
				array("0105", 18),
				array("0106", 18),
				array("1101", 22),
				array("1103", 22),
				array("1104", 22),
				array("1105", 22),
				array("1106", 22),
				array("2101", 24),
				array("2104", 24),
				array("2105", 24),
				array("2106", 24),
				array("3101", 22),
				array("3102", 22),
				array("3103", 22),
				array("3104", 22),
				array("3105", 22),
				array("4101", 25),
				array("4103", 25),
				array("4104", 25),
				array("4105", 25),
				array("LNR", 10)
			);
			getK4Errors($lenapeLocations, $mysqli, $snipe_url, "Lenape K-4", "LE", 8);
			echo"<br>";

			get512Errors($Lenapestudents, $mysqli, $snipe_url, "Lenape 5-6");
			echo"<br>";

			$westPrimaryLocations = array (
				array("K1", 21),
				array("K2", 21),
				array("K3", 21),
				array("K4", 21),
				array("K5", 21),
				array("K6", 21),
				array("B1", 21),
				array("A1", 21),
				array("A2", 21),
				array("A3", 24),
				array("A4", 21),
				array("B2", 22),
				array("B3", 23),
				array("B4", 23),
				array("C2", 25),
				array("C4", 25),
				array("D1", 24),
				array("D2", 24),
				array("D4", 25),
				array("G4", 25),
				array("G5", 26),
				array("H2", 25),
				array("H3", 25),
				array("H4", 25),
				array("E1", 11),
				array("E2", 12),
				array("LNR", 10)
			);
			getK4Errors($westPrimaryLocations, $mysqli, $snipe_url, "West Hills Primary", "WHP", 2);
			echo"<br>";

			get512Errors($Primarystudents, $mysqli, $snipe_url, "West Hills Primary Assigned Chromebooks (should likely be 0)");
			echo"<br>";

			$westIntermediateLocations = array (
				array("4004", 25),
				array("4006", 25),
				array("4007", 25),
				array("4008", 25),
				array("4009", 25),
				array("4010", 25),
				array("2040", 11),
				array("LNR", 20)
			);
			getK4Errors($westIntermediateLocations, $mysqli, $snipe_url, "West Hills Intermediate 4th", "WI", 4);
			echo"<br>";

			get512Errors($Intermediatestudents, $mysqli, $snipe_url, "West Hills Intermediate 5-6");
			echo"<br>";

			get512Errors($Armstrongstudents, $mysqli, $snipe_url, "Armstrong");
			echo"<br>";

			get512Errors($WSstudents, $mysqli, $snipe_url, "West Shamokin");
			echo"<br>";

			get512Errors($Cyberstudents, $mysqli, $snipe_url, "Cyber");
			echo"<br>";
	
			get512Errors($Otherstudents, $mysqli, $snipe_url, "Other students on roster (marked as Census?)");
			echo"<br>";

			getExtraStudentErrors($Daytonstudents, $Eldertonstudents, $Shannockstudents, $Lenapestudents, $Primarystudents, $Intermediatestudents, $Armstrongstudents, $WSstudents, $Cyberstudents, $Otherstudents, $mysqli, $snipe_url, "Students not on roster with assigned Chromebooks");
			echo"<br>";

			
		?>

</div>
</body>
</html>
