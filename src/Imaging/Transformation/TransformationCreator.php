<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Transformation;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class TransformationCreator implements TransformationCreatorInterface
{
    /** @var TransformationFactoryMap */
    private $factoryMap;
    
    public function __construct(TransformationFactoryMap $factoryMap = null)
    {
        $this->factoryMap = $factoryMap ?? $this->getDefaultTransformationFactoryMap();
    }

    public function createTransformation(string $configuration): ? TransformationInterface
    {
        /**
         * @var string $pattern
         * @var TransformationFactoryInterface $factory
         */
        foreach ($this->factoryMap as $pattern => $factory) {
            if (preg_match_all($pattern, $configuration, $matches)) {
                return $factory->createTransformation($matches['parameters'][0] ?? '');
            }
        }

        return null;
    }

    private function getDefaultTransformationFactoryMap(): TransformationFactoryMap
    {
        return new TransformationFactoryMap([
            '/^(s|size)(?P<parameters>\d.*)$/' => new ResizeTransformationFactory(),
        ]);
    }
}
