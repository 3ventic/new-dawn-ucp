<?php
if($access < MODERATOR)
{
    echo "Permission denied.";
}
else
{
    if(empty($args[3]))
    {
    ?>
    <div class="form">
        <input type="text" id="st"/>
        <button id="sb" data-page="search">Search</button>
    </div>
    <h3>Search filters</h3>
    <ul>
        <li>No filter searches IP, serial and name for matches</li>
        <li><code class="i">score:[min,max]</code></li>
        <li><code class="i">cash:[min,max]</code></li>
        <li><code class="i">banned:yes</code> or <code class="i">banned:no</code></li>
    </ul>
    <?php
    }
    else
    {
        $banned = $game->bannedUsers(FALSE);
        $result = $game->modSearch($args);
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
        else
        {
            ?>
            <table>
                <thead>
                    <th>User</th>
                    <th>IP</th>
                    <th>Serial</th>
                    <th>Old name</th>
                    <th>Car text</th>
                    <th>Score</th>
                    <th>Cash</th>
                    <th>Ban</th>
                    <th>Lock</th>
                </thead>
                <tbody>
            <?php
            foreach($result as $row)
            {
                $row['banned'] = in_array([0 => strtolower($row['user']), 'Username' => strtolower($row['user'])], $banned) ? 'yes' : 'no';
                $row['Locked'] = $row['Locked'] == 1 ? 'yes' : 'no';
                if(isset($modcpBanSearch) && $row['banned'] != $modcpBanSearch && $row['Locked'] != $modcpBanSearch) continue;
                echo "<tr" . ($row['Deleted'] == 1 ? ' class="deleted"' : '') . "><td><a href=\"/summary/{$row['ID']}\">" . $game->highlightSearchArgs($args, $row['user']) . "</a></td><td>" . $game->highlightSearchArgs($args, $row['IP']) . "</td><td>" . $game->highlightSearchArgs($args, $row['Serial']) . "</td><td>" . $game->highlightSearchArgs($args, $row['oldname']) . "</td><td>" . $game->highlightSearchArgs($args, $row['CarString']) . "</td><td>" . $game->highlightSearchArgs($args, $row['Score']) . "</td><td>" . $game->highlightSearchArgs($args, $row['Cash']) . "</td><td>" . $game->highlightSearchArgs($args, $row['banned']) . "</td><td>" . $game->highlightSearchArgs($args, $row['Locked']) . "</td></tr>";
            }
            echo "</tbody></table>";
        }
    }
}