<?php
function compare($col, $row, $g = NULL)
{
    if($g === NULL) $g = $GLOBALS['g'];
    return $row[$col] == $g[$col] ? likelihood($col) : 0;
}
        
function likelihood($col)
{
    if($col == 'IP') return 40;
    return 5;
}
if($other) $g = $gdata_other;
else $g = $gdata;

$bandata = $game->getBanState($g);
$bandata_old = $game->getBanState($g, true);

echo '<div id="ban-info">';
if ($bandata !== false) {
	foreach ($bandata as $banrow) {
		echo "<p class=\"error\"><strong><a href=\"/modcp/bans/details/{$banrow["ID"]}/\">Account Banned</a></strong><br/><br/><strong>Details:</strong><br/><br/><strong>Name:</strong> {$banrow["Username"]}<br/><strong>Date:</strong> {$banrow["Date"]}<br/><strong>Admin:</strong> {$banrow["Admin"]}<br/><strong>Reason:</strong> " . htmlspecialchars($banrow["Reason"]) . "</p>";
	}
}

if ($bandata_old !== false) {
	foreach ($bandata_old as $banrow) {
		echo "<p class=\"error warning\"><strong><a href=\"/modcp/bans/details/{$banrow["ID"]}/\">Expired Ban Found</a></strong><br/><br/><strong>Details:</strong><br/><br/><strong>Name:</strong> {$banrow["Username"]}<br/><strong>Date:</strong> {$banrow["Date"]}<br/><strong>Admin:</strong> {$banrow["Admin"]}<br/><strong>Reason:</strong> " . htmlspecialchars($banrow["Reason"]) . "</p>";
	}
}
echo '</div>';

$skip = false;
if (!is_array($g)) {
    echo '<p class="error">This user does not exist!</p>';
    $skip = true;
}
else if ($g['Deleted'] == 1 && $access < MODERATOR) {
    echo '<p class="error">This user no longer exists!</p>';
    $skip = true;
}
else if ($g['Deleted'] == 1) {
    echo '<p class="error">This user has been deleted! You are currently viewing the archived account data!</p>';
}
if ($skip === false) {

    ?><p>
        Permalink to this profile: <code><?=BASEURL . "summary/" . $g["ID"]?></code>
    </p><table class="summary">
	    <thead>
		    <th class="left">Stat</th>
		    <th class="right">Value</th>
	    </thead>
	    <tbody>
		    <?php
		    $shownstats = ['user', 'Score', 'Skin', 'Wins', 'Runups', 'C1', 'C2', 'IRC', 'Locked', 'Muted', 'Donor', 'color', 'Cash', 'Warn', 'LastLogged', 'CreatedAt'];
		    if($access >= MODERATOR)
		    {
			    $shownstats = array_merge($shownstats, ['IP', 'Serial']);
		    }
			
		    foreach($g as $stat => $value)
		    {
			    if(in_array($stat, $shownstats) && $stat != '0')
			    {
				    echo "<tr><td class=\"left\">".$game->getFriendlyColumnName($stat)."</td><td class=\"right\">".$game->getFriendlyColumnValue($stat, $value)."</td></tr>";
			    }
		    }
		    ?>
	    </tbody>
    </table>
    <div class="mod_actions">
        <a class="button" href="/uploader/list/<?=$g['ID']?>">Uploaded Maps</a>
    <?php

    if($access >= MODERATOR) {
        if ($g['Deleted'] == 0) {
    
    ?>
	    <a class="button" href="#" id="mod-ban">Ban</a>
	    <a class="button" href="/ajax/mod/unban/<?=$g['ID']?>">Unban</a>
	    <a class="button" href="/ajax/mod/unlock/<?=$g['ID']?>">Unlock</a>
    <?php

	        if ($access >= DEVELOPER) {
        
    ?>
	    <a class="button" href="/ajax/mod/delete/<?=$g['ID']?>">Delete Account</a>
    <?php

	        }
        
    ?>
	    <script>
    $("#mod-ban").click(function (e) {
    var reason = window.prompt("Reason for banning <?=$g["user"]?>?", "");
    if (reason.length < 10) {
	    alert("Please specify a longer reason");
    }
    else {
	    if (window.confirm("Are you sure you want to ban <?=$g["user"]?> for \n\n\"" + reason + "\"?")) {
		    window.location.href = ("<?=BASEURL?>ajax/mod/ban/<?=$g['ID']?>/" + encodeURIComponent(reason));
	    }
	    else {
		    alert("Cancelled");
	    }
    }
    e.preventDefault();
    });
	    </script>
    <?php

        }
    
    ?>
    </div>
    
    <?php
    
        $namechanges = $game->getNameChanges($g['ID']);
        if ($namechanges === FALSE)
        {
            echo "<p>An unknown error occured.</p>";
            printDebugInfo($game->sql->error);
        }
        else if ($namechanges !== TRUE)
        {
            ?><h3>Namechange History</h3>
    <table>
        <thead>
            <tr>
                <th>Time</th>
                <th>Old name</th>
                <th>New name</th>
                <th>IP</th>
            </tr>
        </thead>
        <tbody>
            <?php
            
            foreach ($namechanges as $change)
            {
                echo "<tr><td>{$change['created_at']}</td><td>" . htmlspecialchars($change['oldname']) . "</td><td>" . htmlspecialchars($change['newname']) . "</td><td>{$change['ip']}</td></tr>";
            }
            
            ?>
        </tbody>
    </table>
            <?php
        }
    
    ?>
    <?php

	    $banned = $game->bannedUsers(FALSE);
	    $related = $game->relatedAccounts($g['IP'], $g['Serial']);
	    if($related === FALSE)
	    {
		    echo "<p>An unknown error occured.</p>";
		    printDebugInfo($game->sql->error);
	    }
	    else if($related !== TRUE)
	    {
    
		    ?><h3>Possible aliases</h3>
    <table>
	    <thead>
            <tr>
		        <th>User</th>
		        <th>IP</th>
		        <th>Serial</th>
		        <th>Ban</th>
		        <th>Lock</th>
		        <th>Last Seen</th>
		        <th>Likelihood</th>
	        </tr>
        </thead>
	    <tbody>
		    <?php
        
            usort($related, function ($a, $b) {
                if ($a['Score'] > $b['Score']) return -1;
                else if ($a['Score'] < $b['Score']) return 1;
                else return 0;
            });
			
		    foreach($related as $row)
		    {
			    if($row['ID'] == $g['ID']) continue;
			    $likelihood = $row['Serial'] == $g['Serial'] ? 40 : 0;
			    $likelihood += (compare('IP', $row) + compare('C1', $row) + compare('C2', $row) + compare('Skin', $row) + compare('Hat', $row) + compare('Channel', $row) + compare('password', $row) + compare('World', $row) + compare('CarString', $row));
			    if($likelihood > 100) $likelihood = 100;
			    $row['banned'] = in_array([0 => strtolower($row['user']), 'Username' => strtolower($row['user'])], $banned) ? 'yes' : 'no';
			    $row['Locked'] = $row['Locked'] == 1 ? 'yes' : 'no';
			    echo "<tr" . ($row['Deleted'] == 1 ? ' class="deleted"' : '') . "><td><a href=\"/summary/{$row['ID']}\">" . htmlspecialchars($row['user']) . "</a></td><td>" . htmlspecialchars($row['IP']) . "</td><td>" . htmlspecialchars($row['Serial']) . "</td><td>{$row['banned']}</td><td>{$row['Locked']}</td><td>" . $game->getFriendlyColumnValue("LastLogged", $row['LastLogged']) . "</td><td>$likelihood%</td></tr>";
		    }
			
		    ?>
	    </tbody>
    </table>
    <?php

	    }
    }
}