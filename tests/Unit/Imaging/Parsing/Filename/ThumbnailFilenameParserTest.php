<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Parsing\Filename;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Parsing\Processing\ProcessingConfigurationParserInterface;
use Strider2038\ImgCache\Imaging\Parsing\Filename\ThumbnailFilename;
use Strider2038\ImgCache\Imaging\Parsing\Filename\ThumbnailFilenameParser;
use Strider2038\ImgCache\Imaging\Processing\ProcessingConfiguration;
use Strider2038\ImgCache\Imaging\Validation\ImageValidatorInterface;
use Strider2038\ImgCache\Imaging\Validation\KeyValidatorInterface;

class ThumbnailFilenameParserTest extends TestCase
{
    private const INVALID_KEY = 'a';
    private const KEY_WITH_INVALID_CONFIG = 'a_.jpg';

    /** @var KeyValidatorInterface */
    private $keyValidator;

    /** @var ImageValidatorInterface */
    private $imageValidator;

    /** @var ProcessingConfigurationParserInterface */
    private $processingConfigurationParser;

    protected function setUp(): void
    {
        $this->keyValidator = \Phake::mock(KeyValidatorInterface::class);
        $this->imageValidator = \Phake::mock(ImageValidatorInterface::class);
        $this->processingConfigurationParser = \Phake::mock(ProcessingConfigurationParserInterface::class);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestValueException
     * @expectedExceptionCode 400
     * @expectedExceptionMessageRegExp  /Invalid filename .* in request/
     */
    public function parse_givenInvalidKey_exceptionThrown(): void
    {
        $parser = $this->createThumbnailKeyParser();
        $this->givenKeyValidator_isValidPublicFilename_returns(self::INVALID_KEY,false);

        $parser->getParsedFilename(self::INVALID_KEY);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestValueException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Unsupported image extension
     */
    public function parse_givenKeyHasInvalidExtension_exceptionThrown(): void
    {
        $parser = $this->createThumbnailKeyParser();
        $this->givenKeyValidator_isValidPublicFilename_returns(self::INVALID_KEY,true);
        $this->givenImageValidator_hasValidImageExtension_returns(self::INVALID_KEY, false);

        $parser->getParsedFilename(self::INVALID_KEY);
    }

    /**
     * @test
     * @param string $key
     * @param string $publicFilename
     * @param string $thumbnailMask
     * @param string $processingConfigurationString
     * @dataProvider validKeyProvider
     */
    public function parse_givenKey_keyParsedToThumbnailKey(
        string $key,
        string $publicFilename,
        string $thumbnailMask,
        string $processingConfigurationString
    ): void {
        $parser = $this->createThumbnailKeyParser();
        $this->givenKeyValidator_isValidPublicFilename_returns($key,true);
        $this->givenImageValidator_hasValidImageExtension_returns($key, true);
        $processingConfiguration = $this->givenProcessingConfigurationParser_parseConfiguration_returns($processingConfigurationString);

        $thumbnailKey = $parser->getParsedFilename($key);

        $this->assertInstanceOf(ThumbnailFilename::class, $thumbnailKey);
        $this->assertEquals($publicFilename, $thumbnailKey->getValue());
        $this->assertEquals($thumbnailMask, $thumbnailKey->getMask());
        $this->assertProcessingConfigurationParser_parseConfiguration_isCalledOnceWith($processingConfigurationString);
        $this->assertSame($processingConfiguration, $thumbnailKey->getProcessingConfiguration());
    }

    public function validKeyProvider(): array
    {
        return [
            ['a', 'a.', 'a*.', ''],
            ['a.jpg', 'a.jpg', 'a*.jpg', ''],
            ['/a_q1.jpg', '/a.jpg', '/a*.jpg', 'q1'],
            ['/b_a1_b2.png', '/b.png', '/b*.png', 'a1_b2'],
            ['/a/b/c/d_q5.jpg', '/a/b/c/d.jpg', '/a/b/c/d*.jpg', 'q5'],
        ];
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestValueException
     * @expectedExceptionCode 400
     * @expectedExceptionMessageRegExp  /Invalid filename .* in request/
     * @param string $key
     * @dataProvider invalidKeyProvider
     */
    public function parse_givenKeyHasInvalidProcessingConfiguration_exceptionThrown(string $key): void
    {
        $parser = $this->createThumbnailKeyParser();
        $this->givenKeyValidator_isValidPublicFilename_returns($key,true);
        $this->givenImageValidator_hasValidImageExtension_returns($key, true);

        $parser->getParsedFilename($key);
    }

    public function invalidKeyProvider(): array
    {
        return [
            [self::KEY_WITH_INVALID_CONFIG],
            [''],
            [' '],
        ];
    }

    private function createThumbnailKeyParser(): ThumbnailFilenameParser
    {
        return new ThumbnailFilenameParser($this->keyValidator, $this->imageValidator, $this->processingConfigurationParser);
    }

    private function givenKeyValidator_isValidPublicFilename_returns(string $filename, bool $value): void
    {
        \Phake::when($this->keyValidator)->isValidPublicFilename($filename)->thenReturn($value);
    }

    private function givenImageValidator_hasValidImageExtension_returns(string $filename, bool $value): void
    {
        \Phake::when($this->imageValidator)->hasValidImageExtension($filename)->thenReturn($value);
    }

    private function givenProcessingConfigurationParser_parseConfiguration_returns(
        string $processingConfigurationString
    ): ProcessingConfiguration {
        $processingConfiguration = \Phake::mock(ProcessingConfiguration::class);
        \Phake::when($this->processingConfigurationParser)
            ->parseConfiguration($processingConfigurationString)
            ->thenReturn($processingConfiguration);

        return $processingConfiguration;
    }

    private function assertProcessingConfigurationParser_parseConfiguration_isCalledOnceWith(
        string $processingConfigurationString
    ): void {
        \Phake::verify($this->processingConfigurationParser, \Phake::times(1))
            ->parseConfiguration($processingConfigurationString);
    }
}