<?php

//use Silex\Application;
//use Silex\Provider\ServiceControllerServiceProvider;
//use Silex\Provider\DoctrineServiceProvider;
//use Symfony\Component\HttpFoundation\Request;
//use Silex\Provider\TwigServiceProvider;

/*
 * Load configs from default_config.yml and project specific config.yml
 */
include 'config/config.php';

/*
 * Set script timezone
 */
date_default_timezone_set($config['timezone']);

$app->register(new ServiceControllerServiceProvider());
$app->register(new TwigServiceProvider(), array(
    'twig.path'    => array(__DIR__.'/../templates'),
));

/*
|---------------------------------------------------------------------------------
| Register doctrine provider to enable DBAL usage.
|---------------------------------------------------------------------------------
*/

$app->register(new \Silex\Provider\DoctrineServiceProvider(), array(
        'db.options' => include 'config/database.php'
    ));


$app->register(new DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'    =>  $config['db']['driver'],
        'dbname'    =>  $config['db']['dbname'],
        'host'      =>  $config['db']['host'],
        'user'      =>  $config['db']['user'],
        'password'  =>  $config['db']['password'],
        'driverOptions' => array(
            1002    =>  'SET NAMES utf8'
        )
    ),
));

if (isset($config['debug']) && $config['debug'] === true) {
    ini_set('display_errors', E_ALL ^ E_NOTICE);
    $app['debug'] = true;
}

// accepting JSON
$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

return $app;
