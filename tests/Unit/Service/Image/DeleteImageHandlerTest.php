<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Service\Image;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Imaging\ImageCacheInterface;
use Strider2038\ImgCache\Imaging\ImageStorageInterface;
use Strider2038\ImgCache\Imaging\Naming\ImageFilenameFactoryInterface;
use Strider2038\ImgCache\Imaging\Naming\ImageFilenameInterface;
use Strider2038\ImgCache\Service\Image\DeleteImageHandler;
use Strider2038\ImgCache\Tests\Support\Phake\ResponseFactoryTrait;

class DeleteImageHandlerTest extends TestCase
{
    use ResponseFactoryTrait;

    private const FILE_NAME_MASK = 'file_name_mask';

    /** @var ImageFilenameFactoryInterface */
    private $filenameFactory;

    /** @var ImageStorageInterface */
    private $imageStorage;

    /** @var ImageCacheInterface */
    private $imageCache;

    protected function setUp(): void
    {
        $this->givenResponseFactory();
        $this->filenameFactory = \Phake::mock(ImageFilenameFactoryInterface::class);
        $this->imageStorage = \Phake::mock(ImageStorageInterface::class);
        $this->imageCache = \Phake::mock(ImageCacheInterface::class);
    }

    /** @test */
    public function handlerRequest_imageExistsInStorage_imageDeletedFromStorageAndFromCacheAndOkResponseReturned(): void
    {
        $handler = $this->createDeleteImageHandler();
        $request = $this->givenRequest();
        $filename = $this->givenFilenameFactory_createImageFilenameFromRequest_returnsImageFilename();
        $this->givenImageStorage_imageExists_returns(true);
        $this->givenImageStorage_getImageFileNameMask_returnsFileNameMask();
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::OK);

        $response = $handler->handleRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertFilenameFactory_createImageFilenameFromRequest_isCalledOnceWithRequest($request);
        $this->assertImageStorage_imageExists_isCalledOnceWithFilename($filename);
        $this->assertImageStorage_deleteImage_isCalledOnceWithFilename($filename);
        $this->assertImageStorage_getImageFileNameMask_isCalledOnceWithFilename($filename);
        $this->assertImageCache_deleteImagesByMask_isCalledOnceWithFileNameMask();
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithCode(HttpStatusCodeEnum::OK);
        $this->assertEquals(HttpStatusCodeEnum::OK, $response->getStatusCode()->getValue());
    }

    /** @test */
    public function handlerRequest_imageDoesNotExistInStorage_notFoundResponseReturned(): void
    {
        $handler = $this->createDeleteImageHandler();
        $request = $this->givenRequest();
        $filename = $this->givenFilenameFactory_createImageFilenameFromRequest_returnsImageFilename();
        $this->givenImageStorage_imageExists_returns(false);
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::NOT_FOUND);

        $response = $handler->handleRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertFilenameFactory_createImageFilenameFromRequest_isCalledOnceWithRequest($request);
        $this->assertImageStorage_imageExists_isCalledOnceWithFilename($filename);
        $this->assertImageStorage_deleteImage_isNeverCalled();
        $this->assertImageCache_deleteImagesByMask_isNeverCalled();
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithCode(HttpStatusCodeEnum::NOT_FOUND);
        $this->assertEquals(HttpStatusCodeEnum::NOT_FOUND, $response->getStatusCode()->getValue());
    }

    private function createDeleteImageHandler(): DeleteImageHandler
    {
        return new DeleteImageHandler(
            $this->responseFactory,
            $this->filenameFactory,
            $this->imageStorage,
            $this->imageCache
        );
    }

    private function givenRequest(): RequestInterface
    {
        return \Phake::mock(RequestInterface::class);
    }

    private function assertFilenameFactory_createImageFilenameFromRequest_isCalledOnceWithRequest(
        RequestInterface $request
    ): void {
        \Phake::verify($this->filenameFactory, \Phake::times(1))->createImageFilenameFromRequest($request);
    }

    private function givenFilenameFactory_createImageFilenameFromRequest_returnsImageFilename(): ImageFilenameInterface
    {
        $filename = \Phake::mock(ImageFilenameInterface::class);
        \Phake::when($this->filenameFactory)
            ->createImageFilenameFromRequest(\Phake::anyParameters())
            ->thenReturn($filename);

        return $filename;
    }

    private function givenImageStorage_imageExists_returns(bool $value): void
    {
        \Phake::when($this->imageStorage)->imageExists(\Phake::anyParameters())->thenReturn($value);
    }

    private function givenImageStorage_getImageFileNameMask_returnsFileNameMask(): void
    {
        \Phake::when($this->imageStorage)->getImageFileNameMask(\Phake::anyParameters())->thenReturn(self::FILE_NAME_MASK);
    }

    private function assertImageStorage_imageExists_isCalledOnceWithFilename(ImageFilenameInterface $filename): void
    {
        \Phake::verify($this->imageStorage, \Phake::times(1))->imageExists($filename);
    }

    private function assertImageStorage_deleteImage_isCalledOnceWithFilename(ImageFilenameInterface $filename): void
    {
        \Phake::verify($this->imageStorage, \Phake::times(1))->deleteImage($filename);
    }

    private function assertImageStorage_deleteImage_isNeverCalled(): void
    {
        \Phake::verify($this->imageStorage, \Phake::times(0))->deleteImage(\Phake::anyParameters());
    }

    private function assertImageStorage_getImageFileNameMask_isCalledOnceWithFilename(
        ImageFilenameInterface $filename
    ): void {
        \Phake::verify($this->imageStorage, \Phake::times(1))->getImageFileNameMask($filename);
    }

    private function assertImageCache_deleteImagesByMask_isCalledOnceWithFileNameMask(): void
    {
        \Phake::verify($this->imageCache, \Phake::times(1))->deleteImagesByMask(self::FILE_NAME_MASK);
    }

    private function assertImageCache_deleteImagesByMask_isNeverCalled(): void
    {
        \Phake::verify($this->imageCache, \Phake::times(0))->deleteImagesByMask(\Phake::anyParameters());
    }
}
