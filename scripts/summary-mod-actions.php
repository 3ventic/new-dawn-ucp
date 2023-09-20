<?php
if ($access < MODERATOR) die;
if (!isset($args[4]) || !is_numeric($args[4])) {
	die;
}
file_put_contents("logs/summary-mod-actions.log", date(DATE_ATOM) . " {$gdata['ID']} ({$gdata['user']}) called {$args[3]} for {$args[4]}" . PHP_EOL, FILE_APPEND);

if ($args[3] == "ban") {
	$game->userBan($args[4], true, $gdata['user'], (!isset($args[5]) || empty($args[5]) ? "None specified" : $args[5]));
}
else if ($args[3] == "unban") {
	$game->userBan($args[4], false);
}
else if ($args[3] == "unlock") {
	$game->userLock($args[4], false);
}
else if ($args[3] == "delete") {
    if ($access >= DEVELOPER) {
	    $game->userDelete($args[4], true);
    }
    else {
        header("HTTP/1.1 403 Forbidden");
        echo "Forbidden";
        die;
    }
}
//*
header("HTTP/1.1 302 Found");
header("Location: /summary/{$args[4]}");
die;
//*/