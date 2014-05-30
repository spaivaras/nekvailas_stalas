<?php
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Register service
 */
//$app['action.service'] = $app->share(
//    function () use ($app) {
//        return new ActionService($app['db']);
//    }
//);

/**
 * Get table status.
 *
 * returns table status:
 * status: ok
 * message: table free|table busy
 *
 * @return JsonResponse
 */
//$app->get('/kickertable/api/v1/status', "action.service:statusAction");