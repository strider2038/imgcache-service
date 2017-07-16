<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Application;
use Strider2038\ImgCache\Core\{
    Controller,
    RequestInterface,
    ResponseInterface,
    SecurityInterface
};
use Strider2038\ImgCache\Response\ForbiddenResponse;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ControllerTest extends TestCase 
{
    /** @var RequestInterface */
    private $request;
    
    protected function setUp()
    {
        parent::setUp();
        $this->request = \Phake::mock(RequestInterface::class);
    }
    
    /**
     * @expectedException \Strider2038\ImgCache\Exception\ApplicationException
     * @expectedExceptionMessage does not exists
     */
    public function testRunAction_ActionDoesNotExists_ExceptionThrown(): void
    {
        $controller = new class extends Controller {};
        
        $controller->runAction('test', $this->request);
    }
    
    public function testRunAction_ActionExistsNoSecurityControl_MethodExecuted(): void
    {
        $controller = new class extends Controller {
            public $success = false;
            public function actionTest()
            {
                $this->success = true;
                return new class implements ResponseInterface {
                    public function send(): void {}
                };
            }
        };
        
        $this->assertFalse($controller->success);
        $result = $controller->runAction('test', $this->request);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertTrue($controller->success);
    }

    public function testRunAction_ActionIsNotSafeAndNotAuthorized_ForbiddenResponseReturned(): void
    {
        $security = \Phake::mock(SecurityInterface::class);
        \Phake::when($security)->isAuthorized()->thenReturn(false);
        $controller = new class($security) extends Controller {
            public $success = false;
            public function actionTest()
            {
                $this->success = true;
                return new class implements ResponseInterface {
                    public function send(): void {}
                };
            }
        };
        
        $this->assertFalse($controller->success);
        $result = $controller->runAction('test', $this->request);

        $this->assertInstanceOf(ForbiddenResponse::class, $result);
        $this->assertFalse($controller->success);
    }
    
    public function testRunAction_ActionIsNotSafeAndIsAuthorized_MethodExecuted(): void
    {
        $security = \Phake::mock(SecurityInterface::class);
        \Phake::when($security)->isAuthorized()->thenReturn(true);
        $controller = new class($security) extends Controller {
            public $success = false;
            public function actionTest()
            {
                $this->success = true;
                return new class implements ResponseInterface {
                    public function send(): void {}
                };
            }
        };
        
        $this->assertFalse($controller->success);
        $result = $controller->runAction('test', $this->request);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertTrue($controller->success);
    }
}
