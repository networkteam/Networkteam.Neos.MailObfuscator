<?php
namespace Networkteam\Neos\MailObfuscator\String\Converter;

use Neos\Flow\Annotations as Flow;

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

class RewriteAtCharConverter implements EmailLinkNameConverterInterface
{

    /**
     * @Flow\InjectConfiguration(path="atCharReplacementString", package="Networkteam.Neos.MailObfuscator")
     * @var array
     */
    protected $replacementString;

    /**
     * @param string $emailAddress
     * @return string
     */
    public function convert($emailAddress)
    {
        return str_replace('@', $this->replacementString, $emailAddress);
    }

    /**
     * @param string $replacementString
     */
    public function setReplacementString($replacementString) {
        $this->replacementString = $replacementString;
    }
}
