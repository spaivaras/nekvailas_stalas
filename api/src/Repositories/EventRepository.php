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

    public function getActiveEventCount($idleTimeFrame)
    {
        $sql = "SELECT count(*) as `count`
            FROM kickertable
            WHERE timeSec > (UNIX_TIMESTAMP() - $idleTimeFrame)
            AND timeSec < UNIX_TIMESTAMP()";
        $count = $this->connection->fetchColumn($sql);

        return $count;
    }

} 