<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 14.5.23
 * Time: 20.28
 */
namespace Models;

use Doctrine\DBAL\Connection;

class BaseModel
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var array
     */
    protected $black_list = ['connection' => null, 'black_list' => null];

    /**
     * @param Connection $connection
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * Dump document data as an array
     *
     * @return array
     */
    public function dump()
    {
        return array_diff_key(get_object_vars($this), $this->black_list);
    }

    /**
     * Assign data into document
     *
     * @param array $data
     * @return $this
     */
    public function assign($data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }

        return $this;
    }

    /**
     * @param string $tableName
     * @throws \Exception
     */
    public function save($tableName)
    {
        $data = $this->dump();
        $columns = join(',', array_keys($data));
        $keys = ':' . join(',:', array_keys($data));
        $sql = "INSERT INTO {$tableName}
          ({$columns}) VALUES ($keys)";
        $stmt = $this->connection->prepare($sql);
        foreach ($data as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        if (!$stmt->execute()) {
            throw new \Exception('Error with executing query.');
        }
    }
}
