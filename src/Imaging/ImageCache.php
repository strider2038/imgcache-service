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

use Strider2038\ImgCache\Core\FileOperationsInterface;
use Strider2038\ImgCache\Exception\FileNotFoundException;
use Strider2038\ImgCache\Exception\InvalidConfigurationException;
use Strider2038\ImgCache\Exception\InvalidValueException;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Image\ImageFile;
use Strider2038\ImgCache\Imaging\Processing\ImageProcessorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageCache implements ImageCacheInterface
{
    /**
     * Web directory that contains image files
     * @var string
     */
    private $webDirectory;

    /** @var FileOperationsInterface */
    private $fileOperations;

    /** @var ImageProcessorInterface */
    private $imageProcessor;

    public function __construct(
        string $webDirectory,
        FileOperationsInterface $fileOperations,
        ImageProcessorInterface $imageProcessor
    ) {
        if (!$fileOperations->isDirectory($webDirectory)) {
            throw new InvalidConfigurationException(sprintf(
                'Directory "%s" does not exist',
                $webDirectory
            ));
        }

        $this->webDirectory = $webDirectory;
        $this->fileOperations = $fileOperations;
        $this->imageProcessor = $imageProcessor;
    }

    public function getImage(string $fileName): ImageFile
    {
        $destinationFileName = $this->composeDestinationFileName($fileName);

        if (!$this->fileOperations->isFile($destinationFileName)) {
            throw new FileNotFoundException(sprintf('File "%s" does not exist', $destinationFileName));
        }

        return new ImageFile($destinationFileName);
    }

    public function putImage(string $fileName, Image $image): void
    {
        $destinationFileName = $this->composeDestinationFileName($fileName);
        $this->imageProcessor->saveToFile($image, $destinationFileName);
    }

    public function deleteImagesByMask(string $fileNameMask): void
    {
        $destinationFileNameMask = $this->composeDestinationFileName($fileNameMask);
        $cachedFileNames = $this->fileOperations->findByMask($destinationFileNameMask);
        foreach ($cachedFileNames as $cachedFileName) {
            $this->fileOperations->deleteFile($cachedFileName);
        }
    }

    private function composeDestinationFileName(string $fileName): string
    {
        if (\strlen($fileName) <= 0 || $fileName[0] !== '/') {
            throw new InvalidValueException('Filename must start with slash');
        }

        return $this->webDirectory . $fileName;
    }
}
