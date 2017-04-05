<?php

ini_set('display_errors',true);
error_reporting (E_ALL);



session_start();
session_name('session');

define('APP_VALID', true);
require_once 'autoload.php';



$apiController = new wt\ApiController();

$apiController->run($_REQUEST);