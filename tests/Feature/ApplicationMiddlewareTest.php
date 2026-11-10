<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckAppLifecycle;
use App\Http\Middleware\CheckLocalNetwork;
use Carbon\Carbon;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class ApplicationMiddlewareTest extends TestCase
{
    private function requestForIp(string $ip): \Illuminate\Http\Request
    {
        return \Illuminate\Http\Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => $ip]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_local_network_middleware_accepts_private_and_configured_cidrs_only(): void
    {
        config(['app.local_networks' => [
            '127.0.0.0/8',
            '::1/128',
            '10.0.0.0/8',
            '172.16.0.0/12',
            '192.168.0.0/16',
            '169.254.0.0/16',
            'fc00::/7',
            'fe80::/10',
            '203.0.113.8/32',
        ]]);
        $middleware = new CheckLocalNetwork();
        $next = fn () => new Response('allowed');

        foreach (['192.168.4.10', '172.31.5.2', '10.12.0.3', 'fd12::1', '203.0.113.8'] as $ip) {
            $response = $middleware->handle(
                $this->requestForIp($ip),
                $next
            );

            $this->assertSame('allowed', $response->getContent());
        }

        try {
            $middleware->handle($this->requestForIp('8.8.8.8'), $next);
            $this->fail('Une adresse réseau inconnue doit être refusée.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }
    }

    public function test_lifecycle_uses_configured_start_date_and_five_year_duration(): void
    {
        config([
            'app.installation_date' => '2020-11-10',
            'app.lifecycle_years' => 5,
        ]);
        $middleware = new CheckAppLifecycle();
        $next = fn () => new Response('allowed');

        Carbon::setTestNow('2025-11-10 23:59:59');
        $response = $middleware->handle($this->requestForIp('127.0.0.1'), $next);
        $this->assertSame('allowed', $response->getContent());

        Carbon::setTestNow('2025-11-11 00:00:00');
        $response = $middleware->handle($this->requestForIp('127.0.0.1'), $next);

        $this->assertSame(route('maintenance.licence'), $response->headers->get('Location'));
    }

    public function test_lifecycle_does_not_silently_reset_when_installation_date_is_missing(): void
    {
        config([
            'app.installation_date' => null,
            'app.lifecycle_years' => 5,
        ]);

        $this->expectException(InvalidArgumentException::class);

        (new CheckAppLifecycle())->handle(
            $this->requestForIp('127.0.0.1'),
            fn () => new Response('allowed')
        );
    }
}
