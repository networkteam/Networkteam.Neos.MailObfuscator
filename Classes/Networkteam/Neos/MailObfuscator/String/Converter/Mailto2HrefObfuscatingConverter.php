<?php
namespace Networkteam\Neos\MailObfuscator\String\Converter;

/***************************************************************
 *  (c) 2014 networkteam GmbH - all rights reserved
 ***************************************************************/

use TYPO3\Flow\Annotations as Flow;

class Mailto2HrefObfuscatingConverter implements Mailto2HrefConverterInterface {

	protected $offsetSeed;

	/**
	 * @param string $mailAddress
	 * @return string
	 */
	public function convert($mailAddress) {
		$this->offsetSeed = rand(1,26);
		return 'javascript:linkTo_UnCryptMailto(\'' . $this->blurEmailAddress($mailAddress) . '\', -' . $this->offsetSeed . ')';
	}

	/**
	 * @param $emailAddress
	 * @return string
	 */
	protected function blurEmailAddress($emailAddress) {
		return $this->encryptEmail($emailAddress);
	}

	/**
	 * Encryption (or decryption) of a single character.
	 * Within the given range the character is shifted with the supplied offset.
	 *
	 * @param integer $n Ordinal of input character
	 * @param integer $start Start of range
	 * @param integer $end End of range
	 * @param integer $offset Offset
	 * @return string encoded/decoded version of character
	 */
	protected function encryptCharcode($n, $start, $end, $offset) {
		$n = $n + $offset;
		if ($offset > 0 && $n > $end) {
			$n = $start + ($n - $end - 1);
		} elseif ($offset < 0 && $n < $start) {
			$n = $end - ($start - $n - 1);
		}
		return chr($n);
	}

	/**
	 * Encryption of email addresses for <A>-tags See the spam protection setup in TS 'config.'
	 *
	 * @param string $string Input string to en/decode: "mailto:blabla@bla.com
	 * @param boolean $back If set, the process is reversed, effectively decoding, not encoding.
	 * @return string encoded/decoded version of $string
	 */
	protected function encryptEmail($string, $back = FALSE) {
		$out = '';
		// like str_rot13() but with a variable offset and a wider character range
		$len = strlen($string);
		$offset = (int)$this->offsetSeed * ($back ? -1 : 1);
		for ($i = 0; $i < $len; $i++) {
			$charValue = ord($string[$i]);
			// 0-9 . , - + / :
			if ($charValue >= 43 && $charValue <= 58) {
				$out .= $this->encryptCharcode($charValue, 43, 58, $offset);
			} elseif ($charValue >= 64 && $charValue <= 90) {
				// A-Z @
				$out .= $this->encryptCharcode($charValue, 64, 90, $offset);
			} elseif ($charValue >= 97 && $charValue <= 122) {
				// a-z
				$out .= $this->encryptCharcode($charValue, 97, 122, $offset);
			} else {
				$out .= $string[$i];
			}
		}
		return $out;
	}
}