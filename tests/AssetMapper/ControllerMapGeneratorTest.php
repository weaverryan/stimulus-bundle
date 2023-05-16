<?php

namespace Symfony\StimulusBundle\Tests\AssetMapper;

use PHPUnit\Framework\TestCase;
use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Component\AssetMapper\MappedAsset;
use Symfony\StimulusBundle\AssetMapper\ControllersMapGenerator;
use Symfony\StimulusBundle\Ux\UxPackageReader;

class ControllerMapGeneratorTest extends TestCase
{
    public function testGetControllersMap()
    {
        $mapper = $this->createMock(AssetMapperInterface::class);
        $mapper->expects($this->any())
            ->method('getAssetFromSourcePath')
            ->willReturnCallback(function ($path) {
                if (str_ends_with($path, 'package-controller-first.js')) {
                    $logicalPath = 'fake-vendor/ux-package1/package-controller-first.js';
                } elseif (str_ends_with($path, 'package-controller-second.js')) {
                    $logicalPath = 'fake-vendor/ux-package1/package-controller-second.js';
                } elseif (str_ends_with($path, 'package-hello-controller.js')) {
                    $logicalPath = 'fake-vendor/ux-package2/package-hello-controller.js';
                } else {
                    // replace windows slashes
                    $path = str_replace('\\', '/', $path);
                    $assetsPosition = strpos($path, '/assets/');
                    $logicalPath = substr($path, $assetsPosition + 1);
                }

                $mappedAsset = new MappedAsset($logicalPath);
                $mappedAsset->setSourcePath($path);
                $mappedAsset->setContent(file_get_contents($path));

                return $mappedAsset;
            });

        $packageReader = new UxPackageReader(__DIR__.'/../fixtures');

        $generator = new ControllersMapGenerator(
            $mapper,
            $packageReader,
            [
                __DIR__.'/../fixtures/assets/controllers',
                __DIR__.'/../fixtures/assets/more-controllers',
            ],
            __DIR__.'/../fixtures/assets/controllers.json',
        );

        $map = $generator->getControllersMap();
        // + 3 controller.json UX controllers
        // - 1 controllers.json UX controller is disabled
        // + 4 custom controllers (1 file is not a controller & 1 is overridden)
        $this->assertCount(6, $map);
        $packageNames = array_keys($map);
        sort($packageNames);
        $this->assertSame([
            'bye',
            'fake-vendor--ux-package1--controller-second',
            'fake-vendor--ux-package2--hello-controller',
            'hello',
            'other',
            'subdir--deeper',
        ], $packageNames);

        $controllerSecond = $map['fake-vendor--ux-package1--controller-second'];
        $this->assertSame('fake-vendor/ux-package1/package-controller-second.js', $controllerSecond->asset->getLogicalPath());
        // lazy from user's controller.json
        $this->assertTrue($controllerSecond->isLazy);

        $helloControllerFromPackage = $map['fake-vendor--ux-package2--hello-controller'];
        $this->assertSame('fake-vendor/ux-package2/package-hello-controller.js', $helloControllerFromPackage->asset->getLogicalPath());
        $this->assertFalse($helloControllerFromPackage->isLazy);

        $helloController = $map['hello'];
        $this->assertStringContainsString('hello-controller.js override', file_get_contents($helloController->asset->getSourcePath()));
        $this->assertFalse($helloController->isLazy);

        // lazy from stimulusFetch comment
        $byeController = $map['bye'];
        $this->assertTrue($byeController->isLazy);

        $otherController = $map['other'];
        $this->assertTrue($otherController->isLazy);
    }
}
