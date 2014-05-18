<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 14.5.18
 * Time: 11.42
 */
namespace Services;

use Repositories\EventRepository;

class HardwareService
{

    /**
     * @var EventRepository
     */
    protected $eventRepository;

    /**
     * @param EventRepository $eventRepository
     */
    public function _construct($eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @param array $requestData
     */
    public function handleEvent($requestData)
    {
        $this->checkIsNewGame($requestData);

        // array of events
        foreach ($requestData as $event) {
            $this->eventRepository->insert(
                $event['time']['sec'],
                $event['time']['usec'],
                $event['type'],
                json_encode($event['data'])
            );
        }
    }

    protected function checkIsNewGame($requestData)
    {
        $idleTimeFrame = 50; // 50 sec
        $count = $this->eventRepository->getActiveEventCount($idleTimeFrame);

        //if last $idleTimeFrame second not have event, then it will be new game
        if (!$count) {
            $this->eventRepository->insert(
                $requestData[0]['time']['sec'] - 1,
                $requestData[0]['time']['usec'],
                EventRepository::TYPE_TABLE_RESET,
                "[]"
            );
        }
    }
} 