<?php
namespace Networkteam\Neos\MailObfuscator\String\Converter;

/***************************************************************
 *  (c) 2014 networkteam GmbH - all rights reserved
 ***************************************************************/

interface Mailto2HrefConverterInterface {

	/**
	 * @param string $mailAddress
	 */
	public function convert($mailAddress);
}