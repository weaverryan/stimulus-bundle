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
class UxPackageReader
{
    public function __construct(private readonly string $projectDir)
    {
    }

    public function readPackageMetadata(string $packageName): UxPackageMetadata
    {
        // remove the '@' from the name to get back to the PHP package name
        $phpPackageName = substr($packageName, 1);
        $phpPackagePath = $this->projectDir.'/vendor/'.$phpPackageName;
        if (!is_dir($phpPackagePath)) {
            throw new \RuntimeException(sprintf('Could not find package "%s" referred to from controllers.json.', $phpPackageName));
        }
        $packageConfigJsonPath = $phpPackagePath.'/assets/package.json';
        if (!file_exists($packageConfigJsonPath)) {
            $packageConfigJsonPath = $phpPackagePath.'/Resources/assets/package.json';
        }
        if (!file_exists($packageConfigJsonPath)) {
            throw new \RuntimeException(sprintf('Could not find package.json in the "%s" package.', $phpPackagePath));
        }

        $packageConfigJson = file_get_contents($packageConfigJsonPath);
        $packageConfigData = json_decode($packageConfigJson, true);

        return new UxPackageMetadata(
            \dirname($packageConfigJsonPath),
            $packageConfigData['symfony'] ?? [],
            $phpPackageName
        );
    }
}
