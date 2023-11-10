<?php

use Core\App;
use JetBrains\PhpStorm\NoReturn;
use JetBrains\PhpStorm\Pure;

if (!function_exists('app')) {

    #[Pure]
    function app(): App
    {
        return App::app();
    }

}

if (!function_exists('instance')) {

    function instance(string $class, ...$args)
    {
        return \app()->instance($class, ...$args);
    }

}

if (!function_exists('request')) {

    function request()
    {
        return \app()->instance(\Core\Support\RequestInterface::class);
    }

}

if (!function_exists('response')) {
    /**
     * @param string $body
     * @param int $status
     * @param array $headers
     *
     * @return mixed
     */
    function response(string $body = '', int $status = 200, array $headers = []): \Core\Response
    {
        $response = \app()->instance(\Core\Support\ResponseInterface::class);
        return $response->make($body, $status, $headers);
    }

}

if (!function_exists('asset')) {

    #[Pure]
    function asset(string $asset): string
    {
        if (file_exists(\app()->assetsPath . $asset))
            return \app()->assetsUrl . $asset;

        return "";
    }
}

if (!function_exists('redirect')) {

    #[NoReturn]
    function redirect(string $uri, int $status = 301)
    {
        header('Location: ' . $uri, true, $status);
        exit();
    }
}

if (!function_exists('tag')) {

    function tag(string $name, array $attributes, bool $selfClosing = false): string
    {
        $tag = "<$name ";
        foreach ($attributes as $attr => $value) {
            $tag .= "$attr = \"$value\" ";
        }
        $tag .= !$selfClosing ? "</$name>" : "/>";

        return $tag;
    }

}

if (!function_exists('_assets')) {

    #[Pure]
    function _assets($assets): string
    {
        $renderedAssets = '';
        foreach ($assets as $rel => $assetOfType) {
            foreach ($assetOfType as $assetPath) {
                $renderedAssets .= tag('link', ['rel' => $rel, 'href' => asset($assetPath)]);
            }
        }

        return $renderedAssets;
    }
}