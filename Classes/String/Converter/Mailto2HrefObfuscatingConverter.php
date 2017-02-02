<?php
namespace Networkteam\Neos\MailObfuscator\String\Converter;

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

use Neos\Flow\Annotations as Flow;

class Mailto2HrefObfuscatingConverter implements MailtoLinkConverterInterface
{

    /**
     * @var int
     */
    protected $randomOffset;

    /**
     * @param int $randomOffset If not-null a fixed random offset will be used (useful for testing, but not for production)
     */
    public function __construct(int $randomOffset = null)
    {
        $this->randomOffset = $randomOffset;
    }

    /**
     * @inheritDoc
     */
    public function convert($mailAddress)
    {
        if ($this->randomOffset !== null) {
            $randomOffset = $this->randomOffset;
        } else {
            $randomOffset = random_int(1, 26);
        }

        return 'javascript:linkTo_UnCryptMailto(\'' . $this->encryptEmail($mailAddress, $randomOffset) . '\', -' . $randomOffset . ')';
    }

    /**
     * Encryption of email addresses for <a>-tags
     *
     * This method is taken form TYPO3 CMS.
     *
     * @param string $string Input string to en/decode: "mailto:blabla@bla.com"
     * @param int $randomOffset The random offset to use
     * @return string encoded/decoded version of $string
     */
    protected function encryptEmail(string $string, int $randomOffset): string
    {
        $out = '';
        // like str_rot13() but with a variable offset and a wider character range
        $len = strlen($string);
        $offset = $randomOffset;
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

    /**
     * Encryption (or decryption) of a single character.
     * Within the given range the character is shifted with the supplied offset.
     *
     * This method is taken from TYPO3 CMS.
     *
     * @param integer $n Ordinal of input character
     * @param integer $start Start of range
     * @param integer $end End of range
     * @param integer $offset Offset
     *
     * @return string encoded/decoded version of character
     */
    protected function encryptCharcode($n, $start, $end, $offset): string
    {
        $n += $offset;
        if ($offset > 0 && $n > $end) {
            $n = $start + ($n - $end - 1);
        } elseif ($offset < 0 && $n < $start) {
            $n = $end - ($start - $n - 1);
        }

        return chr($n);
    }
}
