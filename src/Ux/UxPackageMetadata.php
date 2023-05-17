<?php

/*
 * This file is part of the Symfony StimulusBundle package.
 * (c) Fabien Potencier <fabien@symfony.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\StimulusBundle\Ux;

/**
 * @internal
 */
class UxPackageMetadata
{
    public function __construct(
        public readonly string $packageDirectory,
        public readonly array $symfonyConfig,
    ) {
    }
}
