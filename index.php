<?php
	session_start();
	
	define('HOME_PATH', realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR);
	define('ROOT_PATH', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
	include_once ROOT_PATH.'core.php';
?>