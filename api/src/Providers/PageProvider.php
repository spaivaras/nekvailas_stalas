<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 14.5.17
 * Time: 23.32
 */

namespace Providers;

use Controllers\PageController;
use Silex\Application;
use Silex\ServiceProviderInterface;

class PageProvider implements ServiceProviderInterface
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
                return new PageController($app['twig'], $app);
            }
        );
    }
} 