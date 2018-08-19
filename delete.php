<?php
	require_once("common.php");
	
	$confirmed = isset($_POST['confirmed']);
	
	/* DELETING */
	
	if ($confirmed) {
		$referer = $_POST['referer'];
		$ids = isset($_POST['ids']) ? $_POST['ids'] : null;
		if (empty($ids)) {
			throw new Exception('No file selected.');
		}
		
		foreach($ids as $md5) {
			delete(getPathForMD5Chain(DOWNLOADS_DIR, $md5));
		}
?>
<html>
	<head>
		<title>Suppression de fichier</title>
		<meta http-equiv="refresh" content="0; URL=<?php echo $referer; ?>">
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	</head>
	<body>
	</body>
</html>
<?php
	}
	
	/* DISPLAY */
	
	else {
		$fileIds = array();
		if (isset($_GET["grouped"])) {
			if (isset($_POST['selection'])) {
				foreach($_POST['selection'] as $md5) {
					$fileIds[] = $md5;
				}
			} else {
				throw new Exception("No selection provided.");
			}
		} else {
			$fileIds[] = $_GET['md5'];
		}
		
		$fileName = "";
		foreach($fileIds as $md5) {
			$fileName .= fileName(getPathForMD5Chain(DOWNLOADS_DIR, $md5)).' + ';
		}
		$fileName = substr($fileName, 0, strlen($fileName)-3);
		
		$title = TITLE." - Supprimer ".$fileName;
		
		$formList = "";
		foreach($fileIds as $md5) {
			$formList .= '<input type="hidden" name="ids[]" value="'.$md5.'"/>';
		}
		$formList .= '<input type="hidden" name="confirmed"/>';
		$formList .= '<input type="hidden" name="referer" value="'.$_SERVER['HTTP_REFERER'].'"/>';
?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $title;?></title>
		<link rel="stylesheet" media="screen" type="text/css" title="Style" href="style.css" />
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	</head>
	<body>
		<h1><?php echo $title;?></h1>
		
		<?php displayWarning(); ?>
		
		<p>
			Souhaitez-vous vraiment supprimer ces fichiers ?
		</p>
		<form method='POST' enctype="multipart/form-data">
			<?php echo $formList;?>
			<input type="submit" value="Supprimer"/>
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