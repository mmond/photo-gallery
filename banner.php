<?
$gall = $_GET[gallerylink];

session_start();
if (!session_is_registered("valid_user")) {
	header("location: http://austinauts.com/tools/gallery/login.php?gallerylink=$gall");
}

print "<h2>You are logged in as: $_SESSION[valid_user]</h2>";

print "<br><br>";
?>

<html>
<body 
<? 
if (isset($_POST[header])) {
	print $_POST[header].$_POST[gallerylink].$_POST[text]   ;   
	print '<BODY onLoad="opener.window.location.reload();self.close();return false;" ' ;
}
?> >

<?

if (isset($_POST[header])) {
	$file = $_SERVER[DOCUMENT_ROOT].'/pics'. $_POST[gallerylink].'/banner.txt';
	$fp = fopen($file, "w");
	$banner = 	'<font size="6">'.htmlspecialchars(stripslashes($_POST[header]), ENT_QUOTES).'</font><br>
	<end header>';
    
    if (strlen($_POST[text]) > 0) { 
        $banner .= '    
            <begin text content> 
            
            '.nl2br(htmlspecialchars(stripslashes($_POST[text]), ENT_QUOTES)).'<br>';
    }
	
    fwrite($fp, $banner);
    fclose($fp);
}
?>
<font size="6">Header</font><br>
<form method="POST" action="banner.php">
<input type="text" name="header" size="50"><br>

Content<br>
<textarea rows="10" cols="40" name="text" size="10">
</textarea><br>
<br>

<b>File to be created or overwritten is: <? print $_SERVER[DOCUMENT_ROOT].'/pics'. $gall.'/banner.txt' ?><b><br><br>
<input type="hidden" name="gallerylink" value="<? print $gall ?>">
<input type="reset" value="Clear">
<input type="submit" value="Write to file: ">
</form>
</body>
</html>

