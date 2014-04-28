<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Main / to say hello
 *
 * @return Response
 */
$app->get('/', function () use ($app) {
    return new Response("hi");
});

/**
 * Get table status.
 *
 * returns table status:
 * status: ok|error
 * message: table free|table busy
 *
 * @return JsonResponse
 */

$app->get('/app/v1/status', function () {
    return new JsonResponse(["status" => "ok", "message" => "table free"]);
});

/**
 * Get table events.
 *
 * returns table events
 *
 * @return JsonResponse
 */
$app->get('/api/v1/events', function () {
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
$app->post('/api/v1/event', function (Request $request) use ($app) {
    if (!$data = $request->request->all()) {
        return new JsonResponse(["status" => "error", "message" => "bad request"], 400);
    }

    // @todo
    // do something with data from pusher
    // e.g. save to DB
    //
    // example data:
    // {"time":{"sec":1398619851,"usec":844563},"type":"TableShake","data":{}}
    // {"time":{"sec":1398619851,"usec":847409},"type":"CardSwipe","data":{"team":1,"player":2,"card_id":123456789}}

    return new JsonResponse(["status" => "ok"], ["X-TableEventStored: 1"]);
});
