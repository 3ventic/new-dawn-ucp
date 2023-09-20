<table class="summary">
    <thead>
        <tr>
            <th>Map Name</th>
            <th>Author</th>
            <th>Top Time</th>
        </tr>
    </thead>
    <tbody>
        <?php
        
        $maplist = $game->mapList();
        
        if (is_array($maplist)) {
            foreach ($maplist as $map) {
                $time = $map['Rec1'] == '0' ? "None" : formatMilliseconds($map['Rec1']);
                echo "<tr><td>" . htmlspecialchars($map['Map']) . "</td><td>" . htmlspecialchars($map['Author']) . "</td><td>$time</td></tr>";
            }
        }
        
        ?>
    </tbody>
</table>