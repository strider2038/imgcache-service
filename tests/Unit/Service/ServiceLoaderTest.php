<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Strider2038\ImgCache\Configuration\Configuration;
use Strider2038\ImgCache\Configuration\ConfigurationLoaderInterface;
use Strider2038\ImgCache\Configuration\ConfigurationSetterInterface;
use Strider2038\ImgCache\Core\ErrorHandlerInterface;
use Strider2038\ImgCache\Service\ServiceLoader;
use Strider2038\ImgCache\Utility\RequestLoggerInterface;

class ServiceLoaderTest extends TestCase
{
    /** @var ErrorHandlerInterface */
    private $errorHandler;

    /** @var RequestLoggerInterface */
    private $requestLogger;

    /** @var ConfigurationLoaderInterface */
    private $configurationLoader;

    /** @var ConfigurationSetterInterface */
    private $configurationSetter;

    protected function setUp(): void
    {
        $this->errorHandler = \Phake::mock(ErrorHandlerInterface::class);
        $this->requestLogger = \Phake::mock(RequestLoggerInterface::class);
        $this->configurationLoader = \Phake::mock(ConfigurationLoaderInterface::class);
        $this->configurationSetter = \Phake::mock(ConfigurationSetterInterface::class);
    }

    /** @test */
    public function loadServices_givenContainer_servicesInitializedAndConfigurationLoadedAndSetToContainer(): void
    {
        $serviceLoader = $this->createServiceLoader();
        $container = \Phake::mock(ContainerInterface::class);
        $configuration = $this->givenConfigurationLoader_loadConfiguration_returnsConfiguration();

        $serviceLoader->loadServices($container);

        $this->assertErrorHandler_register_isCalledOnce();
        $this->assertRequestLogger_logClientRequest_isCalledOnce();
        $this->assertConfigurationLoader_loadConfiguration_isCalledOnce();
        $this->assertConfigurationSetter_setConfigurationToContainer_isCalledOnceWithConfigurationAndContainer($configuration, $container);
    }

    private function createServiceLoader(): ServiceLoader
    {
        return new ServiceLoader(
            $this->errorHandler,
            $this->requestLogger,
            $this->configurationLoader,
            $this->configurationSetter
        );
    }

    private function givenConfigurationLoader_loadConfiguration_returnsConfiguration(): mixed
    {
        $configuration = \Phake::mock(Configuration::class);
        \Phake::when($this->configurationLoader)->loadConfiguration()->thenReturn($configuration);
        return $configuration;
    }

    private function assertErrorHandler_register_isCalledOnce(): void
    {
        \Phake::verify($this->errorHandler, \Phake::times(1))->register();
    }

    private function assertRequestLogger_logClientRequest_isCalledOnce(): void
    {
        \Phake::verify($this->requestLogger, \Phake::times(1))->logClientRequest();
    }

    private function assertConfigurationLoader_loadConfiguration_isCalledOnce(): void
    {
        \Phake::verify($this->configurationLoader, \Phake::times(1))->loadConfiguration();
    }

    private function assertConfigurationSetter_setConfigurationToContainer_isCalledOnceWithConfigurationAndContainer(
        Configuration $configuration,
        ContainerInterface $container
    ): void {
        \Phake::verify($this->configurationSetter, \Phake::times(1))
            ->setConfigurationToContainer($configuration, $container);
    }
}
