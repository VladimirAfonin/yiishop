<?php

namespace shop\entities;


use yii\db\ActiveRecord;
use Webmozart\Assert\Assert;

class Network extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%user_networks}}';
    }

    /**
     * @param $network
     * @param $identity
     * @return Network
     */
    public static function create($network, $identity): self
    {
        Assert::notEmpty($network);
        Assert::notEmpty($identity);

        $item = new static();
        $item->network = $network;
        $item->identity = $identity;
        return $item;
    }

    /**
     * check if we have a new network in our DB.
     *
     * @param $network
     * @param $identity
     * @return bool
     */
    public function isFor($network, $identity): bool
    {
        return $this->network === $network && $this->identity === $identity;
    }

}