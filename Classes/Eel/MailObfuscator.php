<?php

namespace Networkteam\Neos\MailObfuscator\Eel;

use Neos\Flow\Annotations as Flow;
use Neos\Eel\ProtectedContextAwareInterface;
use Networkteam\Neos\MailObfuscator\Converter\EmailLinkNameConverterInterface;
use Networkteam\Neos\MailObfuscator\Converter\MailtoLinkConverterInterface;

/**
 * Eel helpers to provide MailObfuscator functions
 */
class MailObfuscator implements ProtectedContextAwareInterface {

    /**
     * @var EmailLinkNameConverterInterface
     * @Flow\Inject
     */
    protected $emailLinkNameConverter;

    /**
     * @var MailtoLinkConverterInterface
     * @Flow\Inject
     */
    protected $mailtoLinkConverter;

    /**
     * Convert at Character
     *
     * @param $email string
     * @return string
     */
    public function convertAtChar($email = false) {
        return $this->emailLinkNameConverter->convert($email);
    }

    /**
     * Convert Mailto to Href
     *
     * @param $email string
     * @return string
     */
    public function convertMailto2Href($email = false) {
        return $this->mailtoLinkConverter->convert($email);
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
