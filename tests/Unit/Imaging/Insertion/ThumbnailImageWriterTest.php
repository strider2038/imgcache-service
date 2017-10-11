<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Insertion;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Imaging\Insertion\ThumbnailImageWriter;
use Strider2038\ImgCache\Imaging\Parsing\Thumbnail\ThumbnailKeyInterface;
use Strider2038\ImgCache\Imaging\Parsing\Thumbnail\ThumbnailKeyParserInterface;
use Strider2038\ImgCache\Imaging\Source\Accessor\SourceAccessorInterface;
use Strider2038\ImgCache\Tests\Support\Phake\ProviderTrait;

class ThumbnailImageWriterTest extends TestCase
{
    use ProviderTrait;

    private const KEY = 'key';
    private const PUBLIC_FILENAME = 'public_filename';
    private const THUMBNAIL_MASK = 'thumbnail_mask';

    /** @var ThumbnailKeyParserInterface */
    private $keyParser;

    /** @var SourceAccessorInterface */
    private $sourceAccessor;

    protected function setUp()
    {
        $this->keyParser = \Phake::mock(ThumbnailKeyParserInterface::class);
        $this->sourceAccessor = \Phake::mock(SourceAccessorInterface::class);
    }

    /**
     * @test
     * @param bool $expectedExists
     * @dataProvider boolValuesProvider
     */
    public function exists_sourceImageExtractorExistsReturnsBool_boolIsReturned(bool $expectedExists): void
    {
        $writer = $this->createThumbnailImageWriter();
        $this->givenKeyParser_parse_returnsThumbnailKey();
        $this->givenSourceAccessor_exists_returns($expectedExists);

        $actualExists = $writer->exists(self::KEY);

        $this->assertEquals($expectedExists, $actualExists);
    }

    /** @test */
    public function insert_givenKeyAndData_keyIsParsedAndSourceAccessorPutIsCalled(): void
    {
        $writer = $this->createThumbnailImageWriter();
        $stream = \Phake::mock(StreamInterface::class);
        $this->givenKeyParser_parse_returnsThumbnailKey();

        $writer->insert(self::KEY, $stream);

        $this->assertKeyParser_parse_isCalledOnce();
        $this->assertSourceAccessor_put_isCalledOnceWith($stream);
    }

    /** @test */
    public function delete_givenKey_keyIsParsedAndSourceAccessorDeleteIsCalled(): void
    {
        $writer = $this->createThumbnailImageWriter();
        $this->givenKeyParser_parse_returnsThumbnailKey();

        $writer->delete(self::KEY);

        $this->assertKeyParser_parse_isCalledOnce();
        $this->assertSourceAccessor_delete_isCalledOnce();
    }

    /** @test */
    public function getFileMask_givenKey_keyIsParsedAndThumbnailMaskIsReturned(): void
    {
        $writer = $this->createThumbnailImageWriter();
        $this->givenKeyParser_parse_returnsThumbnailKey();

        $filename = $writer->getFileMask(self::KEY);

        $this->assertKeyParser_parse_isCalledOnce();
        $this->assertEquals(self::THUMBNAIL_MASK, $filename);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestValueException
     * @expectedExceptionCode 400
     * @expectedExceptionMessageRegExp /Image name .* for source image cannot have process configuration/
     * @param string $method
     * @param array $parameters
     * @dataProvider methodAndParametersProvider
     */
    public function method_givenKeyHasProcessingConfiguration_exceptionThrown(
        string $method,
        array $parameters
    ): void {
        $writer = $this->createThumbnailImageWriter();
        $this->givenKeyParser_parse_returnsThumbnailKey(true);

        call_user_func_array([$writer, $method], $parameters);
    }

    public function methodAndParametersProvider(): array
    {
        return [
            ['exists', [self::KEY]],
            ['insert', [self::KEY, \Phake::mock(StreamInterface::class)]],
            ['delete', [self::KEY]],
            ['getFileMask', [self::KEY]],
        ];
    }

    private function givenKeyParser_parse_returnsThumbnailKey(
        bool $hasProcessingConfiguration = false
    ): ThumbnailKeyInterface {
        $parsedKey = \Phake::mock(ThumbnailKeyInterface::class);
        \Phake::when($this->keyParser)->parse(self::KEY)->thenReturn($parsedKey);
        \Phake::when($parsedKey)->getPublicFilename()->thenReturn(self::PUBLIC_FILENAME);
        \Phake::when($parsedKey)->getThumbnailMask()->thenReturn(self::THUMBNAIL_MASK);
        \Phake::when($parsedKey)->hasProcessingConfiguration()->thenReturn($hasProcessingConfiguration);

        return $parsedKey;
    }

    private function givenSourceAccessor_exists_returns(bool $value): void
    {
        \Phake::when($this->sourceAccessor)->exists(self::PUBLIC_FILENAME)->thenReturn($value);
    }

    private function assertKeyParser_parse_isCalledOnce(): void
    {
        \Phake::verify($this->keyParser, \Phake::times(1))->parse(self::KEY);
    }

    private function assertSourceAccessor_put_isCalledOnceWith(StreamInterface $stream): void
    {
        \Phake::verify($this->sourceAccessor, \Phake::times(1))
            ->put(self::PUBLIC_FILENAME, $stream);
    }

    private function assertSourceAccessor_delete_isCalledOnce(): void
    {
        \Phake::verify($this->sourceAccessor, \Phake::times(1))
            ->delete(self::PUBLIC_FILENAME);
    }

    private function createThumbnailImageWriter(): ThumbnailImageWriter
    {
        return new ThumbnailImageWriter($this->keyParser, $this->sourceAccessor);
    }
}