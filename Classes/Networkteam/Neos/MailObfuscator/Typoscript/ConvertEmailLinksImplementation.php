<?php
namespace Networkteam\Neos\MailObfuscator\Typoscript;

/***************************************************************
 *  (c) 2014 networkteam GmbH - all rights reserved
 ***************************************************************/

use Networkteam\Neos\Util\Exception;
use TYPO3\TypoScript\TypoScriptObjects\AbstractTypoScriptObject;
use TYPO3\Flow\Annotations as Flow;

class ConvertEmailLinksImplementation extends AbstractTypoScriptObject {

	const PATTERN_MAIL_TO = '/href="mailto:([^"]*)/';

	const PATTERN_MAIL_DISPLAY = '/href="mailto:[^"]*">([^<]*)/';

	/**
	 * @var \Networkteam\Neos\MailObfuscator\String\Converter\EmailDisplayConverterInterface
	 * @Flow\Inject
	 */
	protected $mailDisplayConverter;

	/**
	 * @var \Networkteam\Neos\MailObfuscator\String\Converter\Mailto2HrefConverterInterface
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
	 */
	public function evaluate() {
		$text = $this->getValue();
		if (!is_string($text)) {
			throw new Exception(sprintf('Only strings can be processed by this TypoScript object, given: "%s".', gettype($text)), 1382624080);
		}
		$currentContext = $this->tsRuntime->getCurrentContext();
		$node = $currentContext['node'];
		if (!$node instanceof \TYPO3\TYPO3CR\Domain\Model\NodeInterface) {
			throw new Exception(sprintf('The current node must be an instance of NodeInterface, given: "%s".', gettype($text)), 1382624087);
		}
		if ($node->getContext()->getWorkspace()->getName() !== 'live') {
			return $text;
		}
		$self = $this;
		$text = preg_replace_callback(self::PATTERN_MAIL_DISPLAY, function(array $matches) use ($self) {
			return $self->convertMailDisplay($matches);
		}, $text);

		return preg_replace_callback(self::PATTERN_MAIL_TO, function(array $matches) use ($self, $node) {
			return $self->convertMailLink($matches, $node);
		}, $text);
	}

	/**
	 * @param string $displayEmail
	 * @return string
	 */
	protected function convertMailDisplay(array $matches) {
		$replacedEmail = $this->mailDisplayConverter->convert($matches[1]);
		return substr($matches[0], 0, strpos($matches[0], '>') + 1) . $replacedEmail;
	}

	/**
	 * @param array $matches
	 * @param $node
	 */
	protected function convertMailLink($matches, $node) {
		$email = $matches[1];
		$replacedHrefContent = $this->mailToHrefConverter->convert($email);
		return 'href="' . $replacedHrefContent;
	}
}