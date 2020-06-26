<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

include_once 'vendor/autoload.php';

use Simcify\Application;

$app = new Application();
$app->route();
