<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 14.5.18
 * Time: 22.44
 */

namespace Services;

use Repositories\EventRepository;
use Repositories\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class TableService
{
    /**
     * @var EventRepository
     */
    protected $eventRepository;

    /**
     * @param EventRepository $eventRepository
     * @param UserRepository $userRepository
     */
    public function __construct($eventRepository, $userRepository)
    {
        $this->eventRepository = $eventRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @return JsonResponse
     */
    public function getTableStatus()
    {
        $data = $this->eventRepository->getActiveEvent();

        if ($data && $data[count($data) - 1]['timeSec'] > time() - EventRepository::TIME_IDLE_FRAME) {
            $returnDataEmpty = [];
            $goals = 0;
            $players = [
                0 => UserRepository::USER_DEFAULT_ID,
                1 => UserRepository::USER_DEFAULT_ID
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
                        if ($this->isResetGame($returnData, $event)) {
                            $returnData = $returnDataEmpty;
                        }
                        // check for dublicate users reset user id
                        if ($returnData['teams'][0]['players'][0] == $eventData->card_id) {
                            $returnData['teams'][0]['players'][0] = UserRepository::USER_DEFAULT_ID;
                        }
                        if ($returnData['teams'][0]['players'][1] == $eventData->card_id) {
                            $returnData['teams'][0]['players'][1] = UserRepository::USER_DEFAULT_ID;
                        }
                        if ($returnData['teams'][1]['players'][0] == $eventData->card_id) {
                            $returnData['teams'][1]['players'][0] = UserRepository::USER_DEFAULT_ID;
                        }
                        if ($returnData['teams'][1]['players'][1] == $eventData->card_id) {
                            $returnData['teams'][1]['players'][1] = UserRepository::USER_DEFAULT_ID;
                        }
                        // write user id
                        $returnData['teams'][$eventData->team]['players'][$eventData->player] = $eventData->card_id;
                        break;
                    case 'AutoGoal':
                        if ($this->isResetGame($returnData, $event)) {
                            $returnData = $returnDataEmpty;
                        }
                        $returnData['teams'][$eventData->team]['goals'] += 1;
                        break;
                }
            }

            return new JsonResponse(["status" => "ok", "table" => "busy", "data" => $this->processData($returnData)]);
        }

        return new JsonResponse(["status" => "ok", "table" => "free"]);
    }

    /**
     * @param array $data
     * @param array $event
     * @return bool
     */
    protected function isResetGame($data, $event)
    {
        $res = false;
        // if goals eq 10 - reset game
        if ($data['teams'][0]['goals'] >= 10 || $data['teams'][1]['goals'] >= 10) {
            $this->eventRepository->insert(
                $event['timeSec'] - 1,
                0,
                EventRepository::TYPE_TABLE_RESET,
                "[]"
            );
            $res = true;
        }
        return $res;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function processData($data)
    {
        $data["teams"][0]["players"][0] = $this->getUserInfoByCardId($data["teams"][0]["players"][0]);
        $data["teams"][0]["players"][1] = $this->getUserInfoByCardId($data["teams"][0]["players"][1]);
        $data["teams"][1]["players"][0] = $this->getUserInfoByCardId($data["teams"][1]["players"][0]);
        $data["teams"][1]["players"][1] = $this->getUserInfoByCardId($data["teams"][1]["players"][1]);

        return $data;
    }

    /**
     * @param int $cardId
     * @return array
     */
    protected function getUserInfoByCardId($cardId)
    {
        $user = $this->userRepository->getUserByCardId($cardId);
        if ($user === null) {
            $user = $this->userRepository->getUserByCardId(UserRepository::USER_UNKNOWN_ID);
        }

        return ["img" => $user->getUserId() . '.png', "name" => $user->getFirstName()];
    }
} 