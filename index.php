<?php
/*

Created by Nigamanth Srivatsan
MIT License, https://github.com/nigamanthsrivatsan/easy-php-router

*/


require PATH_LIB . "core.php";

error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);
ini_set("log_errors", 0);
ini_set("error_log", "./assets/errors/error.log");

define("PATH_LIB", __DIR__ . DIRECTORY_SEPARATOR);
define("PATH_ROOT", dirname(PATH_LIB));
?>