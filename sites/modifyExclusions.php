<!DOCTYPE html>
<html lang="en">

<?php
//create 
$exclusionList = file_get_contents("../exclusions.txt");
//assign variable to write to file and prefill textarea
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$exclusionList = $_POST['exclusions'];
	//write input to file
	file_put_contents("../exclusions.txt", $exclusionList);
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
	<title>Modify Exclusions | SnipeTools</title>
</head>
<body>
	<div style="text-align: center;">
	<h2>Enter any Asset Tags, Serial Numbers, and Usernames (99#s) you would like to exclude from the Inventory Health Report</h2>
	<h3>Enter each value as a new line with <b>NO</b> commas</h3>
	<h3>It doesn't matter if you separate tags, serials, or usernames, enter them in any order</h3>
	<form action="modifyExclusions.php" method="POST">
		<textarea id="exclusions" name="exclusions" rows="35" cols="35"><?php echo $exclusionList; ?></textarea>
		<br>
		<input type="submit" value="Submit">
	</form>

	</div>
</body>
</html>
