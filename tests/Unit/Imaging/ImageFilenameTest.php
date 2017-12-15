<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\ImageFilename;
use Strider2038\ImgCache\Imaging\Validation\ModelValidator;
use Strider2038\ImgCache\Imaging\Validation\ModelValidatorInterface;

class ImageFilenameTest extends TestCase
{
    /** @var ModelValidatorInterface */
    private $validator;

    protected function setUp(): void
    {
        $this->validator = new ModelValidator();
    }

    /**
     * @test
     * @dataProvider filenameProvider
     * @param string $filename
     * @param int $violationsCount
     */
    public function validate_givenImageFilename_violationsReturned(string $filename, int $violationsCount): void
    {
        $imageFilename = new ImageFilename($filename);

        $violations = $this->validator->validateModel($imageFilename);

        $this->assertCount($violationsCount, $violations);
    }

    public function filenameProvider(): array
    {
        return [
            ['', 1],
            ['/', 2],
            ['*image.jpg', 1],
            ['/image.jpg', 1],
            ['image.dat', 1],
            ['image.jpeg', 0],
            ['Image_Jpeg-1=0+.jpg', 0],
            ['dir/name/file.png', 0],
            ['dir/name/file..png', 1],
            ['dir//name//file.png', 1],
        ];
    }
}
