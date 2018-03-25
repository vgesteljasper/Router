<?php

use PHPUnit\Framework\TestCase;
use VanGestelJasper\Router\Router;

class RouterTest extends TestCase
{

  public $router;

  public function __construct()
  {
    $this->router = new Router;
  }

  public function getPrivateMethod($className, $methodName)
  {
    $reflector = new ReflectionClass($className);
    $method = $reflector->getMethod($methodName);
    $method->setAccessible(true);

    return $method;
  }

  public function getPrivateProperty($className, $propertyName)
  {
    $reflector = new ReflectionClass($className);
    $property = $reflector->getProperty($propertyName);
    $property->setAccessible(true);

    return $property;
  }

  public function testRouterDispatchMethodWorks()
  {
    $this->router->get('/test', 'TestController@test');
  }

  // public function testCanAddRoutes()
  // {
  //   // $this->router->post('/path', 'TestController@index');

  //   //$routes = $this->getPrivateProperty('Router', 'routes')->getValue($this->router);
  //   //$this->assertCount(1, $routes);
  // }

}
