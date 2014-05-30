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
    public function __construct($eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @param array $requestData
     */
    public function handleEvent($requestData)
    {
        $this->checkIsNewGame($requestData[0]['time']['sec'] - 1);

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

    /**
     * @param integer $time
     */
    protected function checkIsNewGame($time)
    {
        $count = $this->eventRepository->getActiveEventCount();

        //if last $idleTimeFrame second not have event, then it will be new game
        if (!$count) {
            $this->eventRepository->insert(
                $time,
                0,
                EventRepository::TYPE_TABLE_RESET,
                "[]"
            );
        }
    }
} 