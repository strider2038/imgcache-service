<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Processing;

use Strider2038\ImgCache\Imaging\Image\Image;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface ImageProcessorInterface
{
    public function process(Image $image, ProcessingConfiguration $configuration): Image;
    public function saveToFile(Image $image, string $filename): void;
}
