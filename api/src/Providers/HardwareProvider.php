<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 14.5.17
 * Time: 22.49
 */
namespace Providers;

use Controllers\HardwareController;
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

//        $this->registerServices($app);
//        $this->registerRepositories($app);
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
//        $app['stock.service'] = $app->share(
//            function () use ($app) {
//                $service = new StockService($app['db'], $app['stock.repository']);
//                $service->setEventDispatcher($app['dispatcher']);
//                $service->setValidator($app['validator']);
//                $service->setErrorManager($app['error.manager.service']);
//                return $service;
//            }
//        );
//
//        $app['reservation.service'] = $app->share(
//            function () use ($app) {
//                $service = new ReservationService($app['reservation.repository']);
//                $service->setEventDispatcher($app['dispatcher']);
//                $service->setValidator($app['validator']);
//                $service->setErrorManager($app['error.manager.service']);
//                return $service;
//            }
//        );
//
//        $app['history.service'] = $app->share(
//            function () use ($app) {
//                $service = new HistoryService($app['stock.repository']);
//                $service->setEventDispatcher($app['dispatcher']);
//                return $service;
//            }
//        );
    }

    /**
     * Register used repositories.
     *
     * @param Application $app An Application instance
     * @return void
     */
    protected function registerRepositories(Application $app)
    {
//        $app['stock.repository'] = $app->share(
//            function () use ($app) {
//                $repository = new StockRepository($app['db']);
//                $repository->setErrorManager($app['error.manager.service']);
//                return $repository;
//            }
//        );
//
//        $app['reservation.repository'] = $app->share(
//            function () use ($app) {
//                $repository = new ReservationRepository($app['db'], $app['stock.repository']);
//                $repository->setErrorManager($app['error.manager.service']);
//                return $repository;
//            }
//        );
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

//// Create new stock entry
//        $app->post(
//            $app['stock.options.api_stock_route_prefix'],
//            'stock.controller:create'
//        );
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
                return new HardwareController($app);
            }
        );
//
//        $app['reservation.controller'] = $app->share(
//            function () use ($app) {
//                return new ReservationController($app['reservation.service'], $app['stock.service']);
//            }
//        );
//
//        $app['history.controller'] = $app->share(
//            function () use ($app) {
//                return new HistoryController($app['history.service']);
//            }
//        );
//
//        $app['bulk.reservation.controller'] = $app->share(
//            function () use ($app) {
//                return new BulkReservationController($app['reservation.service']);
//            }
//        );
    }
}
