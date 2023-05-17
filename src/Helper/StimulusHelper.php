<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony StimulusBundle package.
 * (c) Fabien Potencier <fabien@symfony.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\StimulusBundle\Helper;

use Symfony\StimulusBundle\Dto\StimulusActionsDto;
use Symfony\StimulusBundle\Dto\StimulusControllersDto;
use Symfony\StimulusBundle\Dto\StimulusTargetsDto;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

final class StimulusHelper
{
    private readonly Environment $twig;

    public function __construct(?Environment $twig)
    {
        // Twig needed just for its escaping mechanism
        $this->twig = $twig ?? new Environment(new ArrayLoader());
    }

    public function buildStimulusControllerDto(string $controllerName, array $controllerValues = [], array $controllerClasses = [], ?StimulusControllersDto $previousDto = null): StimulusControllersDto
    {
        $dto = $previousDto ?? new StimulusControllersDto($this->twig);
        $dto->addController($controllerName, $controllerValues, $controllerClasses);

        return $dto;
    }

    public function buildStimulusActionDto(string $controllerName, string $actionName = null, string $eventName = null, array $parameters = [], ?StimulusActionsDto $previousDto = null): StimulusActionsDto
    {
        $dto = $previousDto ?? new StimulusActionsDto($this->twig);
        $dto->addAction($controllerName, $actionName, $eventName, $parameters);

        return $dto;
    }

    public function buildStimulusTargetDto(string $controllerName, string $targetNames = null, ?StimulusTargetsDto $previousDto = null): StimulusTargetsDto
    {
        $dto = $previousDto ?? new StimulusTargetsDto($this->twig);
        $dto->addTarget($controllerName, $targetNames);

        return $dto;
    }
}
