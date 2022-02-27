<?php
include_once("header_base.php");

function create_hash_value($participant_data, $timestamp) {
    return $participant_data["id"].$participant_data["lastname"].$participant_data["firstname"].$participant_data["created"].$participant_data["edited"].$timestamp;
}

function make_token($participant_data, $timestamp) {
    $hash = sha1(create_hash_value($participant_data, $timestamp).CONFIG_DATA["general"]["secret"]);
    return $timestamp."-".$hash;
}

function create_timestamp() {
    // Shorten the timestamp as there is no need for seconds of precision
    // shortens the link
    return floor(time()/10000);
}

function make_token_current_time($participant_data) {
    $timestamp = create_timestamp();
    return make_token($participant_data, $timestamp);
}

function check_token($participant_data, $token) {
    if (!($participant_data and $token)) {
        return false;
    }
    $split_token = explode("-", $token, 2);

    if (sizeof($split_token) !== 2 or empty($split_token[0]) or empty($split_token[1])) {
        return false;
    }

    $timestamp = $split_token[0];

    if (!hash_equals(make_token($participant_data, $timestamp), $token)) {
        return false;
    }

    return true;
}

?>