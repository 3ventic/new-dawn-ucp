<?php
$validstyles = array();
for($s=1;file_exists("signatures/bgimg$s.png");$s++)
{
	$validstyles[] = $s;
}

$style = (!isset($_GET['style'])) ? 1 : $_GET['style'];
$i = $_GET['i'];
if(!is_numeric($i))
{
    header("HTTP/1.1 500 Internal Server Error", true, 500);
    die;
}
if(!is_numeric($style) || !in_array($style, $validstyles)) die('not numeric or invalid style');

header("Content-Type: image/png");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); // HTTP/1.0

// Check if the file is cached
if(file_exists("signatures/cache/$i-$style.txt"))
{
    $cachetime = file_get_contents("signatures/cache/$i-$style.txt");
}
else
{
    $cachetime = 0;
}

// Set cache time to 1 hour
if(time()-(int)$cachetime <= 3600)
{
    readfile("signatures/cache/$i-$style.png");
    die();
}

// Set fonts and positions

$nh = 20; // Y difference between stats positions
$sh = 50; // Stats Y pos (first stat)
$nameh = 25; // Name Y pos
$dsize = 20; // Donator star's font size
$nsize = 18; // Name font size
$ssize = 16; // Stats font size
$font = "./fonts/MYRIADPRO-REGULAR.OTF"; // Rest of the fonts
$fontc = "./fonts/IMPACT.TTF"; // Name font
$stroke = 0.5; // Stroke size
$npos = 5; // Name X pos
$opos = 120; // Stats X pos
switch($style) {
	case 9:
	case 10:
	case 11:
		$fontc = "./fonts/TYPOGRAPHPROXTRABOLD.ttf";
		$font = "./fonts/TYPOGRAPHPROXTRABOLD.ttf";
		$ssize = 12;
		$nsize = 16;
		$dsize = 18;
		break;
	case 23:
		$fontc = "./fonts/COMIC.TTF";
		$font = "./fonts/COMIC.TTF";
		$nh = 22;
		$opos = 80;
		$ssize = 14;
		break;
	case 24:
		$fontc = "./fonts/TELEV2.TTF";
		$font = "./fonts/TELEV2.TTF";
		$opos = 140;
		$nh = 21;
		break;
	case 26:
		$opos = 85;
		$sh = 53;
		$nh = 20.5;
		$nameh = 22;
		$stroke = 1;
        break;
	case 30:
		$opos = 100;
		$sh = 30;
		$nh = 24;
        break;
	case 32:
		$opos = 180;
		$nh = 23;
        break;
	case 36:
		$opos = 90;
        break;
    case 62:
        $sh = 60;
        $nh = 18;
        $ssize = 14;
        break;
    case 69:
        $sh = 28;
        $nh = 25;
        break;
    case 78:
    case 79:
        $nh = 21;
        break;
	default:
}

require_once('config.php');

$sql = new mysql(new mysqli(SQLHOST, SQLUSER, SQLPASS, SQLDB));

$result = $sql->query("SELECT `Score`, `Wins`, `Runups`, `Cash`, `Hunters`, `ForumName`, `Donor`, `user` FROM `PlayerInfo` WHERE id = ?", "i", $i);
if (is_array($result)) {
	$row = $result[0];
}
else {
	die;
}
$user = $row[7];
$raw = imagecreatefrompng("./signatures/bgimg$style.png");
header('Content-Type: image/png');
$color = imagecolorallocate($raw, 0, 0, 0);
$ncolor = imagecolorallocate($raw, 30, 226, 214);
switch($row[6]) {
	case 1:
		$dcolor = imagecolorallocate($raw, 176, 109, 32); // Bronze
		break;
	case 2:
		$dcolor = imagecolorallocate($raw, 157, 167, 168);
		break;
	case 3:
		$dcolor = imagecolorallocate($raw, 222, 214, 0);
		break;
	case 4:
		$dcolor = imagecolorallocate($raw, 222, 214, 0);
		break;
	case 6:
		$dcolor = imagecolorallocate($raw, 0, 0, 0);
		break;
	default:
		break;
}

function processNum($num)
{
	$str = $num;
	$len = strlen($num);
	if($len >= 7) $str = substr_replace($str, " ", -6, 0);
	if($len >= 4) $str = substr_replace($str, " ", -3, 0);
	return $str;
}

$score = processNum($row[0]."");
$money = "\$".processNum($row[3]."");
$wins = processNum($row[1]."");
$runups = processNum($row[2]."");
$hunters = processNum($row[4]."");
if($row[6] >= 1 && !in_array($style, [30])) {
	imagefttext($raw, $dsize, 0, 5, 25, $dcolor, $font, "*");
	$npos = 20;
}
if(in_array($style, [26,30,69]))
{
	$array = imagettfbbox($nsize, 0, $fontc, $user);
	$w = abs($array[0] - $array[2]);
	$npos = ceil((562 - $w) / 2);
}
// White text
$whitebgs = array(19,20,22,24,25,26,28,29,30,33,34,36,37,38,39,42,43,44,45,46,47,48,49,50,55,56,57,58,59,60,62,63,64,65,66,67,68,69,70,71,76,78,79,80,83);
// Black shadow under stats
$shadowbgs = array(22,24,25,28,30,33,34,49,50,55,56,57,58,59,60,62,64,67,68,69,71,76,78,80,83);
// Black shadow under name
$shadownames = array(26,49,50,55,56,57,58,59,60,64,67,68,71,76,80,83);
$dds = array(45,46);

if(in_array($style, $dds)) $dd = true;
else $dd = false;

if(in_array($style, $whitebgs)) $color = imagecolorallocate($raw, 255, 255, 255);

if(in_array($style, $shadownames)) imageftstroketext($raw, $nsize, 0, $npos, $nameh, $color, imagecolorallocate($raw, 0, 0, 0), $fontc, $user, $stroke);
else imagefttext($raw, $nsize, 0, $npos, $nameh, $color, $fontc, $user);

if(in_array($style, $shadowbgs)) {
	imageftstroketext($raw, $ssize, 0, $opos, $sh, $color, imagecolorallocate($raw, 0, 0, 0), $font, $score, $stroke);
	imageftstroketext($raw, $ssize, 0, $opos, $sh+$nh, $color, imagecolorallocate($raw, 0, 0, 0), $font, $money, $stroke);
	imageftstroketext($raw, $ssize, 0, $opos, $sh+2*$nh, $color, imagecolorallocate($raw, 0, 0, 0), $font, $wins, $stroke);
	imageftstroketext($raw, $ssize, 0, $opos, $sh+3*$nh, $color, imagecolorallocate($raw, 0, 0, 0), $font, $runups, $stroke);
	if(!$dd) imageftstroketext($raw, $ssize, 0, $opos, $sh+4*$nh, $color, imagecolorallocate($raw, 0, 0, 0), $font, $hunters, $stroke);
} else {
	imagefttext($raw, $ssize, 0, $opos, $sh, $color, $font, $score);
	imagefttext($raw, $ssize, 0, $opos, $sh+$nh, $color, $font, $money);
	imagefttext($raw, $ssize, 0, $opos, $sh+2*$nh, $color, $font, $wins);
	imagefttext($raw, $ssize, 0, $opos, $sh+3*$nh, $color, $font, $runups);
	if(!$dd) imagefttext($raw, $ssize, 0, $opos, $sh+4*$nh, $color, $font, $hunters);
}
imagepng($raw);
imagepng($raw, "signatures/cache/$i-$style.png");
file_put_contents("signatures/cache/$i-$style.txt", time());
chmod("signatures/cache/$i-$style.png", 0777);
chmod("signatures/cache/$i-$style.txt", 0777);
imagedestroy($raw);

function imageftstroketext($image, $size, $angle, $x, $y, $textcolor, $strokecolor, $fontfile, $text, $px) {
 
    for($c1 = ($x-abs(floor($px))); $c1 <= ($x+abs(ceil($px))); $c1++)
        for($c2 = ($y-abs(floor($px))); $c2 <= ($y+abs(ceil($px))); $c2++)
            $bg = imagefttext($image, $size, $angle, $c1, $c2, $strokecolor, $fontfile, $text);
 
   return imagefttext($image, $size, $angle, $x, $y, $textcolor, $fontfile, $text);
}
?>