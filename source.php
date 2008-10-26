<?
// 	Choose the URL argument being passed to source.php
//	Build the content type link based on file type

if ($_GET['pic']) {
	$filename = $_GET['pic'];
	$len = filesize($filename);
	$lastslash =  strrpos($filename, "/");
	$name =  substr($filename, $lastslash + 1);   

	header("Content-type: image/jpeg;\r\n");
	header("Content-Length: $len;\r\n");
	header("Content-Transfer-Encoding: binary;\r\n");
	header('Content-Disposition: inline; filename="'.$name.'"');	//  Render the photo inline.
	readfile($filename);
} 
if ($_GET['zip']) {
	
	$path = "../../" . $_GET['zip'];
	$len = filesize($path);

	$lastslash =  strrpos($path, "/");
	$filename =  substr($path, $lastslash + 1);   
	
	header('Content-type: application/x-zip-compressed');
	header('Content-Length: $len');
	header('Content-Disposition: attachment; filename="' . $filename . '"');  // Create a download stream link
	readfile($path);	
}

if ($_GET['avi']) {
	$filename = $_GET['avi'];
	$len = filesize($filename);
	$lastslash =  strrpos($filename, "/");
	$name =  substr($filename, $lastslash + 1);   

	header("Content-type: video/x-msvideo;\r\n");
	header("Content-Length: $len;\r\n");
	header("Content-Transfer-Encoding: binary;\r\n");
	header('Content-Disposition: inline; filename="'.$name.'"');	//  Render the photo inline.
	readfile($filename);
}
?>