<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Storage\Driver\WebDAV;

use Strider2038\ImgCache\Core\Streaming\StreamInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface ResourceManipulatorInterface
{
    public function getResource(string $resourceUri): StreamInterface;
    public function putResource(string $resourceUri, StreamInterface $contents): void;
    public function deleteResource(string $resourceUri): void;
    public function createDirectory(string $directoryUri): void;
}
