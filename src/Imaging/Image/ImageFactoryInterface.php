<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Image;

use Strider2038\ImgCache\Core\Streaming\StreamInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface ImageFactoryInterface
{
    public function createImage(StreamInterface $data, ImageParameters $parameters): Image;
    public function createImageFromFile(string $filename): Image;
    public function createImageFromData(string $data): Image;
    public function createImageFromStream(StreamInterface $stream): Image;
}
