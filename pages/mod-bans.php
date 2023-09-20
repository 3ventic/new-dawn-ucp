<?php
if ($access < MODERATOR)
{
    echo "Permission denied.";
}
else
{
    if (empty($args[3]))
    {
    ?>
    <div class="form">
        <input type="text" id="st"/>
        <button id="sb" data-page="bans">Search</button>
    </div>
    <h3>Search filters</h3>
    <ul>
        <li>IP, serial and name are searched for matches</li>
    </ul>
    <?php
    }
    else if ($args[3] == "details")
    {
        $bandetails = $game->banDetails($args[4]);
        
        if(is_array($bandetails)) {
            
            $banrow = $bandetails[0];
            unset($bandetails);
            
            if ($banrow['Expired'] == 1) echo '<p class="error warning">This ban is expired and no longer active.</p>';
            echo "<p><strong>Details:</strong><br/><br/><strong>Name:</strong> {$banrow["Username"]}<br/><strong>Date:</strong> {$banrow["Date"]}<br/><strong>Admin:</strong> {$banrow["Admin"]}<br/><strong>Reason:</strong> " . htmlspecialchars($banrow["Reason"]) . "</p>";
            
            $result = $game->getUsersByIpAndSerial($banrow['IP'], $banrow['Serial']);
            
            echo '<h1>Players Affected By This Ban</h1>';
            
            if (!is_array($result)) {
                echo 'No results or error.';
            }
            else {
                ?>
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Score</th>
                            <th>Last Online</th>
                            <th>Registered</th>
                        </tr>
                    </thead>
                    <tbody>
                <?php
                foreach ($result as $row) {
                    echo "<tr" . ($row['Deleted'] == 1 ? ' class="deleted"' : '') . "><td><a href=\"/summary/{$row['ID']}\">" . htmlspecialchars($row['user']) . "</a></td><td>{$row['Score']}</td><td>{$row['LastLogged']}</td><td>{$row['CreatedAt']}</td></tr>";
                }
                ?>
                    </tbody>
                </table>
                <?php
            }
        }
    }
    else
    {
        $result = $game->searchBannedUsers($args);
        if($result === FALSE)
        {
            echo "<p class=\"error\">An unknown error occured.</p>";
            
            /// DEBUG
            if (BRANCH == "dev") printDebugInfo($game->sql->error);
            //*/
        }
        else if($result === TRUE)
        {
            echo "<p>No results.</p>";
        }
        else {
            ?>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Date</th>
                        <th>Admin</th>
                        <th>Reason</th>
                        <th>IP</th>
                        <th>Serial</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    
                    foreach ($result as $row) {
                        echo "<tr" . ($row['Expired'] == 1 ? ' class="deleted"' : '') . "><td><a href=\"/modcp/bans/details/{$row['ID']}/\">" . $game->highlightSearchArgs($args, $row['Username']) . "</a></td><td>" . $game->highlightSearchArgs($args, $row['Date']) . "</td><td>" . $game->highlightSearchArgs($args, $row['Admin']) . "</td><td>" . $game->highlightSearchArgs($args, $row['Reason']) . "</td><td>" . $game->highlightSearchArgs($args, $row['IP']) . "</td><td>" . $game->highlightSearchArgs($args, $row['Serial']) . "</td></tr>";
                    }
                    
                    ?>
                </tbody>
            </table>
            <?php
        }
    }
}