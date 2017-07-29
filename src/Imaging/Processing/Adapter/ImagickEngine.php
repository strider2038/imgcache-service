<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Processing\Adapter;
use Strider2038\ImgCache\Imaging\Processing\ProcessingEngineInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingImageInterface;
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImagickEngine implements ProcessingEngineInterface
{
    public function openFromFile(string $filename, SaveOptions $saveOptions): ProcessingImageInterface
    {
        $processor = new \Imagick($filename);

        return new ImagickImage($processor, $saveOptions);
    }

    public function openFromBlob(string $data, SaveOptions $saveOptions): ProcessingImageInterface
    {
        $processor = new \Imagick();
        $processor->readImageBlob($data);

        return new ImagickImage($processor, $saveOptions);
    }
}
