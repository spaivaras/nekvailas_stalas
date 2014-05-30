<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 14.5.17
 * Time: 22.49
 */
namespace Providers;

use Controllers\HardwareController;
use Repositories\EventRepository;
use Services\HardwareService;
use Silex\Application;
use Silex\ServiceProviderInterface;

class HardwareProvider implements ServiceProviderInterface
{
    /**
     * @inherit
     *
     * @param Application $app
     */
    public function register(Application $app)
    {
        $app['hardware.event.register_path'] = '/api/v1/event';

        $this->registerRepositories($app);
        $this->registerServices($app);
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
        $app['hardware.service'] = $app->share(
            function () use ($app) {
                $service = new HardwareService($app['event.repository']);
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
        $app['event.repository'] = $app->share(
            function () use ($app) {
                $repository = new EventRepository($app['db']);
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
        //Register events from hardware
        $app->post(
            $app['config']['path_prefix'] . $app['hardware.event.register_path'],
            'hardware.controller:eventRegister'
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
        $app['hardware.controller'] = $app->share(
            function () use ($app) {
                return new HardwareController($app['hardware.service']);
            }
        );
    }
}
