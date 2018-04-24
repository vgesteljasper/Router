# VanGestelJasper\Router

Very simple URL router for just about anything.

It will match the current request against a set of predefined routes.
If there is a match, it will return a `VanGestelJasper\Router\Route` instance.
If it doesn't match it will return null.

The `Route` instance contains all the data from the request including:

* headers
* url
* path
* method
* query
* payload

..., as well as the handler, name and middleware you set for it.

**_Note:_** This little library is still in a very early stage.

## Usage

example:

```php
use \VanGestelJasper\Router\Router;

// instantiate the router
$router = new Router;

// Add routes
$router->get('/', 'PageController@index');
$router->get('/projects/{slug}', function($route) { /* ... */ });

// Optional: Add a name to the route. This name will be available on the route object.
$router
    ->post('/uploads', 'UploadController@create')
    ->name('uploads-route')
    ->use('UploadMiddleware');
// The `use` method is for running middleware before the route handler is called.
// You can chain `use` calls like `->use('...')->use('...');` to add multiple.
// The router will call the `run` method on the class you provide.
// If `run` returns true, the next middleware will be called or the route handler
// when there is no further middleware.
// You can do anything to the $route object inside the middleware.
// Returning anything else then true will stop the router.
// You should handle termination yourself.

// Optional: Define a fallback route in case no route matches.
$router->fallback('ErrorController@NoMatch');

// Returns the matched route or null
// You can modify the route however you want
$route = $router->dispatch();

// Optional: If you just need the matched route object, then you don't need this next method.

// This will trigger the matched route handler for you.
// If there was no matching route, the fallback route will be used if you defined it.
// The route handler will be called with the $route as parameter.
// The return value of Router->run() is a `true` if a handler got triggered, `false` if not.
$ran = $router->run();
```

middleware example:

```php
class UploadMiddleware {

    /**
     * Run middleware.
     * @param \VanGestelJasper\Router\Route $route
     * @return bool $next
     */
    public function run($route): bool
    {
        // manipulate the $route here
        // or anything else oyu need to do

        return true // go to next middleware or run route handler
        return false // stop the router
    }
}
```

Note: It is currently not supported to run middleware as a closure like you
can with route handlers.

An example return value of `$router->dispatch()` of the example above in case
the url was `localhost:5000/projects/test?page=1`:

```
VanGestelJasper\Router\Route Object (
    [handler] => ProjectController@show
    [name] => The route name
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
    [middleware] => Array ()
)
```

As you can see in the example, you can either provide a function callback or a
string in the form of `Class@method`.
`VanGestelJasper\Router\Router->dispatch()` will always trigger the handler with
the matched route as it's first parameter.

See the example below.

```php
class RouteHandler {
    /**
     * @param VanGestelJasper\Router\Route $route
     */
    public function handle($route)
    {
        /* ... */
    }
}

$router = new VanGestelJasper\Router\Router;

$router->get('/foo', 'RouteHandler@handle');
$router->get('/bar', function($route) {
    /* ... */
});

$router->dispatch();
$router->run();
```

### Request data

The request data will be read as JSON. If it succeeds, you will be able to
access on the `Route` -> `Request` -> `Payload` object.

See the example result below.

**_Note:_** The wildcards defined in routes between `{` and `}` will be
accessable on the `Route` -> `Request` -> `Payload` object as well.

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
