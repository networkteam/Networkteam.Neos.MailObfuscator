<?php
namespace Networkteam\Neos\MailObfuscator\Tests\Unit\TypoScript;

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

class ConvertEmailLinksImplementationTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @var \Networkteam\Neos\Util\Typoscript\ConvertEmailLinksImplementation
	 */
	protected $convertEmailLinks;

	/**
	 * @var \TYPO3\TypoScript\Core\Runtime
	 */
	protected $mockTsRuntime;

	/**
	 * @var \TYPO3\TYPO3CR\Domain\Repository\NodeDataRepository
	 */
	protected $mockNodeDataRepository;

	/**
	 * @var \TYPO3\TYPO3CR\Domain\Service\ContextInterface
	 */
	protected $mockContext;

	/**
	 * @var \TYPO3\TYPO3CR\Domain\Model\NodeInterface
	 */
	protected $mockNode;

	/**
	 * @var \TYPO3\TYPO3CR\Domain\Model\Workspace
	 */
	protected $mockWorkspace;

	/**
	 * @var \TYPO3\TYPO3CR\Domain\Factory\NodeFactory
	 */
	protected $mockNodeFactory;

	/**
	 * @var \TYPO3\Flow\Mvc\Controller\ControllerContext
	 */
	protected $mockControllerContext;

	/**
	 * @var \TYPO3\Flow\Mvc\Routing\UriBuilder
	 */
	protected $mockUriBuilder;


	public function setUp() {
		$this->convertEmailLinks = $this->getAccessibleMock('Networkteam\Neos\MailObfuscator\Typoscript\ConvertEmailLinksImplementation', array('getValue'), array(), '', FALSE);

		$this->mockWorkspace = $this->getMockBuilder('TYPO3\TYPO3CR\Domain\Model\Workspace')->disableOriginalConstructor()->getMock();

		$this->mockContext = $this->getMockBuilder('TYPO3\TYPO3CR\Domain\Service\ContextInterface')->disableOriginalConstructor()->getMock();
		$this->mockContext->expects($this->any())->method('getWorkspace')->will($this->returnValue($this->mockWorkspace));

		$this->mockNode = $this->getMockBuilder('TYPO3\TYPO3CR\Domain\Model\NodeInterface')->getMock();
		$this->mockNode->expects($this->any())->method('getContext')->will($this->returnValue($this->mockContext));

		$this->mockUriBuilder = $this->getMockBuilder('TYPO3\Flow\Mvc\Routing\UriBuilder')->disableOriginalConstructor()->getMock();

		$this->mockControllerContext = $this->getMockBuilder('TYPO3\Flow\Mvc\Controller\ControllerContext')->disableOriginalConstructor()->getMock();
		$this->mockControllerContext->expects($this->any())->method('getUriBuilder')->will($this->returnValue($this->mockUriBuilder));

		$this->mockTsRuntime = $this->getMockBuilder('TYPO3\TypoScript\Core\Runtime')->disableOriginalConstructor()->getMock();
		$this->mockTsRuntime->expects($this->any())->method('getCurrentContext')->will($this->returnValue(array('node' => $this->mockNode)));
		$this->mockTsRuntime->expects($this->any())->method('getControllerContext')->will($this->returnValue($this->mockControllerContext));
		$this->convertEmailLinks->_set('tsRuntime', $this->mockTsRuntime);

		$this->mockNodeDataRepository = $this->getMockBuilder('TYPO3\TYPO3CR\Domain\Repository\NodeDataRepository')->disableOriginalConstructor()->getMock();
		$this->convertEmailLinks->_set('nodeDataRepository', $this->mockNodeDataRepository);

		$this->mockNodeFactory = $this->getMockBuilder('TYPO3\TYPO3CR\Domain\Factory\NodeFactory')->disableOriginalConstructor()->getMock();
		$this->convertEmailLinks->_set('nodeFactory', $this->mockNodeFactory);

		$this->convertEmailLinks->_set('linkNameConverter', new \Networkteam\Neos\MailObfuscator\String\Converter\RewriteAtCharConverter());

		$mailToHrefConverter = new \Networkteam\Neos\MailObfuscator\String\Converter\Mailto2HrefObfuscatingConverter();
		$this->convertEmailLinks->_set('mailToHrefConverter', $mailToHrefConverter);

	}
	/**
	 * @test
	 * @dataProvider emailTexts
	 */
	public function emailsAreConverted($rawText, $expectedText) {
		$this->convertEmailLinks->expects($this->atLeastOnce())->method('getValue')->will($this->returnValue($rawText));

		$this->mockWorkspace->expects($this->any())->method('getName')->will($this->returnValue('live'));
		srand(10);
		$actualResult = $this->convertEmailLinks->evaluate();
		$this->assertSame($expectedText, $actualResult);
	}

	public function emailTexts() {
		return array(
			'just some text not to touch' => array(
				' this Is some string with line' . chr(10) . ' breaks, special chärß and leading/trailing space  ',
				' this Is some string with line' . chr(10) . ' breaks, special chärß and leading/trailing space  '
			),
			'singel Mail in text' => array(
				'Email <a href="mailto:test@example.com">test@example.com</a>',
				'Email <a href="javascript:linkTo_UnCryptMailto(\'ithiOtmpbeat-rdb\', -15)">test (at) example.com</a>',
			),
			'multiple mails in text' => array(
				'Email <a href="mailto:test@example.com">test@example.com</a> and afterwards another email <a href="mailto:foobar@example.com">foobar@example.com</a>',
				'Email <a href="javascript:linkTo_UnCryptMailto(\'ithiOtmpbeat-rdb\', -15)">test (at) example.com</a> and afterwards another email <a href="javascript:linkTo_UnCryptMailto(\'veerqhPunqcfbu.sec\', -16)">foobar (at) example.com</a>',
			)
		);
	}
}
