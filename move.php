<?php
	require_once("common.php");
	
	$destination = null;
	$warning = array();
	
	/* EXISTING DIRECTORY */
	
	if (isset($_POST['select'])) {
		$destination = html_entity_decode($_POST['select']);
	}
	
	/* NEW DIRECTORY */
	
	else if (isset($_POST['new'])) {
		$dirName = $_POST['new'];
		$destination = DOWNLOADS_DIR."/".$dirName;
		if (!empty($dirName)) {
			mkdir($destination);
		} else {
			// poot at the root, no need to create it
		}
	}
	
	/* MOVING */
	if ($destination !== null) {
		$files = isset($_POST['files']) ? $_POST['files'] : null;
		if (empty($files)) {
			throw new Exception('No file selected.');
		}
		
		foreach($files as $path) {
			$newPath = $destination."/".fileName(html_entity_decode($path));
			rename($path, $newPath);
		}
?>
<html>
	<head>
		<title>D&eacute;placement de fichier</title>
		<meta http-equiv="refresh" content="0; URL=index.php<?php echo count($warning) > 0 ? "?warning=".urlencode(implode("<br/>", $warning)) : ""; ?>">
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	</head>
	<body>
	</body>
</html>
<?php
	}
	
	/* DISPLAY */
	
	else {
		$filePaths = array();
		if (isset($_GET["grouped"])) {
			if (isset($_POST['selection'])) {
				foreach($_POST['selection'] as $md5) {
					$filePaths[] = getPathForMD5Chain(DOWNLOADS_DIR, $md5);
				}
			} else {
				throw new Exception("No selection provided.");
			}
		} else {
			$filePaths[] = getPathFromURL();
		}
		
		$fileName = "";
		foreach($filePaths as $path) {
			$fileName .= fileName($path).' + ';
		}
		$fileName = substr($fileName, 0, strlen($fileName)-3);
		$dirPath = DOWNLOADS_DIR;
		$allFiles = getContentOf($dirPath);
		$dirs = array();
		foreach($allFiles as $file) {
			$path = $dirPath."/".$file;
			if (is_dir($path) && !in_array($path, $filePaths)) {
				array_push($dirs, $path);
			}
		}
		
		$title = TITLE." - D&eacute;placer ".$fileName;
		
		$formList = "";
		foreach($filePaths as $path) {
			$formList .= '<input type="hidden" name="files[]" value="'.htmlentities($path).'"/>';
		}
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
		
		<h2>R&eacute;pertoire de destination :</h2>
		<p>
			Vous vous appr&ecirc;tez &agrave; d&eacute;placer <b><?php echo $fileName;?></b>,
			dans quel r&eacute;pertoire souhaitez-vous le placer ?
		</p>
		<form method='POST' enctype="multipart/form-data">
			<?php echo $formList;?>
			<label>
				Existant : 
				<select name="select">
					<?php
						foreach($dirs as $dirPath) {
							echo '<option value="'.htmlentities($dirPath).'">'.fileName($dirPath).'</option>';
						}
					?>
				</select>
			</label>
			<input type="submit" value="D&eacute;placer"/>
		</form>
		<form method='POST' enctype="multipart/form-data">
			<?php echo $formList;?>
			<label>
				Nouveau : <input type="text" name="new" />
			</label>
			<input type="submit" value="D&eacute;placer"/>
		</form>
		
		<p>
			En cas de soucis ou pour toute question, vous pouvez toujours contacter l'administrateur &agrave; cette adresse e-mail :
			<a href="mailto:<?php echo ADMIN_MAIL; ?>"><?php echo ADMIN_MAIL; ?></a>.
		</p>
	</body>
</html>
<?php
	}
?>