<?php

/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 14.5.28
 * Time: 21.24
 */
namespace Console;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Helper\Helper;

class ConnectionHelper extends Helper
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @param Connection $connection
     */
    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     *
     * @api
     */
    public function getName()
    {
        return 'connection';
    }

}
 