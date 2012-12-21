<?php 
error_reporting(E_ALL);
ini_set("display_errors", true);
require_once("oabt.php");
date_default_timezone_set("Asia/Shanghai");
$maxpda = new Oabt();
$maxpda->run();
