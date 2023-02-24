<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use Symfony\StimulusBundle\Helper\StimulusHelper;
use Symfony\StimulusBundle\Twig\StimulusTwigExtension;
use Twig\Environment;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set(StimulusHelper::class)
            ->arg('$twig', service(Environment::class))

        ->set(StimulusTwigExtension::class)
            ->tag('twig.extension')
    ;
};
