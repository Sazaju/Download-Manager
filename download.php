<?php
	require_once("common.php");
	
	$filePath = getPathFromURL();
	smartReadFile($filePath, basename($filePath));
	exit;
?>