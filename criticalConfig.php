<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
	<head>
		<title>Configuration initiale</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="Content-Language" content="fr" />
		<meta http-equiv="Content-Style-Type" content="text/css" />
		<meta name="DC.Language" scheme="RFC3066" content="fr" />
		<link rel="stylesheet" href="style.css" type="text/css" media="screen" title="Normal" />  
		<link rel="icon" type="image/gif" href="fav.gif" />
		<link rel="shortcut icon" href="fav.ico" />
		<style type="text/css">
			pre.code {
				text-align: left;
				border: 1px black solid;
				padding: 5px;
			}
			p {
				text-align: justify;
			}
		</style>
	</head>
	<body>
		<div id="main">
			<div id="page">
				<h1>Initialisation des données critiques</h1>
				<p>
					Le fichier <b><?php echo $criticalDataFile; ?></b> n'existe pas ou ses données ne sont pas correctes. Il vous faut donc le créer ou le corriger. Pour se faire, ouvrez un fichier portant ce nom à la racine du site (au même endroit que l'index) et remplissez-le selon ce modèle :
				</p>
				<pre class='code'>&lt;?php
/*
	This file contains customized data and should never be written
	in the repository of a version management system (ensure it
	is ignored).
*/
define("OWNER", "owner");
define("DOWNLOADS_DIR", "downloads");
define("TORRENTS_DIR", "torrents");
?&gt;</pre>
				
				<p>Pour toute question, contactez l'administrateur par mail: <a href='mailto:sazaju@gmail.com'>sazaju@gmail.com</a>.</p>
			</div>
		</div>
	</body>
</html>
