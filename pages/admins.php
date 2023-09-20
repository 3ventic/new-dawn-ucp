<?php

$admins = $ipb->getAdmins();

if ($gdata["Admin"] > 0) {
    ?><a class="button" href="/ajax/mark-me-inactive/">I AM <?=$gdata["AdminInactive"] > 0 ? "" : "IN"?>ACTIVE</a><?php
}

if (is_array($admins)) {
?>
	<p><?=count($admins)?> game moderators</p>
	<table class="fixed">
		<thead>
			<th>
				Forum Name
			</th>
			<th>
				In-Game Name
			</th>
			<th>
				Title
			</th>
			<th>
				Level
			</th>
		</thead>
		<tbody>
			<?php
			
			foreach ($admins as $key => $admin) {
				$ig = (int)$admin["ign"] > 0 && !empty($admin["ign"]) ? $game->getMemberData($admin["ign"]) : ["user" => "N/A", "Admin" => 0];
				$admins[$key]["iguser"] = $ig["user"];
				$admins[$key]["igAdmin"] = $ig["AdminInactive"] == 0 ? $ig["Admin"] : $ig["AdminInactive"] . " (inactive since " . $ig["AdminInactiveDate"] . ")";
			}
			
			usort($admins, function ($a, $b) {
				if ($a["member_title"] == "Community Owner") return -1;
				if ($b["member_title"] == "Community Owner") return 1;
				if ($a["igAdmin"] > $b["igAdmin"]) return -1;
				if ($a["igAdmin"] < $b["igAdmin"]) return 1;
				if ($a["member_group_id"] < $b["member_group_id"]) return -1;
				if ($a["member_group_id"] > $b["member_group_id"]) return 1;
				else return 0;
			});
			
			foreach ($admins as $admin) {
				echo "<tr><td><a href=\"" . FORUMURL . "user/{$admin["member_id"]}-\" class=\"memberlink\">" . htmlspecialchars($admin["name"]) . "</a></td><td><a href=\"/summary/{$admin["ign"]}/\" class=\"memberlink\">" . htmlspecialchars($admin["iguser"]) . "</td><td>" . htmlspecialchars($admin["member_title"]) . "</td><td>" . $admin["igAdmin"] . "</td></tr>";
			}
			
			?>
		</tbody>
	</table>
	<p><?=count($admins)?> game moderators</p>
<?php
}
else {
	echo "<p class=\"error\">Unknown error</p>";
}