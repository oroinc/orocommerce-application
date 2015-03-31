<?php

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;

// Use APC for autoloading to improve performance
// Change 'sf2' by the prefix you want in order to prevent key conflict with another application
/*
$loader = new ApcClassLoader('sf2', $loader);
$loader->register(true);
*/

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';
$request = Request::createFromGlobals();

// TODO: Move parse logic to some helper
$parameters = Yaml::parse(file_get_contents(__DIR__.'/../app/front/config/parameters.yml'));
unset($parameters);

require_once __DIR__.'/../app/AppKernel.php';
//require_once __DIR__.'/../../app_front/FrontCache.php';
$kernel = new AppKernel('front', 'prod', false);
$kernel->loadClassCache();
//$kernel = new FrontCache($kernel);

$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
