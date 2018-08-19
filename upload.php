<?php
	require_once("common.php");
	
	$warning = array();
	
	/* TORRENTS */
	
	if (isset($_FILES['torrents'])) {
		$files = $_FILES['torrents'];
		for($index = 0 ; $index < count($files['name']) ; $index ++) {
			$filename = $files['name'][$index];
			$downloadedFile = TORRENTS_DIR."/".$filename;
			if ($files['error'][$index] == UPLOAD_ERR_OK) {
				$uploadedFile = $files['tmp_name'][$index];
				if (!is_torrent($uploadedFile)) {
					array_push($warning, "Impossible de r&eacute;cup&eacute;rer le fichier <b>".$filename."</b>, ce n'est pas un fichier <i>torrent</i>.");
				} else if(file_exists($downloadedFile)) {
					array_push($warning, "Impossible de r&eacute;cup&eacute;rer le torrent <b>".$filename."</b>, il existe d&eacute;j&agrave; dans votre liste.");
				}
				else {
					move_uploaded_file($uploadedFile, $downloadedFile);
					if (!file_exists($downloadedFile)) {
						array_push($warning, "Impossible de r&eacute;cup&eacute;rer le torrent <b>".$filename."</b>, <a href='".ADMIN_MAIL."'>contactez l'administrateur</a>.");
					}
				}
			} else {
				array_push($warning, "Le fichier <b>".$filename."</b> a &eacute;t&eacute; mal transmis, veuillez r&eacute;essayer.");
			}
		}
	}
	
	/* DDL */
	
	if (isset($_POST['direct'])) {
		$link = $_POST['direct'];
		if (isWebLink($link)) {
			if (PHP_OS == "Linux") {
				$cmd = 'wget "'.$link.'"';
				$originDir = getcwd();
				chdir(DOWNLOADS_DIR);
				exec($cmd);
				chdir($originDir);
			}
			else {
				array_push($warning, "Fonctionnalit&eacute; non g&eacute;r&eacute;e sur les syst&egrave;mes ".PHP_OS);
			}
		}
		else {
			array_push($warning, "Le lien <b>'".$link."'</b> n'est pas un lien Internet valide.");
		}
	}
	
	/* MEGAUPLOAD */
	
	if (isset($_POST['megaupload'])) {
		$link = $_POST['megaupload'];
		if (isMegauploadLink($link)) {
			if (PHP_OS == "Linux") {
				$cmd = 'plowdown "'.$link.'"';
				$originDir = getcwd();
				chdir(DOWNLOADS_DIR);
				exec($cmd);
				chdir($originDir);
			}
			else {
				array_push($warning, "Fonctionnalit&eacute; non g&eacute;r&eacute;e sur les syst&egrave;mes ".PHP_OS);
			}
		}
		else {
			array_push($warning, "Le lien <b>'".$link."'</b> n'est pas un lien Megaupload valide.");
		}
	}
	
	/* E-HENTAI */
	
	if (isset($_POST['eh'])) {
		$link = $_POST['eh'];
		$matches = array();
		$isEHentaiLink = preg_match('#^https?://(?:exhentai|g\\.e-hentai)\\.org/g/([0-9]+)/([a-z0-9]+)/?$#i', $link, $matches);
		if ($isEHentaiLink === 1) {
			if (PHP_OS == "Linux") {
				$gid = $matches[1];
				$token = $matches[2];
				
				$clientId = "1465";
				$clientKey = "KstkyFm1a7UmFgeeUhY3";
				$clientSpec = "412021";
				$hash = 0;// TODO
				$request = "https://exhentai.org/archiver.php?gid=$gid&token=$token&or=$clientSpec--$hash#";
				
				array_push($warning, "Lien: $link");
				array_push($warning, "GID: $gid");
				array_push($warning, "Token: $token");
				array_push($warning, "Hash: $hash");
			}
			else {
				array_push($warning, "Fonctionnalit&eacute; non g&eacute;r&eacute;e sur les syst&egrave;mes ".PHP_OS);
			}
		}
		else if ($isEHentaiLink === 0) {
			array_push($warning, "Le lien <b>'".$link."'</b> n'est pas un lien EH valide.");
		}
		else if ($isEHentaiLink === FALSE) {
			array_push($warning, "Une erreur s'est produite. Réessayez ou contactez l'administrateur.");
		}
		else {
			throw new Exception("This case should not happen!");
		}
	}
?>

<html>
	<head>
		<title>Upload de fichier</title>
		<meta http-equiv="refresh" content="0; URL=index.php<?php echo count($warning) > 0 ? "?warning=".urlencode(implode("<br/>", $warning)) : ""; ?>">
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	</head>
	<body>
	</body>
</html> 
