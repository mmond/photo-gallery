<?
// Map short variables globals=off version's long syntax
$dir = "tools/gallery";
$gallery = $_GET['gallerylink'];
$src = $_GET['src'];
$w = $_GET['w'];
if (!isset($dir)) $dir = ".";

if (isset($src)) {		// Trim the filename off the end of the src link and ../.. off the beginning
	$lastslash =  strrpos($src, "/");
	$gallery =  substr($src, 11, $lastslash - 10);   
}

//  consider ".." in path an attempt to read dirs outside gallery, so redirect to gallery root
if (strstr($gallery, "..")) $gallery = "";

//  Display the Banner 
if (file_exists($_SERVER['DOCUMENT_ROOT']."/pics/".$gallery."/banner.txt")) {
	print '<td width="100%" align="center">';
    include($_SERVER['DOCUMENT_ROOT']."/pics/".$gallery."/banner.txt");
	print '<br></td>';
}

print '<tr valign="top"><td align="center">';
print '<b><a href="?gallerylink=" >Gallery Home</a></b>';

if ($gallery == "") {
	$gallery =  "";
} else {   	//  If $gallerylink is set and not "" then....
	
	//  Build the full gallery path into an array
	$gallerypath =  explode("/", $gallery);
	
	//  Render the Up directory links
	foreach ($gallerypath as $key => $level) {
		$parentpath = $parentpath . $level ;
		//  Unless it is the current directory
		if ($key < count($gallerypath) - 1) {
			print '<b> / <a href="?gallerylink='. $parentpath .'" >'. $level .'</a></b>';
		}  else {
			//  In that case render the current gallery name, but don't hyperlink
			print "<b> / $level</b>";
		}
		$parentpath = $parentpath . "/";
	}
}

print '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=".?content=tools/gallery/zip.php&gallerylink=' . $gallery . '" title="Download a zipped archive of all photos in this gallery">-zip-</a>
</table><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

// Create the arrays with the dir's media files
$dp = opendir($_SERVER['DOCUMENT_ROOT']."/pics/".$gallery);
while ($filename = readdir($dp)) {
	if (!is_dir($_SERVER['DOCUMENT_ROOT']."/pics/".$gallery. "/". $filename))  {  // If it's a file, begin
			$pic_types = array("JPG", "jpg", "GIF", "gif", "PNG", "png"); 		
			if (in_array(substr($filename, -3), $pic_types)) $pic_array[] = $filename;							// If it's a picture, add it to thumb array
			else {
				$movie_types = array("AVI", "avi", "MOV", "mov", "MP3", "mp3", "MP4", "mp4");								
				if (in_array(substr($filename, -3), $movie_types)) $movie_array[$filename] = size_readable(filesize($_SERVER['DOCUMENT_ROOT']."/pics/".$gallery. "/". $filename)); 							// If it's a movie, add name and size to the movie array
			}						
	}
} 
if($pic_array) sort($pic_array);  

//print the movie items
if($movie_array) {
	print "Movies:&nbsp;&nbsp;";
	foreach ($movie_array as $filename => $filesize) {
		print  '
			<a href="tools/gallery/source.php?avi=../../pics/'. $parentpath.$subdir.$filename. '" title="Movies may take much longer to download.  This file size is '. $filesize .'">'	.$filename.'</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	}
}
closedir($dp);

//  If this is a gallery marked hidden, link to the index of other galleries marked hidden
if($level == "hidden") print '<a href="./?content=tools/gallery/hidden.php">- Index of all hidden galleries - </a><br>';
	
print 'Sub Galleries&nbsp;&nbsp;/&nbsp;&nbsp;';
//  Render the Subdirectory links
$dp = opendir($_SERVER['DOCUMENT_ROOT']."/pics/".$gallery);

//  If the subdir is not a unix marker or set as hidden, enter it into the array
while ($subdir = readdir($dp)) {
	if (is_dir($_SERVER['DOCUMENT_ROOT']."/pics/".$gallery. "/". $subdir) && $subdir !="thumb_cache" && $subdir != "." && $subdir != ".." && !strstr($subdir, "hidden")) {
		$subdirs[] = $subdir;
	}
}

if($subdirs) {
	sort($subdirs);	
	foreach ($subdirs as $key => $subdir) {
		print  '
<a href="?gallerylink='. $parentpath.$subdir. '" >'	.$subdir.'</a> /     ';
	}
}
closedir($dp);
print '</b>
<br><br>

<table border="0" cellpadding="0" cellspacing="0">';



//  Render the gallery view, and links
if (!isset($src) && isset($pic_array)) {
	if ($gallery == "")  $w=700;            //  If it is the root gallery, display that single picture larger
	$column = 0;
	print '<tr align="top">';
	foreach ($pic_array as $filename) {		//  Use the pic_array to assign the links and img src
											//  If it is a jpeg include the exif rotation logic
       		if(strstr($filename, ".JPG")) print '
				<td valign="top"><a href="?src=../../pics/'.$gallery. "/" .$filename.'"><img src="'. $dir .'/jpeg_rotate.php?src=../../pics/'.$gallery. "/". $filename.'&w=' .$w. '"></a></td>'; 
   			else print '
				<td valign="top"><a href="?src=../../pics/'.$gallery. "/" .$filename.'"><img src="'. $dir .'/thumb.php?src=../../pics/'.$gallery. "/". $filename.'&w=' .$w. '"></a></td>';    
		$column++;
 		if ( $column == 6 ) {
			$column = 0;
          		print '</tr><tr>
';
		}            	
	} 
} else {	

// Render the 700 pixel wide version, link to original, last/next picture, and link to parent gallery
	if (!strstr($src, "../pics/")) die;     //  If "../pics" is not in path it may be an attempt to read files outside gallery, so redirect to gallery root
	$filename = substr($src, $lastslash + 1);
	$before_filename = $pic_array[array_search($filename, $pic_array) - 1 ];
	$after_filename = $pic_array[array_search($filename, $pic_array) + 1 ];

	// Display the before thumb
	if ($before_filename) {
		//  If it is a jpeg include the exif rotation logic
		if(strstr($before_filename, ".JPG")) print '<td align="center"><a href="?src=../../pics/' . $gallery.$before_filename .'">...<img src="'. $dir .'/jpeg_rotate.php?src=../../pics/' .$gallery.$before_filename .'"></a></td>';
		else print '<td align="center"><a href="?src=../../pics/' . $gallery.$before_filename .'">...<img src="'. $dir .'/thumb.php?src=../../pics/' .$gallery.$before_filename .'"></a></td>';
	}
	//  Display the current/websize pic
	//  If it is a jpeg include the exif rotation logic
	if(strstr($src, ".JPG")) print '<td><a href="tools/gallery/source.php?pic=' . $src . '"><img src="./'. $dir .'/jpeg_rotate.php?src='. $src. '&w=700"></a></td>';
		else print '<td><a href="tools/gallery/source.php?pic=' . $src . '"><img src="./'. $dir .'/thumb.php?src='. $src. '&w=700"></a></td>';

	// Display the after thumb
	if ($after_filename) {
		if(strstr($after_filename, ".JPG")) print '<td align="center"><a href="?src=../../pics/' . $gallery.$after_filename .'"><img src="'. $dir .'/jpeg_rotate.php?src=../../pics/' .$gallery.$after_filename .'">...</a></td></tr>';		
		else print '<td align="center"><a href="?src=../../pics/' . $gallery.$after_filename .'"><img src="'. $dir .'/thumb.php?src=../../pics/' .$gallery.$after_filename .'">...</a></td></tr>';
	}
}
print '</tr>';

function size_readable ($size, $retstring = null) {
        // adapted from code at http://aidanlister.com/repos/v/function.size_readable.php
        $sizes = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        if ($retstring === null) { $retstring = '%01.2f %s'; }
        $lastsizestring = end($sizes);
        foreach ($sizes as $sizestring) {
                if ($size < 1024) { break; }
                if ($sizestring != $lastsizestring) { $size /= 1024; }
        }
        if ($sizestring == $sizes[0]) { $retstring = '%01d %s'; } // Bytes aren't normally fractional
        return sprintf($retstring, $size, $sizestring);
}
?>