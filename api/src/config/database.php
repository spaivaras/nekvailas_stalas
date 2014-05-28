<?php

/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 14.5.27
 * Time: 23.18
 */

$app->register(
    new \Silex\Provider\DoctrineServiceProvider(),
    array(
        'db.options' => array(
            'driver' => $config['db']['driver'],
            'dbname' => $config['db']['dbname'],
            'host' => $config['db']['host'],
            'user' => $config['db']['user'],
            'password' => $config['db']['password'],
            'driverOptions' => array(
                1002 => 'SET NAMES utf8'
            )
        )
    )
);