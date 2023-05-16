<?php

namespace Symfony\StimulusBundle\AssetMapper;

use Symfony\Component\AssetMapper\MappedAsset;

class MappedControllerAsset
{
    public function __construct(
        public readonly MappedAsset $asset,
        public readonly bool $isLazy,
    )
    {
    }
}
