<?php
chdir("..");
require("config.php");
$sql = new mysql(new mysqli(SQLHOST, SQLUSER, SQLPASS, SQLDB));

$result = $sql->query("UPDATE PlayerInfo SET FreeCamper = 2, FreeMapTicket = 1 WHERE Donor > 0");
$sql->query("UPDATE PlayerInfo SET CreatedAt = ? WHERE CreatedAt IS NULL", "s", date('Y-m-d'));
?>