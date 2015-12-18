<?php
	
	# 浮水印啟動
	define('ROOT_PATH', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
	include_once ROOT_PATH.'watermark/index.php';

	new WATERMARK($_GET["path"]);
?>