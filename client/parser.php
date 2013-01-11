<?php 
error_reporting(E_ALL);
ini_set("display_errors", true);
require_once("scrapy.php");
date_default_timezone_set("Asia/Shanghai");
$maxpda = new Scrapy();
$maxpda->parser();
?>