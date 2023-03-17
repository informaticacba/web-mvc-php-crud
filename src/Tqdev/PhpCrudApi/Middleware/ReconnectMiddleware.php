<?php

namespace Tqdev\PhpCrudApi\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tqdev\PhpCrudApi\Column\ReflectionService;
use Tqdev\PhpCrudApi\Config;
use Tqdev\PhpCrudApi\Controller\Responder;
use Tqdev\PhpCrudApi\Database\GenericDB;
use Tqdev\PhpCrudApi\Middleware\Base\Middleware;
use Tqdev\PhpCrudApi\Middleware\Router\Router;

class ReconnectMiddleware extends Middleware
{
    private $reflection;
    private $db;

    public function __construct(Router $router, Responder $responder, Config $config, string $middleware, ReflectionService $reflection, GenericDB $db)
    {
        parent::__construct($router, $responder, $config, $middleware);
        $this->reflection = $reflection;
        $this->db = $db;
    }

    private function getDriver(): string
    {
        $driverHandler = $this->getProperty('driverHandler', '');
        if ($driverHandler) {
            return call_user_func($driverHandler);
        }
        return '';
    }

    private function getAddress(): string
    {
        $addressHandler = $this->getProperty('addressHandler', '');
        if ($addressHandler) {
            return call_user_func($addressHandler);
        }
        return '';
    }

    private function getPort(): int
    {
        $portHandler = $this->getProperty('portHandler', '');
        if ($portHandler) {
            return call_user_func($portHandler);
        }
        return 0;
    }

    private function getDatabase(): string
    {
        $databaseHandler = $this->getProperty('databaseHandler', '');
        if ($databaseHandler) {
            return call_user_func($databaseHandler);
        }
        return '';
    }

    private function getCommand(): string
    {
        $commandHandler = $this->getProperty('commandHandler', '');
        if ($commandHandler) {
            return call_user_func($commandHandler);
        }
        return '';
    }

    private function getTables(): array
    {
        $tablesHandler = $this->getProperty('tablesHandler', '');
        if ($tablesHandler) {
            return call_user_func($tablesHandler);
        }
        return [];
    }

    private function getMapping(): array
    {
        $mappingHandler = $this->getProperty('mappingHandler', '');
        if ($mappingHandler) {
            return call_user_func($mappingHandler);
        }
        return [];
    }

    private function getUsername(): string
    {
        $usernameHandler = $this->getProperty('usernameHandler', '');
        if ($usernameHandler) {
            return call_user_func($usernameHandler);
        }
        return '';
    }

    private function getPassword(): string
    {
        $passwordHandler = $this->getProperty('passwordHandler', '');
        if ($passwordHandler) {
            return call_user_func($passwordHandler);
        }
        return '';
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface
    {
        $driver = $this->getDriver();
        $address = $this->getAddress();
        $port = $this->getPort();
        $database = $this->getDatabase();
        $command = $this->getCommand();
        $tables = $this->getTables();
        $mapping = $this->getMapping();
        $username = $this->getUsername();
        $password = $this->getPassword();
        if ($driver || $address || $port || $database || $command || $tables || $mapping || $username || $password) {
            $this->db->reconstruct($driver, $address, $port, $database, $command, $tables, $mapping, $username, $password);
        }
        return $next->handle($request);
    }
}
