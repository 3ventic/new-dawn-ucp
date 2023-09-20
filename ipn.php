<?php

error_log("IPN FOR ID: {$_GET['id']}");

include("config.php");
$ipb = new ipb(new mysql(new mysqli(FORUMHOST, FORUMUSER, FORUMPASS, FORUMDB)));
$game = new game(new mysql(new mysqli(SQLHOST, SQLUSER, SQLPASS, SQLDB)));

// Send an empty HTTP 200 OK response to acknowledge receipt of the notification 
header('HTTP/1.1 200 OK');

define("USE_SANDBOX", false);

// Assign payment notification values to local variables
$item_name        = $_POST['item_name'];
$item_number      = $_POST['item_number'];
$payment_status   = $_POST['payment_status'];
$payment_amount   = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];
$txn_id           = $_POST['txn_id'];
$receiver_email   = $_POST['receiver_email'];
$payer_email      = $_POST['payer_email'];

if ($payment_currency == "USD" && USE_SANDBOX) {
	$payment_currency = "EUR";
}

error_log(str_replace("\n", "", print_r($_POST, true)));

// Build the required acknowledgement message out of the notification just received
// Read POST data
// reading posted data directly from $_POST causes serialization
// issues with array data in POST. Reading raw POST data from input stream instead.
$raw_post_data = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$myPost = array();
foreach ($raw_post_array as $keyval) {
	$keyval = explode ('=', $keyval);
	if (count($keyval) == 2)
		$myPost[$keyval[0]] = urldecode($keyval[1]);
}
// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';
if(function_exists('get_magic_quotes_gpc')) {
	$get_magic_quotes_exists = true;
}
foreach ($myPost as $key => $value) {
	if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
		$value = urlencode(stripslashes($value));
	} else {
		$value = urlencode($value);
	}
	$req .= "&$key=$value";
}

// Post IPN data back to PayPal to validate the IPN data is genuine
// Without this step anyone can fake IPN data

if(USE_SANDBOX == true) {
	$paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
} else {
	$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
}

$ch = curl_init($paypal_url);
if ($ch == FALSE) {
	error_log("CURL FAILED", E_USER_ERROR);
	return FALSE;
}

curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);

// Set TCP timeout to 30 seconds
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

$res = curl_exec($ch);
error_log($res);
if (curl_errno($ch) != 0) {
	error_log("Can't connect to PayPal to validate IPN message: " . curl_error($ch));
	curl_close($ch);
	exit;
}
curl_close($ch);

if (strcmp ($res, "VERIFIED") == 0) {  // Response contains VERIFIED - process notification

    if (!isset($_POST['custom']) || empty($_POST['custom'])) {
        mail("admin@3v.fi", "IPN failure at server.dawn-tdm.com", "custom parameter was empty upon arrival. Please check that it's not empty in the future. Payment details are below:\r\n\r\n" . json_encode($_POST), "From: no-reply@server.dawn-tdm.com\r\n");
    }
	file_put_contents("logs/ipn.log", date(DATE_ATOM) . " ID: {$_GET['ID']} | " .  json_encode($_POST) . PHP_EOL, FILE_APPEND);

	if($payment_status == "Completed")
	{
		if($payment_currency == "EUR")
		{
			$AccountID = $_POST['custom'];
			$DaysPurchased = floor($payment_amount * 8);
			$data = $ipb->getMemberData($AccountID, true);
			$gdata = $game->getMemberData($AccountID);

			if($gdata['Donor'] == 0)
			{
				$Expiry = strtotime("+$DaysPurchased days");
				$game->setDonator($AccountID, $Expiry, 4);
				$ipb->setDonator($AccountID);
			}
			else {
				$Expiry = ($gdata['Donor'] == 4 && $gdata["VIPDate"] != "") || $gdata['Donor'] == 6 ? strtotime("+$DaysPurchased days", strtotime("+60 days")) : strtotime("+$DaysPurchased days", strtotime($gdata['VIPDate']));
				$game->setDonator($AccountID, $Expiry, 4);
				$ipb->setDonator($AccountID);
			}
		}
	}
	else if (strcmp ($res, "INVALID") == 0) { // Response contains INVALID - reject notification

		error_log("$res", E_USER_ERROR);

	}
}


?>