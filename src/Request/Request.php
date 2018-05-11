<?php namespace vjee\Router\Request;

use vjee\Collection\Collection;

class Request extends Collection
{

  /**
   * @var string
   */
  public $url;

  /**
   * @var string
   */
  public $path;

  /**
   * @var string
   */
  public $method;

  /**
   * @var vjee\Router\Request\Headers
   */
  public $headers;

  /**
   * @var vjee\Router\Request\Query
   */
  public $query;

  /**
   * @var vjee\Router\Request\Payload
   */
  public $payload;

  /**
   * Request constructor.
   */
  public function __construct()
  {
    $this->url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $this->path = parse_url($this->url, PHP_URL_PATH);
    $this->method = $_SERVER['REQUEST_METHOD'];

    $this->initHeaders();
    $this->initPayload();
    $this->parseQuery();
  }

  /**
   * Get the request headers.
   * @return void
   */
  public function initHeaders(): void
  {
    $this->headers = new Headers(apache_request_headers());
  }

  /**
   * Get the request body.
   * @return void
   */
  public function initPayload(): void
  {
    $payload = @json_decode(file_get_contents("php://input"), true) ?: null;
    $this->payload = new Payload($payload);
  }

  /**
   * Parse the query.
   * @return void
   */
  public function parseQuery(): void
  {
    parse_str(parse_url($this->url, PHP_URL_QUERY), $query);
    $this->query = new Query($query);
  }

}
