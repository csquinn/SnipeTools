<?php
$retag = "on";
$serial = "1";
$assetTag = "2";
$status = "4";
$currentStatus="5";
$modelID = "6";
$location= "7";
$remName = "on";
echo '{'
		.((isset($retag) and $retag=="on")?('"asset_tag": "'.$serial.'"'):('"asset_tag": "'.$assetTag.'"'))	//asset_tag
		.((isset($status))?(', "status_id": '.$status):(', "status_id": '.$currentStatus))	//status_id
		.', "model_id": '.$modelID	//model_id
		.''	//rtd_location_id
		.''	//name
		.'}';

?>