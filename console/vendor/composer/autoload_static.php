<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit64293a56245c3da3d4f45e560321c257
{
    public static $prefixLengthsPsr4 = array (
        'l' => 
        array (
            'library\\wuti\\shard\\' => 19,
            'library\\wuti\\log\\' => 17,
            'library\\wuti\\database\\' => 22,
            'library\\wuti\\cache\\' => 19,
        ),
        'f' => 
        array (
            'framework\\wuti\\factory\\' => 23,
        ),
        'c' => 
        array (
            'console\\model\\' => 14,
            'console\\lib\\' => 12,
            'console\\job\\' => 12,
            'console\\factory\\' => 16,
        ),
        'V' => 
        array (
            'Vendor\\Namespace\\' => 17,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'library\\wuti\\shard\\' => 
        array (
            0 => __DIR__ . '/../..' . '/../composers/library/wuti/shard',
        ),
        'library\\wuti\\log\\' => 
        array (
            0 => __DIR__ . '/../..' . '/../composers/library/wuti/log',
        ),
        'library\\wuti\\database\\' => 
        array (
            0 => __DIR__ . '/../..' . '/../composers/library/wuti/database',
        ),
        'library\\wuti\\cache\\' => 
        array (
            0 => __DIR__ . '/../..' . '/../composers/library/wuti/cache',
        ),
        'framework\\wuti\\factory\\' => 
        array (
            0 => __DIR__ . '/../..' . '/../composers/framework/wuti/factory',
        ),
        'console\\model\\' => 
        array (
            0 => __DIR__ . '/../..' . '/model',
        ),
        'console\\lib\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib',
        ),
        'console\\job\\' => 
        array (
            0 => __DIR__ . '/../..' . '/job',
        ),
        'console\\factory\\' => 
        array (
            0 => __DIR__ . '/../..' . '/factory',
        ),
        'Vendor\\Namespace\\' => 
        array (
            0 => __DIR__ . '/../..' . '/',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit64293a56245c3da3d4f45e560321c257::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit64293a56245c3da3d4f45e560321c257::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
