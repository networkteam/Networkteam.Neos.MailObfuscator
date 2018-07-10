<?php
namespace Networkteam\Neos\MailObfuscator\Typoscript;

/**
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

use Networkteam\Neos\MailObfuscator\Exception;
use TYPO3\TypoScript\TypoScriptObjects\AbstractTypoScriptObject;
use Neos\Flow\Annotations as Flow;

class ConvertEmailLinksImplementation extends AbstractTypoScriptObject {

	const PATTERN_MAIL_TO = '/(href=")mailto:([^"]*)/';

	const PATTERN_MAIL_DISPLAY = '/(href="mailto:[^"]*">)([^<]*)/';

	/**
	 * @var \Networkteam\Neos\MailObfuscator\String\Converter\EmailLinkNameConverterInterface
	 * @Flow\Inject
	 */
	protected $linkNameConverter;

	/**
	 * @var \Networkteam\Neos\MailObfuscator\String\Converter\MailtoLinkConverterInterface
	 * @Flow\Inject
	 */
	protected $mailToHrefConverter;

	/**
	 * The string to be processed
	 *
	 * @return string
	 */
	public function getValue() {
		return $this->tsValue('value');
	}

	/**
	 * Evaluate this TypoScript object and return the result
	 *
	 * @return mixed
	 * @throws \Networkteam\Neos\MailObfuscator\Exception
	 */
	public function evaluate() {
		$text = $this->getValue();
		if (empty($text)) {
			return $text;
		}
		if (!is_string($text)) {
			throw new Exception(sprintf('Only strings can be processed by this TypoScript object, given: "%s".', gettype($text)), 1409659552);
		}
		$currentContext = $this->tsRuntime->getCurrentContext();
		$node = $currentContext['node'];
		if (!$node instanceof \TYPO3\TYPO3CR\Domain\Model\NodeInterface) {
			throw new Exception(sprintf('The current node must be an instance of NodeInterface, given: "%s".', gettype($text)), 1409659564);
		}
		if ($node->getContext()->getWorkspaceName() !== 'live') {
			return $text;
		}
		$self = $this;
		$text = preg_replace_callback(self::PATTERN_MAIL_DISPLAY, function(array $matches) use ($self) {
			return $self->convertLinkName($matches);
		}, $text);

		return preg_replace_callback(self::PATTERN_MAIL_TO, function(array $matches) use ($self) {
			return $self->convertMailLink($matches);
		}, $text);
	}

	/**
	 * @param array $matches
	 * @return string
	 */
	public function convertLinkName(array $matches) {
		$replacedEmail = $this->linkNameConverter->convert(trim($matches[2]));
		return $matches[1] . $replacedEmail;
	}

	/**
	 * @param array $matches
	 * @return string
	 */
	public function convertMailLink($matches) {
		$email = trim($matches[2]);
		$replacedHrefContent = $this->mailToHrefConverter->convert($email);
		return  $matches[1] . htmlspecialchars($replacedHrefContent);
	}
}
