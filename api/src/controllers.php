<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Main page to say hello and show table status via json api
 *
 * @return Response
 */
$app->get('/kickertable/', function () use ($app) {
    return $app['twig']->render('index.twig', []);
});

/**
 * Register service
 */
$app['action.service'] = $app->share(
    function () use ($app) {
        return new ActionService($app['db']);
    }
);

/**
 * Get table status.
 *
 * returns table status:
 * status: ok
 * message: table free|table busy
 *
 * @return JsonResponse
 */
$app->get('/kickertable/api/v1/status', "action.service:statusAction");

/**
 * Save table event from pusher
 *
 * return ok|error status with human readable message
 * on success sets X-TableEventStored: 1 header
 *
 * @return JsonResponse
 */
$app->post('/kickertable/api/v1/event', function (Request $request) use ($app) {

    // example data
    // [
    // {"time":{"sec":1398619851,"usec":844563},"type":"TableShake","data":{}},
    // {"time":{"sec":1398619851,"usec":846044},"type":"AutoGoal","data":{"team":1}},
    // {"time":{"sec":1398619851,"usec":847409},"type":"CardSwipe","data":{"team":0,"player":1,"card_id":123456789}}
    // ]

    if (!$data = $request->request->all()) {
        return new JsonResponse(["status" => "error", "message" => "bad request"], 400);
    }

    $idleTimeFrame = 50; // 50 sec
    $sql = "SELECT count(*) as `count`
            FROM kickertable
            WHERE timeSec > (UNIX_TIMESTAMP() - $idleTimeFrame)
            AND timeSec < UNIX_TIMESTAMP()";
    $count = $app['db']->fetchColumn($sql);

    if (!$count) {
        $app['db']->insert(
            'kickertable',
            [
                "timeSec"   => $data[0]['time']['sec']-1,
                "usec"      => $data[0]['time']['usec'],
                "type"      => "TableReset",
                "data"      => "[]"
            ]
        );
    }

    // array of events
    foreach ($data as $event) {
        $app['db']->insert(
            'kickertable',
            [
                "timeSec"   => $event['time']['sec'],
                "usec"      => $event['time']['usec'],
                "type"      => $event['type'],
                "data"      => json_encode($event['data'])
            ]
        );
    }

    return new JsonResponse(["status" => "ok"], 200, ["X-TableEventStored" => "1"]);
});
