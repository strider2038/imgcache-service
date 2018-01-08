<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Parsing\Processing;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Image\ImageParameters;
use Strider2038\ImgCache\Imaging\Image\ImageParametersFactoryInterface;
use Strider2038\ImgCache\Imaging\Parsing\ImageParametersConfiguratorInterface;
use Strider2038\ImgCache\Imaging\Parsing\Processing\ThumbnailProcessingConfigurationParser;
use Strider2038\ImgCache\Imaging\Processing\ProcessingConfiguration;
use Strider2038\ImgCache\Imaging\Transformation\TransformationCreatorInterface;
use Strider2038\ImgCache\Imaging\Transformation\TransformationInterface;

class ThumbnailProcessingConfigurationParserTest extends TestCase
{
    /** @var TransformationCreatorInterface */
    private $transformationsCreator;

    /** @var ImageParametersFactoryInterface */
    private $imageParametersFactory;

    /** @var ImageParametersConfiguratorInterface */
    private $imageParametersConfigurator;

    protected function setUp(): void
    {
        $this->transformationsCreator = \Phake::mock(TransformationCreatorInterface::class);
        $this->imageParametersFactory = \Phake::mock(ImageParametersFactoryInterface::class);
        $this->imageParametersConfigurator = \Phake::mock(ImageParametersConfiguratorInterface::class);
    }

    /**
     * @test
     * @param string $configuration
     * @param int $count
     * @dataProvider configurationsProvider
     */
    public function parse_givenConfigurationWithTransformations_countOfTransformationsReturned(
        string $configuration,
        int $count
    ): void {
        $parser = $this->createThumbnailProcessingConfigurationParser();
        $this->givenTransformationCreator_createTransformation_returnsTransformation();
        $defaultParameters = $this->givenImageParametersFactory_createImageParameters_returnsImageParameters();

        $parsedConfiguration = $parser->parseConfiguration($configuration);

        $this->assertTransformationsCount($count, $parsedConfiguration);
        $this->assertTransformationCreator_createTransformation_isCalledTimes($count);
        $this->assertImageParametersConfigurator_updateParametersByConfiguration_isCalledTimes(0);
        $this->verifyProcessingConfiguration($parsedConfiguration, $defaultParameters);
    }

    /**
     * @test
     * @param string $configuration
     * @param int $count
     * @dataProvider configurationsProvider
     */
    public function getRequestConfiguration_givenConfigurationWithParameters_countOfParametersConfiguratorConfigureVerified(
        string $configuration,
        int $count
    ): void {
        $parser = $this->createThumbnailProcessingConfigurationParser();
        $this->givenTransformationCreator_createTransformation_returnsNull();
        $defaultParameters = $this->givenImageParametersFactory_createImageParameters_returnsImageParameters();

        $parsedConfiguration = $parser->parseConfiguration($configuration);

        $this->assertTransformationsCount(0, $parsedConfiguration);
        $this->assertTransformationCreator_createTransformation_isCalledTimes($count);
        $this->assertImageParametersConfigurator_updateParametersByConfiguration_isCalledTimes($count);
        $this->verifyProcessingConfiguration($parsedConfiguration, $defaultParameters);
    }

    public function configurationsProvider(): array
    {
        return [
            ['', 0],
            ['q95', 1],
            ['sz85_q7', 2],
        ];
    }

    private function createThumbnailProcessingConfigurationParser(): ThumbnailProcessingConfigurationParser
    {
        $parser = new ThumbnailProcessingConfigurationParser(
            $this->transformationsCreator,
            $this->imageParametersFactory,
            $this->imageParametersConfigurator
        );

        return $parser;
    }

    private function givenTransformationCreator_createTransformation_returnsTransformation(): void
    {
        $transformation = \Phake::mock(TransformationInterface::class);

        \Phake::when($this->transformationsCreator)
            ->createTransformation(\Phake::anyParameters())
            ->thenReturn($transformation);
    }

    private function verifyProcessingConfiguration(
        ProcessingConfiguration $configuration,
        ImageParameters $imageParameters
    ): void {
        $this->assertImageParametersFactory_createImageParameters_isCalledOnce();
        $this->assertSame($imageParameters, $configuration->getImageParameters());
    }

    private function assertTransformationsCount(int $count, ProcessingConfiguration $configuration): void
    {
        $transformations = $configuration->getTransformations();
        $this->assertEquals($count, $transformations->count());
    }

    private function assertTransformationCreator_createTransformation_isCalledTimes(int $times): void
    {
        \Phake::verify($this->transformationsCreator, \Phake::times($times))
            ->createTransformation(\Phake::anyParameters());
    }

    private function assertImageParametersConfigurator_updateParametersByConfiguration_isCalledTimes(int $times): void
    {
        \Phake::verify($this->imageParametersConfigurator, \Phake::times($times))
            ->updateParametersByConfiguration(\Phake::anyParameters());
    }

    private function givenTransformationCreator_createTransformation_returnsNull(): void
    {
        \Phake::when($this->transformationsCreator)
            ->createTransformation(\Phake::anyParameters())
            ->thenReturn(null);
    }

    private function givenImageParametersFactory_createImageParameters_returnsImageParameters(): ImageParameters
    {
        $imageParameters = \Phake::mock(ImageParameters::class);
        \Phake::when($this->imageParametersFactory)->createImageParameters()->thenReturn($imageParameters);

        return $imageParameters;
    }

    private function assertImageParametersFactory_createImageParameters_isCalledOnce(): void
    {
        \Phake::verify($this->imageParametersFactory, \Phake::times(1))->createImageParameters();
    }
}
