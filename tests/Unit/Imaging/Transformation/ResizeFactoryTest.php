<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Transformation;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Transformation\Resize;
use Strider2038\ImgCache\Imaging\Transformation\ResizeFactory;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ResizeFactoryTest extends TestCase
{

    /**
     * @param string $configuration
     * @param int $width
     * @param int $height
     * @param string $mode
     * @dataProvider resizeConfigProvider
     */
    public function testCreate_ValidConfig_ClassIsConstructed(
        string $configuration,
        int $width,
        int $height,
        string $mode
    ): void {
        $factory = new ResizeFactory();

        /** @var Resize $resize */
        $resize = $factory->create($configuration);

        $this->assertInstanceOf(Resize::class, $resize);
        $this->assertEquals($width, $resize->getWidth());
        $this->assertEquals($height, $resize->getHeight());
        $this->assertEquals($mode, $resize->getMode());
    }

    public function resizeConfigProvider(): array
    {
        return [
            ['100x100f', 100, 100, Resize::MODE_FIT_IN],
            ['500x200s', 500, 200, Resize::MODE_STRETCH],
            ['50x1000w', 50, 1000, Resize::MODE_PRESERVE_WIDTH],
            ['300x200h', 300, 200, Resize::MODE_PRESERVE_HEIGHT],
            ['400X250H', 400, 250, Resize::MODE_PRESERVE_HEIGHT],
            ['200x300', 200, 300, Resize::MODE_STRETCH],
            ['200f', 200, 200, Resize::MODE_FIT_IN],
            ['150', 150, 150, Resize::MODE_STRETCH],
        ];
    }

    /**
     * @param string $configuration
     * @dataProvider resizeInvalidConfigProvider
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestValueException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Invalid config for resize transformation
     */
    public function testBuild_InvalidConfig_ExceptionThrown(string $configuration): void
    {
        $factory = new ResizeFactory();

        $factory->create($configuration);
    }

    public function resizeInvalidConfigProvider(): array
    {
        return [
            ['1500k'],
            ['100x15i'],
            ['100x156sp'],
            ['100x'],
        ];
    }

}