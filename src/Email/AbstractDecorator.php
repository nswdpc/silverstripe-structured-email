<?php

namespace NSWDPC\StructuredEmail;

use SilverStripe\Control\Director;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Core\Manifest\ResourceURLGenerator;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\SiteConfig\SiteConfig;

abstract class AbstractDecorator extends \SilverStripe\Model\ModelData
{
    public const LAYOUT_TYPE_BASIC = 'basic';

    public const LAYOUT_TYPE_BASIC_FULL = 'basic-full';

    public const LAYOUT_TYPE_PLAIN = 'plain';

    private static array $decorations = [];

    /**
     * @var array
     */
    private $_cache_decorations = [];

    private static array $font_sources = [];

    /**
     * @var string
     */
    protected $layout_type = self::LAYOUT_TYPE_BASIC;

    /**
     * @var string
     * Masthead text, will have HTML removed
     */
    private static string $masthead = '';

    /**
     * @var string
     * URL to masthead logo, can be used with/without a Content logo
     */
    private static string $masthead_logo = '';

    /**
     * @var string
     * URL to content logo, can be used with/without a Masthead logo
     */
    private static string $content_logo = '';

    /**
     * @var string
     * HTML physical address of sender
     */
    private static string $physical_address = '';

    /**
     * @inheritdoc
     */
    #[\Override]
    public function getField(string $field): mixed
    {
        return $this->getDecoration($field);
    }

    /**
     * @inheritdoc
     */
    #[\Override]
    public function hasField(string $field): bool
    {
        $this->getDecorations();
        return isset($this->_cache_decorations[ $field ]);
    }

    protected function getDecorations(): array
    {
        if (empty($this->_cache_decorations)) {
            $this->_cache_decorations = $this->config()->get('decorations');
        }

        return $this->_cache_decorations;
    }

    protected function getDecoration(string $decoration): string
    {
        $this->getDecorations();
        $value = $this->_cache_decorations[ $decoration ] ?: '';
        return strval($value);
    }

    /**
     * Return the decorator as CSS for inlining in a template
     */
    #[\Override]
    public function forTemplate(): string
    {
        $decorations = $this->config()->get('decorations');
        $font_sources = $this->config()->get('font_sources');
        $font_sources_value = '';
        if (is_array($font_sources)) {
            foreach ($font_sources as $font_source) {
                $font_sources_value .= "@import url(\"{$font_source}\");\n";
            }
        }

        return <<<CSS
/* font sources */
{$font_sources_value}
/* standard body styles */
body {
    font-weight: normal;
    font-size: {$decorations['FontSize']};
    font-family: {$decorations['FontFamily']};
    color: {$decorations['Color']};
    background-color: {$decorations['BodyBackgroundColor']};
}
CSS;
    }

    public function setLayoutType(string $type): self
    {
        $this->layout_type = $type;
        return $this;
    }

    /**
     * Called via Decorator.LayoutType
     */
    public function getLayoutType(): string
    {
        return $this->layout_type;
    }

    /**
     * Return a copyright string when $EmailDecorator.Copyright is called
     * @return string
     */
    public function getCopyright()
    {
        return _t('StructuredEmail.COPYRIGHT', 'Copyright © {year}', ['year' => date('Y') ]);
    }

    /**
     * Return the physical address
     * This is treated as HTML in the template
     * @return string
     */
    public function getPhysicalAddress()
    {
        $value = $this->config()->get('physical_address');
        if ($value) {
            return DBField::create_field(
                'HTMLFragment',
                _t('StructuredEmail.PHYSICAL_ADDRESS', $value)
            );
        } else {
            return '';
        }
    }

    /**
     * Return the masthead
     * Use the placeholder `SiteConfig.Title` to return the result of that method
     * @return string
     */
    public function getMasthead()
    {
        $value = $this->config()->get('masthead');
        if ($value == "SiteConfig.Title" && class_exists(SiteConfig::class)) {
            $config = SiteConfig::current_site_config();
            return $config ? $config->Title : '';
        } elseif ($value) {
            return _t('StructuredEmail.MASTHEAD', $value);
        } else {
            return '';
        }
    }

    /**
     * Return the masthead link, if any
     * Use the placeholder `Director.absoluteBaseURL` to return the result of that method
     * @return string
     */
    public function getMastheadLink()
    {
        $value = $this->config()->get('masthead_link');
        if ($value == "Director.absoluteBaseURL") {
            return Director::absoluteBaseURL();
        } else {
            return $value;
        }
    }

    /**
     * Return the signoff link, if any
     * Use the placeholder `Director.absoluteBaseURL` to return the result of that method
     * This link is displayed as text at the bottom of the email
     * @return string
     */
    public function getSignoffLink()
    {
        $value = $this->config()->get('signoff_link');
        if ($value == "Director.absoluteBaseURL") {
            return Director::absoluteBaseURL();
        } else {
            return $value;
        }
    }

    /**
     * Return the masthead logo URL
     * @return string
     */
    public function getMastheadLogo()
    {
        $value = $this->config()->get('masthead_logo');
        if (!$value) {
            return '';
        }

        return $this->convertToResourceUrl($value);
    }

    /**
     * Return the content logo URL
     * @return string
     */
    public function getContentLogo()
    {
        $value = $this->config()->get('content_logo');
        if (!$value) {
            return '';
        }

        return $this->convertToResourceUrl($value);
    }

    /**
     * Convert parameter to a resource URL
     * @param string $resourceOrURL - either a absolute URL or a resource understandable by urlForResource
     */
    public function convertToResourceUrl(string $resourceOrURL): string
    {
        $scheme = parse_url($resourceOrURL, PHP_URL_SCHEME);
        if ($scheme != '') {
            // return the URL
            return $resourceOrURL;
        } else {
            // process path
            return Injector::inst()->get(ResourceURLGenerator::class)->urlForResource($resourceOrURL);
        }
    }
}
