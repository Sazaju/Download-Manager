<?php
	require_once("common.php");
	
	if (isset($_GET["grouped"])) {
		if (isset($_POST['selection'])) {
			$paths = array();
			foreach($_POST['selection'] as $md5) {
				delete(getPathForMD5Chain(DOWNLOADS_DIR, $md5));
			}
		} else {
			throw new Exception("No selection provided.");
		}
	} else {
		delete(getPathFromURL());
	}
?>

<html>
	<head>
		<title>Suppression de fichier</title>
		<meta http-equiv="refresh" content="0; URL=<?php echo $_SERVER['HTTP_REFERER']; ?>">
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	</head>
	<body>
	</body>
</html> 