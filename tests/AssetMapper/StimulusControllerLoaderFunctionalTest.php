<?php

/*
 * This file is part of the Symfony StimulusBundle package.
 * (c) Fabien Potencier <fabien@symfony.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\StimulusBundle\Tests\AssetMapper;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\StimulusBundle\Tests\fixtures\StimulusTestKernel;
use Zenstruck\Browser\Test\HasBrowser;

class StimulusControllerLoaderFunctionalTest extends WebTestCase
{
    use HasBrowser;

    public function testFullApplicationLoad()
    {
        $filesystem = new Filesystem();
        $filesystem->remove(__DIR__.'/../fixtures/var/cache');

        $crawler = $this->browser()
            ->get('/')
            ->crawler()
        ;

        $importMapJson = $crawler->filter('script[type="importmap"]')->html();
        $importMap = json_decode($importMapJson, true);
        $importMapKeys = array_keys($importMap['imports']);
        sort($importMapKeys);
        $this->assertSame([
            // 2x from "controllers" (hello is overridden)
            '/assets/controllers/bye_controller.js',
            '/assets/controllers/subdir/deeper-controller.js',
            // 2x from UX packages, which are enabled in controllers.json
            '/assets/fake-vendor/ux-package1/package-controller-second.js',
            '/assets/fake-vendor/ux-package2/package-hello-controller.js',
            // 2x from more-controllers
            '/assets/more-controllers/hello-controller.js',
            '/assets/more-controllers/other-controller.js',
            // 3x from importmap.php
            '@hotwired/stimulus',
            '@symfony/stimulus-bundle',
            'app',
        ], $importMapKeys);

        // "app" is preloaded and it imports loader.js. So, all non-lazy controllers should be preloaded.
        $preLoadHrefs = $crawler->filter('link[rel="modulepreload"]')->each(function ($link) {
            return $link->attr('href');
        });
        $this->assertCount(4, $preLoadHrefs);
        sort($preLoadHrefs);
        $this->assertStringStartsWith('/assets/app-', $preLoadHrefs[0]);
        $this->assertStringStartsWith('/assets/controllers/subdir/deeper-controller-', $preLoadHrefs[1]);
        $this->assertStringStartsWith('/assets/fake-vendor/ux-package2/package-hello-controller-', $preLoadHrefs[2]);
        $this->assertStringStartsWith('/assets/more-controllers/hello-controller-', $preLoadHrefs[3]);
    }

    protected static function getKernelClass(): string
    {
        return StimulusTestKernel::class;
    }
}
