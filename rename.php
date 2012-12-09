<?php
	require_once("common.php");
	
	$filePath = getPathFromURL();
	$warning = array();
	
	if (isset($_GET['name'])) {
		$newName = urldecode($_GET['name']);
		$newPath = dirname($filePath)."/".$newName;
		rename($filePath, $newPath);
	}
	
?>

<html>
	<head>
		<title>Renommage de fichier</title>
		<meta http-equiv="refresh" content="0; URL=<?php echo $_SERVER['HTTP_REFERER']; ?>">
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	</head>
	<body>
	</body>
</html>
