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

    public static function create($network, $identity): self
    {
        Assert::notEmpty($network);
        Assert::notEmpty($identity);

        $item = new static();
        $item->network = $network;
        $item->identity = $identity;
        return $item;
    }

}