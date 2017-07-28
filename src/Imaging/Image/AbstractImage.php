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

use Strider2038\ImgCache\Imaging\Processing\SaveOptions;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
abstract class AbstractImage implements ImageInterface
{
    /** @var SaveOptions */
    protected $saveOptions;

    public function __construct(SaveOptions $saveOptions)
    {
        $this->saveOptions = $saveOptions;
    }

    public function getSaveOptions(): SaveOptions
    {
        return $this->saveOptions;
    }

    public function setSaveOptions(SaveOptions $saveOptions): void
    {
        $this->saveOptions = $saveOptions;
    }
}