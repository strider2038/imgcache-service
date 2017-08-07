<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Parsing\Thumbnail;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Parsing\Thumbnail\ThumbnailKey;
use Strider2038\ImgCache\Imaging\Parsing\Thumbnail\ThumbnailKeyParser;
use Strider2038\ImgCache\Imaging\Validation\ImageValidatorInterface;
use Strider2038\ImgCache\Imaging\Validation\KeyValidatorInterface;

class ThumbnailKeyParserTest extends TestCase
{
    const INVALID_KEY = 'a';
    const KEY_WITH_INVALID_CONFIG = 'a_.jpg';

    /** @var KeyValidatorInterface */
    private $keyValidator;

    /** @var ImageValidatorInterface */
    private $imageValidator;

    protected function setUp()
    {
        $this->keyValidator = \Phake::mock(KeyValidatorInterface::class);
        $this->imageValidator = \Phake::mock(ImageValidatorInterface::class);
    }

    /**
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestValueException
     * @expectedExceptionCode 400
     * @expectedExceptionMessageRegExp  /Invalid filename .* in request/
     */
    public function testParse_GivenInvalidKey_ExceptionThrown(): void
    {
        $parser = $this->createThumbnailKeyParser();
        $this->givenKeyValidator_IsValidPublicFilename_Returns(self::INVALID_KEY,false);

        $parser->parse(self::INVALID_KEY);
    }

    /**
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestValueException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Unsupported image extension
     */
    public function testParse_GivenKeyHasInvalidExtension_ExceptionThrown(): void
    {
        $parser = $this->createThumbnailKeyParser();
        $this->givenKeyValidator_IsValidPublicFilename_Returns(self::INVALID_KEY,true);
        $this->givenImageValidator_hasValidImageExtension_Returns(self::INVALID_KEY, false);

        $parser->parse(self::INVALID_KEY);
    }

    /**
     * @param string $key
     * @param string $publicFilename
     * @param string $processingConfiguration
     * @dataProvider validKeyProvider
     */
    public function testParse_GivenKey_KeyParsedToThumbnailKey(
        string $key,
        string $publicFilename,
        string $processingConfiguration
    ): void {
        $parser = $this->createThumbnailKeyParser();
        $this->givenKeyValidator_IsValidPublicFilename_Returns($key,true);
        $this->givenImageValidator_hasValidImageExtension_Returns($key, true);

        $thumbnailKey = $parser->parse($key);

        $this->assertInstanceOf(ThumbnailKey::class, $thumbnailKey);
        $this->assertEquals($publicFilename, $thumbnailKey->getPublicFilename());
        $this->assertEquals($processingConfiguration, $thumbnailKey->getProcessingConfiguration());
    }

    public function validKeyProvider(): array
    {
        return [
            ['a.jpg', 'a.jpg', ''],
            ['/a_q1.jpg', '/a.jpg', 'q1'],
            ['/b_a1_b2.png', '/b.png', 'a1_b2'],
            ['/a/b/c/d_q5.jpg', '/a/b/c/d.jpg', 'q5'],
        ];
    }

    /**
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestValueException
     * @expectedExceptionCode 400
     * @expectedExceptionMessageRegExp  /Invalid filename .* in request/
     */
    public function testParse_GivenKeyHasInvalidProcessingConfig_ExceptionThrown(): void
    {
        $parser = $this->createThumbnailKeyParser();
        $this->givenKeyValidator_IsValidPublicFilename_Returns(self::KEY_WITH_INVALID_CONFIG,true);
        $this->givenImageValidator_hasValidImageExtension_Returns(self::KEY_WITH_INVALID_CONFIG, true);

        $parser->parse(self::KEY_WITH_INVALID_CONFIG);
    }

    private function createThumbnailKeyParser(): ThumbnailKeyParser
    {
        $parser = new ThumbnailKeyParser($this->keyValidator, $this->imageValidator);

        return $parser;
    }

    private function givenKeyValidator_IsValidPublicFilename_Returns(string $filename, bool $value): void
    {
        \Phake::when($this->keyValidator)->isValidPublicFilename($filename)->thenReturn($value);
    }

    private function givenImageValidator_hasValidImageExtension_Returns(string $filename, bool $value): void
    {
        \Phake::when($this->imageValidator)->hasValidImageExtension($filename)->thenReturn($value);
    }
}