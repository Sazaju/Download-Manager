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
		<?php displayWarning(); ?>
		
		<?php
			if(is_dir($filePath)) {
				echo "<h1><?php echo $title;?></h1>";
				
				$parentDirPath = dirname($filePath);
				$parentDirName = basename($parentDirPath);
				$parentMD5 = getMD5ChainForPath($parentDirPath, DOWNLOADS_DIR);
				$parentLink = "<p>Dossier parent : <a href='".($parentMD5 === "" ? "index.php'>liste de t&eacute;l&eacute;chargement" : "explore.php?md5=".$parentMD5."'>".$parentDirName)."</a></p>";
				
				echo "<h2>Contenu du dossier :</h2>";
				echo $parentLink;
				echo getDirectoryDescription($filePath);
				echo $parentLink;
			} else if (is_file($filePath)) {
				echo "<div class='navigator'>";
				
				$md5 = getMD5ChainForPath($filePath, DOWNLOADS_DIR);
				$downloadUrl = PAGE_DOWNLOAD.'?md5='.$md5;
				
				$parentDirPath = dirname($filePath);
				$parentMD5 = getMD5ChainForPath($parentDirPath, DOWNLOADS_DIR);
				$parentUrl = $parentMD5 === "" ? "index.php" : "explore.php?md5=".$parentMD5;
				$parentLink = "<a class='parent' href='$parentUrl' title='Retour au répertoire parent'>".ICON_PARENT."</a>";
				
				$files = array_filter(getContentOf($parentDirPath), function($f) use($parentDirPath) {return is_file("$parentDirPath/$f");});
				$currentIndex = array_search(basename($filePath), $files);
				$downloadLink = "<a class='download' href='$downloadUrl' title='T&eacute;l&eacute;charger'>".ICON_DOWNLOAD."</a>";
				
				if ($currentIndex == 0) {
					$firstLink = "<a class='first hidden'>".ICON_FIRST."</a>";
				} else {
					$firstPath = $parentDirPath."/".$files[0];
					$firstUrl = new Url();
					$firstUrl->setQueryVar('md5', getMD5ChainForPath($firstPath, DOWNLOADS_DIR));
					$firstLink = "<a class='first' href='$firstUrl' title='Premier'>".ICON_FIRST."</a>";
				}
				
				if ($currentIndex == 0) {
					$previousLink = "<a class='previous hidden'>".ICON_PREVIOUS."</a>";
				} else {
					$previousPath = $parentDirPath."/".$files[$currentIndex-1];
					$previousUrl = new Url();
					$previousUrl->setQueryVar('md5', getMD5ChainForPath($previousPath, DOWNLOADS_DIR));
					$previousLink = "<a class='previous' href='$previousUrl' title='Précédent'>".ICON_PREVIOUS."</a>";
				}
				
				if ($currentIndex == sizeof($files)-1) {
					$nextLink = "<a class='next hidden'>".ICON_NEXT."</a>";
				} else {
					$nextPath = $parentDirPath."/".$files[$currentIndex+1];
					$nextUrl = new Url();
					$nextUrl->setQueryVar('md5', getMD5ChainForPath($nextPath, DOWNLOADS_DIR));
					$nextLink = "<a class='next' href='$nextUrl' title='Suivant'>".ICON_NEXT."</a>";
				}
				
				if ($currentIndex == sizeof($files)-1) {
					$lastLink = "<a class='last hidden'>".ICON_LAST."</a>";
				} else {
					$lastPath = $parentDirPath."/".$files[sizeof($files)-1];
					$lastUrl = new Url();
					$lastUrl->setQueryVar('md5', getMD5ChainForPath($lastPath, DOWNLOADS_DIR));
					$lastLink = "<a class='last' href='$lastUrl' title='Dernier'>".ICON_LAST."</a>";
				}
				
				echo "<div class='links'>$firstLink$previousLink$parentLink$downloadLink$nextLink$lastLink</div>";
				echo "<div class='content'>";
				echo "$previousLink$nextLink";
				echo "<div class='file'>";
				if (is_image($filePath)) {
					echo get_HTML_picture($filePath);
				} else if (is_video($filePath)) {
					echo get_HTML_video($filePath);
				} else {
					$finfo = new finfo(FILEINFO_MIME_TYPE);
					$mimeType = $finfo->file($filePath);
					
					$fileName = basename($filePath);
					
					$linkName = htmlentities("Télécharger");
					echo "<p>Ce type de fichier (<code>$mimeType</code>) n'est pas géré. Cliquez ici pour le télécharger :</p>";
					echo "<a href='".$downloadUrl."' download='$fileName' title='$linkName'>$linkName</a>";
					
				}
				echo "</div>";
				echo "</div>";
				echo "</div>";
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
