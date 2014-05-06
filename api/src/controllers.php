<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Yaml\Yaml;

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
    $idleTimeFrame = 50; // 50 sec
    $sql = "SELECT timeSec, type, data
            FROM kickertable
            WHERE timeSec > (SELECT MAX(timeSec) FROM kickertable WHERE type = 'TableReset')
            ORDER BY timeSec";
    $data = $app['db']->fetchAll($sql);

    if ($data && $data[count($data)-1]['timeSec'] > time() - $idleTimeFrame) {
        $users = Yaml::parse(__DIR__."/users.yml");
        $returnDataEmpty = [];
        $goals = 0;
        $players = [
            0 => $users["0"],
            1 => $users["0"],
        ];
        $teams = [
            0 => [
                'players' => $players,
                'goals' => $goals
            ],
            1 => [
                'players' => $players,
                'goals' => $goals
            ]
        ];
        $returnDataEmpty['teams'] = $teams;

        $returnData = $returnDataEmpty;
        foreach ($data as $event) {
            // check for idle time frame gap
            // if so reset game
            // if ($event['timeSec'] < time() - $idleTimeFrame) {
            //     $returnData = $returnDataEmpty;
            // }

            $eventData = json_decode($event['data']);
            switch ($event['type']) {
                case 'CardSwipe':
                    // check for dublicate users reset user id
                    if ($returnData['teams'][0]['players'][0] == $users[$eventData->card_id]) {
                        $returnData['teams'][0]['players'][0] = $users["0"];
                    }
                    if ($returnData['teams'][0]['players'][1] == $users[$eventData->card_id]) {
                        $returnData['teams'][0]['players'][1] = $users["0"];
                    }
                    if ($returnData['teams'][1]['players'][0] == $users[$eventData->card_id]) {
                        $returnData['teams'][1]['players'][0] = $users["0"];
                    }
                    if ($returnData['teams'][1]['players'][1] == $users[$eventData->card_id]) {
                        $returnData['teams'][1]['players'][1] = $users["0"];
                    }
                    // write user id
                    if (isset($users[$eventData->card_id])) {
                        $returnData['teams'][$eventData->team]['players'][$eventData->player] = $users[$eventData->card_id];
                    } else {
                        $returnData['teams'][$eventData->team]['players'][$eventData->player] = $users["1"];
                    }
                    break;
                case 'AutoGoal':
                    $returnData['teams'][$eventData->team]['goals'] += 1;
                    // if goals eq 10 - reset game
                    if ($returnData['teams'][$eventData->team]['goals'] > 10) {
                        $returnData = $returnDataEmpty;
                    }
                    break;
            }
        }

        return new JsonResponse(["status" => "ok", "table" => "busy", "data" => $returnData]);
    }

    return new JsonResponse(["status" => "ok", "table" => "free"]);
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
            AND timeSec < UNIX_TIMESTAMP()
            ORDER BY timeSec";
    $count = $app['db']->fetchColumn($sql);

    if (!$count) {
        $app['db']->insert('kickertable',[
            "timeSec"   => $data[0]['time']['sec']-1,
            "usec"      => $data[0]['time']['usec'],
            "type"      => "TableReset"
        ]);
    }

    // array of events
    foreach ($data as $event) {
        $app['db']->insert('kickertable', [
            "timeSec"   => $event['time']['sec'],
            "usec"      => $event['time']['usec'],
            "type"      => $event['type'],
            "data"      => json_encode($event['data'])
        ]);
    }

    return new JsonResponse(["status" => "ok"], 200, ["X-TableEventStored" => "1"]);
});
