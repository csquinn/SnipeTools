<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="styles/indexStyle.css">
	<title>SnipeTools</title>
</head>
<body>
	<div class = "wrapper">
		<div id = "title">
			<h1>SnipeTools</h1>
		</div>
		<hr>

		<?php echo ((time()-filemtime("../snipe-it/.github") > 30 * 24 * 3600)?("<h3 class = 'alert'>SnipeIT hasn't been updated in over a month. Please remote into<br>this server and run the SnipeIT update script on the Desktop.</h3>"):("")); ?>
		
		<div id = "sites">
			<a href="sites/validate.php">Asset Validation</a>
			<a href="sites/office.php">Return to Office</a>
			<a href="sites/deprovision.php">Deprovisioning</a>
			<a href="sites/report.php">Inventory Health Report</a>
			<a href="sites/plug.php">TEMPORARY</a>
		</div>	
		
		<div id = "cred">
			<a href="credits.html">Credits</a>
		</div>
	</div>
</body>
</html>