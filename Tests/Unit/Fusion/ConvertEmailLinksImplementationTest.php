<?php
namespace Networkteam\Neos\MailObfuscator\Tests\Unit\Fusion;

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

use Neos\ContentRepository\Core\DimensionSpace\DimensionSpacePoint;
use Neos\ContentRepository\Core\DimensionSpace\OriginDimensionSpacePoint;
use Neos\ContentRepository\Core\Feature\NodeModification\Dto\SerializedPropertyValues;
use Neos\ContentRepository\Core\Infrastructure\Property\PropertyConverter;
use Neos\ContentRepository\Core\NodeType\NodeTypeName;
use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use Neos\ContentRepository\Core\Projection\ContentGraph\NodeTags;
use Neos\ContentRepository\Core\Projection\ContentGraph\PropertyCollection;
use Neos\ContentRepository\Core\Projection\ContentGraph\Timestamps;
use Neos\ContentRepository\Core\Projection\ContentGraph\VisibilityConstraints;
use Neos\ContentRepository\Core\SharedModel\ContentRepository\ContentRepositoryId;
use Neos\ContentRepository\Core\SharedModel\Node\NodeAggregateClassification;
use Neos\ContentRepository\Core\SharedModel\Node\NodeAggregateId;
use Neos\ContentRepository\Core\SharedModel\Workspace\WorkspaceName;
use Neos\Flow\Tests\UnitTestCase;
use Neos\Fusion\Core\Runtime;
use Networkteam\Neos\MailObfuscator\Converter\Mailto2HrefObfuscatingConverter;
use Networkteam\Neos\MailObfuscator\Converter\RewriteAtCharConverter;
use Networkteam\Neos\MailObfuscator\Fusion\ConvertEmailLinksImplementation;
use Symfony\Component\Serializer\Serializer;

class ConvertEmailLinksImplementationTest extends UnitTestCase
{
    /**
     * @var ConvertEmailLinksImplementation
     */
    protected $convertEmailLinks;

    /**
     * @var Runtime
     */
    protected $mockRuntime;

    public function setUp(): void
    {
        $this->convertEmailLinks = $this->getAccessibleMock(ConvertEmailLinksImplementation::class, ['fusionValue'], [], '', false);

        $this->mockNode = Node::create(
            ContentRepositoryId::fromString('mock'),
            WorkspaceName::forLive(),
            DimensionSpacePoint::fromArray([]),
            NodeAggregateId::create(), OriginDimensionSpacePoint::createWithoutDimensions(),
            NodeAggregateClassification::CLASSIFICATION_REGULAR,
            NodeTypeName::fromString('Some.Node:Type'),
            new PropertyCollection(SerializedPropertyValues::createEmpty(), new PropertyConverter(new Serializer())),
            null,
            NodeTags::createEmpty(),
            Timestamps::create(new \DateTimeImmutable(), new \DateTimeImmutable(), null, null),
            VisibilityConstraints::default()
        );

        $this->mockRuntime = $this->getMockBuilder(Runtime::class)->disableOriginalConstructor()->getMock();
        $this->mockRuntime->expects($this->any())->method('getCurrentContext')->will($this->returnValue(['node' => $this->mockNode]));

        $this->convertEmailLinks->_set('runtime', $this->mockRuntime);
        $linkNameConverter = new RewriteAtCharConverter();
        $linkNameConverter->setReplacementString(' (at) ');
        $this->convertEmailLinks->_set('linkNameConverter', $linkNameConverter);
        $this->convertEmailLinks->_set('mailToHrefConverter', new Mailto2HrefObfuscatingConverter(15));
    }

    /**
     * @test
     * @dataProvider emailTexts
     */
    public function emailsAreConverted($rawText, $expectedText)
    {
        $this->convertEmailLinks
            ->expects(self::atLeastOnce())
            ->method('fusionValue')
            ->will($this->returnValueMap([
                ['value', $rawText],
                ['patternMailTo', '/(href=")mailto:([^"]*)/'],
                ['patternMailDisplay', '/(href="mailto:[^>]*>)((.|\n)*?)(<\/a>)/']
            ]));

        $actualResult = $this->convertEmailLinks->evaluate();
        $this->assertSame($expectedText, $actualResult);
    }

    static public function emailTexts(): array
    {

        $htmlEncodedDecryptionString = htmlspecialchars('javascript:linkTo_UnCryptMailto(\'ithiOtmpbeat-rdb\',-15)', ENT_NOQUOTES);
        $htmlEncodedSecondDecryptionString = htmlspecialchars('javascript:linkTo_UnCryptMailto(\'uddqpgOtmpbeat-rdb\',-15)', ENT_NOQUOTES);

        return [
            'just some text not to touch' => [
                ' this Is some string with line' . chr(10) . ' breaks, special chärß and leading/trailing space  ',
                ' this Is some string with line' . chr(10) . ' breaks, special chärß and leading/trailing space  '
            ],
            'single mail link in text' => [
                'Email <a href="mailto:test@example.com">test@example.com</a>',
                'Email <a href="' . $htmlEncodedDecryptionString . '">test (at) example.com</a>'
            ],
            'multiple mail links in text' => [
                'Email <a href="mailto:test@example.com">test@example.com</a> and afterwards another email <a href="mailto:foobar@example.com">foobar@example.com</a>',
                'Email <a href="' . $htmlEncodedDecryptionString . '">test (at) example.com</a> and afterwards another email <a href="' . $htmlEncodedSecondDecryptionString . '">foobar (at) example.com</a>'
            ],
            'email address outside of link' => [
                'Email test@example.com should not be replaced',
                'Email test@example.com should not be replaced'
            ],
            'email address with space at the beginning' => [
                'Email <a href="mailto: test@example.com">test@example.com</a>',
                'Email <a href="' . $htmlEncodedDecryptionString . '">test (at) example.com</a>'
            ],
            'email address with attributes after href' => [
                'Email <a href="mailto: test@example.com" itemprop="email">test@example.com</a>',
                'Email <a href="' . $htmlEncodedDecryptionString . '" itemprop="email">test (at) example.com</a>'
            ],
            'email address enclosed by HTML tag' => [
                'Email <a href="mailto: test@example.com" itemprop="email"><strong>test@example.com</strong></a>',
                'Email <a href="' . $htmlEncodedDecryptionString . '" itemprop="email"><strong>test (at) example.com</strong></a>'
            ],
            'email address in link tag enclosed by multiple styling tags' => [
                'Email <a href="mailto: test@example.com" itemprop="email"><i class="fa-light fa-paper-plane"></i><span class="btn__text">test@example.com</span></a>',
                'Email <a href="' . $htmlEncodedDecryptionString . '" itemprop="email"><i class="fa-light fa-paper-plane"></i><span class="btn__text">test (at) example.com</span></a>'
            ],
            'email address in link tag enclosed by multiple styling tags and new line characters' => [
                'Email <a href="mailto: test@example.com" itemprop="email">
    <i class="fa-light fa-paper-plane"></i>
    <span class="btn__text">test@example.com</span>
</a>',
                'Email <a href="' . $htmlEncodedDecryptionString . '" itemprop="email"><i class="fa-light fa-paper-plane"></i>
    <span class="btn__text">test (at) example.com</span></a>'
            ]
        ];
    }
}
