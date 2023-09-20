<?php
chdir("..");
require("config.php");
require("api/config.php");
$sql = new mysql(new mysqli(SQLHOST, SQLUSER, SQLPASS, SQLDB));

if (isset($_GET["name"]) && !empty($_GET["name"])) {
    $id = $sql->query("SELECT ID FROM PlayerInfo WHERE user LIKE ?", "s", $_GET["name"])[0];
    if ($sql->num_rows > 0) {
        $json = json_encode(["id" => $id[0], "name" => $_GET["name"]]);
    }
    else {
        $json = json_encode(apiError(404, "Name not registered."));
    }
}
else {
    $json = json_encode(["error" => true, "code" => 400, "message" => "Invalid request. Missing or empty GET parameter &quot;name&quot;."]);
}
echo $json;