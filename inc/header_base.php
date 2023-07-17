<?php
include_once(__DIR__ . "/../config.php");

if (session_status() !== PHP_SESSION_ACTIVE and empty($_SESSION))
    session_start($session_options ?? ["name" => "ANMELDUNGSESSID"]);

include_once(__DIR__ . "/csrf_token.php");

if (!CONFIG_DATA["general"]["debug"])
    error_reporting(0);

use nadar\quill\listener\BackgroundColor;

// source: Laravel Framework
// https://github.com/laravel/framework/blob/8.x/src/Illuminate/Support/Str.php
if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle) {
        return (string) $needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}
if (!function_exists('str_ends_with')) {
    function str_ends_with($haystack, $needle) {
        return $needle !== '' && substr($haystack, -strlen($needle)) === (string) $needle;
    }
}
if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle) {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}

spl_autoload_register(function ($class) {
    if (str_starts_with($class, "League")) {
        $class = preg_split("/League\\\\Plates\\\\/", $class);
        $class = str_replace("\\", "/", $class[1]);
        include __DIR__ . "/../plates/" . $class . ".php";
    } elseif (str_starts_with($class, "nadar")) {
        $class = preg_split("/nadar\\\\quill\\\\/", $class);
        $class = str_replace("\\", "/", $class[1]);
        include __DIR__ . "/../quill_delta_parser/" . $class . ".php";
    }
});
require "quill_listener/backgroundColor.php";

require __DIR__ . "/../plates/Engine.php";

$templates = new \League\Plates\Engine();
$templates->addFolder("main", __DIR__ . "/../templates");
$templates->addFolder("admin", __DIR__ . "/../admin/templates");

function parse_delta(string $json): string {
    $lexer = new \nadar\quill\Lexer($json);
    $lexer->registerListener(new BackgroundColor);
    return $lexer->render();
}

setlocale(LC_TIME, "de_DE", "deu", "de");
putenv('LANG=de_DE.UTF8');
putenv('LANGUAGE=de_DE.UTF8');

function exit_with_code(int $code) {
    http_response_code($code);
    exit;
}
function redirect(string $location) {
    header("Location:" . $location);
    exit;
}

function add_type_to_msgs(array $messages, string $type) {
    foreach ($messages as $key => $value) {
        $replacement = array();
        if (!is_array($value) or !key_exists("msg", $value)) {
            $replacement["msg"] = $value;
        } else {
            $replacement["msg"] = $value["msg"];
        }
        $replacement["type"] = $type;
        $messages[$key] = $replacement;
    }
    return $messages;
}

function check_if_empty(array $data, array $keys, ?string $err_msg = null, ?array $errors = null) {
    if ($errors === null)
        $errors = [];

    $data_keys = array_keys($data);

    foreach ($keys as $key) {
        // Backwards compatibility
        if (!str_starts_with($key, "/")) {
            $key = "/" . preg_quote($key, "/") . "/";
        }

        $matched = preg_grep($key, $data_keys);
        if ($matched == false) {
            $errors[$key] = "empty";
            continue;
        }
        foreach ($matched as $matched_key) {
            if (empty($data[$matched_key])) {
                $errors[$matched_key] = $err_msg ? $err_msg : "empty";
            }
        }
    }

    return $errors;
}