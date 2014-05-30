<?php

//use Silex\Provider\DoctrineServiceProvider;
//;

/** @var \Silex\Application $app */

/**
 * Load configs from default_config.yml and project specific config.yml
 */
include 'Config/config.php';

/**
 * Set script timezone
 */
date_default_timezone_set($config['timezone']);

/**
 * Register silex provider to use controllers as services.
 */
$app->register(new \Silex\Provider\ServiceControllerServiceProvider());

/**
 * Register twig service provides to use the Twig template engine
 */
$app->register(
    new \Silex\Provider\TwigServiceProvider(),
    array('twig.path' => array(__DIR__ . '/../templates'))
);

/**
 * Register doctrine provider to enable DBAL usage.
 */
include 'Config/database.php';

/**
 * Debug mode configuration
 */
if (isset($config['debug']) && $config['debug'] === true) {
    ini_set('display_errors', E_ALL ^ E_NOTICE);
    $app['debug'] = true;
}

/**
 * Request with JSON transform data to associative array
 */
$app->before(
    function (\Symfony\Component\HttpFoundation\Request $request) {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : array());
        }
    }
);

/**
 * Register hardware provider to enable routing and services
 */
$app->register(new \Providers\HardwareProvider());

/**
 * Register page provider to enable routing and services
 */
$app->register(new \Providers\TableProvider());
