<?php

$count = function ($array) { return count($array); };

function ucpLog($file, $text)
{
    return file_put_contents("logs/" . $file, date("[Y-m-d H:i:s] ") . $text . PHP_EOL, FILE_APPEND);
}

function multi_in_array($value, $array) 
{ 
    foreach ($array AS $item) 
    { 
        if (!is_array($item)) 
        { 
            if ($item == $value) 
            { 
                return true; 
            } 
            continue; 
        } 

        if (in_array($value, $item)) 
        { 
            return true; 
        } 
        else if (multi_in_array($value, $item)) 
        { 
            return true; 
        } 
    } 
    return false; 
}

function printDebugInfo($variable)
{
    echo "<pre><code>";
    var_dump($variable);
    echo "</code></pre>";
}

function signatureAuthor($sigid) {
	switch($sigid) {
        case 1:
            $sigau = "3ventic";
            break;
		case 5:
			$sigau = "Harsimar";
			break;
		case 2:
		case 3:
		case 4:
		case 6:
		case 7:
		case 8:
		case 27:
		case 28:
		case 31:
        case 61:
			$sigau = "V3X";
			break;
		case 9:
		case 10:
		case 11:
			$sigau = "Done.";
			break;
		case 12:
		case 13:
		case 14:
		case 15:
		case 16:
		case 17:
		case 22:
			$sigau = "Whispy";
			break;
		case 18:
			$sigau = "Moita";
			break;
		case 19:
		case 20:
		case 21:
		case 33:
		case 34:
			$sigau = "Flamingo";
			break;
		case 23:
			$sigau = "ManiaX";
			break;
		case 24:
			$sigau = "Moody";
			break;
		case 25:
			$sigau = "DeCoyz";
			break;
		case 26:
			$sigau = "FightFox";
			break;
		case 29:
		case 30:
			$sigau = "AceR";
			break;
		case 32:
			$sigau = "TheNicO";
			break;
		case 35:
		case 36:
		case 37:
		case 38:
		case 39:
		case 47:
			$sigau = "Nova";
			break;
		case 40:
			$sigau = "airplaNe";
			break;
		case 41:
			$sigau = "reasoN_";
			break;
		case 42:
			$sigau = "VENx";
			break;
		case 43:
		case 44:
			$sigau = "RaedoX";
			break;
		case 45:
		case 46:
			$sigau = "GreenFanta";
			break;
		case 48:
		case 49:
		case 50:
			$sigau = "SexyBoi";
			break;
		case 51:
		case 52:
		case 53:
		case 54:
			$sigau = "Slimmy";
			break;
		case 55:
		case 56:
			$sigau = "TeKu";
			break;
        case 57:
        case 58:
        case 59:
        case 60:
            $sigau = "Ovni.Boy";
            break;
        case 62:
        case 63:
        case 65:
        case 66:
            $sigau = "Alae";
            break;
        case 64:
            $sigau = "Houssam";
            break;
        case 67:
        case 68:
            $sigau = "Jeson";
            break;
        case 69:
            $sigau = "Locked";
            break;
        case 70:
            $sigau = "Andre02_PT";
            break;
        case 71:
            $sigau = "Jonny";
            break;
        case 72:
        case 73:
            $sigau = "Sp3ctra";
            break;
        case 74:
        case 75:
        case 76:
        case 77:
            $sigau = "powerX2";
            break;
        case 78:
        case 79:
            $sigau = "BadBoyZz";
            break;
        case 80:
        case 81:
            $sigau = "Cyton";
            break;
        case 82:
            $sigau = "ZeXz";
            break;
        case 83:
            $sigau = "MarksmannF7U12";
            break;
        case 84:
            $sigau = "iKakashi";
            break;
		default:
			$sigau = "???";
	}
	return $sigau;
}


// http://stackoverflow.com/a/4763699/1780502 by ircmaxell http://stackoverflow.com/users/338665/ircmaxell
// Modified
function formatMilliseconds($milliseconds) {
    $seconds = floor($milliseconds / 1000);
    $minutes = floor($seconds / 60);
    $milliseconds = $milliseconds % 1000;
    $seconds = $seconds % 60;
    $minutes = $minutes % 60;

    $format = '%02u:%02u.%03u';
    $time = sprintf($format, $minutes, $seconds, $milliseconds);
    return $time;
}