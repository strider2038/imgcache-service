<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Extraction;

use Strider2038\ImgCache\Imaging\Processing\ProcessingEngineInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingImageInterface;
use Strider2038\ImgCache\Imaging\Transformation\SaveOptions;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface ExtractedImageInterface
{
    public function setSaveOptions(SaveOptions $saveOptions): void;
    public function saveTo(string $filename): void;
    public function open(ProcessingEngineInterface $engine): ProcessingImageInterface;
}