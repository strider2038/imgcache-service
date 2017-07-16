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
 * Description of ErrorResponse
 *
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ErrorResponse extends MessageResponse
{
    /** @var string */
    private $message;
    
    public function __construct(int $httpCode = null, string $message = null)
    {
        parent::__construct($httpCode ?? self::HTTP_CODE_INTERNAL_SERVER_ERROR);
        $this->message = $message;
    }
}
