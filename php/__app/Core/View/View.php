<?php


namespace Core\View;

use Core\Support\ViewInterface;

/**
 * Class View
 *
 * Pretty simple example and no more
 *
 * @package Core\View
 */
class View implements ViewInterface
{
    protected static string $templatesDirectory;
    protected static string $assetsDirectory;

    protected static array  $assets = [];
    protected static array  $assetsTypes = ['stylesheet' => 'css', 'script' => 'js'];

    protected static string $currentTemplate;

    /**
     * @var array<callable> ['template' => 'callback($buffer)']
     */
    protected static array $renderCallback;

    protected static function asset(string $template)
    {
        $assetFilePath = static::$assetsDirectory . $template;
        $assetPath = dirname($assetFilePath);

        if (file_exists($assetPath)) {
            foreach (static::$assetsTypes as $rel => $type) {
                if (file_exists("{$assetFilePath}.{$type}")) {
                    static::$assets[$rel][] = "{$template}.{$type}";
                }
            }
        }
    }

    public static function getAssets(): array
    {
        return static::$assets;
    }

    /**
     * @param callable $renderCallback running after ob_get_clean for current template
     */
    public static function callback(callable $renderCallback)
    {
        static::$renderCallback[static::$currentTemplate] = $renderCallback;
    }

    /**
     *
     * Yeah, there is alternative way with stopping buffer and reorder content, but:
     * this is unreadable/unmaintainable code that unnecessarily complicates the logic
     *
     * And again: that's conceptual example (that used for assets)
     *
     * @param callable $getStringCallback
     * @param ?string $replace
     */

    public static function callbackReplace(callable $getStringCallback, ?string $replace = null)
    {
        if ($replace === null)
            $replace = '{{'.uniqid('replace_').'}}';

        static::callback(function (&$buffer) use ($getStringCallback, $replace) {
            return $buffer = strtr($buffer, [$replace => $getStringCallback()]);
        });

        echo $replace;
    }

    public static function __onAutoload()
    {
        static::$templatesDirectory = app()->viewsPath;
        static::$assetsDirectory = app()->assetsPath;
    }

    public static function render(string $template, array $data = []): string
    {
        $templatePath = static::$templatesDirectory . "{$template}.php";
        static::$currentTemplate = $template;

        if (file_exists($templatePath)) {
            static::asset($template);
            ob_start();
            // yes, there is an option for unsecure use,
            // but it is assumed that secure data that has passed validation will be transferred here
            // (and view class is not responsible for that, obviously)
            extract($data);
            include $templatePath;
            $content = ob_get_clean();
        } else {
            throw new \InvalidArgumentException("[". static::class ."]: template is not found!");
        }

        // ob_get_clean does not uses a specified callback.. php, wtf? (ob_start($callback))
        if (isset(static::$renderCallback[$template]) && $callback = static::$renderCallback[$template])
            $content = $callback($content);

        return $content;
    }
}