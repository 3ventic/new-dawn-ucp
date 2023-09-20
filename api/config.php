<?php

header("Content-Type: application/json", true);

function apiError($code, $message) {
    return ["error" => true, "code" => $code, "message" => $message];
}