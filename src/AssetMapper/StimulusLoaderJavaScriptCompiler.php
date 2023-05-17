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
        return $asset->getSourcePath() === realpath(__DIR__ . '/../../assets/loader.js');
    }

    public function compile(string $content, MappedAsset $asset, AssetMapperInterface $assetMapper): string
    {
        $lines = explode("\n", $content);
        $controllersMapLineIndex = array_search('export const eagerControllers = {};', $lines);
        if (false === $controllersMapLineIndex) {
            throw new \RuntimeException('The Stimulus loader.js file must contain the line "export const controllersMap = {};"');
        }

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

        $dynamicContents = <<<EOF
        $importCode
        export const eagerControllers = $eagerControllersJson;
        export const lazyControllers = $lazyControllersExpression;
        EOF;

        // replace $controllersMapLineIndex + 2 lines above and below
        array_splice($lines, $controllersMapLineIndex - 2, 5, $dynamicContents);

        $finalContent = implode("\n", $lines);

        if (!$this->isDebug) {
            $finalContent = str_replace('application.debug = true;', 'application.debug = false;', $finalContent);
        }

        return $finalContent;
    }
}
