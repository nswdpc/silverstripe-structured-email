<?php

namespace NSWDPC\StructuredEmail;

/**
 * Trait a {@link \SilverStripe\Control\Email\Email} subclass can use
 * to provide custom parameter handling for a {@link \SilverStripe\Control\Email\Mailer}
 *
 * @author James
 *
 */
trait CustomParameters
{
    /**
     * @var array
     */
    private $customParameters = [];

    /**
     * @param array $args
     */
    public function setCustomParameters(array $args)
    {
        $this->customParameters = $args;
        return $this;
    }

    /**
     * @return array
     */
    public function getCustomParameters(): array
    {
        return $this->customParameters;
    }

    /**
     * @param array $args
     */
    public function clearCustomParameters()
    {
        $this->customParameters = [];
        return $this;
    }
}
