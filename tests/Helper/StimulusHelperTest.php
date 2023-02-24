<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony StimulusBundle package.
 * (c) Fabien Potencier <fabien@symfony.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\StimulusBundle\Tests\Helper;

use PHPUnit\Framework\TestCase;
use Symfony\StimulusBundle\Helper\StimulusHelper;
use Symfony\StimulusBundle\Tests\StimulusIntegrationTestKernel;
use Twig\Environment;

final class StimulusHelperTest extends TestCase
{
    private StimulusHelper $helper;

    protected function setUp(): void
    {
        $kernel = new StimulusIntegrationTestKernel();
        $kernel->boot();
        $container = $kernel->getContainer()->get('test.service_container');
        $twig = $container->get(Environment::class);
        $this->helper = new StimulusHelper($twig);
    }

    public function testBuildStimulusControllerDto(): void
    {
        $dto = $this->helper->buildStimulusControllerDto(
            '@symfony/ux-dropzone/dropzone',
            ['my"Key"' => true],
            ['second"Key"' => 'loading'],
        );

        $this->assertSame(
            'data-controller="symfony--ux-dropzone--dropzone" data-symfony--ux-dropzone--dropzone-my-key-value="true" data-symfony--ux-dropzone--dropzone-second-key-class="loading"',
            (string) $dto,
        );
        $this->assertSame(
            ['data-controller' => 'symfony--ux-dropzone--dropzone', 'data-symfony--ux-dropzone--dropzone-my-key-value' => 'true', 'data-symfony--ux-dropzone--dropzone-second-key-class' => 'loading'],
            $dto->toArray(),
        );

        $secondDto = $this->helper->buildStimulusControllerDto('my-controller', ['myValue' => 'scalar-value'], previousDto: $dto);
        $this->assertSame(
            'data-controller="symfony--ux-dropzone--dropzone my-controller" data-symfony--ux-dropzone--dropzone-my-key-value="true" data-symfony--ux-dropzone--dropzone-second-key-class="loading" data-my-controller-my-value-value="scalar-value"',
            (string) $secondDto,
        );
    }

    public function testBuildStimulusActionDto(): void
    {
        $dto = $this->helper->buildStimulusActionDto('my-controller', 'onClick');
        $this->assertSame('data-action="my-controller#onClick"', (string) $dto);
        $this->assertSame(['data-action' => 'my-controller#onClick'], $dto->toArray());

        $secondDto = $this->helper->buildStimulusActionDto('second-controller', 'onClick', 'click', previousDto: $dto);
        $this->assertSame(
            'data-action="my-controller#onClick click->second-controller#onClick"',
            (string) $secondDto,
        );
    }

    public function testBuildStimulusTargetDto(): void
    {
        $dto = $this->helper->buildStimulusTargetDto('my-controller', 'myTarget');
        $this->assertSame('data-my-controller-target="myTarget"', (string) $dto);
        $this->assertSame(['data-my-controller-target' => 'myTarget'], $dto->toArray());

        $secondDto = $this->helper->buildStimulusTargetDto('second-controller', 'secondTarget', previousDto: $dto);
        $this->assertSame(
            'data-my-controller-target="myTarget" data-second-controller-target="secondTarget"',
            (string) $secondDto,
        );
    }
}
