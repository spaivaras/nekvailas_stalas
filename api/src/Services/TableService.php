<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 14.5.18
 * Time: 22.44
 */

namespace Services;

use Repositories\EventRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Yaml\Yaml;

class TableService
{
    /**
     * @var EventRepository
     */
    protected $eventRepository;

    /**
     * @param EventRepository $eventRepository
     */
    public function __construct($eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @return JsonResponse
     */
    public function getTableStatus()
    {
        $data = $this->eventRepository->getActiveEvent();

        if ($data && $data[count($data) - 1]['timeSec'] > time() - EventRepository::TIME_IDLE_FRAME) {
            $users = Yaml::parse(__DIR__ . "/../users.yml");
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
                $eventData = json_decode($event['data']);
                switch ($event['type']) {
                    case 'CardSwipe':
                        // if goals eq 10 - reset game
                        if ($returnData['teams'][$eventData->team]['goals'] >= 10 || $returnData['teams'][(1 - $eventData->team)]['goals'] >= 10) {
                            $returnData = $returnDataEmpty;
                            $this->eventRepository->insert(
                                $event['timeSec'] - 1,
                                0,
                                EventRepository::TYPE_TABLE_RESET,
                                "[]"
                            );
                        }
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
                            $returnData['teams'][$eventData->team]['players'][$eventData->player] =
                                $users[$eventData->card_id];
                        } else {
                            $returnData['teams'][$eventData->team]['players'][$eventData->player] = $users["1"];
                        }
                        break;
                    case 'AutoGoal':
                        // if goals eq 10 - reset game
                        if ($returnData['teams'][$eventData->team]['goals'] >= 10 || $returnData['teams'][(1 - $eventData->team)]['goals'] >= 10) {
                            $returnData = $returnDataEmpty;
                            $this->eventRepository->insert(
                                $event['timeSec'] - 1,
                                0,
                                EventRepository::TYPE_TABLE_RESET,
                                "[]"
                            );
                        }
                        $returnData['teams'][$eventData->team]['goals'] += 1;
                        break;
                }
            }

            return new JsonResponse(["status" => "ok", "table" => "busy", "data" => $returnData]);
        }

        return new JsonResponse(["status" => "ok", "table" => "free"]);
    }
} 