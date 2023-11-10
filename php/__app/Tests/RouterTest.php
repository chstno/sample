<?php


namespace Tests;


use Core\Exception\RouteNotFoundException;
use Core\Request;
use Core\Routing\Router;
use Core\Support\RequestInterface;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    protected Router $router;

    /**
     * @var object $fakeController - from anonymous class
     */
    protected object $fakeController;

    public function setUp(): void
    {
        parent::setUp();
        $this->router = Router::getInstance();
        $this->router::cleanRoutes();

        // mock is useless (in that situation), cause we don't have strict need to use controllers
        // (and them with no methods by default)

        $this->fakeController = new class {

            public function action(): string { return 'action'; }

            public function actionGet(): string { return 'actionGet'; }

            public function actionPost(): string { return 'actionPost'; }

        };

        $this->router::any('/action', [$this->fakeController, 'action']);
        $this->router::get('/actionGet', [$this->fakeController, 'actionGet']);
        $this->router::post('/actionPost', [$this->fakeController, 'actionPost']);
    }

    /**
     * @dataProvider routeFoundCases
     * @throws RouteNotFoundException
     */
    public function test_route_is_found(RequestInterface $request, $expectedReturn)
    {
        $handleReturn = $this->router::handle($request);
        $this->assertSame($handleReturn, $expectedReturn);
    }


    /**
     * @dataProvider routeNotFoundCases
     */
    public function test_route_is_not_found(RequestInterface $request)
    {
        self::expectException(RouteNotFoundException::class);
        $this->router::handle($request);
    }

    public static function routeNotFoundCases(): array
    {
        return [
            [new Request('/notFoundGet', 'post')],
            [new Request('/notFoundPost', 'get')],
            [new Request('/notFoundPatch', 'patch')],
            [new Request('/notFoundPut', 'put')]
        ];
    }

    public static function routeFoundCases(): array
    {

        return [
            [new Request('/action', 'patch'), 'action'],
            [new Request('/action', 'post'), 'action'],
            [new Request('/action', 'get'), 'action'],
            [new Request('/actionGet', 'get'), 'actionGet'],
            [new Request('/actionPost', 'post'), 'actionPost'],
        ];
    }
}