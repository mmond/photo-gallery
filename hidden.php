<?
print '<font size="5" color="#5571B0">Hidden Files</font><br><br>';
$home = $_SERVER['DOCUMENT_ROOT']."/pics/";
hidden_search($home);

function secret_search($home) {
	$dp = opendir($home);
	while ($dir = readdir($dp)) {
		if (is_dir($home.$dir) and $dir !== "." and $dir !== "..") {
			if($dir == "secret") {
				$gallerylink = substr($home.$dir, 37);
				$galleryname = substr($home, 31);
				print '<a href="SERVERNAME/?gallerylink='. $gallerylink .'">'. $galleryname .'</a><br>';
			}
			hidden_search($home.$dir."/");
		}
	} 
}
?>