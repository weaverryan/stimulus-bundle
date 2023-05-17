<?php

namespace Symfony\StimulusBundle\AssetMapper;

use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Component\AssetMapper\Compiler\AssetCompilerInterface;
use Symfony\Component\AssetMapper\Compiler\AssetCompilerPathResolverTrait;
use Symfony\Component\AssetMapper\MappedAsset;

/**
 * Compiles the loader.js file to dynamically import the controllers.
 */
class StimulusLoaderJavaScriptCompiler implements AssetCompilerInterface
{
    use AssetCompilerPathResolverTrait;

    public function __construct(
        private readonly ControllersMapGenerator $controllersMapGenerator,
        private readonly bool $isDebug,
    )
    {
    }

    public function supports(MappedAsset $asset): bool
    {
        return $asset->getSourcePath() === realpath(__DIR__ . '/../../assets/controllers.js');
    }

    public function compile(string $content, MappedAsset $asset, AssetMapperInterface $assetMapper): string
    {
        $importLines = [];
        $eagerControllerParts = [];
        $lazyControllers = [];
        $loaderPublicPath = $asset->getPublicPathWithoutDigest();
        foreach ($this->controllersMapGenerator->getControllersMap() as $name => $mappedControllerAsset) {
            $controllerPublicPath = $mappedControllerAsset->asset->getPublicPathWithoutDigest();
            $relativeImportPath = $this->createRelativePath($loaderPublicPath, $controllerPublicPath);

            if ($mappedControllerAsset->isLazy) {
                $lazyControllers[] = sprintf('%s: () => import(%s)', json_encode($name), json_encode($relativeImportPath, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES));
                continue;
            }

            $controllerNameForVariable = sprintf('controller_%s', count($eagerControllerParts));

            $importLines[] = sprintf(
                "import %s from '%s';",
                $controllerNameForVariable,
                $relativeImportPath
            );
            $eagerControllerParts[] = sprintf('"%s": %s', $name, $controllerNameForVariable);
        }

        $importCode = implode("\n", $importLines);
        $eagerControllersJson = sprintf('{%s}', implode(', ', $eagerControllerParts));
        $lazyControllersExpression = sprintf('{%s}', implode(', ', $lazyControllers));

        $isDebugString = $this->isDebug ? 'true' : 'false';

        return <<<EOF
        $importCode
        export const eagerControllers = $eagerControllersJson;
        export const lazyControllers = $lazyControllersExpression;
        export const isApplicationDebug = $isDebugString;
        EOF;
    }
}
