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
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Image\ImageFile;
use Strider2038\ImgCache\Imaging\Naming\DirectoryNameInterface;
use Strider2038\ImgCache\Imaging\Naming\ImageFilenameInterface;
use Strider2038\ImgCache\Imaging\Processing\ImageProcessorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageCache implements ImageCacheInterface
{
    /**
     * Web directory containing image files
     * @var DirectoryNameInterface
     */
    private $webDirectory;
    /** @var FileOperationsInterface */
    private $fileOperations;
    /** @var ImageProcessorInterface */
    private $imageProcessor;

    public function __construct(
        DirectoryNameInterface $webDirectory,
        FileOperationsInterface $fileOperations,
        ImageProcessorInterface $imageProcessor
    ) {
        $this->webDirectory = $webDirectory;
        $this->fileOperations = $fileOperations;
        $this->imageProcessor = $imageProcessor;
    }

    public function getImage(ImageFilenameInterface $filename): ImageFile
    {
        $destinationFileName = $this->composeDestinationFileName($filename);

        if (!$this->fileOperations->isFile($destinationFileName)) {
            throw new FileNotFoundException(sprintf('File "%s" does not exist', $destinationFileName));
        }

        return new ImageFile($destinationFileName);
    }

    public function putImage(ImageFilenameInterface $filename, Image $image): void
    {
        $destinationFileName = $this->composeDestinationFileName($filename);
        $this->imageProcessor->saveImageToFile($image, $destinationFileName);
    }

    public function deleteImagesByMask(string $fileNameMask): void
    {
        $destinationFileNameMask = $this->composeDestinationFileName($fileNameMask);
        $cachedFileNames = $this->fileOperations->findByMask($destinationFileNameMask);

        foreach ($cachedFileNames as $cachedFileName) {
            $this->fileOperations->deleteFile($cachedFileName);
        }
    }

    public function deleteDirectoryContents(string $directory): void
    {
        $destinationDirectoryName = $this->composeDestinationFileName(ltrim('/', $directory));
        $this->fileOperations->deleteDirectoryContents($destinationDirectoryName);
    }

    private function composeDestinationFileName(string $fileName): string
    {
        return $this->webDirectory . $fileName;
    }
}
