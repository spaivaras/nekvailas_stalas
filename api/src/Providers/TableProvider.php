<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 14.5.17
 * Time: 23.32
 */

namespace Providers;

use Controllers\TableController;
use Repositories\UserRepository;
use Services\TableService;
use Silex\Application;
use Silex\ServiceProviderInterface;

class TableProvider implements ServiceProviderInterface
{
    /**
     * @inherit
     *
     * @param Application $app
     */
    public function register(Application $app)
    {
        $app['page.path.start_page'] = '/';
        $app['page.path.status_json'] = '/api/v1/status';

        $this->registerServices($app);
        $this->registerRepositories($app);
        $this->registerControllers($app);
        $this->registerRoutes($app);
    }

    /**
     * @inherit
     */
    public function boot(Application $app)
    {
    }

    /**
     * Register used services.
     *
     * @param Application $app An Application instance
     * @return void
     */
    protected function registerServices(Application $app)
    {
        $app['table.service'] = $app->share(
            function () use ($app) {
                $service = new TableService($app['event.repository']);
                return $service;
            }
        );
    }

    /**
     * Register used repositories.
     *
     * @param Application $app An Application instance
     * @return void
     */
    protected function registerRepositories(Application $app)
    {
        $app['user.repository'] = $app->share(
            function () use ($app) {
                $repository = new UserRepository($app['db']);
                return $repository;
            }
        );
    }

    /**
     * Register used routes.
     *
     * @param Application $app An Application instance
     * @return void
     */
    protected function registerRoutes(Application $app)
    {
        //index page
        $app->get(
            $app['config']['path_prefix'] . $app['page.path.start_page'],
            'page.controller:index'
        );

        //status page
        $app->get(
            $app['config']['path_prefix'] . $app['page.path.status_json'],
            'page.controller:status'
        );
    }

    /**
     * Register used controllers.
     *
     * @param Application $app An Application instance
     * @return void
     */
    protected function registerControllers(Application $app)
    {
        $app['page.controller'] = $app->share(
            function () use ($app) {
                return new TableController($app['twig'], $app['table.service']);
            }
        );
    }
} 