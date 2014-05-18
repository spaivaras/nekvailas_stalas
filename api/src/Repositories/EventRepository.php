<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 14.5.18
 * Time: 11.08
 */
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

        $this->connection->insert('kickertable_event', $modelData);
    }

    public function getActiveEventCount()
    {
        $sql = "SELECT count(*) as `count`
            FROM kickertable
            WHERE timeSec > (UNIX_TIMESTAMP() - " . self::TIME_IDLE_FRAME . ")
            AND timeSec < UNIX_TIMESTAMP()";
        $count = $this->connection->fetchColumn($sql);

        return $count;
    }

    public function getActiveEvent()
    {
        $sql = "SELECT timeSec, type, data
            FROM kickertable
            WHERE timeSec > (SELECT MAX(timeSec) FROM kickertable WHERE type = '" . self::TYPE_TABLE_RESET . "')
            ORDER BY timeSec";

        $data = $this->connection->fetchAll($sql);

        return $data;
    }

} 