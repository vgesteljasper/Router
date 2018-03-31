<?php namespace VanGestelJasper\Router;

use VanGestelJasper\Router\Request\request;

class Route
{

  /**
   * @var string
   */
  public $handler;

  /**
   * @var array
   */
  public $middleware;

  /**
   * @var \VanGestelJasper\Router\Request\Request
   */
  public $request;

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
    $this->middleware = [];
  }

  /**
   * Trigger defined middleware for Route.
   * @return void
   */
  public function use(): void
  {
    $this->middleware = func_get_args();
  }

  /**
   * Use this method to get an empty mock route.
   * @param mixed<Closure|string> $handler
   * @param \VanGestelJasper\Router\Request\Request
   * @return \VanGestelJasper\Router\Route
   */
  public static function mock($handler, Request $request): Route
  {
    $mock = new self('', '', [], $handler);
    $mock->request = $request;
    unset($mock->temp);

    return $mock;
  }

}
