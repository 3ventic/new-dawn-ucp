<?php
chdir("..");
require("config.php");
$sql = new mysql(new mysqli(SQLHOST, SQLUSER, SQLPASS, SQLDB));

$result = $sql->query("SELECT * FROM PlayerInfo WHERE Donor > 0");
if ($result === false) {
	exit("Error");
}
foreach($result as $row) {
	$Expiry = strtotime($row['VIPDate']);
	$Expiry = strtotime("+1 day", $Expiry);
	if($Expiry < time())
	{
		file_put_contents("logs/vipexpiry.log", "[" . date('Y-m-d H:i:s') . "] [{$row['ID']}] {$row['user']}'s VIP expired ({$row['VIPDate']} became " . date('Y-m-d H:i:s', $Expiry) . ")" . PHP_EOL, FILE_APPEND);
		$sql->query("UPDATE PlayerInfo SET Donor = 0, HasCarText = 0 WHERE ID = ?", "i", $row['ID']);
	}
}
?>