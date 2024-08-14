<?php

namespace NSWDPC\StructuredEmail;

/**
 * Emails with custom parameter handling should implement this handling
 *
 * This allows for simple instanceof detection when a message supporting
 * custom parameters is being sent
 *
 * @author James
 */
interface EmailWithCustomParameters
{
    /**
     * @return self
     */
    public function setCustomParameters(array $args);

    public function getCustomParameters(): array;

    /**
     * @return self
     */
    public function clearCustomParameters();
}
