<?php

namespace NSWDPC\StructuredEmail;
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
        return <<<CSS
body {
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

}
