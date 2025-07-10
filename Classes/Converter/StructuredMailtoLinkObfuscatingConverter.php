<?php
namespace Networkteam\Neos\MailObfuscator\Converter;

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

class StructuredMailtoLinkObfuscatingConverter extends AbstractObfuscatingConverter implements MailtoLinkConverterInterface, StructuredLinkConverterInterface
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
     * Encrypt given email address and returns a string with encrypted email (token) and used offset (vector) separated by pipe character (|).
     *
     * @throws \Random\RandomException
     */
    public function convert(string $mailAddress): string
    {
        if ($this->randomOffset !== null) {
            $vector = $this->randomOffset;
        } else {
            $vector = random_int(1, 26);
        }
        $token = $this->encryptEmail($mailAddress, $vector);

        return sprintf('%s|%s', $token, $vector);
    }
}
