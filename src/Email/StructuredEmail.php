<?php

namespace NSWDPC\StructuredEmail;

use SilverStripe\Control\Email\Email;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\View\ArrayData;
use SilverStripe\View\ViewableData;

/**
 * Convert standard email template requests to structured email
 */
class StructuredEmail extends Email {

    use Configurable;

    /**
     * @var bool
     */
    private static $is_structured = true;

    /**
     * @var NSWDPC\StructuredEmail\AbstractDecorator
     **/
    private $decorator = null;

    /**
     * @return self
     */
    public function setDecorator(AbstractDecorator $decorator) : self {
        $this->decorator = $decorator;
        return $this;
    }

    /**
     * @return AbstractDecorator
     */
    public function getDecorator() : AbstractDecorator {
        return $this->decorator ? $this->decorator : Injector::inst()->create(Decorator::class);
    }

    /**
     * @inheritdoc
     */
    public function renderWith($template, $customFields = null)
    {

        // Base configurable data for all emails
        $data = [];
        $data['EmailCopyright'] = _t('StructuredEmail.COPYRIGHT', 'Copyright © {year}', ['year' => date('Y') ]);

        if(!$customFields instanceof ViewableData) {
            if(is_array($customFields)) {
                // merge custom fields on top of base data to allow override
                $data = array_merge($data, $customFields);
            }
            // ensure it is a viewable data
            $customFields = ViewableData::create();
        }

        foreach($data as $field => $value) {
            $customFields->setField($field, $value);
        }

        $customFields->setField( 'EmailDecorator', $this->getDecorator());

        return parent::renderWith($template, $customFields);
    }
}
