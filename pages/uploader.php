<div id="mod-nav">
    <div class="mod-nav"><a href="/uploader/" class="button">Send</a></div>
    <div class="mod-nav"><a href="/uploader/list/" class="button">Uploaded</a></div>
    <div class="mod-nav"><a href="/uploader/list/<?=$gdata["ID"]?>" class="button">Mine</a></div>
</div>

<?php
if (!isset($args[2]) || empty($args[2])) {
?>
    <p class="info">The file must be a valid ZIP archive. It must contain a MAP file and <code>meta.xml</code> file. Filename's length must be less than or equal to <strong>39</strong>! Max filesize is 8 MB</p>
    <form enctype="multipart/form-data" method="POST" id="upload-form">
        <input type="hidden" name="MAX_FILE_SIZE" value="8000000" />
        Send this file: <input name="map" type="file" />
        <input type="button" id="upload-button" value="Send File" />
    </form>
    <div class="progress">
        <div id="progress_outer"><div id="progress_inner"></div><div id="progress_text">0%</div></div>
    </div>
    <script src="/external/upload.js"></script>
<?php
}
else if ($args[2] == "list") {
    $page = isset($args[4]) && is_numeric($args[4]) ? (int)$args[4] : 0;
    $user = isset($args[3]) && is_numeric($args[3]) ? (int)$args[3] : -1;
    $maps = $game->listUploadedMaps($user, $page);
    $table_rows = "";
    $statuses = [-1 => "Declined", 0 => "Untested", 1 => "Accepted"];
    foreach ($maps as $map)
    {
        $filename = htmlspecialchars($map["filename"]);
        $acdc = "";
        if ($gdata["Admin"] > 3) {
            $filename = "<a href=\"/uploader/download/$filename\">$filename</a>";
            $acdc = "<td><a href=\"#\" onclick=\"acceptMap({$map["id"]}, 1)\" class=\"button\">Accept</a><a href=\"#\" onclick=\"acceptMap({$map["id"]}, -1)\" class=\"button\">Decline</a></td>";
        }
        $table_rows .= "<tr class=\"status{$map["status"]}\"><td><a href=\"/summary/{$map["userid"]}\">" . htmlspecialchars($game->getMemberData($map["userid"])["user"]) . "</a></td><td>$filename</td><td>{$map["date"]}</td><td class=\"status\" data-id=\"{$map["id"]}\">{$statuses[$map["status"]]}</td>$acdc</tr>";
        $table_rows .= "<tr class=\"hidden\" id=\"reason-{$map["id"]}\"><td><div><p><strong>Reason:</strong> " . htmlspecialchars($map["reason"]) . "</p></div></td></tr>";
    }
    $lastbutton = "";
    if ($page > 0)
    {
        $lastbutton = '<a class="button" href="/uploader/list/' . $user . '/' . ($page - 1) . '">&lt;</a>';
    }
    $nextbutton = "";
    if (count($maps) >= 29)
    {
        $nextbutton = '<a class="button" href="/uploader/list/' . $user . '/' . ($page + 1) . '">&gt;</a>';
    }
    echo $lastbutton, $nextbutton;
    ?>
    <table id="uploadlist">
        <thead>
            <tr>
                <th>Author</th>
                <th>File</th>
                <th>Date</th>
                <th>Status</th>
                <?=$gdata["Admin"] > 3 ? "<th>Accept / Decline</th>" : ""?>
            </tr>
        </thead>
        <tbody>
            <?=$table_rows?>
        </tbody>
    </table>
    <script src="/external/uploadaccept.js"></script>
<?php
}
else if ($args[2] == "download" && $gdata["Admin"] > 3) {
    if (file_exists(UPLOAD_DIR . $args[3])) {
        header("Content-Type: application/zip");
        readfile(UPLOAD_DIR . $args[3]);
    }
}