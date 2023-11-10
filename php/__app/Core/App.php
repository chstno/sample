<?php


namespace Core;


use Core\Database\DBConnection;
use Core\Database\DBConnectionDriverInterface;
use Core\Database\PDOConnectionDriver;
use Core\Database\Query\QueryBuilder;
use Core\Database\Query\SQLQueryBuilder;
use Core\Database\Query\SQLQueryTemplateEngine;
use Core\Service\Container;
use Core\Support\ContainerInterface;
use Core\Support\RequestInterface;
use Core\Support\ResponseInterface;
use Databases\MysqlConnection;
use Repositories\PostRepository;
use Repositories\UserRepository;
use Services\UserService;
use Support\PostRepositoryInterface;
use Support\UserRepositoryInterface;
use Support\UserServiceInterface;

/**
 * Class App
 * @property string rootPath;
 * @property string viewsPath directory with layouts/templates;
 * @property string assetsPath path for access to asserts;
 * @property string $assetsUrl url for access to asserts;
 * @property array  dbConf config for connections with specified drivers;
 * @package Core
 */

final class App
{

    private ContainerInterface  $container;
    private static App          $app;

    private array               $attributes = [];


    private function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->init();

        self::$app = &$this;
    }

    public static function __onAutoload(): void
    {
        if (!isset(self::$app))
            new App(Container::getInstance());
    }

    public static function app() : App
    {
        return self::$app;
    }

    public function __get($name)
    {
        return $this->attributes[$name] ?? null;
    }

    public function __set($name, $value)
    {
        return $this->attributes[$name] = $value;
    }

    private function init(): void
    {
        $this->dbConf = [
            MysqlConnection::class => [
                   PDOConnectionDriver::class =>
                       [
                        'ext' => 'mysql',
                        'host' => 'db',
                        'user' => 'root',
                        'pass' => 'root',
                        'db' => 'test',
                        'port' => 3306
                       ]
               ],
        ];

        $this->rootPath = $_SERVER['DOCUMENT_ROOT'] ?? dirname(__DIR__);
        $this->viewsPath = $this->rootPath . '/Views/';
        $this->assetsPath = $this->rootPath . '/_public/';
        $this->assetsUrl = ''; // in case of configuration changes

        $this->container->set(DBConnection::class, MysqlConnection::class);
        $this->container->set(DBConnectionDriverInterface::class, PDOConnectionDriver::class);
        $this->container->set(RequestInterface::class, Request::class);
        $this->container->set(ResponseInterface::class, Response::class);
        /*$this->container->set(ErrorLoggerInterface::class, ErrorLogger::class);*/

        $this->container->set(SQLQueryTemplateEngine::class, function () {
           return $this->container->resolve(SQLQueryTemplateEngine::class);
        });

        $this->container->set(QueryBuilder::class, function () {
           return $this->container->resolve(SQLQueryBuilder::class); // new builder
        });

        $this->container->set(UserServiceInterface::class, UserService::class);
        $this->container->set(UserRepositoryInterface::class, UserRepository::class);
        $this->container->set(PostRepositoryInterface::class, PostRepository::class);

    }

    public function instance(string $class, ...$args)
    {
        return $this->container->get($class, ...$args);
    }
}