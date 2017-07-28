<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Processing;

use Strider2038\ImgCache\Imaging\Processing\{
    ProcessingImageInterface, Strider2038\ImgCache\Imaging\Processing\Adapter\ImagickEngine, Strider2038\ImgCache\Imaging\Processing\Adapter\ImagickImage
};
use Strider2038\ImgCache\Tests\Support\FileTestCase;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImagickEngineTest extends FileTestCase
{
    /**
     * @expectedException \Exception
     */
    public function testOpen_FileDoesNotExist_ExceptionThrown(): void
    {
        $engine = new \Strider2038\ImgCache\Imaging\Processing\Adapter\ImagickEngine();
        $engine->openFromFile(self::TEST_CACHE_DIR . '/a.jpg');
    }

    public function testOpen_FileExist_ImagickImageIsReturned(): void
    {
        $engine = new \Strider2038\ImgCache\Imaging\Processing\Adapter\ImagickEngine();
        $image = $engine->openFromFile($this->givenFile(self::IMAGE_CAT300));
        $this->assertInstanceOf(ProcessingImageInterface::class, $image);
        $this->assertInstanceOf(\Strider2038\ImgCache\Imaging\Processing\Adapter\ImagickImage::class, $image);
    }
}