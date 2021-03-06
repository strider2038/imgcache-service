<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Strider2038\ImgCache\Configuration\ImageSource\FilesystemImageSource;
use Strider2038\ImgCache\Configuration\ImageSource\WebDAVImageSource;
use Strider2038\ImgCache\Enum\ImageProcessorTypeEnum;
use Strider2038\ImgCache\Imaging\Extraction\SourceImageExtractor;
use Strider2038\ImgCache\Imaging\Extraction\ThumbnailImageCreatorInterface;
use Strider2038\ImgCache\Imaging\Extraction\ThumbnailImageExtractor;
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\Insertion\SourceImageWriter;
use Strider2038\ImgCache\Imaging\Insertion\ThumbnailImageWriter;
use Strider2038\ImgCache\Imaging\Naming\DirectoryNameFactoryInterface;
use Strider2038\ImgCache\Imaging\Parsing\Filename\PlainFilenameParser;
use Strider2038\ImgCache\Imaging\Parsing\Filename\ThumbnailFilenameParser;
use Strider2038\ImgCache\Imaging\Storage\Accessor\FilesystemStorageAccessor;
use Strider2038\ImgCache\Imaging\Storage\Data\StorageFilenameFactory;
use Strider2038\ImgCache\Imaging\Storage\Driver\FilesystemStorageDriverFactory;
use Strider2038\ImgCache\Imaging\Storage\Driver\FilesystemStorageDriverInterface;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class FilesystemImageStorageFactory
{
    /** @var FilesystemStorageDriverFactory */
    private $filesystemStorageDriverFactory;
    /** @var EntityValidatorInterface */
    private $validator;
    /** @var ImageFactoryInterface */
    private $imageFactory;
    /** @var ThumbnailImageCreatorInterface */
    private $thumbnailImageCreator;
    /** @var DirectoryNameFactoryInterface */
    private $directoryNameFactory;
    /** @var LoggerInterface */
    private $logger;

    /** @var FilesystemStorageDriverInterface */
    private $storageDriver;

    public function __construct(
        FilesystemStorageDriverFactory $filesystemStorageDriverFactory,
        EntityValidatorInterface $validator,
        ImageFactoryInterface $imageFactory,
        ThumbnailImageCreatorInterface $thumbnailImageCreator,
        DirectoryNameFactoryInterface $directoryNameFactory
    ) {
        $this->filesystemStorageDriverFactory = $filesystemStorageDriverFactory;
        $this->validator = $validator;
        $this->imageFactory = $imageFactory;
        $this->thumbnailImageCreator = $thumbnailImageCreator;
        $this->directoryNameFactory = $directoryNameFactory;
        $this->logger = new NullLogger();
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function createImageStorageForImageSource(FilesystemImageSource $imageSource): ImageStorageInterface
    {
        $this->createStorageDriverForImageSource($imageSource);
        $storageRootDirectory = $imageSource->getStorageDirectory();
        $storageAccessor = $this->createStorageAccessorWithStorageDriverAndRootDirectory($storageRootDirectory);
        $processorType = $imageSource->getProcessorType();

        return $this->createImageStorageByProcessorTypeWithStorageAccessor($processorType, $storageAccessor);
    }

    private function createStorageDriverForImageSource(FilesystemImageSource $imageSource): void
    {
        if ($imageSource instanceof WebDAVImageSource) {
            $this->storageDriver = $this->filesystemStorageDriverFactory->createWebDAVStorageDriver(
                $imageSource->getDriverUri(),
                $imageSource->getOauthToken()
            );
        } else {
            $this->storageDriver = $this->filesystemStorageDriverFactory->createFilesystemStorageDriver();
        }
    }

    private function createStorageAccessorWithStorageDriverAndRootDirectory(string $storageRootDirectory): FilesystemStorageAccessor
    {
        $directoryName = $this->directoryNameFactory->createDirectoryName($storageRootDirectory);

        $storageAccessor = new FilesystemStorageAccessor(
            $this->storageDriver,
            $this->imageFactory,
            new StorageFilenameFactory($directoryName)
        );
        $storageAccessor->setLogger($this->logger);

        return $storageAccessor;
    }

    private function createImageStorageByProcessorTypeWithStorageAccessor(
        ImageProcessorTypeEnum $processorType,
        FilesystemStorageAccessor $storageAccessor
    ): ImageStorage {
        if ($processorType->getValue() === ImageProcessorTypeEnum::COPY) {
            $filenameParser = new PlainFilenameParser($this->validator);
            $imageExtractor = new SourceImageExtractor($filenameParser, $storageAccessor);
            $imageWriter = new SourceImageWriter($filenameParser, $storageAccessor);
        } else {
            $filenameParser = new ThumbnailFilenameParser($this->validator);
            $imageExtractor = new ThumbnailImageExtractor($filenameParser, $storageAccessor, $this->thumbnailImageCreator);
            $imageWriter = new ThumbnailImageWriter($filenameParser, $storageAccessor);
        }

        return new ImageStorage($imageExtractor, $imageWriter);
    }
}
