<?php

namespace Providers;

use Controllers\OpenApiController;
use Services\OpenApiService;
use Silex\Application;
use Silex\ServiceProviderInterface;

class OpenApiProvider implements ServiceProviderInterface
{
    /**
     * @param Application $app
     */
    public function register(Application $app)
    {
        $app['page.path.open_api'] = '/api/v1/events';

        $this->registerServices($app);
        $this->registerControllers($app);
        $this->registerRoutes($app);
    }

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
        $app['open_api.service'] = $app->share(
            function () use ($app) {
                $service = new OpenApiService($app['event.repository']);
                return $service;
            }
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
        $app['open_api.controller'] = $app->share(
            function () use ($app) {
                return new OpenApiController($app['open_api.service']);
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
        //Register events from hardware
        $app->get(
            $app['config']['path_prefix'] . $app['page.path.open_api'],
            'open_api.controller:indexAction'
        );
    }
}
