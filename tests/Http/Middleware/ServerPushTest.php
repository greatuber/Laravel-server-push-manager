<?php

namespace BabDev\ServerPushManager\Tests\Http\Middleware;

use BabDev\ServerPushManager\Http\Middleware\ServerPush;
use BabDev\ServerPushManager\PushManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;

final class ServerPushTest extends TestCase
{
    /**
     * @var PushManager
     */
    private $pushManager;

    /**
     * @var ServerPush
     */
    private $middleware;

    protected function setUp(): void
    {
        $this->pushManager = new PushManager();

        $this->middleware = new ServerPush($this->pushManager);
    }

    public function testNoLinkHeaderIsAddedIfThereAreNoPushedResources(): void
    {
        $next = function () {
            return new Response();
        };

        $request = new Request();

        /** @var Response $response */
        $response = $this->middleware->handle($request, $next);

        $this->assertFalse($response->headers->has('Link'));
    }

    public function testALinkHeaderIsAddedForPushedResources(): void
    {
        $next = function () {
            $this->pushManager->preload('/css/app.css');

            return new Response();
        };

        $request = new Request();

        /** @var Response $response */
        $response = $this->middleware->handle($request, $next);

        $this->assertTrue($response->headers->has('Link'));
    }
}
