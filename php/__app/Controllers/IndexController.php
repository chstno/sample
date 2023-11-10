<?php


namespace Controllers;


use Core\Controller\Controller;
use Core\View\View;
use Models\User;
use Repositories\PostRepository;
use Repositories\UserRepository;
use Services\UserService;
use Support\UserServiceInterface;

class IndexController extends Controller
{

    public function index(): void
    {
        $render = View::render('layouts/main', ['content' => '----- testContent -----']);
        response($render)->send();
    }

    public function test(int $userId): void
    {
        /**
         * @var UserService $userService
         */
        $userService = instance(UserServiceInterface::class);
        var_dump($userService->test($userId));
    }

    public function otherTest(int $someId, string $anotherVal): void
    {
        var_dump($someId);
        var_dump($anotherVal);
    }

    public function error404(): void
    {
        response(404, 404)->send();
    }
}