<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1d8c8014a4eb576d0f85f66ce21d986a
{
    public static $prefixLengthsPsr4 = array (
        'd' => 
        array (
            'dekor\\' => 6,
        ),
        'B' => 
        array (
            'BenMorel\\ApacheLogParser\\' => 25,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'dekor\\' => 
        array (
            0 => __DIR__ . '/..' . '/dekor/php-array-table/src',
        ),
        'BenMorel\\ApacheLogParser\\' => 
        array (
            0 => __DIR__ . '/..' . '/benmorel/apache-log-parser/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit1d8c8014a4eb576d0f85f66ce21d986a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1d8c8014a4eb576d0f85f66ce21d986a::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit1d8c8014a4eb576d0f85f66ce21d986a::$classMap;

        }, null, ClassLoader::class);
    }
}
