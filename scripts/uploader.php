<?php

if (isset($_FILES["map"])) {
    ucpLog("uploader.log", "{$gdata["user"]} (ID:{$gdata["ID"]}) uploaded a file (name: {$_FILES["map"]["name"]}, size: {$_FILES["map"]["size"]} bytes, type: {$_FILES["map"]["type"]}, error: {$_FILES["map"]["error"]})");
    if ($_FILES["map"]["size"] > 8000000) {
        dieHeader('File too large (' . $_FILES["map"]["size"] . ')');
    }
    if ($_FILES["map"]["size"] < 100) {
        dieHeader('File too small or non-existent (' . $_FILES["map"]["size"] . ')');
    }
    $zip = new ZipArchive();
    $res = $zip->open($_FILES["map"]["tmp_name"], ZipArchive::CHECKCONS);
    if ($res !== true) {
        switch ($res) {
            case ZipArchive::ER_NOZIP :
                dieHeader('Not a ZIP archive');
            case ZipArchive::INCONS :
                dieHeader('ZIP consistency check failed');
            case ZipArchive::ER_CSC :
                dieHeader('ZIP checksum check failed');
            case ZipArchive::ER_MEMORY :
                dieHeader('Memory failure');
            case ZipArchive::ER_NOENT :
                dieHeader('File went missing! Contact 3ventic.');
            case ZipArchive::ER_OPEN :
                dieHeader('Cannot open ZIP file');
            case ZipArchive::ER_READ :
                dieHeader('Error reading the ZIP file');
            case ZipArchive::ER_SEEK :
                dieHeader('Unknown Error');
        }
    }
    if (!$zip->getFromName("meta.xml")) {
        dieHeader('Could not find or read meta.xml');
    }
    $name = str_replace(".zip", "", $_FILES["map"]["name"]) . "-" . time() . ".zip";
    if (strlen($name) > 50) {
        dieHeader('Filename is too long! (max 39 chars)');
    }
    if (move_uploaded_file($_FILES["map"]["tmp_name"], UPLOAD_DIR . $name)) {
        $game->addUploadedMap($gdata["ID"], $name);
        dieHeader('Success');
    }
    else {
        dieHeader('Failed to move the file');
    }
}
else {
    dieHeader('Error, no file');
}

function dieHeader($text)
{
    header("x-response-text: $text", true);
    die($text);
}