# VanGestelJasper\Router

Very simple URL router for just about anything.

It will match the current request against a set of predefined routes.
If there is a match, it will return a `VanGestelJasper\Router\Route` instance.
If it doesn't match it will return null.

The `Route` instance contains all the data from the request including:

- url
- path
- method
- headers
- query
- payload

As well as the names of the controller and possible middleware you define for that route.
This way you can call the required actions based on the matched route.

**_Note:_** This little library is still in a very early stage.

## Usage

example:
```php
use \VanGestelJasper\Router\Router;

// url is localhost:5000/projects/test?page=1

$router = new Router;

$router->get('/', 'PageController@index');
$router->get('/projects/{slug}', 'ProjectController@show')->use('ProjectMiddleware');
$router->post('/uploads', 'UploadController@create')->use('AuthMiddleware');

$route = $router->dispatch();

print_r($route);
```

Result:
```
VanGestelJasper\Router\Route Object (
    [handler] => ProjectController@show
    [middleware] => Array (
        [0] => ProjectMiddleware
    )
    [request] => VanGestelJasper\Router\Request Object (
        [headers] => Array (
            [cache-control] => no-cache
            [Accept] => */*
            [Host] => localhost:5000
            [accept-encoding] => gzip, deflate
            [Connection] => keep-alive
        )
        [url] => https://localhost:5000/projects/jasper
        [path] => /projects/test
        [method] => GET
        [query] => VanGestelJasper\Router\Query Object (
            [page] => 1
        )
        [payload] => VanGestelJasper\Router\Payload Object (
            [slug] => test
        )
    )
)
```

If the route doesn't match, the return value of `VanGestelJasper\Router\Router->dispatch()` will be null;

### Request data

The request data will be read as JSON. If it succeeds, you will be able to access on the `Route` -> `Request` -> `Payload` object.

See the result example above.

**_Note:_** The wildcards defined in routes between `{` and `}` will be accessable on the `Route` -> `Request` -> `Payload` object as well.

example:
```php
$router = new VanGestelJasper\Router\Router;
$route->post('/projects/{slug}', 'ProjectController@create');
$route = $router->dispatch();

print_r($route);

// the request url is: POST https://example.com/projects/test
// the request data is:
// {
//     "title": "PHP",
//     "subtitle": "Router"
// }
```
```
VanGestelJasper\Router\Route Object (
    [request] => VanGestelJasper\Router\Request Object (
        [payload] => VanGestelJasper\Router\Payload Object (
            [slug] => test
            [title] => PHP
            [subtitle] => Router
        )
    )
)
```
