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
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>SnipeTools</title>
</head>
<body>
	<div style="margin: 0; position: absolute; top: 40%; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%); text-align: center;">
	<h1>SnipeTools</h1>
	<?php echo ((time()-filemtime("../snipe-it/.github") > 30 * 24 * 3600)?("<h3 style='color: #FF474C;'>SnipeIT hasn't been updated in over a month. Please remote into<br>this server and run the SnipeIT update script on the Desktop.</h3>"):("")); ?>
	<a style="padding:15px;" href="sites/validate.php">Asset Validation</a>
	<a style="padding:15px;" href="sites/office.php">Return to Office</a>
	<a style="padding:15px;" href="sites/deprovision.php">Deprovisioning</a>
	<a style="padding:15px;" href="sites/report.php">Inventory Health Report</a>
	<a style="padding:15px;" href="sites/plug.php">TEMPORARY</a>
	</div>

	<div style="position: fixed; bottom: 0; right: 0; padding: 15px;">
	<a href="credits.html">Credits</a>
	</div>
</body>
</html>
