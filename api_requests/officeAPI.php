<?php
require ../vendor/autoload.php';

use GuzzleHttp\Client;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$serial = $_POST['serial'];