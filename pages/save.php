<?php
if($args[2] == "link-accounts") {
    $ign = $game->linkAccounts($_POST['username'], $_POST['password']);
    if($ign === FALSE)
    {
        echo "<h2>First time setup</h2><p>Failed to link accounts. Invalid credentials.</p>";
    }
    else
    {
        if($ipb->linkAccount($data['member_id'], $ign)) {
            echo "<h2>First time setup</h2><p>Accounts linked successfully. You can now start using the UCP.</p>";
        }
        else {
            echo "<h2>First time setup</h2><p>Failed to link accounts. Unknown error.</p>";
        }
    }
}