<?php

use Strider2038\ImgCache\Application;
use Strider2038\ImgCache\Core\{
    Component,
    RequestInterface,
    SecurityInterface
};
use PHPUnit\Framework\TestCase;

/**
 * Description of ApplicationTest
 *
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ApplicationTest extends TestCase 
{
    
    /**
     * @expectedException \Strider2038\ImgCache\Exception\ApplicationException
     */
    public function testIdIsRequired(): void
    {
        $app = new Application([]);
    }
    
    public function testIdIsAssigned(): void 
    {
        $app = new Application(['id' => 'test']);
        $this->assertEquals('test', $app->getId());
    }
    
    public function testCoreComponentsInjection(): void
    {
        $app = new Application([
            'id' => 'test',
            'components' => [
                'request' => function($app) {
                    return new class($app) extends Component implements RequestInterface {
                        public function getMethod(): string 
                        {
                            return 'requestGetMethodResult';
                        }
                        public function getHeader(string $key): ?string 
                        {
                            return 'requestGetHeaderResult';
                        }
                    };
                },
                'security' => function($app) {
                    return new class($app) extends Component implements SecurityInterface {
                        public function isAuthorized(): bool 
                        {
                            return true;
                        }
                    };
                }
            ],
        ]);
        $this->assertInstanceOf(RequestInterface::class, $app->request);
        $this->assertInstanceOf(SecurityInterface::class, $app->security);
        $this->assertEquals('requestGetMethodResult', $app->request->getMethod());
        $this->assertEquals('requestGetHeaderResult', $app->request->getHeader(''));
        $this->assertTrue($app->security->isAuthorized());
    }
    
}
