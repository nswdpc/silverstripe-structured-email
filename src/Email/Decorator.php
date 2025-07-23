<?php

namespace NSWDPC\StructuredEmail;

use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;

/**
 * Standard decorator for inlining styles into emails
 */
class Decorator extends AbstractDecorator
{
    use Injectable;

    use Configurable;

    private static string $type = 'basic';

    private static array $decorations = [
        'FontFamily' => "-apple-system, BlinkMacSystemFont, avenir next, avenir, segoe ui, helvetica neue, helvetica, Ubuntu, roboto, noto, arial, sans-serif",
        'FontSize' => '16px',
        'BodyBackgroundColor' => '#F2F4F6',
        'BackgroundColor' => '#ffffff',
        'Color' => '#51545E',
        'SubColor' => '#6B6E76',
        'HeadingColor' => '#333333',
        'PrimaryColor' => '#002664',
        'PrimaryTextColor' => '#ffffff',
        'PrimaryButtonColor' => '#002664',
        'PrimaryButtonTextColor' => '#ffffff',
        'HighlightColor' => '#d7153a',
        'SecondaryColor' => '#2e5299',
        'DarkModeBackgroundColor' => '#121212',
        'DarkModeBackgroundSubColor' => '#333333',
        'DarkModeColor' => '#ffffff',
        'DarkModeButtonColor' => '#002664'
    ];

    private static array $font_sources = [];
}
