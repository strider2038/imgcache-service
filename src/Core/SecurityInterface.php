<?php

namespace Strider2038\ImgCache\Core;

/**
 *
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface SecurityInterface 
{
    public function isAuthorized(): bool;
}
