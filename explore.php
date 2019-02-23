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
				if (is_image($filePath)) {
					echo get_HTML_picture($filePath);
				} else if (is_video($filePath)) {
					echo get_HTML_video($filePath);
				} else {
					$finfo = new finfo(FILEINFO_MIME_TYPE);
					$mimeType = $finfo->file($filePath);
					
					$fileName = basename($filePath);
					
					$md5 = getMD5ChainForPath($filePath, DOWNLOADS_DIR);
					
					$title = htmlentities("Télécharger");
					echo "<p>Ce type de fichier (<code>$mimeType</code>) n'est pas géré. Cliquez ici pour le télécharger :</p>";
					echo "<a href='".PAGE_DOWNLOAD.'?md5='.$md5."' download='$fileName' title='$title'>$title</a>";
					
				}
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
