<?php
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * Verifys that a given token is the correct csrf token. Either returns true or exits with code 403.
 *
 * @param string $token the token to be verified
 * @return bool
 */
function verify_csrf_token(string $token): bool {
    $response_code = http_response_code();
    http_response_code(403);
    if (!empty($token)) {
        if (hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code($response_code);
            return true;
        } else {
            error_log("[Warning] Someone used an invalid csrf token!");
            exit;
        }
    }
    exit;
}

/**
 * Verifys that a given token is the correct token for that form. Either returns true or exits with code 403.
 *
 * @param string $token the token to be verified
 * @param string $form the form name
 * @return bool
 */
function verify_csrf_form_token(string $token, string $form): bool {
    $response_code = http_response_code();
    http_response_code(403);
    if (!empty($token)) {
        if (hash_equals(generate_form_token($form), $token)) {
            http_response_code($response_code);
            return true;
        } else {
            error_log("[Warning] Someone used an invalid csrf token!");
            exit;
        }
    }
    exit;
}

/**
 * Generate a token for a specific form
 *
 * @param string $form the name of the form
 * @return string the token
 */
function generate_form_token(string $form): string {
    return hash_hmac("sha256", $form, $_SESSION['csrf_token']);
}

?>