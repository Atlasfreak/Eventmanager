<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite28543cfcc9fa32263abca66b5c61180
{
    public static $prefixLengthsPsr4 = array (
        'n' => 
        array (
            'nadar\\quill\\' => 12,
        ),
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
        'L' => 
        array (
            'League\\Plates\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'nadar\\quill\\' => 
        array (
            0 => __DIR__ . '/..' . '/nadar/quill-delta-parser/src',
        ),
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
        'League\\Plates\\' => 
        array (
            0 => __DIR__ . '/..' . '/league/plates/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite28543cfcc9fa32263abca66b5c61180::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite28543cfcc9fa32263abca66b5c61180::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInite28543cfcc9fa32263abca66b5c61180::$classMap;

        }, null, ClassLoader::class);
    }
}
