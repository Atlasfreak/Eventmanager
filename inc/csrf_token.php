<?php
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * Verifys that a given token is the correct csrf token.
 *
 * @param string $token the token to be verified
 * @return bool
 */
function verify_csrf_token(string $token): bool {
    if (!empty($token)) {
        if (hash_equals($_SESSION['csrf_token'], $token)) {
            return true;
        } else {
            error_log("[Warning] Someone used an invalid csrf token!");
            return false;
        }
    }
    return false;
}

/**
 * Verifys that a given token is the correct csrf token. Either returns true or exits with code 403.
 *
 * @param string $token the token to be verified
 * @return void
 */
function verify_and_exit_csrf_token(string $token) {
    if (verify_csrf_token($token)) {
        return;
    }
    http_response_code(403);
    exit;
}

/**
 * Verifys that a given token is the correct token for that form.
 *
 * @param string $token the token to be verified
 * @param string $form the form name
 * @return bool
 */
function verify_csrf_form_token(string $token, string $form): bool {
    if (!empty($token)) {
        if (hash_equals(generate_form_token($form), $token)) {
            return true;
        } else {
            error_log("[Warning] Someone used an invalid csrf token!");
            return false;
        }
    }
    return false;
}

/**
 * Verifys that a given token is the correct token for that form. Exits with code 403 if token is invalid.
 *
 * @param string $token the token to be verified
 * @param string $form the form name
 * @return void
 */
function verify_and_exit_csrf_form_token(string $token, string $form) {
    if (verify_csrf_form_token($token, $form)) {
        return;
    }
    http_response_code(403);
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