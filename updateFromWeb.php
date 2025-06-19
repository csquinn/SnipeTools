<?php

if(isset($_GET['update'])){
	// Launch the batch file
	exec('psexec -i 1 cmd.exe /k "update.bat"'); //I should make this script use a modifiable directory, but I don't want to :)
	
	// After the batch completes, redirect
	header("Location: index.html");
	exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<style> body {background-color: #337ab7; color: white;} </style>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Update SnipeIT | SnipeTools</title>
</head>
<body>
	<div style="margin: 0; position: absolute; top: 40%; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%); text-align: center;">
	<h1>Click me to Update SnipeIT</h1>
	<form action="updateFromWeb.php" method="GET" autocomplete="on">
		<input type="hidden" name="update" value="update">
		<button type="submit">UPDATE</button>
	</form>
	</div>
</body>
</html>
