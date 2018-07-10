<?php
namespace Networkteam\Neos\MailObfuscator\Tests\Unit\TypoScript;

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

class ConvertEmailLinksImplementationTest extends \Neos\Flow\Tests\UnitTestCase {

	/**
	 * @var \Networkteam\Neos\MailObfuscator\Typoscript\ConvertEmailLinksImplementation
	 */
	protected $convertEmailLinks;

	/**
	 * @var \Neos\Fusion\Core\Runtime
	 */
	protected $mockTsRuntime;

	/**
	 * @var \Neos\ContentRepository\Domain\Service\Context
	 */
	protected $mockContext;

	/**
	 * @var \Neos\ContentRepository\Domain\Model\NodeInterface
	 */
	protected $mockNode;

    /**
     * @Flow\Inject(setting="atCharReplacementString", package="Networkteam.Neos.MailObfuscator")
     * @var array
     */
    protected $replacementString;

	public function setUp() {
		$this->convertEmailLinks = $this->getAccessibleMock('Networkteam\Neos\MailObfuscator\Typoscript\ConvertEmailLinksImplementation', array('getValue'), array(), '', FALSE);

		$this->mockContext = $this->getMockBuilder('Neos\ContentRepository\Domain\Service\Context')->disableOriginalConstructor()->getMock();
		$this->mockContext->expects($this->any())->method('getWorkspaceName')->will($this->returnValue('live'));

		$this->mockNode = $this->getMockBuilder('Neos\ContentRepository\Domain\Model\NodeInterface')->getMock();
		$this->mockNode->expects($this->any())->method('getContext')->will($this->returnValue($this->mockContext));

		$this->mockTsRuntime = $this->getMockBuilder('Neos\Fusion\Core\Runtime')->disableOriginalConstructor()->getMock();
		$this->mockTsRuntime->expects($this->any())->method('getCurrentContext')->will($this->returnValue(array('node' => $this->mockNode)));

		$this->convertEmailLinks->_set('tsRuntime', $this->mockTsRuntime);
		$this->convertEmailLinks->_set('linkNameConverter', new \Networkteam\Neos\MailObfuscator\String\Converter\RewriteAtCharConverter());
		$this->convertEmailLinks->_set('mailToHrefConverter', new \Networkteam\Neos\MailObfuscator\String\Converter\Mailto2HrefObfuscatingConverter());

		srand(10);
	}

	/**
	 * @test
	 * @dataProvider emailTexts
	 */
	public function emailsAreConverted($rawText, $expectedText) {
		$this->convertEmailLinks->expects($this->atLeastOnce())->method('getValue')->will($this->returnValue($rawText));

		$actualResult = $this->convertEmailLinks->evaluate();
		$this->assertSame($expectedText, $actualResult);
	}

	public function emailTexts() {
		return array(
			'just some text not to touch' => array(
				' this Is some string with line' . chr(10) . ' breaks, special chärß and leading/trailing space  ',
				' this Is some string with line' . chr(10) . ' breaks, special chärß and leading/trailing space  '
			),
			'single mail link in text' => array(
				'Email <a href="mailto:test@example.com">test@example.com</a>',
				'Email <a href="javascript:linkTo_UnCryptMailto(\'ithiOtmpbeat-rdb\', -15)">test' . $this->replacementString . 'example.com</a>'
			),
			'multiple mail links in text' => array(
				'Email <a href="mailto:test@example.com">test@example.com</a> and afterwards another email <a href="mailto:foobar@example.com">foobar@example.com</a>',
				'Email <a href="javascript:linkTo_UnCryptMailto(\'ithiOtmpbeat-rdb\', -15)">test' . $this->replacementString . 'example.com</a> and afterwards another email <a href="javascript:linkTo_UnCryptMailto(\'veerqhPunqcfbu.sec\', -16)">test' . $this->replacementString . 'example.com</a>'
			),
			'email address outside of link' => array(
				'Email test@example.com should not be replaced',
				'Email test@example.com should not be replaced'
			),
			'email address with space at the beginning' => array(
				'Email <a href="mailto: test@example.com">test@example.com</a>',
				'Email <a href="javascript:linkTo_UnCryptMailto(\'ithiOtmpbeat-rdb\', -15)">test' . $this->replacementString . 'example.com</a>'
			)
		);
	}
}
