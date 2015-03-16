<?php

namespace Services;

use Repositories\EventRepository;

class OpenApiService
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
     * @param int $lastRows
     * @param int $fromRecord
     *
     * @return array
     */
    public function getLastRows($lastRows, $fromRecord)
    {
        if ($fromRecord != 0) {
            $result = $this->eventRepository->getRowsFrom($lastRows, $fromRecord);
        } else {
            $result = $this->eventRepository->getLastRows($lastRows);
        }

        return $result;
    }
}
