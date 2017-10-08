<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Parsing\Yandex;

use Strider2038\ImgCache\Imaging\Parsing\Yandex\Map\ValueConfiguratorFactoryInterface;
use Strider2038\ImgCache\Imaging\Source\Yandex\YandexMapParametersInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class YandexMapParametersParser implements YandexMapParametersParserInterface
{
    /** @var ValueConfiguratorFactoryInterface */
    private $valueConfiguratorFactory;

    public function __construct(ValueConfiguratorFactoryInterface $valueConfiguratorFactory)
    {
        $this->valueConfiguratorFactory = $valueConfiguratorFactory;
    }

    public function parse(string $key): YandexMapParametersInterface
    {

    }
}
