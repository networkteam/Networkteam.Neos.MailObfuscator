<?php
namespace Networkteam\Neos\MailObfuscator\String\Converter;

/***************************************************************
 *  (c) 2014 networkteam GmbH - all rights reserved
 ***************************************************************/

class SimpleEmailConverter implements EmailDisplayConverterInterface {

	/**
	 * @param string $string
	 * @return string
	 */
	public function convert($string) {
		return str_replace('@', ' (at) ', $string);
	}
}