<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Processing\Adapter;

use Psr\Log\LoggerInterface;
use Strider2038\ImgCache\Imaging\Processing\Adapter\ImagickEngine;
use Strider2038\ImgCache\Imaging\Processing\Adapter\ImagickImage;
use Strider2038\ImgCache\Imaging\Processing\ProcessingImageInterface;
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;
use Strider2038\ImgCache\Tests\Support\FileTestCase;
use Strider2038\ImgCache\Tests\Support\Phake\FileOperationsTrait;
use Strider2038\ImgCache\Tests\Support\Phake\LoggerTrait;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImagickEngineTest extends FileTestCase
{
    use FileOperationsTrait, LoggerTrait;

    /** @var LoggerInterface */
    private $logger;

    protected function setUp()
    {
        parent::setUp();
        $this->logger = $this->givenLogger();
    }

    /**
     * @expectedException \Exception
     */
    public function testOpenFromFile_FileDoesNotExist_ExceptionThrown(): void
    {
        $engine = $this->createImagick();
        $saveOptions = \Phake::mock(SaveOptions::class);

        $engine->openFromFile(self::TEST_CACHE_DIR . '/a.jpg', $saveOptions);
    }

    public function testOpenFromFile_FileExist_ImagickImageIsReturned(): void
    {
        $engine = $this->createImagick();
        $saveOptions = \Phake::mock(SaveOptions::class);
        $filename = $this->givenAssetFile(self::IMAGE_BOX_PNG);

        $image = $engine->openFromFile($filename, $saveOptions);

        $this->assertInstanceOf(ProcessingImageInterface::class, $image);
        $this->assertInstanceOf(ImagickImage::class, $image);
        $this->assertSame($saveOptions, $image->getSaveOptions());
    }

    public function testOpenFromBlob_GivenBlob_ImagickImageIsReturned(): void
    {
        $engine = $this->createImagick();
        $saveOptions = \Phake::mock(SaveOptions::class);
        $filename = $this->givenAssetFile(self::IMAGE_BOX_PNG);
        $blob = file_get_contents($filename);

        $image = $engine->openFromBlob($blob, $saveOptions);

        $this->assertInstanceOf(ProcessingImageInterface::class, $image);
        $this->assertInstanceOf(ImagickImage::class, $image);
        $this->assertSame($saveOptions, $image->getSaveOptions());
    }

    private function createImagick(): ImagickEngine
    {
        $engine = new ImagickEngine($this->givenFileOperations());

        $engine->setLogger($this->logger);

        return $engine;
    }
}
