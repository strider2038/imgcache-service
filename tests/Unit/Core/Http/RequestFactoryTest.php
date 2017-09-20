<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Core\Http;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\Http\Request;
use Strider2038\ImgCache\Core\Http\RequestFactory;
use Strider2038\ImgCache\Core\ReadOnlyResourceStream;
use Strider2038\ImgCache\Enum\HttpMethod;
use Strider2038\ImgCache\Enum\HttpProtocolVersion;

class RequestFactoryTest extends TestCase
{
    private const REQUEST_URI_VALUE = 'http://example.org';

    /** @test */
    public function createRequest_givenServerConfiguration_requestIsCreatedAndReturned(): void
    {
        $serverConfiguration = [
            'REQUEST_METHOD' => HttpMethod::GET,
            'REQUEST_URI' => self::REQUEST_URI_VALUE,
        ];
        $factory = new RequestFactory();

        $request = $factory->createRequest($serverConfiguration);

        $this->assertInstanceOf(Request::class, $request);
        $this->assertEquals(HttpMethod::GET, $request->getMethod());
        $this->assertEquals(self::REQUEST_URI_VALUE, $request->getUri());
        $this->assertInstanceOf(ReadOnlyResourceStream::class, $request->getBody());
        $this->assertEquals(HttpProtocolVersion::V1_1, $request->getProtocolVersion()->getValue());
    }

    /**
     * @test
     * @param null|string $givenServerProtocol
     * @param string $expectedServerProtocol
     * @dataProvider serverProtocolProvider
     */
    public function createRequest_givenServerConfigurationWithServerProtocol_requestWithValidServerProtocolIsReturned(
        ?string $givenServerProtocol,
        string $expectedServerProtocol
    ): void {
        $serverConfiguration = [
            'REQUEST_METHOD' => HttpMethod::GET,
            'REQUEST_URI' => self::REQUEST_URI_VALUE,
            'SERVER_PROTOCOL' => $givenServerProtocol
        ];
        $factory = new RequestFactory();

        $request = $factory->createRequest($serverConfiguration);

        $this->assertEquals($expectedServerProtocol, $request->getProtocolVersion()->getValue());
    }

    public function serverProtocolProvider(): array
    {
        return [
            [null, '1.1'],
            ['HTTP/1.0', '1.0'],
            ['HTTP/1.1', '1.1'],
        ];
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Unsupported http method
     */
    public function createRequest_givenInvalidHttpMethod_exceptionThrown(): void
    {
        $serverConfiguration = ['REQUEST_METHOD' => 'Unknown'];
        $factory = new RequestFactory();

        $factory->createRequest($serverConfiguration);
    }
}