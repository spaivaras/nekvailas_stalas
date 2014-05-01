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
 * Get table status.
 *
 * returns table status:
 * status: ok
 * message: table free|table busy
 *
 * @return JsonResponse
 */

$app->get('/kickertable/api/v1/status', function () use ($app) {
    $timeFrame = 50;
    $sql = "SELECT count(id) as motionsCount FROM kickertable WHERE timeSec > (UNIX_TIMESTAMP() - $timeFrame) and timeSec < UNIX_TIMESTAMP()";
    $motionsCount = $app['db']->fetchColumn($sql);
    if ($motionsCount) {
        return new JsonResponse(["status" => "ok", "message" => "table taken $motionsCount"]);
    }

    return new JsonResponse(["status" => "ok", "message" => "table free $motionsCount"]);
});

/**
 * Get table events.
 *
 * returns table events
 *
 * @return JsonResponse
 */
$app->get('/kickertable/api/v1/events', function () {
    // @todo
    // return events data from DB
    // sample data
    $data = [
        [
            'timestamp' => 1398619851,
            'type' => "TableShake",
            'data' => ''
        ],
        [
            'timestamp' => 1398619851,
            'type' => "CardSwipe",
            'data' => [
                'team' => 1,
                'player' => 2,
                'card_id' => 123456789
            ]
        ]
    ];

    return new JsonResponse(["status" => "ok", "data" => $data]);
});

/**
 * Save table event from pusher
 *
 * return ok|error status with human readable message
 * on success sets X-TableEventStored: 1 header
 *
 * @return JsonResponse
 */
$app->post('/kickertable/api/v1/event', function (Request $request) use ($app) {

    // example data:
    // {"time":{"sec":1398619851,"usec":844563},"type":"TableShake","data":{}}
    // {"time":{"sec":1398619851,"usec":847409},"type":"CardSwipe","data":{"team":1,"player":2,"card_id":123456789}}

    if (!$data = $request->request->all()) {
        return new JsonResponse(["status" => "error", "message" => "bad request"], 400);
    }

    $aData = array(
        "timeSec"   => $data[0]['time']['sec'],
        "usec"      => $data[0]['time']['usec'],
        "type"      => $data[0]['type'],
        "data"      => json_encode($data[0]['data'])
    );

    if ($app['db']->insert('kickertable', $aData)) {
        return new JsonResponse(["status" => "ok"], 200, ["X-TableEventStored" => "1"]);
    }

    return new JsonResponse(["status" => "error", "message" => "db insert error"], 400);
});
