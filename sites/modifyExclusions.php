<!DOCTYPE html>
<html lang="en">

<style> 
body {background-color: #337ab7; color: white;} 
a:link{color:white;}
a:visited{color:white;}
a:hover{color:white;}
a:focus{color:white;}
a:active{color:white;}
</style>

<?php
//initialize success message
$successMessage = "";

//fill exclusionList with file contents of txt file
$exclusionList = file_get_contents("../exclusions.txt");

//reassign exclusionList if it's being updated
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$exclusionList = $_POST['exclusions'];
	//write input to file
	file_put_contents("../exclusions.txt", $exclusionList);
	//pretty color
	echo "<style>body {background-color: green; color: black;}</style>";
	$successMessage = "Successfully updated the Exclusion List";
}

?>

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Modify Exclusions | SnipeTools</title>
</head>
<body>
	<div style="text-align: center;">
	<h2>Enter any Asset Tags, Serial Numbers, and Usernames (99#s) you would</h2>
	<h2>like to exclude from the Inventory Health Report</h2>
	<h3>Enter each value as a new line with <b>NO</b> commas</h3>
	<h3>It doesn't matter if you separate tags, serials, or usernames, enter them in any order</h3>
	<form action="modifyExclusions.php" method="POST">
		<textarea id="exclusions" name="exclusions" rows="25" cols="35"><?php echo $exclusionList; ?></textarea>
		<br>
		<input type="submit" value="Submit">
	</form>
	<h2><?php echo $successMessage; ?></h2>
	<a href = "report.php">Back to Inventory Health Report</a>
	</div>

	<div style="position: fixed; bottom: 0; right: 0; padding: 15px;">
	<a href="../sfx/nut/nut.html">Butternut</a>
	</div>
</body>
</html>
