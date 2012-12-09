<?php
	require_once("common.php");
	
	delete(getPathFromURL());
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