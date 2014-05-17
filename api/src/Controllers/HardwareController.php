<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 14.5.17
 * Time: 23.08
 */

namespace Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class HardwareController
{
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     *  * Save table event from pusher
     *
     * return ok|error status with human readable message
     * on success sets X-TableEventStored: 1 header
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function eventRegister(Request $request)
    {
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
        $count = $this->app['db']->fetchColumn($sql);

        if (!$count) {
            $this->app['db']->insert(
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
            $this->app['db']->insert(
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
    }
}
