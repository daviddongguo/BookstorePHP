<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit80299fb774f6eb2b0a58d902ddac1f2e
{
    public static $files = array (
        '320cde22f66dd4f5d3fd621d3e88b98f' => __DIR__ . '/..' . '/symfony/polyfill-ctype/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Twig\\' => 5,
        ),
        'S' => 
        array (
            'Symfony\\Polyfill\\Ctype\\' => 23,
            'Slim\\Views\\' => 11,
        ),
        'P' => 
        array (
            'Psr\\Log\\' => 8,
        ),
        'M' => 
        array (
            'Monolog\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Twig\\' => 
        array (
            0 => __DIR__ . '/..' . '/twig/twig/src',
        ),
        'Symfony\\Polyfill\\Ctype\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-ctype',
        ),
        'Slim\\Views\\' => 
        array (
            0 => __DIR__ . '/..' . '/slim/views',
        ),
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
        'Monolog\\' => 
        array (
            0 => __DIR__ . '/..' . '/monolog/monolog/src/Monolog',
        ),
    );

    public static $prefixesPsr0 = array (
        'T' => 
        array (
            'Twig_' => 
            array (
                0 => __DIR__ . '/..' . '/twig/twig/lib',
            ),
        ),
        'S' => 
        array (
            'Slim' => 
            array (
                0 => __DIR__ . '/..' . '/slim/slim',
            ),
        ),
    );

    public static $classMap = array (
        'DB' => __DIR__ . '/..' . '/sergeytsalkov/meekrodb/db.class.php',
        'DBHelper' => __DIR__ . '/..' . '/sergeytsalkov/meekrodb/db.class.php',
        'DBTransaction' => __DIR__ . '/..' . '/sergeytsalkov/meekrodb/db.class.php',
        'MeekroDB' => __DIR__ . '/..' . '/sergeytsalkov/meekrodb/db.class.php',
        'MeekroDBEval' => __DIR__ . '/..' . '/sergeytsalkov/meekrodb/db.class.php',
        'MeekroDBException' => __DIR__ . '/..' . '/sergeytsalkov/meekrodb/db.class.php',
        'WhereClause' => __DIR__ . '/..' . '/sergeytsalkov/meekrodb/db.class.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit80299fb774f6eb2b0a58d902ddac1f2e::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit80299fb774f6eb2b0a58d902ddac1f2e::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit80299fb774f6eb2b0a58d902ddac1f2e::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit80299fb774f6eb2b0a58d902ddac1f2e::$classMap;

        }, null, ClassLoader::class);
    }
}
