<?php

namespace Tests\Unit;

use App\Http\Middleware\AdminAuth;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AdminAuthMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private function getAdminUser(): User
    {
        return User::first() ?? User::create([
            'name' => 'Admin',
            'email' => 'admin@lichtmoment.de',
            'password' => bcrypt('wasd1234'),
        ]);
    }

    public function test_middleware_allows_session_based_auth(): void
    {
        $user = $this->getAdminUser();

        $request = Request::create('/admin', 'GET');
        $request->setLaravelSession($this->app['session']->driver());
        $request->session()->put('admin_id', $user->id);

        $middleware = new AdminAuth();

        $response = $middleware->handle($request, function ($req) {
            return response('OK', 200);
        });

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getContent());
    }

    public function test_middleware_redirects_guest(): void
    {
        $request = Request::create('/admin', 'GET');
        $request->setLaravelSession($this->app['session']->driver());

        $middleware = new AdminAuth();

        $response = $middleware->handle($request, function ($req) {
            return response('OK', 200);
        });

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/admin/login', $response->headers->get('Location'));
    }

    public function test_middleware_handles_no_session(): void
    {
        $request = Request::create('/admin', 'GET');

        $middleware = new AdminAuth();

        $response = $middleware->handle($request, function ($req) {
            return response('OK', 200);
        });

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function test_middleware_allows_existing_admin_session(): void
    {
        $user = $this->getAdminUser();

        $request = Request::create('/admin/dashboard', 'GET');
        $request->setLaravelSession($this->app['session']->driver());
        $request->session()->put('admin_id', $user->id);

        $middleware = new AdminAuth();

        $response = $middleware->handle($request, function ($req) {
            return response('Dashboard OK', 200);
        });

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Dashboard OK', $response->getContent());
    }

    public function test_middleware_redirects_with_no_admin_id_in_session(): void
    {
        $request = Request::create('/admin', 'GET');
        $request->setLaravelSession($this->app['session']->driver());
        // Session exists but no admin_id

        $middleware = new AdminAuth();

        $response = $middleware->handle($request, function ($req) {
            return response('OK', 200);
        });

        $this->assertEquals(302, $response->getStatusCode());
    }
}
