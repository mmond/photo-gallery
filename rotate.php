<?

$exif = exif_read_data($_GET['src'], EXIF, true);

$o = $exif["IFD0"]["Orientation"];
$rotation = 0;
$flip = false;

switch($o) {

case 1:
$rotation = 0;
$flip = false;
break;

case 2:
$rotation = 0;
$flip = true;
break;

case 3:
$rotation = 180;
$flip = false;
break;

case 4:
$rotation = 180;
$flip = true;
break;

case 5:
$rotation = 270;
$flip = true;
break;

case 6:
$rotation = 270;
$flip = false;
break;

case 7:
$rotation = 90;
$flip = true;
break;

case 8:
$rotation = 90;
$flip = false;
break;
}
?>