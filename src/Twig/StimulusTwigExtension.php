<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony StimulusBundle package.
 * (c) Fabien Potencier <fabien@symfony.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\StimulusBundle\Twig;

use Symfony\StimulusBundle\Dto\StimulusActionsDto;
use Symfony\StimulusBundle\Dto\StimulusControllersDto;
use Symfony\StimulusBundle\Dto\StimulusTargetsDto;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class StimulusTwigExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('stimulus_controller', [$this, 'renderStimulusController'], ['needs_environment' => true, 'is_safe' => ['html_attr']]),
            new TwigFunction('stimulus_action', [$this, 'renderStimulusAction'], ['needs_environment' => true, 'is_safe' => ['html_attr']]),
            new TwigFunction('stimulus_target', [$this, 'renderStimulusTarget'], ['needs_environment' => true, 'is_safe' => ['html_attr']]),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('stimulus_controller', [$this, 'appendStimulusController'], ['is_safe' => ['html_attr']]),
            new TwigFilter('stimulus_action', [$this, 'appendStimulusAction'], ['is_safe' => ['html_attr']]),
            new TwigFilter('stimulus_target', [$this, 'appendStimulusTarget'], ['is_safe' => ['html_attr']]),
        ];
    }

    /**
     * @param string $controllerName    the Stimulus controller name
     * @param array  $controllerValues  array of controller values
     * @param array  $controllerClasses array of controller CSS classes
     */
    public function renderStimulusController(Environment $env, string $controllerName, array $controllerValues = [], array $controllerClasses = []): StimulusControllersDto
    {
        $dto = new StimulusControllersDto($env);
        $dto->addController($controllerName, $controllerValues, $controllerClasses);

        return $dto;
    }

    public function appendStimulusController(StimulusControllersDto $dto, string $controllerName, array $controllerValues = [], array $controllerClasses = []): StimulusControllersDto
    {
        $dto->addController($controllerName, $controllerValues, $controllerClasses);

        return $dto;
    }

    /**
     * @param array $parameters Parameters to pass to the action. Optional.
     */
    public function renderStimulusAction(Environment $env, string $controllerName, string $actionName = null, string $eventName = null, array $parameters = []): StimulusActionsDto
    {
        $dto = new StimulusActionsDto($env);
        $dto->addAction($controllerName, $actionName, $eventName, $parameters);

        return $dto;
    }

    /**
     * @param array $parameters Parameters to pass to the action. Optional.
     */
    public function appendStimulusAction(StimulusActionsDto $dto, string $controllerName, string $actionName, string $eventName = null, array $parameters = []): StimulusActionsDto
    {
        $dto->addAction($controllerName, $actionName, $eventName, $parameters);

        return $dto;
    }

    /**
     * @param string      $controllerName the Stimulus controller name
     * @param string|null $targetNames    The space-separated list of target names if a string is passed to the 1st argument. Optional.
     */
    public function renderStimulusTarget(Environment $env, string $controllerName, string $targetNames = null): StimulusTargetsDto
    {
        $dto = new StimulusTargetsDto($env);
        $dto->addTarget($controllerName, $targetNames);

        return $dto;
    }

    /**
     * @param string      $controllerName the Stimulus controller name
     * @param string|null $targetNames    The space-separated list of target names if a string is passed to the 1st argument. Optional.
     */
    public function appendStimulusTarget(StimulusTargetsDto $dto, string $controllerName, string $targetNames = null): StimulusTargetsDto
    {
        $dto->addTarget($controllerName, $targetNames);

        return $dto;
    }
}
