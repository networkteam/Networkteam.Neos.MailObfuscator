<?php
namespace Networkteam\Neos\MailObfuscator\Fusion;

/*
 * Copyright (C) 2014 networkteam GmbH
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General
 * Public License as published by the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the
 * Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 */

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Fusion\FusionObjects\AbstractFusionObject;
use Networkteam\Neos\MailObfuscator\Exception;
use Networkteam\Neos\MailObfuscator\Converter\EmailLinkNameConverterInterface;
use Networkteam\Neos\MailObfuscator\Converter\MailtoLinkConverterInterface;

class ConvertEmailLinksImplementation extends AbstractFusionObject
{

    /**
     * @var EmailLinkNameConverterInterface
     * @Flow\Inject
     */
    protected $linkNameConverter;

    /**
     * @var MailtoLinkConverterInterface
     * @Flow\Inject
     */
    protected $mailToHrefConverter;

    /**
     * Evaluate this TypoScript object and return the result
     *
     * @return mixed
     * @throws Exception
     */
    public function evaluate()
    {
        $text = $this->getValue();
        if (empty($text)) {
            return $text;
        }
        if (!is_string($text)) {
            throw new Exception(sprintf('Only strings can be processed by this Fusion object, given: "%s".', gettype($text)), 1409659552);
        }
        $currentContext = $this->getRuntime()->getCurrentContext();
        $node = $currentContext['node'];
        if (!$node instanceof NodeInterface) {
            throw new Exception(sprintf('The current node must be an instance of NodeInterface, given: "%s".', gettype($text)), 1409659564);
        }
        if ($node->getContext()->getWorkspaceName() !== 'live') {
            return $text;
        }
        $self = $this;
        $text = preg_replace_callback($this->getPatternMailDisplay(), function (array $matches) use ($self) {
            return $self->convertLinkName($matches);
        }, $text);

        return preg_replace_callback($this->getPatternMailTo(), function (array $matches) use ($self) {
            return $self->convertMailLink($matches);
        }, $text);
    }

    /**
     * Get PATTERN_MAIL_TO from fusion
     *
     * @return string
     */
    public function getPatternMailTo()
    {
        return $this->fusionValue('patternMailTo');
    }

    /**
     * Get PATTERN_MAIL_DISPLAY from fusion
     *
     * @return string
     */
    public function getPatternMailDisplay()
    {
        return $this->fusionValue('patternMailDisplay');
    }

    /**
     * The string to be processed
     *
     * @return string
     */
    public function getValue()
    {
        return $this->fusionValue('value');
    }

    /**
     * @param array $matches
     *
     * @return string
     */
    public function convertLinkName(array $matches)
    {
        $replacedEmail = $this->linkNameConverter->convert(trim($matches[2]));

        return $matches[1] . $replacedEmail;
    }

    /**
     * @param array $matches
     *
     * @return string
     */
    public function convertMailLink($matches)
    {
        // make sure that URL parameter dividers (`&`) are not encoded
        $email = html_entity_decode(trim($matches[2]), ENT_QUOTES | ENT_HTML5);
        $replacedHrefContent = $this->mailToHrefConverter->convert($email);

        $uri = new \GuzzleHttp\Psr7\Uri($replacedHrefContent);
        return $matches[1] . (string)$uri;
    }
}
