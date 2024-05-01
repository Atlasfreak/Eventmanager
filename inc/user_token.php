<?php
include_once ("header_base.php");

/**
 * Creates a value from unique participant attributes and a timestamp to be used for hashing
 *
 * @param array{
 *  id: int,
 *  lastName: string,
 *  firstName: string,
 *  created: string,
 *  edited: string
 * } $participant_data
 * @param float $timestamp
 * @return string the computed value
 */
function create_hash_value(array $participant_data, float $timestamp): string {
    return $participant_data["id"] . $participant_data["last_name"] . $participant_data["first_name"] . $participant_data["created"] . $participant_data["edited"] . $timestamp;
}

/**
 * Creates a token for the given participant and timestamp.
 * The token consists of the timestamp followed by a sha1 hash of the participant data and timestamp.
 * @see create_hash_value() is used to create the value that is hashed
 *
 * @param array{
 *  id: int,
 *  lastName: string,
 *  firstName: string,
 *  created: string,
 *  edited: string
 * } $participant_data
 * @param float $timestamp
 * @return string the created token
 */
function make_token(array $participant_data, float $timestamp): string {
    $hash = sha1(create_hash_value($participant_data, $timestamp) . CONFIG_DATA["general"]["secret"]);
    return $timestamp . "-" . $hash;
}


/**
 * @return float a shortened current timestamp
 */
function create_timestamp() {
    // Shorten the timestamp as there is no need for seconds of precision
    // shortens the link
    return floor(time() / 10000);
}

/**
 * Creates a token with the current timestamp
 * @see create_timestamp()
 * @see make_token()
 *
 * @param array $participant_data
 * @return string the token
 */
function make_token_current_time(array $participant_data): string {
    $timestamp = create_timestamp();
    return make_token($participant_data, $timestamp);
}

/**
 * Verifies that a given token is valid for the given participant
 *
 * @param array $participant_data
 * @param string $token
 * @return bool whether the token is valid
 */
function check_token(array $participant_data, string $token): bool {
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