<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use Symfony\StimulusBundle\Twig\StimulusTwigExtension;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set(StimulusTwigExtension::class)
            ->tag('twig.extension')
    ;
};
