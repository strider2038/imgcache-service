<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Enum;

use MyCLabs\Enum\Enum;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class HttpHeaderEnum extends Enum
{
    public const AUTHORIZATION = 'Authorization';
    public const CONTENT_TYPE = 'Content-Type';
    public const CONTENT_LENGTH = 'Content-Length';
    public const ETAG = 'Etag';
    public const SHA256 = 'Sha256';
}
