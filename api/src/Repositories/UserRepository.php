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
    const USER_DEFAULT_CARD_NUMBER = 0;
    const USER_UNKNOWN_CARD_NUMBER = 1;

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

    /**
     * @param int $cardNumber
     * @return User
     * @throws \Exception
     */
    public function getUserByCardNumber($cardNumber)
    {
        if (isset($this->users[$cardNumber])) {
            return $this->users[$cardNumber];
        }

        $userTableName = User::TABLE_NAME;
        $cardTableName = Card::TABLE_NAME;
        $sql = "SELECT U1.userId, U1.firstName, U1.lastName
            FROM {$userTableName} AS U1
            LEFT JOIN {$cardTableName} AS C1 ON U1.userId = C1.userId
            WHERE C1.cardNumber = :cardId";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("cardId", $cardNumber);
        if (!$stmt->execute()) {
            throw new \Exception('UserRepository: Error with executing query 1.');
        }
        $values = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($values === null || $values === false) {
            $user = null;
            if ($cardNumber === self::USER_UNKNOWN_CARD_NUMBER) {
                $user = new User();
                $user->assign(['userId' => 1, 'firstName' => 'Nežinomas']);
            }
            return $user;
        }
        $user = new User();
        $this->users[$cardNumber] = $user->assign($values);

        return $this->users[$cardNumber];
    }

    public function saveUser($user)
    {
        $userTableName = User::TABLE_NAME;
        $data = $user->dump();
        $columns = join(',', array_keys($data));
        $keys = ':' . join(',:', array_keys($data));
        $sql = "INSERT INTO {$userTableName}
          ({$columns}) VALUES ($keys)";
        //      ON DUPLICATE KEY UPDATE c=c+1;";
        $stmt = $this->connection->prepare($sql);
        foreach ($data as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        if (!$stmt->execute()) {
            throw new \Exception('UserRepository: Error with executing query 2.');
        }
    }
}
