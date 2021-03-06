<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Naming;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Naming\ImageFilename;
use Strider2038\ImgCache\Utility\EntityValidator;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;
use Strider2038\ImgCache\Utility\MetadataReader;
use Strider2038\ImgCache\Utility\Validation\CustomConstraintValidatorFactory;
use Strider2038\ImgCache\Utility\ViolationFormatter;

class ImageFilenameTest extends TestCase
{
    private const VALUE = 'value';
    private const IMAGE_FILENAME_ID = 'image filename';

    /** @var EntityValidatorInterface */
    private $validator;

    protected function setUp(): void
    {
        $this->validator = new EntityValidator(
            new CustomConstraintValidatorFactory(
                new MetadataReader()
            ),
            new ViolationFormatter()
        );
    }

    /** @test */
    public function getId_emptyParameters_idReturned(): void
    {
        $imageFilename = new ImageFilename(self::VALUE);

        $id = $imageFilename->getId();

        $this->assertEquals(self::IMAGE_FILENAME_ID, $id);
    }

    /** @test */
    public function toString_givenImageFilename_valueReturned(): void
    {
        $imageFilename = new ImageFilename(self::VALUE);

        $stringValue = (string) $imageFilename;

        $this->assertEquals(self::VALUE, $stringValue);
    }

    /**
     * @test
     * @dataProvider valueProvider
     * @param string $value
     * @param int $violationsCount
     */
    public function validate_givenImageFilename_violationsReturned(string $value, int $violationsCount): void
    {
        $imageFilename = new ImageFilename($value);

        $violations = $this->validator->validate($imageFilename);

        $this->assertCount($violationsCount, $violations);
    }

    public function valueProvider(): array
    {
        return [
            ['', 1],
            ['/', 2],
            ['*image.jpg', 1],
            ['/image.jpg', 1],
            ['image.dat', 1],
            ['image.jpeg', 0],
            ['Image_Jpeg-1=0,1+.jpg', 0],
            ['dir/name/file.png', 0],
            ['dir/name/file..png', 1],
            ['dir//name//file.png', 1],
        ];
    }
}
