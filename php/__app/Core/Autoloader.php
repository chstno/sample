<?php


namespace Core;


final class Autoloader
{

    protected static string $root;

    public static function register()
    {
        self::$root = dirname(__DIR__);
        spl_autoload_register([self::class, 'loadClass'], true, true);
    }

    public static function loadClass($class): bool
    {
        $file = strtr(self::$root.DIRECTORY_SEPARATOR.$class,'\\', DIRECTORY_SEPARATOR).'.php';

        if (file_exists($file)) {

            require_once $file;

            if (method_exists($class, '__onAutoload')) {
                try {
                    $class::__onAutoload();
                } catch (\Throwable $e) {
                    throw new \BadMethodCallException("[{$class}]: __onAutoload must be static!");
                }
            }

            return true;
        }

        return false;
    }
}

Autoloader::register();