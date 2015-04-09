<?php
ini_set('display_errors', 'On');
//ob_start("ob_gzhandler");
ob_start();
error_reporting(E_ALL);

// start the session
session_start();

 //database connection config
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '3310101764121';
$dbName = 'smart_invent';

// setting up the web root, server root and company's name

$thisFile = str_replace('\\', '/', __FILE__);
$docRoot = $_SERVER['DOCUMENT_ROOT'];
$webRoot  = str_replace(array($docRoot, 'library/config.php'), '', $thisFile);
$srvRoot  = str_replace('library/config.php', '', $thisFile);
define('WEB_ROOT', $webRoot);
define('SRV_ROOT', $srvRoot);
define("IMG_DIR",  WEB_ROOT."img/");
define("LIB_ROOT", SRV_ROOT."library/");
require_once 'database.php';
//include SRV_ROOT."library/class/company.php";
$under_zero = dbFetchArray(dbQuery("SELECT value FROM tbl_config WHERE config_name = 'ALLOW_UNDER_ZERO'"));
if($under_zero ==1){ $allow_under_zero = true; }else if($under_zero ==0){ $allow_under_zero = false; }else{ $allow_under_zero = false; }
define("ALLOW_UNDER_ZERO", $allow_under_zero);
$company = new company();
$company->getCompany();
define("COMPANY",$company->name);
function __autoload($class_name){
	include LIB_ROOT."class/".$class_name.".php";
}
if(!isset($_COOKIE['get_rows'])){
	setcookie("get_rows", 50,time()+(3600*24*365*30),'/');
}

?>