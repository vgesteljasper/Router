<?php

require __DIR__ . '/../vendor/autoload.php';

if (!function_exists('apache_request_headers')) {
  function apache_request_headers() {
    return (new Ducks\Component\Apache\Apache)->requestHeaders();
  }
}

if (!function_exists('apache_response_headers')) {
  function apache_response_headers() {
    return (new Ducks\Component\Apache\Apache)->responseHeaders();
  }
}
