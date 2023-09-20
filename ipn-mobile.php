<?php


if(!in_array($_SERVER['REMOTE_ADDR'],
	array('109.70.3.48', '109.70.3.146', '109.70.3.210', '37.59.4.80'))) { 
	header("HTTP/1.1 403 Forbidden"); 
    file_put_contents("logs/ipn-mobile.log", date(DATE_ATOM) . " FROM UNAUTHORIZED IP ADDRESS " . $_SERVER['REMOTE_ADDR'] . " " . json_encode($_GET) . PHP_EOL, FILE_APPEND);
	die("Error " . $_SERVER['REMOTE_ADDR']); 
} 
error_log("IPN MOBILE FOR ID: {$_GET['custom']}");

include("config.php");
$ipb = new ipb(new mysql(new mysqli(FORUMHOST, FORUMUSER, FORUMPASS, FORUMDB)));
$game = new game(new mysql(new mysqli(SQLHOST, SQLUSER, SQLPASS, SQLDB)));

$your_service_id = 100242;

$message_id    = $_GET['message_id'];
$service_id    = $_GET['service_id']; 
$shortcode    = $_GET['shortcode']; 
$keyword    = $_GET['keyword']; 
$message    = $_GET['message']; 
$sender    = $_GET['sender']; 
$operator    = $_GET['operator']; 
$country    = $_GET['country']; 
$custom    = $_GET['custom'];
$points    = $_GET['points']; 
$price    = $_GET['price']; 
$currency    = $_GET['currency'];

if ($your_service_id == $service_id) 
{
	file_put_contents("logs/ipn-mobile.log", date(DATE_ATOM) . json_encode($_GET) . PHP_EOL, FILE_APPEND);
	
	$DaysPurchased = 12;
	$AccountID = $custom;
	$data = $ipb->getMemberData($AccountID, true);
	$gdata = $game->getMemberData($AccountID);
	
	if($gdata['Donor'] == 0)
	{
		$Expiry = strtotime("+12 days");
		$game->setDonator($AccountID, $Expiry, 4);
		$ipb->setDonator($AccountID);
	}
	else {
		$Expiry = ($gdata['Donor'] == 4 && $gdata["VIPDate"] != "") || $gdata['Donor'] == 6 ? (strtotime("+72 days")) : strtotime("+12 days", strtotime($gdata['VIPDate']));
		$game->setDonator($AccountID, $Expiry, 4);
		$ipb->setDonator($AccountID);
	}
}
else {
	error_log("Service ID was $service_id");
}

?>