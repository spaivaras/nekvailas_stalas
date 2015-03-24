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
     * @param int $fromTs
     * @param int $tillTs
     *
     * @return array
     */
    public function getLastRows($lastRows, $fromRecord, $fromTs, $tillTs)
    {
        if ($fromRecord != 0) {
            $result = $this->eventRepository->getRowsFrom($lastRows, $fromRecord, $fromTs, $tillTs);
        } else {
            $result = $this->eventRepository->getLastRows($lastRows, $fromTs, $tillTs);
        }

        return $result;
    }
}
