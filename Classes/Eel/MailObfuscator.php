<?php

namespace Networkteam\Neos\MailObfuscator\Eel;

use Neos\Eel\ProtectedContextAwareInterface;
use Networkteam\Neos\MailObfuscator\Converter\Mailto2HrefObfuscatingConverter;
use Networkteam\Neos\MailObfuscator\Converter\RewriteAtCharConverter;

/**
 * Eel helpers to provide MailObfuscator functions
 */
class MailObfuscator extends RewriteAtCharConverter implements ProtectedContextAwareInterface {

    /**
     * Convert at Character
     *
     * @param $email string
     * @return string
     */
    public function convertAtChar($email = false) {
        return $this->convert($email);
    }

    /**
     * Convert Mailto to Href
     *
     * @param $email string
     * @return string
     */
    public function convertMailto2Href($email = false) {
        return (new Mailto2HrefObfuscatingConverter())->convert($email);
    }

    /**
     * All methods are considered safe
     *
     * @param string $methodName
     * @return boolean
     */
    public function allowsCallOfMethod($methodName) {
        return true;
    }
}
