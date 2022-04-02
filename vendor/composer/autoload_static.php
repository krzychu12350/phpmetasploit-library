<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit485edd850c582654914cacbb822d8870
{
    public static $prefixLengthsPsr4 = array (
        'K' => 
        array (
            'Krzychu12350\\Phpmetasploit\\' => 27,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Krzychu12350\\Phpmetasploit\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit485edd850c582654914cacbb822d8870::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit485edd850c582654914cacbb822d8870::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit485edd850c582654914cacbb822d8870::$classMap;

        }, null, ClassLoader::class);
    }
}