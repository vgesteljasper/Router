<?php namespace vjee\Router;

use vjee\Router\Request\request;

class Route
{

  /**
   * @var string
   */
  public $handler;

  // /**
  //  * @var array
  //  */
  // public $middleware;

  /**
   * @var string
   */
  public $name;

  /**
   * @var vjee\Router\Request\Request
   */
  public $request;

  /**
   * @var array
   */
  public $middleware;

  /**
   * Route constructor.
   * @param string $path
   * @param string $method
   * @param array $parameterKeys
   * @param mixed<Closure|string> $handler
   */
  public function __construct(string $path, string $method, array $parameterKeys, $handler)
  {
    $this->temp = [
      'path' => $path,
      'method' => $method,
      'parameterKeys' => $parameterKeys,
    ];
    $this->handler = $handler;
    $this->name = null;
    $this->middleware = [];
  }

  /**
   * Trigger defined middleware for Route.
   * @param string $middleware
   * @return vjee\Router\Route
   */
  public function use(string $middleware): Route
  {
    $this->middleware[] = $middleware;
    return $this;
  }

  /**
   * Method to set the name of the route.
   * @param string $name
   * @return vjee\Router\Route
   */
  public function name($name): Route
  {
    $this->name = $name;
    return $this;
  }

  /**
   * Use this method to get an empty mock route.
   * @param mixed<Closure|string> $handler
   * @param vjee\Router\Request\Request
   * @return vjee\Router\Route
   */
  public static function mock($handler, Request $request): Route
  {
    $mock = new self('', '', [], $handler);
    $mock->request = $request;
    unset($mock->temp);

    return $mock;
  }

}
