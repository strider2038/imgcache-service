<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Response;


/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class CreatedResponse extends MessageResponse
{
    public function __construct(string $message = null)
    {
        parent::__construct($httpCode ?? self::HTTP_CODE_CREATED, $message);
    }
}