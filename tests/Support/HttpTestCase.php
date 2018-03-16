<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Support;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class HttpTestCase extends TestCase
{
    /** @var \GuzzleHttp\Client */
    protected $client;

    /** @var string */
    protected $host;
    
    protected function setUp(): void
    {
        parent::setUp();

        $this->host = getenv('ACCEPTANCE_HOST');

        $this->client = new Client([
            'base_uri' => $this->host,
            'timeout' => 5,
            'allow_redirects' => false,
            'http_errors' => false,
        ]);
    }

    protected function sendGET(string $uri): ResponseInterface
    {
        return $this->client->request('GET', $uri);
    }
}