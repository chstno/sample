<?php


namespace Core\Support;


interface ViewInterface
{
    public static function render(string $template): string;
    public static function callbackReplace(callable $getStringCallback, string $replace);
    public static function getAssets(): array;
}