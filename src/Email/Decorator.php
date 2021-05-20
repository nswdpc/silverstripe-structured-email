<?php

namespace NSWDPC\StructuredEmail;

use SilverStripe\Core\Injector\Injectable;
use SilverStripe\View\ViewableData;

/**
 * Standard decorator for inlining styles into emails
 */
class Decorator extends AbstractDecorator {


    use Injectable;

    /**
     * @var string
     */
    private static $type = 'basic';

    /**
     * @var array
     */
    private static $decorations = [
        'FontFamily' => "-apple-system, BlinkMacSystemFont, avenir next, avenir, segoe ui, helvetica neue, helvetica, Ubuntu, roboto, noto, arial, sans-serif",
        'FontSize' => '16px',
        'BodyBackgroundColor' => '#F2F4F6',
        'BackgroundColor' => '#ffffff',
        'Color' => '#51545E',
        'SubColor' => '#6B6E76',
        'HeadingColor' => '#333333',
        'PrimaryColor' => '#002664',
        'PrimaryTextColor' => '#ffffff',
        'HighlightColor' => '#d7153a',
        'SecondaryColor' => '#2e5299'
    ];

    /**
     * @var array
     */
    private static $font_sources = [];


}
