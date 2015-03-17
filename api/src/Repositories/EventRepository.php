<?php

namespace Repositories;

use \Doctrine\DBAL\Connection;

class EventRepository
{
    const TYPE_TABLE_RESET = 'TableReset';
    const TYPE_TABLE_SHAKE = 'TableShake';
    const TYPE_GOAL_AUTO = 'AutoGoal';
    const TYPE_CARD_SWIPE = 'CardSwipe';

    //seconds
    const TIME_IDLE_FRAME = 50;

    /**
     * @var string
     */
    protected $tableName = 'kickertable_event';
    /**
     * Used DB connection.
     *
     * @var Connection
     */
    protected $connection;

    /**
     * Create new stock service with injected services.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function insert($sec, $usec, $type, $data)
    {
        $modelData = [
            "timeSec" => $sec,
            "usec" => $usec,
            "type" => $type,
            "data" => $data
        ];

        $this->connection->insert($this->tableName, $modelData);
    }

    public function getActiveEventCount()
    {
        $sql = "SELECT count(*) as `count`
            FROM {$this->tableName}
            WHERE timeSec > (UNIX_TIMESTAMP() - " . self::TIME_IDLE_FRAME . ")
            AND timeSec < UNIX_TIMESTAMP()";
        $count = $this->connection->fetchColumn($sql);

        return $count;
    }

    public function getActiveEvent()
    {
        $sql = "SELECT `timeSec`, `type`, `data`
            FROM {$this->tableName}
            WHERE timeSec > (SELECT MAX(timeSec) FROM {$this->tableName} WHERE type = '" . self::TYPE_TABLE_RESET . "')
            ORDER BY timeSec";

        $data = $this->connection->fetchAll($sql);

        return $data;
    }

    /**
     * @param int $lastRows
     *
     * @return array
     */
    public function getLastRows($lastRows)
    {
        $sql = "SELECT *
          FROM {$this->tableName}
          WHERE `type` <> '" . self::TYPE_TABLE_RESET . "'
          ORDER BY `id` DESC
          LIMIT 0, {$lastRows}";

        $data = $this->connection->fetchAll($sql);

        return $data;
    }

    /**
     * @param int $rows
     * @param int $fromId
     *
     * @return array
     */
    public function getRowsFrom($rows, $fromId)
    {
        $sql = "SELECT *
          FROM {$this->tableName}
          WHERE `id` > {$fromId} AND `type` <> '" . self::TYPE_TABLE_RESET . "'
          ORDER BY `id`
          LIMIT 0, {$rows}";

        $data = $this->connection->fetchAll($sql);

        return $data;
    }
}
