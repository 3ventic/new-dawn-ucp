<script src="/external/modcp.js?v=<?=crc32($revision)?>"></script>
<div id="mod-nav">
    <div class="mod-nav"><a href="/modcp/search/" class="button">Search Players</a></div>
    <div class="mod-nav"><a href="/modcp/bans/" class="button">Search Bans</a></div>
</div>
<?php

if($args[2] == "search")
{
    include("pages/mod-search.php");
}
else if ($args[2] == "bans") {
    include("pages/mod-bans.php");
}