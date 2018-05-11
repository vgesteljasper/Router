<?php namespace vjee\Router;

use vjee\Router\Request\Request;

class Router
{

  /**
   * @var vjee\Router\Route[]
   */
  public $routes;

  /**
   * @var vjee\Router\Route|null The matched route.
   */
  public $route;

  /**
   * @var mixed<Closure|string> fallback route handler.
   */
  public $fallbackHandler;

  /**
   * @var array|null An array of wildcards from the matching route path.
   */
  public $matches;

  /**
   * @var vjee\Router\Request\Request
   */
  public $request;

  /**
   * @var string The prefix for controllers
   */
  private $controllerNamespace;

  /**
   * @var string The prefix for middleware
   */
  private $middlewareNamespace;

  /**
   * Router constructor.
   * @param array $settings
   */
  public function __construct(array $settings)
  {
    $this->routes = [];
    $this->request = new Request;

    if (array_key_exists('controllerNamespace', $settings)) {
      $this->controllerNamespace = $settings['controllerNamespace'];
    }

    if (array_key_exists('middlewareNamespace', $settings)) {
      $this->middlewareNamespace = $settings['middlewareNamespace'];
    }
  }

  /**
   * Dispatch the router.
   * @return vjee\Router\Route|null
   */
  public function dispatch(): ?Route
  {
    $this->findFirstMatchingRoute();

    // return null if there was no matching route
    if (!$this->route) {
      return null;
    }

    $this->satisfyRequestWithRouteParameters();

    $this->route->request = $this->request;

    return $this->route;
  }

  /**
   * Call the route handler if it is a Closure or a string of type "controller @ method"
   * @return bool True if a route got handled, false if not.
   */
  public function run(): bool
  {
    $route = null;
    
    // if there is no matching route and no fallback route
    if (!$this->route) {
      if (!$this->fallbackHandler) {
        return false;
      }

      // generate a mock route to bind to the fallback route handler
      // because there is not actually a route in this case
      $route = Route::mock($this->fallbackHandler, $this->request);
    } else {
      $route = $this->route;
    }

    foreach($route->middleware as $middleware) {
      list($handled, $response) = $this->callUserHandler(
          $middleware.'@run', $this->middlewareNamespace, [$route] );

      // if the response of the middleware was not true, return false
      if ($response !== true) {
        return false;
      }
    }

    // call the user handler
    list($handled, $response) = $this->callUserHandler(
      $route->handler, $this->controllerNamespace, [$route] );

    return $handled;
  }

  /**
   * Call a handler specified by the user.
   * @internal
   * @param mixed $handler
   * @param string $prefix | The prefix for classes
   * @param array $parameters
   * @return array | Containing the status
   */
  private function callUserHandler($handler, string $prefix = null, $parameters = []): array
  {
    // the response of the handler
    $response = null;

    // call the handler is it is a closure
    if ($handler instanceof \Closure) {
      $response = ($handler)(...$parameters);
      return [true, $response];
    }

    // instantiate the handler is it is a class and call the defined method
    if (is_string($handler) && strpos($handler, '@') !== false) {
      $parts = explode('@', $handler);
      list($class, $method) = $parts;

      // prefix the class
      // only when it doesn't start with a '*' character.
      if ($prefix && strpos($class, '*') !== 0) {
        $class = $prefix.'\\'.$class;
      }

      $response = (new $class)->$method(...$parameters);
      return [true, $response];
    }

    return [false, $response];
  }

  /**
   * PHP magic function for catching Router methods like "get", "post", etc.
   * @internal
   * @param string $method
   * @param array $args
   * @return vjee\Router\Route|null
   */
  public function __call(string $method, array $args): ?Route
  {
    if ($method === "fallback") {
      if (count($args) >= 1) {
        $this->fallbackHandler = $args[0];
      }
    }

    // return if the method is not a valid route method
    $validRouteMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];
    if (!in_array($method, $validRouteMethods, true)) {
      return null;
    }

    if (count($args) < 2) {
      return null;
    }

    // get the variables from the args array
    list($path, $handler) = $args;

    // parse the route and add to routes array
    $route = $this->parseRoute($method, $path, $handler);

    return $route;
  }

  /**
   * Parse the route.
   * @internal
   * @param string $method
   * @param string $path
   * @param string $handler
   * @return vjee\Router\Route
   */
  public function parseRoute($method, $path, $handler): Route
  {
    // match all the wildcard "{...}" sections in the path
    preg_match_all('/\{[^\/]+\}/', $path, $matches);

    // make sure the regex path is escaped with backslashes
    $path = preg_quote($path, '/');

    // replace all the wildcards with regex wildcards
    // so we can match our request route later
    // (some strange escaping going on because of the preg_quote)
    $path = preg_replace('/\\\{[^\/]+?\\\}/', '([^\/]+)', $path);

    // make a regex from the path
    $path = '/^' . $path . '$/';

    // transform method to uppercase
    $method = strtoupper($method);

    // all the matches we counted above have the {} brackets around them
    // we will remove them here so we have the pure names/keys of the wildcards
    $parameterKeys = [];
    forEach ($matches[0] as $match) {
      $parameterKeys[] = preg_replace('/(\{|\})/', '', $match);
    }

    // add a new Route to the routes array
    $route = new Route($path, $method, $parameterKeys, $handler);
    $this->routes[] = $route;

    return $route;
  }

  /**
   * Find the first matching Route.
   * Add the $route and it's wildcard matches to $this.
   * @internal
   * @return void
   */
  public function findFirstMatchingRoute(): void
  {
    // loop over the router routes array
    foreach ($this->routes as $route) {
      // see if the route is a match
      // this method returns the array of matches or null
      $matches = $this->routeMatches($route);
      if ($matches !== null) {
        $this->route = $route;
        $this->matches = $matches;
        break;
      }
    }
  }

  /**
   * Fill in the wildcard array so we know the names of the variables.
   * extracted from the Route path
   * @internal
   * @return void
   */
  public function satisfyRequestWithRouteParameters(): void
  {
    // the parameters on the route array
    $parameterKeys = $this->route->temp['parameterKeys'];
    unset($this->route->temp);

    // generate the wildcard matches
    // eg: [ 'slug_id' => 'marv' ]
    $parameters = [];
    for ($i = 0; $i < count($this->matches); $i++) {
      $parameters[ $parameterKeys[ $i ] ] = $this->matches[ $i ];
    }

    // replace the parameters on the $route array with the updated ones
    foreach($parameters as $key => $value) {
      $this->request->payload->set($key, $value);
    }
  }

  /**
   * Check weather the Route matches.
   * If it does, return the matches from the Route path wildcards.
   * Else return null.
   * @internal
   * @param vjee\Router\Route $route
   * @return array|null
   */
  public function routeMatches(Route $route): ?array
  {
    $path = $route->temp['path'];
    $method = $route->temp['method'];

    // if the current path doesn't match the route path
    if (!preg_match($path, $this->request->path, $matches))
      return null;

    // if the current method doesn't match the route method
    if ($this->request->method !== $method)
      return null;

    // return true (the route matches without the first one which is the full path)
    return array_slice($matches, 1);
  }

}
