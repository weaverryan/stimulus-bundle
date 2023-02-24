<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony StimulusBundle package.
 * (c) Fabien Potencier <fabien@symfony.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\StimulusBundle\Tests\Dto;

use PHPUnit\Framework\TestCase;
use Symfony\StimulusBundle\Dto\StimulusTargetsDto;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

final class StimulusTargetsDtoTest extends TestCase
{
    private StimulusTargetsDto $stimulusTargetsDto;

    protected function setUp(): void
    {
        $this->stimulusTargetsDto = new StimulusTargetsDto(new Environment(new ArrayLoader()));
    }

    public function testToStringEscapingAttributeValues(): void
    {
        $this->stimulusTargetsDto->addTarget('foo', '"');
        $attributesHtml = (string) $this->stimulusTargetsDto;
        self::assertSame('data-foo-target="&quot;"', $attributesHtml);
    }

    public function testToArrayNoEscapingAttributeValues(): void
    {
        $this->stimulusTargetsDto->addTarget('foo', '"');
        $attributesArray = $this->stimulusTargetsDto->toArray();
        self::assertSame(['data-foo-target' => '"'], $attributesArray);
    }
}
