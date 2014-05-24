<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 14.5.23
 * Time: 20.28
 */
namespace Models;

class BaseModel
{
    /**
     * Dump document data as an array
     *
     * @return array
     */
    public function dump()
    {
        return get_object_vars($this);
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
}
