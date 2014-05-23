<?php

/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 14.5.23
 * Time: 20.33
 */

namespace Repositories;

use Doctrine\DBAL\Connection;
use Models\Card;
use Models\User;

class UserRepository
{
    /**
     * Used DB connection.
     * @var Connection
     */
    protected $connection;

    /**
     * Local cache
     * @var array
     */
    protected $users = [];

    /**
     * Create new stock service with injected services.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getUserByCardId($cardId)
    {
        if (isset($this->users[$cardId])) {
            return $this->users[$cardId];
        }

        $userTableName = User::TABLE_NAME;
        $cardTableName = Card::TABLE_NAME;
        $sql = "SELECT U1.userId, U1.firstName, U1.lastName
            FROM {$userTableName} AS U1
            LEFT JOIN {$cardTableName} AS C1 ON U1.userId = C1.userId
            WHERE C1.cardId = :cardId";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("cardId", $cardId);
        if (!$stmt->execute()) {
            throw new \Exception('UserRepository: Error with executing query.');
        }
        $values = $stmt->fetch(\PDO::FETCH_ASSOC);
        $user = new User();
        $this->users[$cardId] = $user->assign($values);

        return $this->users[$cardId];
    }
}
