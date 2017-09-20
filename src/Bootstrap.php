<?php

namespace App;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

use Symfony\Component\Routing\Exception\ResourceNotFoundException;

require __DIR__ . '/../vendor/autoload.php';

error_reporting(E_ALL);

$environment = 'development';

/**
* Register the error handler
*/
$whoops = new \Whoops\Run;
if ($environment !== 'production') {
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
} else {
    $whoops->pushHandler(function($e) {
        echo 'Todo: Friendly error page and send an error to log';
    });
}

$whoops->register();

/**
*  Create request
*/
$request = Request::createFromGlobals();

/**
*  Create routing
*/
$route = new Route('/foo/{slug}', ['_controller' => 'Home:show']);
$route2 = new Route('/foo', ['_controller' => 'Home:show']);

$routes = new RouteCollection();
$routes->add('route_name', $route, array('name' => 'dupa'));
$routes->add('route_name2', $route2);

$context = new RequestContext();
$context->fromRequest($request);


/**
*  Matching URI with routes
*/
$matcher = new UrlMatcher($routes, $context);

try {
    $parameters = $matcher->match($request->getPathInfo());

    /**
    *  Dispatching controller
    */
    $params = explode(':', $parameters['_controller']);
    
    $className = 'App\\Controllers\\'.$params[0];
    $method = $params[1];
    
    $class = new $className();
    $class->$method();
} catch (ResourceNotFoundException $e) {
    $response = new Response('404 not Found', 404);
    $response->send();
    
}