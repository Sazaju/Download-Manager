<?php
	require_once('Torrent.php');
	require_once('config.php');
	
	/*******\
	CONSTANTS
	\*******/
	
	define("ADMIN_MAIL", "sazaju@gmail.com");
	define("TITLE", "Torrents ".ucfirst(OWNER));
	
	$constants = array(
		'explore' => array(
			'page' => 'explore.php',
			'icon' => 'http://upload.wikimedia.org/wikipedia/commons/d/d6/Yellow_folder_icon_open.png',
		),
		'download' => array(
			'page' => 'download.php',
			'icon' => 'http://upload.wikimedia.org/wikipedia/commons/5/50/FDM_icon.png',
		),
		'delete' => array(
			'page' => 'delete.php',
			'icon' => 'http://upload.wikimedia.org/wikipedia/commons/2/21/Farm-Fresh_bin_closed.png',
		),
		'rename' => array(
			'page' => 'rename.php',
			'icon' => 'http://upload.wikimedia.org/wikipedia/commons/1/17/Farm-Fresh_textfield_rename.png',
		),
		'move' => array(
			'page' => 'move.php',
			'icon' => 'http://upload.wikimedia.org/wikipedia/commons/2/2b/Farm-Fresh_move_to_folder.png',
		),
		'zip' => array(
			'page' => 'zipUnzip.php',
			'icon' => 'http://upload.wikimedia.org/wikipedia/commons/4/40/Farm-Fresh_compress.png',
		),
		'unzip' => array(
			'page' => 'zipUnzip.php',
			'icon' => 'http://upload.wikimedia.org/wikipedia/commons/f/f0/Farm-Fresh_winrar_extract.png',
		),
	);
	foreach($constants as $key => $data) {
		define("PAGE_".strtoupper($key), $data['page']);
		define("ICON_".strtoupper($key), "<img class='icon' src='".$data['icon']."'/>");
	}
	unset($constants);
	
	/*******\
	FUNCTIONS
	\*******/
	
	function delete($path) {
		if (is_file($path)) {
			$success = unlink($path);
			if (!$success) {
				debug_print_backtrace();
				die("Impossible de supprimer le fichier demand&eacute; : ".$path);
			}
		}
		else if (is_dir($path)) {
			foreach(getContentOf($path) as $file) {
				delete($path.DIRECTORY_SEPARATOR.$file);
			}
			$success = rmdir($path);
			if (!$success) {
				debug_print_backtrace();
				die("Impossible de supprimer le dossier demand&eacute; : ".$path);
			}
		}
		else {
			debug_print_backtrace();
			die("Chemin invalide : ".$relativeFilePath);
		}
	}
	
	function displayWarning() {
		if (isset($_GET["warning"])) {
			echo "<p class='warning'>".urldecode($_GET["warning"])."</p>";
		}
	}
	
	function fileName($filePath)
	{
		return array_pop(explode("/", $filePath));
	}
	
	function format_length($size) {
		$units = array("", "Ki", "Mi", "Gi", "Ti", "Pi");
		$unitIndex = 0;
		while($size > 1024 && $unitIndex < count($units)) {
			$size /= 1024;
			$unitIndex++;
		}
		$result = round($size, 2)." ".$units[$unitIndex]."o";
		unset($units);
		unset($unitIndex);
		return $result;
	}
	
	function getContentOf($dirPath, $sortResult = true) {
		if (!is_dir($dirPath)) {
			debug_print_backtrace();
			die("L'emplacement renseign&eacute; n'est pas un dossier : ".$dirPath);
		}
		$dir = opendir($dirPath);
		if (!$dir) {
			debug_print_backtrace();
			die("Impossible d'ouvrir le dossier : ".$dirPath);
		}
		$files = array();
		while (($file = readdir($dir)) !== false) {
			if ($file != "." && $file != "..") {
				array_push($files, $file);
			}
		}
		closedir($dir);
		unset($dir);
		
		if ($sortResult) {
			natcasesort($files);
		}
		
		return $files;
	}
	
	function getDirectoryDescription($dirPath) {
		if (!is_dir($dirPath)) {
			debug_print_backtrace();
			die("L'emplacement de t&eacute;l&eacute;chargement renseign&eacute; n'est pas un dossier : ".$dirPath);
		}
		
		//get the files of the directory
		$files = array();
		foreach(getContentOf($dirPath) as $file) {
			$files[$file]['file'] = $dirPath.DIRECTORY_SEPARATOR.$file;
			$files[$file]['torrent'] = null;
		}
		
		if ($dirPath == TORRENTS_DIR) {
			//recognize files as torrents
			$temp = array();
			foreach($files as $name => $fileDescription) {
				$path = $fileDescription['file'];
				$torrent = new Torrent($path);
				$temp[$torrent->name()]['torrent'] = $torrent;
				$temp[$torrent->name()]['file'] = null;
			}
			$files = $temp;
			unset($temp);
			
			//look for the downloaded files
			foreach(getContentOf(DOWNLOADS_DIR) as $file) {
				$downloadPath = DOWNLOADS_DIR.DIRECTORY_SEPARATOR.$file;
				$files[$file]['file'] = $downloadPath;
				if (!isset($files[$file]['torrent'])) {
					$files[$file]['torrent'] = null;
				}
			}
			
			//natural sorting on the downloaded file names
			uksort($files, 'strnatcasecmp');
		}
		else if (strpos($dirPath, DOWNLOADS_DIR) === 0) {
			//look for the file or root directory downloaded by torrent
			$downloadName = "";
			$parentDir = $dirPath;
			while($parentDir != DOWNLOADS_DIR) {
				$downloadName = basename($parentDir);
				$parentDir = dirname($parentDir);
			}
			
			//look if there is a torrent for this download
			foreach(getContentOf(TORRENTS_DIR) as $file) {
				$torrentPath = TORRENTS_DIR.DIRECTORY_SEPARATOR.$file;
				$torrent = new Torrent($torrentPath);
				if ($torrent->name() == $downloadName) {
					foreach($files as $name => $fileDescription) {
						$files[$name]['torrent'] = $torrent;
					}
					break;
				}
			}
		}
		
		$description = "<p>
							<span class='warning'>ATTENTION :</span>
							Certains fichiers peuvent appara&icirc;tre &agrave; 100% alors qu'ils sont en cours de t&eacute;l&eacute;chargement (et donc incomplets).
							Si cela arrive, attendez simplement que la progression disparaisse, cela garantit que plus rien n'est en cours.
							En cas de doute, n'h&eacute;sitez pas &agrave; <a href='mailto:".ADMIN_MAIL."'>contacter l'administrateur</a>.
						</p>";
		if (count($files) > 0) {
			//open table
			$description .= "<table>";
			$description .= "<tr class='header'>
								<td>Fichier</td>
								<td>Taille</td>
								<td>Actions</td>
							</tr>";
			
			//start filling
			clearstatcache();
			foreach ($files as $fileName => $fileDescription) {
				$filePath = $fileDescription['file'];
				$torrent = $fileDescription['torrent'];
				
				//generate data
				$hasTorrent = $torrent != null;
				$hasDownload = $filePath != null;
				$isRootDownload = $hasDownload && dirname($filePath) == DOWNLOADS_DIR;
				$isDir = $hasDownload && is_dir($filePath);
				$isCompressed = isCompressedArchive($filePath);
				$MD5 = $filePath != null ? getMD5ChainForPath($filePath, DOWNLOADS_DIR) : null;
				$MD5Arg = $MD5 != null ? "md5=".$MD5 : "";
				
				//format data for columns
				$fileCol = $fileName;
				if($isDir) {
					$fileCol = "<a href='".PAGE_EXPLORE."?".$MD5Arg."'>".ICON_EXPLORE.$fileCol."</a>";
				}
				
				$sizeCol = "";
				$isCompleted = false;
				$realSize = getSize($filePath);
				if ($hasTorrent) {
						$relativePath = substr($filePath, strpos($filePath, $torrent->name()));
						$calculatedSize = 0;
						$dirsToInclude = array();
						foreach($torrent->content() as $fileRelativePath => $fileLength) {
							if (strpos($fileRelativePath, $relativePath) === 0) {
								$dir = substr($fileRelativePath, strpos($fileRelativePath, basename($filePath)));
								while($dir != dirname($dir)) {
									$dir = dirname($dir);
									array_push($dirsToInclude, $dir);
								}
								$calculatedSize += $fileLength;
							}
						}
						$dirsToInclude = array_filter(array_unique($dirsToInclude), "notRecursiveDirectory");
						$calculatedSize += filesize(dirname($filePath)) * count($dirsToInclude);
						
						if ($calculatedSize < $realSize) {
							$calculatedSize = $realSize;
							$percent = "?";
						}
						else {
							$percent = !$hasDownload ? 0 : floor(100 * $realSize / $calculatedSize);
						}
						$sizeCol = format_length($calculatedSize)." (".$percent."%)";
						$isCompleted = $percent == 100;
				}
				else {
					$sizeCol = format_length($realSize);
					$isCompleted = true;
				}
				
				$actionCol = "";
				if ($hasDownload && !$isDir && $realSize > 0) {
					$actionCol .= " <a href='".PAGE_DOWNLOAD."?".$MD5Arg."' title='T&eacute;l&eacute;charger' ".($isCompleted ? "" : "onclick='return(confirm(\"Le fichier est incomplet, voulez-vous quand m&ecirc;me le t&eacute;l&eacute;charger ?\"));'").">".ICON_DOWNLOAD."</a>";
				}
				if ($isDir) {
					$actionCol .= " <a href='".PAGE_ZIP."?".$MD5Arg."' title='Compresser' onclick='return(confirm(\"Compresser ".$fileName." et tout sont contenu ?\"));'>".ICON_ZIP."</a>";
				}
				if ($isCompressed && $isCompleted) {
					$actionCol .= " <a href='".PAGE_UNZIP."?".$MD5Arg."' title='D&eacute;compresser' onclick='return(confirm(\"D&eacute;compresser ".$fileName." ?\"));'>".ICON_UNZIP."</a>";
				}
				if (!$hasTorrent) {
					$id = "ren".$MD5;
					$actionCol .= " <a href='".PAGE_RENAME."?".$MD5Arg."' title='Renommer' id='".$id."' onclick='"
										."oldName = \"".htmlentities($fileName, ENT_QUOTES | ENT_IGNORE, 'UTF-8')."\";"
										."newName = prompt(\"Nouveau nom :\", oldName);"
										."if (newName != oldName && newName != null && newName != \"\") {"
											."document.getElementById(\"".$id."\").href = document.getElementById(\"".$id."\").href + \"&name=\" + encodeURIComponent(newName);"
											."return(true);"
										."} else {"
											."return(false);"
										."}"
									."'>".ICON_RENAME."</a>";
					$actionCol .= " <a href='".PAGE_MOVE."?".$MD5Arg."' title='D&eacute;placer'>".ICON_MOVE."</a>";
					$actionCol .= " <a href='".PAGE_DELETE."?".$MD5Arg."' title='Supprimer' onclick='return(confirm(\"Supprimer ".$fileName.($isDir ? " et tout sont contenu" : "")." ?\"));'>".ICON_DELETE."</a>";
				}
				
				$selectCol = "<input type='checkbox' name='selection[]' value='".$MD5."'>";
				
				//place data in the table
				$description .= "<tr class='row".($hasTorrent ? $isCompleted ? "-complete" : "-incomplete" : "")."'>";
					$description .= "<td>".$fileCol."</td>";
					$description .= "<td>".$sizeCol."</td>";
					$description .= "<td><center>".$actionCol."</center></td>";
				$description .= "</tr>";
			}
			
			//close table
			$description .= "</table>";
		} else {
			$description .= "<p><i>Aucun fichier, ajoutez de nouveaux torrents ci-dessus pour qu'ils soient t&eacute;l&eacute;charg&eacute;s.</i></p>";
		}
		
		unset($files);
		return $description;
	}
	
	function getMD5ChainForPath($filePath, $originPath) {
		$filePath = realpath($filePath);
		$originPath = realpath($originPath);
		if (strpos($filePath, $originPath) === false) {
			debug_print_backtrace();
			die("Les chemins ne correspondent pas : <i>".$filePath."</i> n'a pas l'origine <i>".$originPath."</i>");
		}
		if ($filePath == $originPath) {
			return "";
		}
		else {
			$dirPath = dirname($filePath);
			$file = fileName($filePath);
			$md5Chain = getMD5ChainForPath($dirPath, $originPath);
			$md5Parts = explode("-", $md5Chain);
			array_push($md5Parts, md5($file));
			$md5Parts = array_filter($md5Parts, "notNull");
			return count($md5Parts) > 1 ? implode("-", $md5Parts) : array_pop($md5Parts);
		}
	}
	
	function getPathForMD5Chain($dirPath, $md5Chain) {
		if ($md5Chain == "") {
			return $dirPath;
		}
		else {
			$md5Parts = explode("-", $md5Chain);
			$md5 = array_shift($md5Parts);
			$files = getContentOf($dirPath);
			foreach($files as $file) {
				if (md5($file) == $md5) {
					$filePath = $dirPath.DIRECTORY_SEPARATOR.$file;
					return getPathForMD5Chain($filePath, implode("-", $md5Parts));
				}
			}
			return null;
		}
	}
	
	function getPathFromURL() {
		if (isset($_GET["md5"])) {
			$md5Chain = $_GET["md5"];
			$filePath = getPathForMD5Chain(DOWNLOADS_DIR, $md5Chain);
			if ($filePath === null) {
				debug_print_backtrace();
				die("MD5 ne correspondant &agrave; aucun fichier/dossier : ".$md5Chain);
			}
			$parentPath = realpath(DOWNLOADS_DIR);
			$realPath = realpath($filePath);
			$fileName = basename($filePath);
			$relativeFilePath = substr($filePath, strlen($parentPath));
			if (!file_exists($filePath) || substr($realPath, 0, strlen($parentPath)) != $parentPath) {
				debug_print_backtrace();
				die("Chemin inaccessible : ".$relativeFilePath);
			}
			else {
				return $filePath;
			}
		} else {
			debug_print_backtrace();
			die("La cha&icirc;ne MD5 est manquante, <a href='mailto:".ADMIN_MAIL."'>contactez l'administrateur</a>.");
		}
	}
	
	function getSize($path) {
		if ($path === null) {
			return 0;
		}
		else if (PHP_OS == "Linux") {
			$cmd = 'du -s --block-size=1 "'.$path.'"';
			// echo $cmd;
			$result = (float) exec($cmd);
			return $result;
		}
		else if (is_file($path)) {
			//filesize() gives complete size, what is false if the file is
			// not complete (in downloading process for example)
			return sprintf("%u", filesize($path));
		}
		else if (is_dir($path)) {
			$size = 0;
			foreach(getContentOf($path, true) as $file){
				$size += getSize($path.DIRECTORY_SEPARATOR.$file);
			}
			return $size;
		}
		else {
			debug_print_backtrace();
			die("Chemin invalide : ".$path);
		}
	}
	
	function isCompressedArchive($filePath) {
		return is_zip($filePath) || is_rar($filePath);
	}
	
	function isMegauploadLink($link) {
		return preg_match('#^http://www\\.megaupload\\.com/\\?d=[a-z0-9]+$#i', $link) > 0;
	}
	
	function is_rar($filePath) {
		if (class_exists('RarArchive')) {
			$rar = RarArchive::open($filePath);
			$isRar = $rar !== FALSE;
			if ($isRar) {
				$rar->close();
			}
			return $isRar;
		} else {
			return false;
		}
	}
	
	function is_torrent($filePath) {
		$torrent = new Torrent($filePath);
		$isAccepted = $torrent->errors() ? false : true;
		if (!$isAccepted) {
			die("erreurs : <pre>".print_r($torrent->errors(), true)."</pre>");
		}
		$isWellFormed = $torrent->name() != null && $torrent->announce() != null;
		return $isAccepted && $isWellFormed;
	}
	
	function isWebLink($link) {
		return preg_match('#^(https?|ftp):/(/[a-z0-9:%!-_.\\[\\]]*)+$#i', $link) > 0;
		
	}
	
	function is_zip($filePath) {
		$zip = new ZipArchive();
		$isZip = $zip->open($filePath) === TRUE;
		if ($isZip) {
			$zip->close();
		}
		return $isZip;
	}
	
	function nameWithoutExtension($name) {
		$split = explode(".", $name);
		$split = array_filter($split, "notEmpty");
		if (count($split) > 1) {
			array_pop($split);
		}
		return implode(".", $split);
	}
	
	function notEmpty($a) {
		return !empty($a);
	}
	
	function notNull($var)
	{
		return($var != null);
	}
	
	function notRecursiveDirectory($dir) {
		return $dir != dirname($dir);
	}
	
	function smartReadFile($location, $filename, $mimeType='application/octet-stream') {
		if(!file_exists($location)) {
			header ('HTTP/1.0 404 Not Found');
			return;
		}

		$size = filesize($location);
		$time = date('r', filemtime($location));

		$fm = @fopen($location, 'rb');
		if(!$fm) {
			header('HTTP/1.0 505 Internal server error');
			return;
		}

		$begin = 0;
		$end = $size;

		if(isset($_SERVER['HTTP_RANGE'])) {
			if(preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches)) {
				$begin = intval($matches[1]);
				if(!empty($matches[2])) {
					$end = intval($matches[2]);
				}
			}
		}

		header('HTTP/1.0 '.($begin > 0 || $end < $size ? '206 Partial Content' : '200 OK')); 
		header('Content-Type: '.$mimeType);
		header('Cache-Control: public, must-revalidate, max-age=0');
		header('Pragma: no-cache'); 
		header('Accept-Ranges: bytes');
		header('Content-Length:'.($end-$begin));
		header('Content-Range: bytes '.$begin.'-'.$end.'/'.$size);
		header('Content-Disposition: inline; filename="'.$filename.'"');
		header('Content-Transfer-Encoding: binary\n');
		header('Last-Modified: '.$time);
		header('Connection: close'); 

		$cur = $begin;
		fseek($fm, $begin, 0);

		$step = 1024*16;
		while(!feof($fm) && $cur < $end && connection_status() == 0) {
			print fread($fm, min($step, $end - $cur));
			flush();
			$cur += $step;
		}
		fclose($fm);
	}
	
?>
