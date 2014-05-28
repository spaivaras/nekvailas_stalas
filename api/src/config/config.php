<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 5/14/14
 * Time: 6:38 PM
 */

use Symfony\Component\Yaml\Yaml;

$config = Yaml::parse(__DIR__ . "/../../default_config.yml");

if (file_exists(__DIR__ . '/../../config.yml')) {
    $projectConfig = Yaml::parse(__DIR__ . "/../../config.yml");
    $config = array_replace_recursive($config, $projectConfig);
}

$app['config'] = $config;