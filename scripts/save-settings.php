<?php

if($gdata['Locked'] == 1)
{
    die('Banned users cannot save settings.');
}

$error = [];
$changed = 0;

// Name

file_put_contents("logs/save-settings.log", date(DATE_ATOM) . " [{$data['member_id']}, {$gdata['ID']}, {$gdata['user']}] " . preg_replace('#"pass([^"]+)":".*?"(,|\})#i', '"pass$1":""$2', json_encode($_POST)) . PHP_EOL, FILE_APPEND);

if ($gdata['user'] != $_POST['user'])
{
    if (preg_match('#^[@a-zA-Z0-9.=\[\]()_]{4,20}$#', $_POST['user']))
    {
        $res = $game->sql->query("SELECT * FROM `PlayerInfo` WHERE `user` LIKE ?", 's', $_POST['user']);

        if ($res === FALSE)
        {
            $error[] = "Failed to select rows from the database";
        }
        else if ($game->sql->num_rows == 0)
        {
            if ($gdata['namesLeft'] == 1)
            {
                $res = $game->sql->query("UPDATE `PlayerInfo` SET `user` = ? WHERE `ID` = ?", 'si', $_POST['user'], $gdata['ID']);
                if($res === FALSE)
                {
                    $error[] = "Failed to update username";
                }
                else
                {
                    $game->sql->query("INSERT INTO nameLogs (playerid, oldname, newname, ip) VALUES (?, ?, ?, ?)", "isss", $gdata['ID'], $gdata['user'], $_POST['user'], $_SERVER['REMOTE_ADDR']);
                    ++$changed;
                }
            }
            else
            {
                $error[] = "You have been banned from changing names through the UCP.";
            }
        }
        else
        {
            $error[] = "Chosen username already exists.";
        }
    }
    else
    {
        $error[] = "Chosen username is invalid.";
    }
}

// Skin

if ($gdata['Skin'] != $_POST['user'])
{
    $res = $game->sql->query("UPDATE `PlayerInfo` SET `Skin` = ? WHERE `ID` = ?", 'ii', $_POST['skin'], $gdata['ID']);
    if($res === FALSE)
    {
        $error[] = "Failed to update skin ID!";
    }
    else
    {
        ++$changed;
    }    
}

if (isset($_POST['passforum']) && !empty($_POST['passforum'])) {
    if ($ipb->validCredentials($data['member_id'], $_POST['passforum'])) {
        if (strlen($_POST['pass1']) > 5 && strlen($_POST['pass1']) < 29) {
            if ($_POST['pass1'] == $_POST['pass2']) {
                if ($game->updatePassword($gdata['ID'], $_POST['pass1'])) {
                    ++$changed;
                }
                else {
                    $error[] = "Failed to save new password.";
                }
            }
            else {
                $error[] = "Passwords did not match!";
            }
        }
        else {
            $error[] = "Password must be between 6 and 28 characters long.";
        }
    }
    else {
        $error[] = "Invalid forum password for the currently logged in account!";
    }
}

foreach($error as $line)
{
    echo "$line\n";
}
$pluralfix = $changed == 1 ? 'setting' : 'settings';
echo "$changed $pluralfix saved successfully";