<?php namespace VanGestelJasper\Router;

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
   * @param string $handler
   */
  public function __construct(string $path, string $method, array $parameterKeys, string $handler)
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

}
