<?php
	require_once("common.php");
	
	$filePath = getPathFromURL();
	$title = TITLE." - ".basename($filePath);
?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $title;?></title>
		<link rel="stylesheet" media="screen" type="text/css" title="Style" href="style.css" />
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<script src="scripts.js"></script>
	</head>
	<body>
		<h1><?php echo $title;?></h1>
		
		<?php displayWarning(); ?>
		
		<?php
			if(is_dir($filePath)) {
				$parentDirPath = dirname($filePath);
				$parentDirName = basename($parentDirPath);
				$parentMD5 = getMD5ChainForPath($parentDirPath, DOWNLOADS_DIR);
				$parentLink = "<p>Dossier parent : <a href='".($parentMD5 === "" ? "index.php'>liste de t&eacute;l&eacute;chargement" : "explore.php?md5=".$parentMD5."'>".$parentDirName)."</a></p>";
				
				echo "<h2>Contenu du dossier :</h2>";
				echo $parentLink;
				echo getDirectoryDescription($filePath);
				echo $parentLink;
			} else if (is_file($filePath)) {
				echo "<h2>Aperçu :</h2>";
				display_picture($filePath);
			} else {
				throw new Exception("Unmanaged resource: ".$filePath);
			}
		?>
		
		<p>
			En cas de soucis ou pour toute question, vous pouvez toujours contacter l'administrateur &agrave; cette adresse e-mail :
			<a href="mailto:<?php echo ADMIN_MAIL; ?>"><?php echo ADMIN_MAIL; ?></a>.
		</p>
	</body>
</html>
