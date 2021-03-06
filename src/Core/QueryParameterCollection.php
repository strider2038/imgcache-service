<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Core;

use Strider2038\ImgCache\Collection\AbstractClassCollection;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class QueryParameterCollection extends AbstractClassCollection
{
    protected function getElementClassName(): string
    {
        return QueryParameterInterface::class;
    }

    public function toArray(): array
    {
        $parameters = [];

        /** @var QueryParameterInterface $parameter */
        foreach (parent::toArray() as $parameter) {
            $parameters[$parameter->getName()] = $parameter->getValue();
        }

        return $parameters;
    }
}
