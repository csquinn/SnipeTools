<!DOCTYPE html>
<?php
include handleMissingAsset.php
?>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Return Assets to Office</title>
</head>
<body>
	<h1>Who up holding they place?</h1>
	<h1>Return Assets to Office</h1>
	<form action="../api_requests/getIDBySerial.php" method="GET">
		<label for="serial">Scan Serial #</label>
		<input type="hidden" name="source" value="office">
		<input type="text" id="serial" name="serial" required>
		<button type="submit">Submit (not needed?)</button>
	</form>
	<h2><?php echo $assetMessage; ?></h2>
	<h3><?php echo $assetLink; ?></h3>
</body>
</html>