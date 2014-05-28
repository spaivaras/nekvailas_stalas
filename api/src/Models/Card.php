<?php

/*
 * Created by PhpStorm.
 * User: Darius
 * Date: 14.5.23
 * Time: 20.04
 */

namespace Models;

class Card extends BaseModel
{
    const TABLE_NAME = 'kickertable_user_card';
    /**
     * @var int
     */
    protected $cardId;

    /**
     * @var int
     */
    protected $userId;

    /**
     * @var int
     */
    protected $cardNumber;

    /**
     * @var string
     */
    protected $cardValue;

    /**
     * @param int $cardId
     * @return $this
     */
    public function setCardId($cardId)
    {
        $this->cardId = $cardId;
        return $this;
    }

    /**
     * @return int
     */
    public function getCardId()
    {
        return $this->cardId;
    }

    /**
     * @param int $cardNumber
     * @return $this
     */
    public function setCardNumber($cardNumber)
    {
        $this->cardNumber = $cardNumber;
        return $this;
    }

    /**
     * @return int
     */
    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    /**
     * @param string $cardValue
     * @return $this
     */
    public function setCardValue($cardValue)
    {
        $this->cardValue = $cardValue;
        return $this;
    }

    /**
     * @return string
     */
    public function getCardValue()
    {
        return $this->cardValue;
    }

    /**
     * @param int $userId
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Save model data to DB
     */
    public function save()
    {
        parent::save(self::TABLE_NAME);
    }
}
