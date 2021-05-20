<?php

namespace NSWDPC\StructuredEmail;

use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBHTMLFragment;
use SilverStripe\View\ViewableData;

abstract class AbstractDecorator extends ViewableData {

    const LAYOUT_TYPE_BASIC = 'basic';
    const LAYOUT_TYPE_BASIC_FULL = 'basic-full';
    const LAYOUT_TYPE_PLAIN = 'plain';

    /**
     * @var array
     */
    private static $decorations = [];

    /**
     * @var array
     */
    private $_cache_decorations = [];

    /**
     * @var array
     */
    private static $font_sources = [];

    /**
     * @var string
     */
    protected $layout_type = self::LAYOUT_TYPE_BASIC;

    /**
     * @inheritdoc
     */
    public function getField($field)
    {
        return $this->getDecoration($field);
    }

    /**
     * @inheritdoc
     */
    public function hasField($field) {
        $this->getDecorations();
        return isset($this->_cache_decorations[ $field ]);
    }

    /**
     * @return array
     */
    protected function getDecorations() : array {
        if(empty($this->_cache_decorations)) {
            $this->_cache_decorations = $this->config()->get('decorations');
        }
        return $this->_cache_decorations;
    }

    /**
     * @return string
     */
    protected function getDecoration(string $decoration) : string {
        $this->getDecorations();
        $value = $this->_cache_decorations[ $decoration ] ?: '';
        return strval($value);
    }

    /**
     * Return the decorator as CSS for inlining in a template
     * @return string
     */
    public function forTemplate() : string {
        $decorations = $this->config()->get('decorations');
        $font_sources = $this->config()->get('font_sources');
        $font_sources_value = '';
        if(is_array($font_sources)) {
            foreach($font_sources as $font_source) {
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

    /**
     * @return self
     */
    public function setLayoutType(string $type) : self {
        $this->layout_type = $type;
        return $this;
    }

    /**
     * Called via Decorator.LayoutType
     * @return string
     */
    public function getLayoutType() : string {
        return $this->layout_type;
    }

    /**
     * Return a copyright string when $EmailDecorator.Copyright is called
     * @return string
     */
    public function getCopyright() {
        return _t('StructuredEmail.COPYRIGHT', 'Copyright © {year}', ['year' => date('Y') ]);
    }

    /**
     * Return the physical address
     * This is treated as HTML in the template
     * @return string
     */
    public function getPhysicalAddress() {
        $value = $this->config()->get('physical_address');
        if($value) {
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
     * @return string
     */
    public function getMasthead() {
        $value = $this->config()->get('masthead');
        if($value) {
            return _t('StructuredEmail.MASTHEAD', $value);
        } else {
            return '';
        }
    }

    /**
     * Return the masthead
     * @return string
     */
    public function getMastheadLink() {
        $value = $this->config()->get('masthead_link');
        if($value) {
            return _t('StructuredEmail.MASTHEAD_LINK', $value);
        } else {
            return '';
        }
    }

    /**
     * Return the masthead logo URL
     * @return string
     */
    public function getMastheadLogo() {
        $value = $this->config()->get('masthead_logo');
        if($value) {
            return $value;
        } else {
            return '';
        }
    }

}
