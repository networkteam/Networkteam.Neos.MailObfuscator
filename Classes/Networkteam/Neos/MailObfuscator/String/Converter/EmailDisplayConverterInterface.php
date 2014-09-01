<?php
/***************************************************************
 *  (c) 2014 networkteam GmbH - all rights reserved
 ***************************************************************/

namespace Networkteam\Neos\MailObfuscator\String\Converter;


interface EmailDisplayConverterInterface {

	/**
	 * @param string $string
	 * @return string
	 */
	public function convert($string);
}