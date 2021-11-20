<?php
    use nadar\quill\listener\BackgroundColor;

    // source: Laravel Framework
    // https://github.com/laravel/framework/blob/8.x/src/Illuminate/Support/Str.php
    if (!function_exists('str_starts_with')) {
        function str_starts_with($haystack, $needle) {
            return (string)$needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
        }
    }
    if (!function_exists('str_ends_with')) {
        function str_ends_with($haystack, $needle) {
            return $needle !== '' && substr($haystack, -strlen($needle)) === (string)$needle;
        }
    }
    if (!function_exists('str_contains')) {
        function str_contains($haystack, $needle) {
            return $needle !== '' && mb_strpos($haystack, $needle) !== false;
        }
    }

    spl_autoload_register(function ($class){
        if(str_starts_with($class, "League")) {
            $class = preg_split("/League\\\\Plates\\\\/", $class);
            $class = str_replace("\\", "/", $class[1]);
            include __DIR__."/../plates/".$class.".php";
        } elseif(str_starts_with($class, "nadar")) {
            $class = preg_split("/nadar\\\\quill\\\\/", $class);
            $class = str_replace("\\", "/", $class[1]);
            include __DIR__."/../quill_delta_parser/".$class.".php";
        }
    });
    require "quill_listener/backgroundColor.php";

    require __DIR__."/../plates/Engine.php";
    include(__DIR__."/../config.php");

    $templates = new \League\Plates\Engine();
    $templates->addFolder("main", __DIR__."/../templates");
    $templates->addFolder("admin", __DIR__."/../admin/templates");

    function parse_delta($json) {
        $lexer = new \nadar\quill\Lexer($json);
        $lexer->registerListener(new BackgroundColor);
        return $lexer->render();
    }

    setlocale(LC_TIME, "de_DE", "deu", "de");
    putenv('LANG=de_DE.UTF8');
    putenv('LANGUAGE=de_DE.UTF8');

?>