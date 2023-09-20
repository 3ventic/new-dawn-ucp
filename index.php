<?php

$timestart = microtime(true);

require("config.php");

$ipb = new ipb(new mysql(new mysqli(FORUMHOST, FORUMUSER, FORUMPASS, FORUMDB)));
$game = new game(new mysql(new mysqli(SQLHOST, SQLUSER, SQLPASS, SQLDB)));

$revision = "N/A"; //`git rev-list HEAD --count`;

// Check that the user is logged in and get their data
if(!isset($_COOKIE['ips4_member_id']) || !isset($_COOKIE['ips4_IPSSessionFront']) || !$ipb->isLogged($_COOKIE['ips4_IPSSessionFront'], $_COOKIE['ips4_member_id']))
{
    header("HTTP/1.1 302 Found");
    header("Location: ".FORUMURL."login/?from=ucp_login");
    die;
}
$data = $ipb->getMemberData($_COOKIE['ips4_member_id']);

// Determine access level
if($data['member_group_id'] == '4' || in_array('4', explode(',', $data['mgroup_others'])))
{
    $access = DEVELOPER;
}
else if($data['member_group_id'] == '8' || in_array('8', explode(',', $data['mgroup_others'])))
{
    $access = MODERATOR;
}
else if($data['member_group_id'] == '9' || in_array('9', explode(',', $data['mgroup_others'])))
{
    $access = DONATOR;
}
else if($data['member_group_id'] == '3' || in_array('3', explode(',', $data['mgroup_others'])))
{
    $access = USER;
}
else
{
    header("HTTP/1.1 302 Found");
    header("Location: ".FORUMURL."login/?from=access");
}

if(BRANCH == "dev" && $access < DEVELOPER)
{
    die('Developer access required');
}

if($data['ign'] != 0) $gdata = $game->getMemberData($data['ign']);
else $gdata = [];
for($i = 0; $i < count($gdata); $i++)
{
    unset($gdata[$i]);
}

if ($access == USER && $gdata["Admin"] > 0) {
    die("Please PM 3ventic on forums with your in-game name.");
}

// Get module and such from request URI
$args = explode("/", preg_replace('#\?.*$#', '', $_SERVER['REQUEST_URI']));
if(empty($args[1]) || $args[1] == '-') $args[1] = "summary";

if (count($gdata) > 0 && ($gdata["Locked"] == 1 || $gdata["Deleted"] == 1)) {
	$args[1] = "summary";
}

for($i = 0; $i < count($args); $i++)
{
    $args[$i] = urldecode($args[$i]);
}

if ($args[1] == "payments" && file_exists("paymentsdisabled")) $args[1] = "summary";

$other = FALSE;
if($args[1] == "save")
{
    if($args[2] == "settings")
    {
        include("scripts/save-settings.php");
        die;
    }
}
else if ($args[1] == "ajax") {
	if ($args[2] == "mod") {
		include("scripts/summary-mod-actions.php");
	}
    else if ($args[2] == "mark-me-inactive") {
        if ($gdata["AdminInactive"] > 0) {
            $game->activeAdmin($gdata["ID"]);
        }
        else if ($game->inactiveAdmin($gdata["ID"])) {
            mail("support@dawn-tdm.com", "Inactive Admin - " . $gdata["user"], "Admin " . $gdata["user"] . " has set himself or herself inactive from level " . $gdata["Admin"] . ". Link to their forum profile: http://www.dawn-tdm.com/user/" . $data["member_id"] . "-/", "From: User Control Panel <no-reply@ucp.dawn-tdm.com>\r\n");
        }        
    }
    else if ($args[2] == "uploader" && $gdata["Admin"] > 3) {
        $game->modifyUploadedStatus($_POST["id"], $_POST["action"], $_POST["reason"] . " -" . $data["name"]);
    }
    header("HTTP/1.1 302 Found");
    header("Location: " . BASEURL);
	die;
}
else if ($args[1] == "upload") {
    include("scripts/uploader.php");
    die;
}
else if ($args[1] == "summary" && !empty($args[2]))
{
    $gdata_other = $game->getMemberData($args[2]);
    $other = TRUE;
}

// External handling

// End of external handling

?><!DOCTYPE html>
<html lang="en">
<!-- Access: <?=$access?> -->
    <head>
        <meta charset="utf-8"/>
        <title>New Dawn User Control Panel</title>
        <link rel="stylesheet" href="/external/main.css?v=<?=crc32($revision)?>"/>
        <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
    </head>
    <body>
        <div id="fake_body">
		    <nav class="central">
			    <div id="logged">logged in as <?=$data['members_display_name']?></div>
			    <ul id="nav">
				    <li<?=($args[1] == "summary") ? ' class="active"' : ''?>>
					    <a href="/">Summary</a>
				    </li>
				    <li<?=($args[1] == "chat") ? ' class="active"' : ''?>>
					    <a href="/chat/">Chat</a>
				    </li>
                    <?php if (!file_exists("paymentsdisabled")) { ?>
				    <li<?=($args[1] == "payments") ? ' class="active"' : ''?>>
					    <a href="/payments/">VIP</a>
				    </li>
                    <?php } ?>
				    <li<?=($args[1] == "maps") ? ' class="active"' : ''?>>
					    <a href="/maps/">Maps</a>
				    </li>
				    <li<?=($args[1] == "uploader") ? ' class="active"' : ''?>>
					    <a href="/uploader/">Uploader</a>
				    </li>
				    <li<?=($args[1] == "admins") ? ' class="active"' : ''?>>
					    <a href="/admins/">Admins</a>
				    </li>
				    <li<?=($args[1] == "settings") ? ' class="active"' : ''?>>
					    <a href="/settings/">Settings</a>
				    </li>
				    <?php
				    if($access >= MODERATOR) {
				    ?>
				    <li<?=($args[1] == "modcp") ? ' class="active"' : ''?>>
					    <a href="/modcp/search/">Mod CP</a>
				    </li>
				    <?php
				    }
				    ?>
				    <li>
					    <a href="javascript:alert('Sign out from forums (www.dawn-tdm.com)')">sign out</a>
				    </li>
			    </ul>
		    </nav>
            <div id="container" class="central">
                <div id="content">
                    <?php
                    echo "<!-- ", var_dump($args), "\n\n";
                    var_dump(array_key_exists(2, $args));
                    echo " -->";
                    // Page logic
                    if(array_key_exists(2, $args) && $args[2] == 'link-accounts') {
                        include('pages/save.php');
                    }
                    else if($data['ign'] == 0) {
                        include('pages/ingamelink.php');
                    }
                    else if($args[1] == "summary") {
                        include('pages/summary.php');
                    }
                    else if($args[1] == "chat") {
                        include('pages/chat.php');
                    }
                    else if($args[1] == "uploader") {
                        include('pages/uploader.php');
                    }
                    else if($args[1] == "maps") {
                        include('pages/maps.php');
                    }
                    else if($args[1] == "admins") {
                        include('pages/admins.php');
                    }
                    else if($args[1] == "payments") {
                        include('pages/pay-index.php');
                    }
                    else if($args[1] == "settings") {
                        include('pages/settings.php');
                    }
                    else if($args[1] == "modcp") {
                        include('pages/mod-index.php');
                    }
                    ?>
                </div>
            </div>
            <?php
            // If donator, skip this
            if ((array_key_exists("Donor", $gdata) && $gdata["Donor"] < 1) && in_array($args[1], ["summary", "chat", "uploader", "maps", "admins", "settings"])) {
            ?>
            <div id="ad" class="central" style="background-color:#F2F2F2;text-align:center">
                <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                <!-- UCP Bottom Ad -->
                <ins class="adsbygoogle"
                     data-ad-client="ca-pub-7562757927262892"
                     data-ad-slot="2584989039"></ins>
                <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
                <a class="small" href="/payments/">Go premium to get rid of ads!</a>
            </div>
            <script>
                $(document).ready(function () {
                    setTimeout(function () {
                        if ($('ins.adsbygoogle').length < 1 || $('ins.adsbygoogle')[0].innerHTML == "") {
                            $('#ad').html('<p class="error">Please consider disabling your adblock or <a href="/payments/">go premium to disable ads</a>.</p>');
                        }
                    }, 1000);
                });
            </script>
            <?php
            }
            // Display to everyone
            ?>
		    <div id="copyright" class="central">
			    Project New Dawn, 3ventic &copy; 2015 rev <?=$revision?> | Page generated in <?=substr((string)(microtime(true) - $timestart), 0, 5)?>s
		    </div>
            <?php
            if (BRANCH == "normal") {
            ?>
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-54525906-2', 'auto');
      ga('send', 'pageview');

    </script>
            <?php
            }
            ?>
        </div>
        <div class="loading_screen" id="loading">
            <div class="super_centred">
                <h1>Loading...</h1>
                <noscript><p>Please enable javascript.</p></noscript>
            </div>
        </div>
        <script>
        $(document).ready(function() {
            $('#loading').remove();
            $('#fake_body').css('display', 'block');
            $('body').height($(window).height());
        });
        </script>
    </body>
</html>