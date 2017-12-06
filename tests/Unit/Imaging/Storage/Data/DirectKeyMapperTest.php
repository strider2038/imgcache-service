<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Storage\Data;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Storage\Data\DirectKeyMapper;

class DirectKeyMapperTest extends TestCase
{
    private const FILENAME = '/file.ext';

    public function testGetKey_GivenFilename_FilenameEqualsToReturnedFilenameKeyValue(): void
    {
        $mapper = new DirectKeyMapper();

        $filenameKey = $mapper->getKey(self::FILENAME);

        $this->assertEquals(self::FILENAME, $filenameKey->getValue());
    }
}