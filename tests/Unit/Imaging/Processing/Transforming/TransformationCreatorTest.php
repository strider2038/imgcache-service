<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Processing\Transforming;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Processing\Transforming\ResizingTransformation;
use Strider2038\ImgCache\Imaging\Processing\Transforming\TransformationCreator;
use Strider2038\ImgCache\Imaging\Processing\Transforming\TransformationFactoryInterface;
use Strider2038\ImgCache\Imaging\Processing\Transforming\TransformationFactoryMap;
use Strider2038\ImgCache\Imaging\Processing\Transforming\TransformationInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class TransformationCreatorTest extends TestCase
{
    private const INVALID_CONFIGURATION = 'configuration';
    private const CUSTOM_CONFIGURATION = 'transformation-id400x200';
    private const CUSTOM_CONFIGURATION_VALUE = '400x200';

    /** @test */
    public function findAndCreateTransformation_givenConfigurationAndFactoryNotFound_nullReturned(): void
    {
        $factoryMap = new TransformationFactoryMap();
        $creator = new TransformationCreator($factoryMap);

        $transformation = $creator->findAndCreateTransformation(self::INVALID_CONFIGURATION);

        $this->assertNull($transformation);
    }

    /** @test */
    public function findAndCreateTransformation_givenFactoryMapConfigurationAndFactoryFound_transformationCreatedAndReturned(): void
    {
        $factory = \Phake::mock(TransformationFactoryInterface::class);
        $factoryMap = new TransformationFactoryMap([
            '/^transformation-id(?P<parameters>.*)$/' => $factory
        ]);
        $creator = new TransformationCreator($factoryMap);
        $expectedTransformation = $this->givenTransformationFactory_createTransformation_returnsTransformation($factory);

        $transformation = $creator->findAndCreateTransformation(self::CUSTOM_CONFIGURATION);

        $this->assertNotNull($transformation);
        $this->assertTransformationFactory_createTransformation_isCalledOnceWithValue($factory, self::CUSTOM_CONFIGURATION_VALUE);
        $this->assertSame($expectedTransformation, $transformation);
    }

    private function givenTransformationFactory_createTransformation_returnsTransformation(
        TransformationFactoryInterface $factory
    ): TransformationInterface {
        $transformation = \Phake::mock(TransformationInterface::class);
        \Phake::when($factory)->createTransformation(\Phake::anyParameters())->thenReturn($transformation);

        return $transformation;
    }

    private function assertTransformationFactory_createTransformation_isCalledOnceWithValue(
        TransformationFactoryInterface $factory,
        string $value
    ): void {
        \Phake::verify($factory, \Phake::times(1))->createTransformation($value);
    }
}
