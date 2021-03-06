<?php
	require_once("common.php");
	
	$title = TITLE;
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
	<head>
		<title><?php echo $title;?></title>
		<link rel="stylesheet" media="screen" type="text/css" title="Style" href="style.css" />
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<script type="text/javascript" src="php.js"></script>
		<script type="text/javascript">
			function show(id) {
				if(document.getElementById) {
					x = document.getElementById(id);
					if(x.style.display == "none") {
						x.style.display = "";
					}
					else {
						x.style.display = "none";
					}
				}
			}
		</script>
	</head>
	<body>
		<h1><?php echo $title;?></h1>
		
		<?php displayWarning(); ?>
		<a href="#" onclick="show('addFile');return(false)"><h2>T&eacute;l&eacute;chargement de fichiers :</h2></a>
		<object id="addFile" style="display: none;">
			<form method='POST' action='upload.php' enctype="multipart/form-data">
				<label>
					Fichier torrent :
					<input type="file" name="torrents[]" multiple="multiple"/>
				</label>
				<input type="submit" value="Envoyer"/>
			</form>
			<form method='POST' action='upload.php' enctype="multipart/form-data">
				<label>
					Lien DDL : <span class="warning">(peut-&ecirc;tre long !)</span>
					<input type="text" name="direct" />
				</label>
				<input type="submit" value="T&eacute;l&eacute;charger"/>
			</form>
			<form method='POST' action='upload.php' enctype="multipart/form-data">
				<label>
					Lien Megaupload : <span class="warning">(peut-&ecirc;tre long !)</span>
					<input type="text" name="megaupload" />
				</label>
				<input type="submit" value="T&eacute;l&eacute;charger"/>
			</form>
			<p>Les fichiers sont ajout&eacute;s &agrave; la liste ci-dessous.</p>
		</object>
		
		<h2>Liste des t&eacute;l&eacute;chargements :</h2>
		<p>
			Tant que le t&eacute;l&eacute;chargement d'un torrent n'est pas fini, vous ne pouvez supprimer aucun de ses fichiers.
			Vous pouvez voir la progression du t&eacute;l&eacute;chargement &agrave; c&ocirc;t&eacute; de la taille (quand la progression dispara&icirc;t, le t&eacute;l&eacute;chargement est termin&eacute;).
			Il est possible que le t&eacute;l&eacute;chargement arrive &agrave; 100% et y reste pendant quelques temps.
			Si vous trouvez que cela dure trop longtemps, vous pouvez <a href="mailto:<?php echo ADMIN_MAIL; ?>">contacter l'administrateur</a> pour avoir plus d'informations.
		</p>
		<p>
			Vous utilisez actuellement <?php echo format_length(getSize(DOWNLOADS_DIR)); ?> (<?php echo format_length(disk_free_space(DOWNLOADS_DIR)); ?> restant).
		</p>
		<?php echo getDirectoryDescription(TORRENTS_DIR); ?>
		
		<p>
			En cas de soucis ou pour toute question, vous pouvez toujours contacter l'administrateur &agrave; cette adresse e-mail :
			<a href="mailto:<?php echo ADMIN_MAIL; ?>"><?php echo ADMIN_MAIL; ?></a>.
		</p>
	</body>
</html>
<?php gc_collect_cycles(); ?>
