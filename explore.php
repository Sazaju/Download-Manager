<?php
	require_once("common.php");
	
	$dirPath = getPathFromURL();
	$title = TITLE." - ".basename($dirPath);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "DTD/xhtml1-strict.dtd">
<html>
	<head>
		<title><?php echo $title;?></title>
		<link rel="stylesheet" media="screen" type="text/css" title="Style" href="style.css" />
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	</head>
	<body>
		<h1><?php echo $title;?></h1>
		
		<?php displayWarning(); ?>
		
		<h2>Contenu du dossier :</h2>
		<?php
			$parentDirPath = dirname($dirPath);
			$parentDirName = basename($parentDirPath);
			$parentMD5 = getMD5ChainForPath($parentDirPath, DOWNLOADS_DIR);
			$parentLink = "<p>Dossier parent : <a href='".($parentMD5 === "" ? "index.php'>liste de t&eacute;l&eacute;chargement" : "explore.php?md5=".$parentMD5."'>".$parentDirName)."</a></p>";
			
			echo $parentLink;
			echo getDirectoryDescription($dirPath);
			echo $parentLink;
		?>
		
		<p>
			En cas de soucis ou pour toute question, vous pouvez toujours contacter l'administrateur &agrave; cette adresse e-mail :
			<a href="mailto:<?php echo ADMIN_MAIL; ?>"><?php echo ADMIN_MAIL; ?></a>.
		</p>
	</body>
</html>
