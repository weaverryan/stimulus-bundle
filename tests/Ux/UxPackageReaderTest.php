<?php

/*
 * This file is part of the Symfony StimulusBundle package.
 * (c) Fabien Potencier <fabien@symfony.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\StimulusBundle\Tests\Ux;

use PHPUnit\Framework\TestCase;
use Symfony\StimulusBundle\Ux\UxPackageMetadata;
use Symfony\StimulusBundle\Ux\UxPackageReader;

class UxPackageReaderTest extends TestCase
{
    public function testReadPackageMetadata()
    {
        $reader = new UxPackageReader(__DIR__.'/../fixtures');

        $metadata = $reader->readPackageMetadata('@fake-vendor/ux-package1');
        $this->assertInstanceOf(UxPackageMetadata::class, $metadata);
        $this->assertSame(__DIR__.'/../fixtures/vendor/fake-vendor/ux-package1/assets', $metadata->packageDirectory);
        $symfonyConfig = $metadata->symfonyConfig;
        $this->assertSame([
            'controller_first' => [
                'main' => 'dist/controller.js',
                'fetch' => 'eager',
                'enabled' => true,
            ],
            'controller_second' => [
                'main' => 'dist/controller2.js',
                'fetch' => 'lazy',
                'enabled' => true,
            ],
        ], $symfonyConfig['controllers']);

        $metadata2 = $reader->readPackageMetadata('@fake-vendor/ux-package2');
        $this->assertInstanceOf(UxPackageMetadata::class, $metadata2);
    }

    public function testExceptionIsThrownIfPackageCannotBeFound()
    {
        $reader = new UxPackageReader(__DIR__.'/../fixtures');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Could not find package "fake-vendor/ux-package3" referred to from controllers.json.');

        $reader->readPackageMetadata('@fake-vendor/ux-package3');
    }
}
