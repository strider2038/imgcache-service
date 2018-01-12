<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Functional;

use Strider2038\ImgCache\Enum\ResizeModeEnum;
use Strider2038\ImgCache\Imaging\Processing\Transforming\FlipTransformation;
use Strider2038\ImgCache\Imaging\Processing\Transforming\FlopTransformation;
use Strider2038\ImgCache\Imaging\Processing\Transforming\ResizeTransformation;
use Strider2038\ImgCache\Imaging\Processing\Transforming\TransformationCreatorInterface;
use Strider2038\ImgCache\Tests\Support\FunctionalTestCase;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class TransformationCreatorTest extends FunctionalTestCase
{
    /** @var TransformationCreatorInterface */
    private $transformationCreator;

    protected function setUp(): void
    {
        $container = $this->loadContainer('transformation-creator.yml');
        $this->transformationCreator = $container->get('transformation_creator');
    }

    /**
     * @test
     * @param string $configuration
     * @param string $transformationClass
     * @dataProvider configurationAndTransformationClassProvider
     */
    public function createTransformation_givenStringConfiguration_expectedTransformationCreatedAndReturned(
        string $configuration,
        string $transformationClass
    ): void {
        $transformation = $this->transformationCreator->createTransformation($configuration);

        $this->assertInstanceOf($transformationClass, $transformation);
    }

    public function configurationAndTransformationClassProvider(): array
    {
        return [
            ['s200x100', ResizeTransformation::class],
            ['size200x100', ResizeTransformation::class],
            ['flip', FlipTransformation::class],
            ['flop', FlopTransformation::class],
        ];
    }

    /**
     * @test
     * @param string $configuration
     * @param int $width
     * @param int $height
     * @param string $mode
     * @dataProvider resizeConfigurationProvider
     */
    public function createTransformation_givenResizeConfiguration_resizeTransformationWithValidParametersCreated(
        string $configuration,
        int $width,
        int $height,
        string $mode
    ): void {
        /** @var ResizeTransformation $transformation */
        $transformation = $this->transformationCreator->createTransformation($configuration);

        $this->assertNotNull($transformation);
        $this->assertInstanceOf(ResizeTransformation::class, $transformation);
        $this->assertEquals($width, $transformation->getParameters()->getWidth());
        $this->assertEquals($height, $transformation->getParameters()->getHeight());
        $this->assertEquals($mode, $transformation->getParameters()->getMode());
    }

    public function resizeConfigurationProvider(): array
    {
        return [
            ['s100x100f', 100, 100, ResizeModeEnum::FIT_IN],
            ['s500x200s', 500, 200, ResizeModeEnum::STRETCH],
            ['s50x1000w', 50, 1000, ResizeModeEnum::PRESERVE_WIDTH],
            ['s300x200h', 300, 200, ResizeModeEnum::PRESERVE_HEIGHT],
            ['s400X250H', 400, 250, ResizeModeEnum::PRESERVE_HEIGHT],
            ['s200x300', 200, 300, ResizeModeEnum::STRETCH],
            ['s200f', 200, 200, ResizeModeEnum::FIT_IN],
            ['s150', 150, 150, ResizeModeEnum::STRETCH],
            ['size100x100f', 100, 100, ResizeModeEnum::FIT_IN],
            ['size500x200s', 500, 200, ResizeModeEnum::STRETCH],
            ['size50x1000w', 50, 1000, ResizeModeEnum::PRESERVE_WIDTH],
            ['size300x200h', 300, 200, ResizeModeEnum::PRESERVE_HEIGHT],
            ['size400X250H', 400, 250, ResizeModeEnum::PRESERVE_HEIGHT],
            ['size200x300', 200, 300, ResizeModeEnum::STRETCH],
            ['size200f', 200, 200, ResizeModeEnum::FIT_IN],
            ['size150', 150, 150, ResizeModeEnum::STRETCH],
        ];
    }
}
