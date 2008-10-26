<?php
// justThumb.php - by Jack-the-ripper (c) Lars Ollén 2005
// Feel free to use this program if u like just remember to give credits =D
//
// This is just a small php program that just creates a thumbnail of a picture..
// it can use cached thumbs or not.
// I made this because i could not find anything that just made a thumbnail.
//
/*
 atributes
 src - source image
 w   - thumb width
 h   - thumb hight
 fcu - force cache usage,can be true or false, *not implemented*
 ver - only, show - adds justThumbs verion on the image. *only only is implemented at the moment*
       Only makes an image that only has the version this overrides src.
*/
// config variable
$useCache = true; // true if u want to use a cache
$cacheDir = "thumb_cache/"; // the cachedirectoryname, where the cachefiles will be
$cachePrefix = "thumbcache_"; // the cache for hej.jpg would bee hej_thumbcache.jpg in this case
$cacheQuality = 75; //quality is optional, and ranges from 0 (worst quality, smaller file) to 100 (best quality, biggest file). The default is the default IJG quality value (about 75).


//-----------------------
// don't edit below this
//-----------------------

global $_GET;

$srcFile = false; // src - source image
$thumbW  = false; // w   - thumb width
$thumbH  = false; // h   - thumb hight
$fcu     = false; // fcu - force cache usage,can be true or false
$ver     = false;
$width   = false;
$height  = false;
$type    = false;
$version = "0.9.1 beta";
define("__DEFAULTTHUMBW__", 175);
define("__MAXSIZE__", 940);
getTheGets();

if($ver == "only")
{
	$srcFile = false;
	$thumb = makeVersionOnly();
}
else
{
	if(is_file($srcFile))
	{
		loadInfo();
		$thumb = false;
		
		if($useCache)
		{
			$thumbFile = dirname($srcFile)."/".$cacheDir."/$cachePrefix".basename($srcFile,".jpg")."_w".$thumbW."h".$thumbH.".jpg";
			$thumb = loadImage($thumbFile);
			if($thumb !== false)
				$useCache = false;
		}
		
		if($thumb === false)
			$thumb = loadAndResize();
		
		if($useCache)
		{
			saveImage($thumb,$thumbFile,$cacheQuality);
		}
	}
	else
	{
		$thumb = makeErrorImg("File not found - " . basename($srcFile));
	}
}

// Rotate jpeg using exif information
include("rotate.php");
$thumb = imagerotate($thumb, $rotation, 0);

header('Content-type: image/jpeg');
@imagejpeg($thumb);
imagedestroy($thumb);


//----------------
// functions...
//----------------

	
	function getTheGets()
	{
		global $_GET, $srcFile, $thumbW, $thumbH, $fcu, $ver;
		
 	$srcFile = isset($_GET['src'])?($_GET['src']):false;

		if(isset($_GET['ver']))
		{
			if(strtolower($_GET['ver']) == "only")
				$ver = "only";
		}

// to be implented			$fcu = ($_GET['fcu']=="true" ? true:false);
		
		$thumbW = isset($_GET['w'])?($_GET['w']):false;
		$thumbH = isset($_GET['h'])?($_GET['h']):false;
		
		if(($thumbW > __MAXSIZE__ || ($thumbW <= 0)) || ($thumbH > __MAXSIZE__ || ($thumbH <= 0 && $thumbH !== false)))
		{
			$thumbW = __DEFAULTTHUMBW__;
			$thumbH = false;
		}

		
		if(!$thumbW && !$thumbH)
			$thumbW = __DEFAULTTHUMBW__;
	}
	

	function loadImageByType($filename,$type)
	{	
		switch($type)
		{
			case IMAGETYPE_GIF:
				return @imagecreatefromgif($filename);
			case IMAGETYPE_JPEG:
				return @imagecreatefromjpeg($filename);
			case IMAGETYPE_PNG:
				return @imagecreatefrompng($filename);
			default:
				return false;
		}
	}
	
	function makeErrorImg($msg)
	{
		$thumb  = imagecreate(120, 100); /* Create a blank image */
		$bgc = imagecolorallocate($thumb, 255, 255, 255);
		$tc  = imagecolorallocate($thumb, 0, 0, 0);
		imagefilledrectangle($thumb, 0, 0, 120, 30, $bgc);
		/* Output an errmsg */
		imagestring($thumb, 1, 5, 5, $msg, $tc);
		return $thumb;
	}
	
	function loadInfo()
	{
		global $srcFile, $thumbW, $thumbH, $useCache, $width, $height, $type;
		
		list($width, $height, $type) = getimagesize($srcFile);

		if($thumbH === false && $thumbW !== false)
		{
			$thumbH = round($height*($thumbW/$width));
		}
		else if($thumbH !== false && $thumbW === false)
		{
			$thumbW = round($width*($thumbH/$height));
		}
		else if($thumbH === false && $thumbW === false)
		{
			die("This should not be able to happen..");
		}
			
	}
	
	function loadAndResize()
	{
		global $srcFile, $thumbW, $thumbH, $useCache, $width, $height, $type;
		
		$source = loadImageByType($srcFile,$type);
		
		if (!$source) 
		{
			$useCache = false; // dont save the error thumb
			$thumb = makeErrorImg("Error loading $srcFile");
		}
		else
		{
			if($thumbH === false && $thumbW === false)
			{
				die("This should not be able to happen..");
			}
			
			$thumb = imageCreateTrueColor($thumbW, $thumbH);
			imagecopyresampled($thumb, $source, 0, 0, 0, 0, $thumbW, $thumbH, $width, $height);
			imagedestroy($source);
		}

		return $thumb;
	}
	
	function loadImage($filename)
	{
		$img = false;
		if(is_file($filename))
		{
			list($width, $height, $type) = getimagesize($filename);
			$img = loadImageByType($filename,$type);
		}
		
		if(!$img)
			return false;
		else
			return $img;		
	}
	
	function saveImage($img,$file,$quality)
	{
		if(!$img)
			return;
			
		if(!is_dir(dirname($file)))
		{
			$dirok = RecursiveMkdir(dirname($file));
		}
		else
			$dirok = true;
			
		if($dirok)
			@imagejpeg($img,$file,$quality);
	}
	
	function RecursiveMkdir($path)
	{
	// This function creates the specified directory using mkdir().  Note
	// that the recursive feature on mkdir() is broken with PHP 5.0.4 for
	// Windows, so I have to do the recursion myself.
		if (!file_exists($path))
		{
			RecursiveMkdir(dirname($path));
			mkdir($path, 0777);
		}
	}
	
	function makeVersionOnly()
	{
		global $version;

		
		$thumb  = imagecreate(220, 50); /* Create a blank image */
		$bgc = imagecolorallocate($thumb, 0, 255, 0);
		$tc  = imagecolorallocate($thumb, 0, 0, 0);
		imagefilledrectangle($thumb, 10, 10, 50, 40, $bgc);

		imagestring($thumb, 5, 9, 7, "justThumb.php", $tc);
		imagestring($thumb, 5, 40, 25, "version ".$version, $tc);

		return $thumb;
	}

	
?> 
