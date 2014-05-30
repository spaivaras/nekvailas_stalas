<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 14.5.17
 * Time: 23.08
 */

namespace Controllers;

use Services\HardwareService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class HardwareController
{
    /**
     * @var HardwareService
     */
    protected $hardwareService;

    /**
     * @param HardwareService $hardwareService
     */
    public function __construct($hardwareService)
    {
        $this->hardwareService = $hardwareService;
    }

    /**
     * Save table event from pusher
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

        $this->hardwareService->handleEvent($data);

        return new JsonResponse(["status" => "ok"], 200, ["X-TableEventStored" => "1"]);
    }
}
