<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony StimulusBundle package.
 * (c) Fabien Potencier <fabien@symfony.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\StimulusBundle\Dto;

use Twig\Environment;

/**
 * @internal
 */
abstract class AbstractStimulusDto implements \Stringable
{
    public function __construct(private readonly Environment $env)
    {
    }

    abstract public function toArray(): array;

    protected function getFormattedControllerName(string $controllerName): string
    {
        return $this->escapeAsHtmlAttr($this->normalizeControllerName($controllerName));
    }

    protected function getFormattedValue(mixed $value): string
    {
        if ($value instanceof \Stringable || (\is_object($value) && \is_callable([$value, '__toString']))) {
            $value = (string) $value;
        } elseif (!\is_scalar($value)) {
            $value = json_encode($value);
        } elseif (\is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }

        return (string) $value;
    }

    protected function escapeAsHtmlAttr(mixed $value): string
    {
        return (string) twig_escape_filter($this->env, $value, 'html_attr');
    }

    /**
     * Normalize a Stimulus controller name into its HTML equivalent (no special character and / becomes --).
     *
     * @see https://stimulus.hotwired.dev/reference/controllers
     */
    private function normalizeControllerName(string $controllerName): string
    {
        return preg_replace('/^@/', '', str_replace('_', '-', str_replace('/', '--', $controllerName)));
    }
}
