<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\StimulusBundle\AssetMapper\ControllersMapGenerator;
use Symfony\StimulusBundle\AssetMapper\StimulusLoaderJavaScriptCompiler;
use Symfony\StimulusBundle\Helper\StimulusHelper;
use Symfony\StimulusBundle\Twig\StimulusTwigExtension;
use Symfony\StimulusBundle\Ux\UxPackageReader;
use Twig\Environment;
use function Symfony\Component\DependencyInjection\Loader\Configurator\abstract_arg;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('stimulus.helper', StimulusHelper::class)
            ->arg('$twig', service(Environment::class)->nullOnInvalid())

        ->set('stimulus.twig_extension', StimulusTwigExtension::class)
            ->tag('twig.extension')

        ->set('stimulus.asset_mapper.controllers_map_generator', ControllersMapGenerator::class)
            ->args([
                service('asset_mapper'),
                service('stimulus.asset_mapper.ux_package_reader'),
                abstract_arg('controller paths'),
                abstract_arg('controllers_json_path'),
            ])

        ->set('stimulus.asset_mapper.ux_package_reader', UxPackageReader::class)
            ->args([
                param('kernel.project_dir'),
            ])

        ->set('stimulus.asset_mapper.loader_javascript_compiler', StimulusLoaderJavaScriptCompiler::class)
            ->args([
                service('stimulus.asset_mapper.controllers_map_generator'),
                param('kernel.debug'),
            ])
            ->tag('asset_mapper.compiler', ['priority' => 100])
    ;
};
