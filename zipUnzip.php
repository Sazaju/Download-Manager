<?php
	require_once("common.php");
	
	function doUnzip($path) {
		$zipName = basename($path);
		$dirName = nameWithoutExtension($zipName);
		$parentPath = dirname($path);
		
		$zip = new ZipArchive();
		$zip->open($path);
		if ($zipName != $dirName) {
			$destination = $parentPath.DIRECTORY_SEPARATOR.$dirName;
			if (!file_exists($destination)) {
				mkdir($destination);
				$zip->extractTo($destination);
			}
			else {
				$warning = "Impossible de d&eacute;compresser l'archive <b>".$zipName."</b>, un dossier du m&ecirc;me nom existe d&eacute;j&agrave;.";
			}
		}
		else {
			$warning = "Impossible de traiter le fichier <b>".$zipName."</b>, il n'a pas d'extension.";
		}
		$zip->close();
	}
	
	function doUnrar($path) {
		$rarName = basename($path);
		$dirName = nameWithoutExtension($rarName);
		$parentPath = dirname($path);
		
		$rar = RarArchive::open($path);
		if ($rarName != $dirName) {

			$destination = $parentPath.DIRECTORY_SEPARATOR.$dirName;
			if (!file_exists($destination)) {
				mkdir($destination);
				foreach($rar->getEntries() as $entry) {
					$entry->extract($destination);
				}
			}
			else {
				$warning = "Impossible de d&eacute;compresser l'archive <b>".$rarName."</b>, un dossier du m&ecirc;me nom existe d&eacute;j&agrave;.";
			}
		}
		else {
			$warning = "Impossible de traiter le fichier <b>".$rarName."</b>, il n'a pas d'extension.";
		}
		$rar->close();
	}
	
	function addDirContent($path, $zip, $innerDir = null) {
		foreach (getContentOf($path) as $file) {
			$filePath = $path.DIRECTORY_SEPARATOR.$file;
			$innerPath = ($innerDir == null ? "" : $innerDir.DIRECTORY_SEPARATOR).$file;
			if (is_dir($filePath)) {
				if (!$zip->addEmptyDir($innerPath)) {
					die("Impossible de cr&eacute;er le dossier : ".$innerPath);
				}
				addDirContent($filePath, $zip, $innerPath);
			} else if (is_file($filePath))  {
				if (!$zip->addFile($filePath, $innerPath)) {
					die("Impossible de rajouter le fichier : ".$innerPath." [".$filePath."]");
				}
			}
		}
	}
	
	function doZip($path) {
		$dirName = basename($path);
		$zipName = $dirName.".zip";
		$parentPath = dirname($path);
		$destination = $parentPath.DIRECTORY_SEPARATOR.$zipName;
		
		if (!file_exists($destination)) {
			$zip = new ZipArchive();
			if ($zip->open($destination, ZIPARCHIVE::CREATE) === true) {
				addDirContent($path, $zip);
				$zip->close();
			}
			else {
				$warning = "Impossible de cr&eacute;er une archive ZIP pour le dossier <b>".$dirName."</b>";
			}
		}
		else {
			$warning = "Impossible de cr&eacute;er l'archive <b>".$dirName."</b>, un fichier du m&ecirc;me nom existe d&eacute;j&agrave;.";
		}
	}
	
	function doZipGrouped($paths) {
		$dirName = "";
		foreach($paths as $path) {
			$dirName .= basename($path)."_";
		}
		$zipName = substr($dirName, 0, strlen($dirName)-1).".zip";
		$parentPath = dirname($paths[0]);
		$destination = $parentPath.DIRECTORY_SEPARATOR.$zipName;
		
		if (!file_exists($destination)) {
			$zip = new ZipArchive();
			if ($zip->open($destination, ZIPARCHIVE::CREATE) === true) {
				foreach($paths as $path) {
					addDirContent($path, $zip, basename($path).DIRECTORY_SEPARATOR);
				}
				$zip->close();
			}
			else {
				$warning = "Impossible de cr&eacute;er une archive ZIP <b>".$zipName."</b>";
			}
		}
		else {
			$warning = "Impossible de cr&eacute;er l'archive <b>".$zipName."</b>, un fichier du m&ecirc;me nom existe d&eacute;j&agrave;.";
		}
	}
	
	$warning = "";
	set_time_limit(0);
	if (isset($_GET["grouped"])) {
		if (isset($_POST['selection'])) {
			$paths = array();
			foreach($_POST['selection'] as $md5) {
				$paths[] = getPathForMD5Chain(DOWNLOADS_DIR, $md5);
			}
			doZipGrouped($paths);
		} else {
			throw new Exception("No selection provided.");
		}
	} else {
		$path = getPathFromURL();
		if (is_zip($path)) {
			doUnzip($path);
		}
		else if (is_rar($path)) {
			doUnrar($path);
		}
		else if (is_dir($path)) {
			doZip($path);
		}
		else {
			$warning = "Impossible de traiter le fichier <b>".basename($path)."</b>, ce n'est ni une archive ZIP/RAR ni un dossier.";
		}
	}
?>

<html>
	<head>
		<title>Compression/D&eacute;compression de fichier</title>
		<meta http-equiv="refresh" content="0; URL=<?php
			$origin = preg_replace("#(&|(\?))warning=[^&?]*#", "$2", $_SERVER['HTTP_REFERER']);
			$separator = strpos($origin, "?") === false ? "?" : "&";
			echo $origin.($warning ? $separator."warning=".urlencode($warning) : "");
		?>">
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	</head>
	<body>
	</body>
</html> 
